<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\JobVacancy;
use App\Models\Team;
use App\Models\Facility;
use App\Models\Legalitas;
use App\Models\News;
use App\Models\Video;
use App\Models\CompanyProfileDownload;
use App\Models\LoginLog;
use App\Models\SiteAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\ValidatesFileUpload;

class AdminController extends Controller
{
    use ValidatesFileUpload;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $stats = [
            'clients' => Client::count(),
            'job_vacancies' => JobVacancy::count(),
            'teams' => Team::count(),
            'facilities' => Facility::count(),
            'legalitas' => Legalitas::count(),
            'news' => News::count(),
            'videos' => Video::count(),
            'company_profile_downloads' => CompanyProfileDownload::count(),
            'site_assets' => SiteAsset::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // Login Logs
    public function loginLogs()
    {
        $logs = LoginLog::where('role', 'admin')
            ->where('user_id', Auth::id())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.logs.index', compact('logs'));
    }

    // Clients Management
    public function clients()
    {
        $clients = Client::orderBy('created_at', 'desc')->get();
        return view('admin.clients.index', compact('clients'));
    }

    public function createClient()
    {
        return view('admin.clients.create');
    }

    public function storeClient(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'about_company' => 'required|string',
            'qualifications' => 'required|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
            'is_hidden' => 'required|boolean',
        ]);

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('clients', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_hidden'] = $request->boolean('is_hidden');

        Client::create($validated);
        return redirect()->route('admin.clients')->with('success', 'Client berhasil ditambahkan.');
    }

    public function editClient($id)
    {
        $client = Client::findOrFail($id);
        return view('admin.clients.edit', compact('client'));
    }

    public function updateClient(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'about_company' => 'required|string',
            'qualifications' => 'required|string',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'required|boolean',
            'is_hidden' => 'required|boolean',
        ]);

        if ($request->hasFile('company_logo')) {
            if ($client->company_logo) {
                Storage::disk('public')->delete($client->company_logo);
            }
            $validated['company_logo'] = $request->file('company_logo')->store('clients', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_hidden'] = $request->boolean('is_hidden');

        $client->update($validated);
        return redirect()->route('admin.clients')->with('success', 'Client berhasil diperbarui.');
    }

    public function deleteClient($id)
    {
        $client = Client::findOrFail($id);
        if ($client->company_logo) {
            Storage::disk('public')->delete($client->company_logo);
        }
        $client->delete();
        return redirect()->route('admin.clients')->with('success', 'Client berhasil dihapus.');
    }

    public function bulkUpdateClients(Request $request)
    {
        $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id',
            'action' => 'required|string|in:hide,show,delete',
        ]);

        $ids = $request->client_ids;
        $action = $request->action;

        if ($action === 'hide') {
            Client::whereIn('id', $ids)->update(['is_hidden' => 1]);
            $message = count($ids) . ' client berhasil disembunyikan dari website.';
        } elseif ($action === 'show') {
            Client::whereIn('id', $ids)->update(['is_hidden' => 0]);
            $message = count($ids) . ' client berhasil ditampilkan di website.';
        } elseif ($action === 'delete') {
            $clients = Client::whereIn('id', $ids)->get();
            foreach ($clients as $client) {
                if ($client->company_logo) {
                    Storage::disk('public')->delete($client->company_logo);
                }
                $client->delete();
            }
            $message = count($ids) . ' client berhasil dihapus.';
        }

        return redirect()->route('admin.clients')->with('success', $message);
    }

    // News Management
    public function news()
    {
        $news = News::orderBy('created_at', 'desc')->get();
        return view('admin.news.index', compact('news'));
    }

    public function createNews()
    {
        return view('admin.news.create');
    }

    public function storeNews(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_headline' => 'required|in:0,1',
            'is_published' => 'required|in:0,1',
        ]);

        // Validate max 3 images
        if ($request->hasFile('images')) {
            $imagesCount = count($request->file('images'));
            if ($imagesCount > 3) {
                return back()->withErrors(['images' => 'Maksimal 3 foto yang dapat diupload.'])->withInput();
            }
        }

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        // Handle multiple images upload
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $images[] = $image->store('news', 'public');
                }
            }
        }
        // Set images (can be empty array)
        $validated['images'] = !empty($images) ? $images : null;

        // Convert string to boolean properly
        $validated['is_headline'] = ($request->input('is_headline') == '1' || $request->input('is_headline') == 1);
        $validated['is_published'] = ($request->input('is_published') == '1' || $request->input('is_published') == 1);
        $validated['views'] = 0;

        // Ensure slug is generated from title, not content
        if (empty($validated['slug']) || strlen($validated['slug']) > 100) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
            // Ensure uniqueness
            $baseSlug = $validated['slug'];
            $counter = 1;
            while (News::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        } else {
            // Sanitize slug to remove invalid characters
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['slug']);
        }

        try {
            // Check if images column exists, if not, remove from validated
            try {
                \DB::select("SELECT images FROM news LIMIT 1");
            } catch (\Exception $e) {
                // Column doesn't exist, remove images from validated
                unset($validated['images']);
            }
            
            // Remove images from validated if empty to let model handle it
            if (isset($validated['images']) && empty($validated['images'])) {
                unset($validated['images']);
            }
            
            $news = News::create($validated);
            
            if ($news) {
                return redirect()->route('admin.news')->with('success', 'Berita berhasil ditambahkan.');
            } else {
                return back()->withErrors(['error' => 'Gagal menyimpan berita. Silakan coba lagi.'])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors specifically
            if (strpos($e->getMessage(), "Unknown column 'images'") !== false) {
                // Column doesn't exist, try without images
                unset($validated['images']);
                try {
                    $news = News::create($validated);
                    if ($news) {
                        return redirect()->route('admin.news')->with('warning', 'Berita berhasil ditambahkan, namun foto tambahan tidak tersimpan karena kolom images belum ada di database. Silakan jalankan migration atau tambahkan kolom images secara manual.');
                    }
                } catch (\Exception $e2) {
                    \Log::error('Error creating news (retry): ' . $e2->getMessage());
                }
            }
            \Log::error('Database error creating news: ' . $e->getMessage());
            \Log::error('Validated data: ' . json_encode($validated));
            return back()->withErrors(['error' => 'Terjadi kesalahan database: ' . $e->getMessage() . '. Pastikan kolom images sudah ditambahkan ke tabel news.'])->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating news: ' . $e->getMessage());
            \Log::error('Validated data: ' . json_encode($validated));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan berita: ' . $e->getMessage()])->withInput();
        }
    }

    public function editNews($id)
    {
        $newsItem = News::findOrFail($id);
        return view('admin.news.edit', compact('newsItem'));
    }

    public function updateNews(Request $request, $id)
    {
        $newsItem = News::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $newsItem->id,
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
            'is_headline' => 'required|in:0,1',
            'is_published' => 'required|in:0,1',
        ]);

        // Validate max 3 images (existing + new)
        // Ensure existingImages is an array
        $existingImages = $newsItem->images ?? [];
        if (!is_array($existingImages)) {
            $existingImages = is_string($existingImages) ? json_decode($existingImages, true) : [];
        }
        if ($existingImages === null) {
            $existingImages = [];
        }
        
        $newImagesCount = $request->hasFile('images') ? count($request->file('images')) : 0;
        $removeImagesCount = $request->has('remove_images') ? count($request->remove_images) : 0;
        $totalImages = count($existingImages) - $removeImagesCount + $newImagesCount;
        
        if ($totalImages > 3) {
            return back()->withErrors(['images' => 'Maksimal 3 foto yang dapat diupload.'])->withInput();
        }

        if ($request->hasFile('thumbnail')) {
            if ($newsItem->thumbnail) {
                Storage::disk('public')->delete($newsItem->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('news', 'public');
        }

        // Handle remove images
        if ($request->has('remove_images') && !empty($request->remove_images)) {
            $removeImages = is_array($request->remove_images) ? $request->remove_images : json_decode($request->remove_images, true);
            if ($removeImages) {
                $remainingImages = [];
                foreach ($existingImages as $image) {
                    if (!in_array($image, $removeImages)) {
                        $remainingImages[] = $image;
                    } else {
                        Storage::disk('public')->delete($image);
                    }
                }
                $existingImages = $remainingImages;
            }
        }

        // Handle new images upload
        $finalImages = $existingImages; // Start with existing images
        
        if ($request->hasFile('images')) {
            $newImages = [];
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $newImages[] = $image->store('news', 'public');
                }
            }
            $finalImages = array_merge($finalImages, $newImages);
        }
        
        // Always set images, even if empty array (but set to null if truly empty)
        // Ensure it's a proper array before saving
        if (!empty($finalImages) && is_array($finalImages)) {
            $validated['images'] = array_values($finalImages); // Re-index array
        } else {
            $validated['images'] = null;
        }

        // Convert string to boolean properly
        $validated['is_headline'] = ($request->input('is_headline') == '1' || $request->input('is_headline') == 1);
        $validated['is_published'] = ($request->input('is_published') == '1' || $request->input('is_published') == 1);

        // Ensure slug is generated from title, not content
        if (empty($validated['slug']) || strlen($validated['slug']) > 100) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['title']);
            // Ensure uniqueness
            $baseSlug = $validated['slug'];
            $counter = 1;
            while (News::where('slug', $validated['slug'])->where('id', '!=', $newsItem->id)->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        } else {
            // Sanitize slug to remove invalid characters
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['slug']);
        }

        try {
            // Check if images column exists, if not, remove from validated
            try {
                \DB::select("SELECT images FROM news LIMIT 1");
            } catch (\Exception $e) {
                // Column doesn't exist, remove images from validated
                unset($validated['images']);
            }
            
            // Remove images from validated if empty to let model handle it
            if (isset($validated['images']) && empty($validated['images'])) {
                unset($validated['images']);
            }
            
            $newsItem->update($validated);
            return redirect()->route('admin.news')->with('success', 'Berita berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors specifically
            if (strpos($e->getMessage(), "Unknown column 'images'") !== false) {
                // Column doesn't exist, try without images
                unset($validated['images']);
                try {
                    $newsItem->update($validated);
                    return redirect()->route('admin.news')->with('warning', 'Berita berhasil diperbarui, namun foto tambahan tidak tersimpan karena kolom images belum ada di database. Silakan jalankan migration atau tambahkan kolom images secara manual.');
                } catch (\Exception $e2) {
                    \Log::error('Error updating news (retry): ' . $e2->getMessage());
                }
            }
            \Log::error('Database error updating news: ' . $e->getMessage());
            \Log::error('Validated data: ' . json_encode($validated));
            return back()->withErrors(['error' => 'Terjadi kesalahan database: ' . $e->getMessage() . '. Pastikan kolom images sudah ditambahkan ke tabel news.'])->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating news: ' . $e->getMessage());
            \Log::error('Validated data: ' . json_encode($validated));
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui berita: ' . $e->getMessage()])->withInput();
        }
    }

    public function deleteNews($id)
    {
        $newsItem = News::findOrFail($id);
        if ($newsItem->thumbnail) {
            Storage::disk('public')->delete($newsItem->thumbnail);
        }
        $newsItem->delete();
        return redirect()->route('admin.news')->with('success', 'Berita berhasil dihapus.');
    }

    // Job Vacancies Management
    public function jobVacancies()
    {
        $jobVacancies = JobVacancy::orderBy('created_at', 'desc')->get();
        return view('admin.job-vacancies.index', compact('jobVacancies'));
    }

    public function createJobVacancy()
    {
        return view('admin.job-vacancies.create');
    }

    public function storeJobVacancy(Request $request)
    {
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'responsibilities' => 'required|string',
            'qualifications' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('job_vacancies', 'public');
        }

        JobVacancy::create($validated);
        return redirect()->route('admin.job-vacancies')->with('success', 'Job vacancy berhasil ditambahkan.');
    }

    public function editJobVacancy($id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        return view('admin.job-vacancies.edit', compact('jobVacancy'));
    }

    public function updateJobVacancy(Request $request, $id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'responsibilities' => 'required|string',
            'qualifications' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($jobVacancy->photo) {
                Storage::disk('public')->delete($jobVacancy->photo);
            }
            $validated['photo'] = $request->file('photo')->store('job_vacancies', 'public');
        }

        $jobVacancy->update($validated);
        return redirect()->route('admin.job-vacancies')->with('success', 'Job vacancy berhasil diperbarui.');
    }

    public function deleteJobVacancy($id)
    {
        $jobVacancy = JobVacancy::findOrFail($id);
        if ($jobVacancy->photo) {
            Storage::disk('public')->delete($jobVacancy->photo);
        }
        $jobVacancy->delete();
        return redirect()->route('admin.job-vacancies')->with('success', 'Job vacancy berhasil dihapus.');
    }

    // Teams Management
    public function teams()
    {
        $teams = Team::orderBy('order')->get();
        return view('admin.teams.index', compact('teams'));
    }

    public function createTeam()
    {
        return view('admin.teams.create');
    }

    public function storeTeam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'category' => 'required|string|in:director,executive,marketing,legal',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('teams', 'public');
        }

        Team::create($validated);
        return redirect()->route('admin.teams')->with('success', 'Team member berhasil ditambahkan.');
    }

    public function editTeam($id)
    {
        $team = Team::findOrFail($id);
        return view('admin.teams.edit', compact('team'));
    }

    public function updateTeam(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'category' => 'required|string|in:director,executive,marketing,legal',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order' => 'nullable|integer',
        ]);

        if ($request->hasFile('photo')) {
            if ($team->photo) {
                Storage::disk('public')->delete($team->photo);
            }
            $validated['photo'] = $request->file('photo')->store('teams', 'public');
        }

        $team->update($validated);
        return redirect()->route('admin.teams')->with('success', 'Team member berhasil diperbarui.');
    }

    public function deleteTeam($id)
    {
        $team = Team::findOrFail($id);
        if ($team->photo) {
            Storage::disk('public')->delete($team->photo);
        }
        $team->delete();
        return redirect()->route('admin.teams')->with('success', 'Team member berhasil dihapus.');
    }

    // Facilities Management
    public function facilities()
    {
        $facilities = Facility::orderBy('order')->get();
        return view('admin.facilities.index', compact('facilities'));
    }

    public function createFacility()
    {
        return view('admin.facilities.create');
    }

    public function storeFacility(Request $request)
    {
        $rules = [
            'category' => 'required|string|in:fasilitas,galeri',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->category === 'fasilitas') {
            $rules['name'] = 'required|string|max:255';
            $rules['order'] = 'nullable|integer';
        } else {
            $rules['name'] = 'nullable|string|max:255';
            $rules['order'] = 'nullable|integer';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('facilities', 'public');
        }

        if ($request->category === 'galeri') {
            $validated['name'] = null;
            $validated['order'] = 0;
        }

        Facility::create($validated);
        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function editFacility($id)
    {
        $facility = Facility::findOrFail($id);
        return view('admin.facilities.edit', compact('facility'));
    }

    public function updateFacility(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);
        $rules = [
            'category' => 'required|string|in:fasilitas,galeri',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        if ($request->category === 'fasilitas') {
            $rules['name'] = 'required|string|max:255';
            $rules['order'] = 'nullable|integer';
        } else {
            $rules['name'] = 'nullable|string|max:255';
            $rules['order'] = 'nullable|integer';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('photo')) {
            if ($facility->photo) {
                Storage::disk('public')->delete($facility->photo);
            }
            $validated['photo'] = $request->file('photo')->store('facilities', 'public');
        }

        if ($request->category === 'galeri') {
            $validated['name'] = null;
            $validated['order'] = 0;
        }

        $facility->update($validated);
        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function deleteFacility($id)
    {
        $facility = Facility::findOrFail($id);
        if ($facility->photo) {
            Storage::disk('public')->delete($facility->photo);
        }
        $facility->delete();
        return redirect()->route('admin.facilities')->with('success', 'Fasilitas berhasil dihapus.');
    }

    // Legalitas Management
    public function legalitas()
    {
        $legalitas = Legalitas::orderBy('order')->get();
        return view('admin.legalitas.index', compact('legalitas'));
    }

    public function createLegalitas()
    {
        return view('admin.legalitas.create');
    }

    public function storeLegalitas(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'order' => 'nullable|integer',
        ]);

        if ($request->hasFile('document')) {
            $validated['document'] = $request->file('document')->store('legalitas', 'public');
        }

        Legalitas::create($validated);
        return redirect()->route('admin.legalitas')->with('success', 'Legalitas berhasil ditambahkan.');
    }

    public function editLegalitas($id)
    {
        $legalitas = Legalitas::findOrFail($id);
        return view('admin.legalitas.edit', compact('legalitas'));
    }

    public function updateLegalitas(Request $request, $id)
    {
        $legalitas = Legalitas::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'order' => 'nullable|integer',
        ]);

        if ($request->hasFile('document')) {
            if ($legalitas->document) {
                Storage::disk('public')->delete($legalitas->document);
            }
            $validated['document'] = $request->file('document')->store('legalitas', 'public');
        }

        $legalitas->update($validated);
        return redirect()->route('admin.legalitas')->with('success', 'Legalitas berhasil diperbarui.');
    }

    public function deleteLegalitas($id)
    {
        $legalitas = Legalitas::findOrFail($id);
        if ($legalitas->document) {
            Storage::disk('public')->delete($legalitas->document);
        }
        $legalitas->delete();
        return redirect()->route('admin.legalitas')->with('success', 'Legalitas berhasil dihapus.');
    }

    // Change Password
    public function showChangePasswordForm()
    {
        return view('admin.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|max:255',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'new_password.regex' => 'Password baru harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            \Log::warning('Failed password change attempt (admin)', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return back()->withErrors(['current_password' => 'Password saat ini tidak benar.'])->withInput();
        }

        // Check if new password is different from current password
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'Password baru harus berbeda dengan password saat ini.'])->withInput();
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        \Log::channel('activity')->info('Password changed (admin)', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return redirect()->route('admin.change-password')->with('success', 'Password berhasil diubah.');
    }

    public function updateEmail(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password_email' => 'required|string|max:255',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'current_password_email.required' => 'Password saat ini wajib diisi.',
        ]);

        if (!Hash::check($request->current_password_email, $user->password)) {
            \Log::warning('Failed email change attempt (admin)', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return back()->withErrors(['current_password_email' => 'Password saat ini tidak benar.'])->withInput();
        }

        $oldEmail = $user->email;
        $user->email = $request->email;
        $user->save();

        \Log::channel('activity')->info('Email changed (admin)', [
            'user_id' => $user->id,
            'old_email' => $oldEmail,
            'new_email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return redirect()->route('admin.change-password')->with('success', 'Email berhasil diubah dari ' . $oldEmail . ' ke ' . $user->email);
    }


    // Company Profile Downloads Management
    public function companyProfileDownloads()
    {
        $downloads = CompanyProfileDownload::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.company-profile-downloads.index', compact('downloads'));
    }

    public function editCompanyProfileDownload($id)
    {
        $download = CompanyProfileDownload::findOrFail($id);
        return view('admin.company-profile-downloads.edit', compact('download'));
    }

    public function updateCompanyProfileDownload(Request $request, $id)
    {
        $download = CompanyProfileDownload::findOrFail($id);
        
        $validated = $request->validate([
            'company_name' => 'required|string|min:2|max:255',
            'full_name' => 'required|string|min:2|max:255',
            'position' => 'required|string|min:2|max:255',
            'contact' => 'required|string|max:255',
            'is_contacted' => 'nullable|boolean',
        ], [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'company_name.min' => 'Nama perusahaan minimal 2 karakter.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.min' => 'Nama lengkap minimal 2 karakter.',
            'position.required' => 'Jabatan wajib diisi.',
            'position.min' => 'Jabatan minimal 2 karakter.',
            'contact.required' => 'Kontak wajib diisi.',
        ]);

        // Validate contact format
        $contact = trim($validated['contact']);
        $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
        $phonePattern = '/^(\+62|62|0)[0-9]{9,13}$/';
        $phonePatternSimple = '/^[0-9+\-\s()]{10,20}$/';
        
        $cleanPhone = preg_replace('/[\s\-()]/', '', $contact);
        $isValidEmail = preg_match($emailPattern, $contact);
        $isValidPhone = preg_match($phonePattern, $cleanPhone) || preg_match($phonePatternSimple, $contact);
        
        if (!$isValidEmail && !$isValidPhone) {
            return back()->withErrors(['contact' => 'Nomor HP atau Email tidak valid.'])->withInput();
        }

        $validated['is_contacted'] = $request->has('is_contacted') ? true : false;
        $validated['contact'] = $contact;

        $download->update($validated);
        
        return redirect()->route('admin.company-profile-downloads')->with('success', 'Data berhasil diperbarui.');
    }

    public function deleteCompanyProfileDownload($id)
    {
        $download = CompanyProfileDownload::findOrFail($id);
        $download->delete();
        
        return redirect()->route('admin.company-profile-downloads')->with('success', 'Data berhasil dihapus.');
    }

    public function toggleContactedStatus($id)
    {
        $download = CompanyProfileDownload::findOrFail($id);
        $download->is_contacted = !$download->is_contacted;
        $download->save();
        
        $status = $download->is_contacted ? 'ditandai sudah dihubungi' : 'ditandai belum dihubungi';
        return redirect()->route('admin.company-profile-downloads')->with('success', "Data berhasil {$status}.");
    }

    public function exportCompanyProfileDownloads()
    {
        $downloads = CompanyProfileDownload::orderBy('created_at', 'desc')->get();
        
        // Set headers for Excel file
        $filename = 'Data_Download_Company_Profile_' . date('Y-m-d_His') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ];
        
        $response = response()->stream(function() use ($downloads) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Excel file structure with proper formatting
            echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
            echo "<head>";
            echo "<meta charset='UTF-8'>";
            echo "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Download</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
            echo "</head>";
            echo "<body>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            
            // Header row with styling
            echo "<tr style='background-color: #e63946; color: white; font-weight: bold; text-align: center;'>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>No</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Nama Perusahaan</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Nama Lengkap</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Jabatan</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Kontak (Email/HP)</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Status</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Tanggal Download</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Waktu Download</th>";
            echo "</tr>";
            
            // Data rows
            $no = 1;
            foreach ($downloads as $download) {
                $status = $download->is_contacted ? 'Sudah Dihubungi' : 'Belum Dihubungi';
                $date = $download->created_at->format('d/m/Y');
                $time = $download->created_at->format('H:i:s');
                
                // Alternate row colors for better readability
                $bgColor = $no % 2 == 0 ? '#f9f9f9' : '#ffffff';
                
                echo "<tr style='background-color: {$bgColor};'>";
                echo "<td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>" . $no . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($download->company_name, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($download->full_name, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($download->position, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($download->contact, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>" . $status . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>" . $date . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>" . $time . "</td>";
                echo "</tr>";
                
                $no++;
            }
            
            // Summary row
            echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>";
            echo "<td colspan='5' style='padding: 10px; border: 1px solid #ddd; text-align: right;'>Total Data:</td>";
            echo "<td colspan='3' style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $downloads->count() . " Data</td>";
            echo "</tr>";
            
            echo "</table>";
            echo "</body>";
            echo "</html>";
            
            fclose($file);
        }, 200, $headers);
        
        return $response;
    }

    // ==========================================
    // Site Assets Management (Kelola Gambar Web)
    // ==========================================
    public function siteAssets(Request $request)
    {
        $query = SiteAsset::query();
        if ($request->has('page_filter') && $request->page_filter != '') {
            $query->where('page', $request->page_filter);
        }
        $siteAssets = $query->orderBy('page')->orderBy('id')->get();
        $pages = SiteAsset::select('page')->distinct()->pluck('page');

        return view('admin.site-assets.index', compact('siteAssets', 'pages'));
    }

    public function createSiteAsset()
    {
        $existingPages = SiteAsset::select('page')->distinct()->pluck('page');
        return view('admin.site-assets.create', compact('existingPages'));
    }

    public function storeSiteAsset(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'title' => 'required|string|max:255',
            'page' => 'required|string|max:255',
            'placement' => 'required|string|max:255',
            'asset_key' => 'required|string|max:255',
            'recommended_size' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'default_url' => 'nullable|url',
        ]);

        $asset = SiteAsset::where('asset_key', $request->asset_key)->first();

        $imagePath = $asset ? $asset->image_path : null;
        if ($request->hasFile('image')) {
            // Delete old file if existing
            if ($asset && $asset->image_path && Storage::disk('public')->exists($asset->image_path)) {
                Storage::disk('public')->delete($asset->image_path);
            }
            $imagePath = $request->file('image')->store('site-assets', 'public');
        }

        SiteAsset::updateOrCreate(
            ['asset_key' => $request->asset_key],
            [
                'title' => $request->title,
                'page' => $request->page,
                'placement' => $request->placement,
                'image_path' => $imagePath,
                'default_url' => $request->default_url ?? ($asset ? $asset->default_url : null),
                'recommended_size' => $request->recommended_size,
                'description' => $request->description,
            ]
        );

        return redirect()->route('admin.site-assets')->with('success', "Aset gambar '{$request->title}' berhasil disimpan!");
    }

    public function deleteSiteAsset($id)
    {
        $asset = SiteAsset::findOrFail($id);
        if ($asset->image_path && Storage::disk('public')->exists($asset->image_path)) {
            Storage::disk('public')->delete($asset->image_path);
        }
        $asset->delete();

        return redirect()->route('admin.site-assets')->with('success', "Aset gambar '{$asset->title}' berhasil dihapus!");
    }

    public function editSiteAsset($id)
    {
        $asset = SiteAsset::findOrFail($id);
        return view('admin.site-assets.edit', compact('asset'));
    }

    public function updateSiteAsset(Request $request, $id)
    {
        $asset = SiteAsset::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'title' => 'required|string|max:255',
            'placement' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'default_url' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            // Delete old file if exists
            if ($asset->image_path && Storage::disk('public')->exists($asset->image_path)) {
                Storage::disk('public')->delete($asset->image_path);
            }

            $path = $request->file('image')->store('site-assets', 'public');
            $asset->image_path = $path;
        }

        if ($request->has('remove_custom_image') && $request->remove_custom_image == '1') {
            if ($asset->image_path && Storage::disk('public')->exists($asset->image_path)) {
                Storage::disk('public')->delete($asset->image_path);
            }
            $asset->image_path = null;
        }

        $asset->title = $request->title;
        if ($request->has('placement')) {
            $asset->placement = $request->placement;
        }
        if ($request->has('description')) {
            $asset->description = $request->description;
        }
        if ($request->has('default_url')) {
            $asset->default_url = $request->default_url;
        }

        $asset->save();

        return redirect()->route('admin.site-assets')->with('success', "Gambar '{$asset->title}' berhasil diperbarui!");
    }
}







