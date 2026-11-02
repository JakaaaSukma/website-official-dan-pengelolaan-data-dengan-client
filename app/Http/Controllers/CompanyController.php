<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\Participant;
use App\Models\Member;
use App\Models\MemberStatus;
use App\Models\BroadcastTemplate;
use App\Models\LoginLog;
use App\Helpers\ExcelReader;
use App\Services\GoogleSheetService;
use Carbon\Carbon;
use App\Traits\ValidatesFileUpload;

class CompanyController extends Controller
{
    use ValidatesFileUpload;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('company.user');
    }

    /**
     * Display a listing of participants for the authenticated company user.
     */
    public function index(Request $request)
    {
        $query = Participant::where('user_id', Auth::id());

        // Search by participant name
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Filter by company name
        if ($request->filled('nama_perusahaan')) {
            $query->where('nama_perusahaan', 'like', '%' . $request->nama_perusahaan . '%');
        }

        // Filter by contract status
        $now = Carbon::now();
        $oneMonthFromNow = $now->copy()->addMonth();
        
        if ($request->filled('status_kontrak') && $request->status_kontrak != 'all') {
            switch ($request->status_kontrak) {
                case 'expired':
                    $query->where('kontrak_habis', '<', $now);
                    break;
                case 'expiring_soon':
                    $query->where('kontrak_habis', '>=', $now)
                          ->where('kontrak_habis', '<=', $oneMonthFromNow);
                    break;
                case 'active':
                    $query->where('kontrak_habis', '>', $oneMonthFromNow);
                    break;
            }
        }

        // Filter by followup status
        if ($request->filled('status_followup') && $request->status_followup != 'all') {
            if ($request->status_followup == 'followed_up') {
                $query->where('is_followed_up', true);
            } elseif ($request->status_followup == 'not_followed_up') {
                $query->where('is_followed_up', false);
            }
        }

        // Get all participants for statistics (before filtering)
        $allParticipants = Participant::where('user_id', Auth::id())->get();
        
        // Get participants whose contract will expire within 1 month
        $expiringSoon = $allParticipants->filter(function($participant) use ($now, $oneMonthFromNow) {
            return !$participant->kontrak_habis->isPast() && 
                   $participant->kontrak_habis->gte($now) && 
                   $participant->kontrak_habis->lte($oneMonthFromNow);
        })->sortBy('kontrak_habis');

        // Count by status (from all participants)
        $expiredCount = $allParticipants->filter(function($participant) {
            return $participant->kontrak_habis->isPast();
        })->count();

        $expiringCount = $expiringSoon->count();
        $activeCount = $allParticipants->filter(function($participant) use ($oneMonthFromNow) {
            return $participant->kontrak_habis > $oneMonthFromNow && !$participant->kontrak_habis->isPast();
        })->count();

        // Get unique company names for filter dropdown
        $companyNames = Participant::where('user_id', Auth::id())
            ->distinct()
            ->pluck('nama_perusahaan')
            ->sort()
            ->values();

        // Apply sorting and get filtered results with pagination (50 per page)
        $participants = $query->orderBy('kontrak_habis', 'asc')->paginate(50)->appends(request()->query());

        return view('company.participants.index', compact(
            'participants', 
            'expiringSoon', 
            'expiringCount', 
            'expiredCount', 
            'activeCount',
            'companyNames'
        ));
    }

    /**
     * Show the form for creating a new participant.
     */
    public function create()
    {
        return view('company.participants.create');
    }

    /**
     * Store a newly created participant in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_perusahaan' => 'required|string|max:255',
            'mulai_kontrak' => 'required|date',
            'kontrak_habis' => 'required|date|after:mulai_kontrak',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'nik.string' => 'NIK harus berupa teks.',
            'nik.max' => 'NIK maksimal 20 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'nama_perusahaan.string' => 'Nama perusahaan harus berupa teks.',
            'nama_perusahaan.max' => 'Nama perusahaan maksimal 255 karakter.',
            'mulai_kontrak.required' => 'Mulai kontrak wajib diisi.',
            'mulai_kontrak.date' => 'Mulai kontrak harus berupa tanggal yang valid.',
            'kontrak_habis.required' => 'Kontrak habis wajib diisi.',
            'kontrak_habis.date' => 'Kontrak habis harus berupa tanggal yang valid.',
            'kontrak_habis.after' => 'Tanggal kontrak habis harus setelah tanggal mulai kontrak.',
        ]);

            Participant::create([
                'nama' => $request->nama,
                'nik' => $request->nik ?? '',
                'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'nama_perusahaan' => $request->nama_perusahaan,
            'mulai_kontrak' => $request->mulai_kontrak,
            'kontrak_habis' => $request->kontrak_habis,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('company.participants')->with('success', 'Data peserta magang berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit($id)
    {
        $participant = Participant::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('company.participants.edit', compact('participant'));
    }

    /**
     * Update the specified participant in storage.
     */
    public function update(Request $request, $id)
    {
        $participant = Participant::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_perusahaan' => 'required|string|max:255',
            'mulai_kontrak' => 'required|date',
            'kontrak_habis' => 'required|date|after:mulai_kontrak',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'nama.string' => 'Nama harus berupa teks.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'nik.string' => 'NIK harus berupa teks.',
            'nik.max' => 'NIK maksimal 20 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string' => 'Alamat harus berupa teks.',
            'nama_perusahaan.required' => 'Nama perusahaan wajib diisi.',
            'nama_perusahaan.string' => 'Nama perusahaan harus berupa teks.',
            'nama_perusahaan.max' => 'Nama perusahaan maksimal 255 karakter.',
            'mulai_kontrak.required' => 'Mulai kontrak wajib diisi.',
            'mulai_kontrak.date' => 'Mulai kontrak harus berupa tanggal yang valid.',
            'kontrak_habis.required' => 'Kontrak habis wajib diisi.',
            'kontrak_habis.date' => 'Kontrak habis harus berupa tanggal yang valid.',
            'kontrak_habis.after' => 'Tanggal kontrak habis harus setelah tanggal mulai kontrak.',
        ]);

            $participant->update([
                'nama' => $request->nama,
                'nik' => $request->nik ?? '',
                'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'nama_perusahaan' => $request->nama_perusahaan,
            'mulai_kontrak' => $request->mulai_kontrak,
            'kontrak_habis' => $request->kontrak_habis,
        ]);

        return redirect()->route('company.participants')->with('success', 'Data peserta magang berhasil diperbarui.');
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroy($id)
    {
        $participant = Participant::where('user_id', Auth::id())
            ->findOrFail($id);

        $participant->delete();

        return redirect()->route('company.participants')->with('success', 'Data peserta magang berhasil dihapus.');
    }

    /**
     * Show the form for editing multiple participants (bulk edit).
     */
    public function bulkEdit(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter(array_map('trim', $ids));
        
        if (empty($ids)) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Tidak ada data yang dipilih untuk diedit.']);
        }

        // Get participants that belong to the authenticated user
        $participants = Participant::where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->get();

        if ($participants->isEmpty()) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Data peserta yang dipilih tidak ditemukan atau tidak memiliki akses.']);
        }

        return view('company.participants.bulk-edit', compact('participants'));
    }

    /**
     * Update multiple participants (bulk update).
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'mulai_kontrak' => 'nullable|date',
            'kontrak_habis' => 'nullable|date',
            'nama_perusahaan' => 'nullable|string|max:255',
        ], [
            'ids.required' => 'ID peserta wajib diisi.',
            'mulai_kontrak.date' => 'Mulai kontrak harus berupa tanggal yang valid.',
            'kontrak_habis.date' => 'Kontrak habis harus berupa tanggal yang valid.',
            'nama_perusahaan.string' => 'Nama perusahaan harus berupa teks.',
            'nama_perusahaan.max' => 'Nama perusahaan maksimal 255 karakter.',
        ]);

        // Custom validation: kontrak_habis must be after mulai_kontrak if both are provided
        if ($request->filled('mulai_kontrak') && $request->filled('kontrak_habis')) {
            $request->validate([
                'kontrak_habis' => 'after:mulai_kontrak',
            ], [
                'kontrak_habis.after' => 'Tanggal kontrak habis harus setelah tanggal mulai kontrak.',
            ]);
        }

        // Check if at least one field is provided
        if (!$request->filled('mulai_kontrak') && !$request->filled('kontrak_habis') && !$request->filled('nama_perusahaan')) {
            return redirect()->back()
                ->withErrors(['error' => 'Minimal harus mengisi salah satu field: Mulai Kontrak, Kontrak Habis, atau Nama Perusahaan.'])
                ->withInput();
        }

        $ids = explode(',', $request->ids);
        $ids = array_filter(array_map('trim', $ids));
        
        if (empty($ids)) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Tidak ada data yang dipilih untuk diupdate.']);
        }

        // Get participants that belong to the authenticated user
        $participants = Participant::where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->get();

        if ($participants->isEmpty()) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Data peserta yang dipilih tidak ditemukan atau tidak memiliki akses.']);
        }

        // Prepare update data (only include fields that are filled)
        $updateData = [];
        if ($request->filled('mulai_kontrak')) {
            $updateData['mulai_kontrak'] = $request->mulai_kontrak;
        }
        if ($request->filled('kontrak_habis')) {
            $updateData['kontrak_habis'] = $request->kontrak_habis;
        }
        if ($request->filled('nama_perusahaan')) {
            $updateData['nama_perusahaan'] = $request->nama_perusahaan;
        }

        // Update all selected participants with only the fields that are provided
        $updatedCount = 0;
        foreach ($participants as $participant) {
            $participant->update($updateData);
            $updatedCount++;
        }

        \Log::channel('activity')->info('Bulk update participants', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'participant_ids' => $ids,
            'updated_count' => $updatedCount,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return redirect()->route('company.participants')
            ->with('success', 'Berhasil mengupdate ' . $updatedCount . ' data peserta magang.');
    }

    /**
     * Remove multiple participants from storage (bulk delete).
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter(array_map('trim', $ids));
        
        if (empty($ids)) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Tidak ada data yang dipilih untuk dihapus.']);
        }

        // Get participants that belong to the authenticated user
        $participants = Participant::where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->get();

        if ($participants->isEmpty()) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Data yang dipilih tidak ditemukan atau tidak memiliki akses.']);
        }

        $count = $participants->count();
        $participants->each->delete();

        $message = $count . ' data peserta magang berhasil dihapus.';
        return redirect()->route('company.participants')->with('success', $message);
    }

    /**
     * Bulk followup for multiple participants.
     */
    public function bulkFollowup(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'followup_notes' => 'nullable|string|max:1000',
        ], [
            'ids.required' => 'ID peserta wajib diisi.',
            'followup_notes.max' => 'Catatan followup maksimal 1000 karakter.',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter(array_map('trim', $ids));
        
        if (empty($ids)) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Tidak ada data yang dipilih untuk di-followup.']);
        }

        // Get participants that belong to the authenticated user
        $participants = Participant::where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->get();

        if ($participants->isEmpty()) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Data peserta yang dipilih tidak ditemukan atau tidak memiliki akses.']);
        }

        // Update all selected participants to followed up
        $updatedCount = 0;
        foreach ($participants as $participant) {
            $participant->is_followed_up = true;
            $participant->followed_up_at = Carbon::now();
            if ($request->filled('followup_notes')) {
                $participant->followup_notes = $request->followup_notes;
            }
            $participant->save();
            $updatedCount++;
        }

        \Log::channel('activity')->info('Bulk followup participants', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'participant_ids' => $ids,
            'updated_count' => $updatedCount,
            'has_notes' => $request->filled('followup_notes'),
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $message = 'Berhasil menandai ' . $updatedCount . ' data peserta sebagai "Sudah Difollowup".';
        if ($request->filled('followup_notes')) {
            $message .= ' Catatan followup telah ditambahkan.';
        }
        return redirect()->route('company.participants')->with('success', $message);
    }

    /**
     * Bulk unfollowup (cancel followup) for multiple participants.
     */
    public function bulkUnfollowup(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
        ], [
            'ids.required' => 'ID peserta wajib diisi.',
        ]);

        $ids = explode(',', $request->ids);
        $ids = array_filter(array_map('trim', $ids));
        
        if (empty($ids)) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Tidak ada data yang dipilih untuk membatalkan followup.']);
        }

        // Get participants that belong to the authenticated user
        $participants = Participant::where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->get();

        if ($participants->isEmpty()) {
            return redirect()->route('company.participants')
                ->withErrors(['error' => 'Data peserta yang dipilih tidak ditemukan atau tidak memiliki akses.']);
        }

        // Update all selected participants to unfollowed up
        $updatedCount = 0;
        foreach ($participants as $participant) {
            $participant->is_followed_up = false;
            $participant->followed_up_at = null;
            $participant->followup_notes = null; // Clear notes when unfollowup
            $participant->save();
            $updatedCount++;
        }

        \Log::channel('activity')->info('Bulk unfollowup participants', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'participant_ids' => $ids,
            'updated_count' => $updatedCount,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        $message = 'Berhasil membatalkan followup untuk ' . $updatedCount . ' data peserta.';
        return redirect()->route('company.participants')->with('success', $message);
    }

    /**
     * Toggle followup status for a participant.
     */
    public function toggleFollowup(Request $request, $id)
    {
        $participant = Participant::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'is_followed_up' => 'sometimes|boolean',
            'followup_notes' => 'nullable|string|max:1000',
        ]);

        $participant->is_followed_up = $request->has('is_followed_up') 
            ? $request->boolean('is_followed_up') 
            : !$participant->is_followed_up;
        
        if ($participant->is_followed_up && !$participant->followed_up_at) {
            $participant->followed_up_at = Carbon::now();
        } elseif (!$participant->is_followed_up) {
            $participant->followed_up_at = null;
        }

        if ($request->filled('followup_notes')) {
            $participant->followup_notes = $request->followup_notes;
        }

        $participant->save();

        $statusText = $participant->is_followed_up ? 'Sudah difollowup' : 'Belum difollowup';
        return redirect()->route('company.participants')->with('success', "Status followup berhasil diubah menjadi: {$statusText}");
    }

    /**
     * Display a listing of members.
     */
    public function members(Request $request, GoogleSheetService $googleSheetService)
    {
        // Get filters
        $filters = [
            'age' => $request->input('age'),
            'gender' => $request->input('gender'),
            'status' => $request->input('status'),
            'search' => $request->input('search'),
            'reference' => $request->input('reference'),
            'perusahaan_favorite' => $request->input('perusahaan_favorite'),
        ];

        // Automatic sync: Only sync if explicitly requested or once every 30 minutes
        $lastSyncKey = 'last_member_sync';
        $lastSync = Cache::get($lastSyncKey);
        $refreshCache = $request->input('refresh_cache') == 1;

        if ($refreshCache) {
            // Only sync when user explicitly clicks refresh
            $googleSheetService->syncTemplateReportToDatabase(true);
            Cache::put($lastSyncKey, now(), 1440); // Cache for 24 hours
        }

        // Get members with pagination (no automatic sync on page load)
        $members = $googleSheetService->getMembers($filters, 25, false);

        // Get all unique references for filter - Cache for 1 hour
        $references = Cache::remember('member_references', 3600, function () {
            return Member::whereNotNull('reference')->where('reference', '!=', '')
                ->distinct()
                ->pluck('reference')
                ->sort()
                ->values();
        });
            
        // Get all unique perusahaan favorite for filter - Cache for 1 hour
        $perusahaanFavorites = Cache::remember('member_perusahaan_favorites', 3600, function () {
            return Member::whereNotNull('perusahaan_favorite')->where('perusahaan_favorite', '!=', '')
                ->distinct()
                ->pluck('perusahaan_favorite')
                ->sort()
                ->values();
        });
        
        return view('company.members.index', compact('members', 'references', 'perusahaanFavorites'));
    }

    /**
     * Show the form for creating a new member.
     */
    public function createMember()
    {
        return view('company.members.create');
    }

    /**
     * Show the form for editing the specified member.
     */
    public function editMember($id)
    {
        $member = Member::findOrFail($id);
        return view('company.members.edit', compact('member'));
    }

    /**
     * Store a newly created member in storage.
     */
    public function storeMember(Request $request, GoogleSheetService $googleSheetService)
    {
        $request->validate([
            'reference' => 'nullable|string|max:255',
            'tanggal_daftar' => 'required|date',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'nik_ktp' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'perusahaan_favorite' => 'nullable|string|max:255',
            'status' => 'required|in:sudah_kerja,gagal,belum_tes,sudah_training,belum_training',
        ]);

        $member = Member::create([
            'reference' => $request->reference,
            'tanggal_daftar' => $request->tanggal_daftar,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nik_ktp' => $request->nik_ktp,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'gender' => $request->gender,
            'perusahaan_favorite' => $request->perusahaan_favorite,
            'status' => $request->status,
            'source' => 'local',
        ]);

        // Attempt to write to Google Sheet
        $googleSheetService->appendMemberToSheet($member);

        return redirect()->route('company.members')->with('success', 'Member baru berhasil ditambahkan!');
    }

    /**
     * Update the specified member in storage.
     */
    public function updateMember(Request $request, $id, GoogleSheetService $googleSheetService)
    {
        $request->validate([
            'reference' => 'nullable|string|max:255',
            'tanggal_daftar' => 'required|date',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'nik_ktp' => 'nullable|string|max:20',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'perusahaan_favorite' => 'nullable|string|max:255',
            'status' => 'required|in:sudah_kerja,gagal,belum_tes,sudah_training,belum_training',
        ]);

        $member = Member::findOrFail($id);
        $member->update($request->all());

        // Attempt to update Google Sheet
        $googleSheetService->updateMemberInSheet($member);

        return redirect()->route('company.members')->with('success', 'Data member berhasil diperbarui!');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroyMember($id, GoogleSheetService $googleSheetService)
    {
        $member = Member::findOrFail($id);
        
        // Attempt to delete from Google Sheet first
        $googleSheetService->deleteMemberFromSheet($member);
        
        $member->delete();

        return redirect()->route('company.members')->with('success', 'Data member berhasil dihapus!');
    }

    /**
     * Update the reference of a member.
     */
    public function updateMemberReference(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reference' => 'nullable|string|max:255',
        ]);

        try {
            MemberStatus::updateOrCreate(
                ['email' => $request->email],
                ['reference' => $request->reference]
            );

            return response()->json(['success' => true, 'message' => 'Referensi member berhasil diperbarui.']);
        } catch (\Exception $e) {
            \Log::error('Error updating member reference: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui referensi.'], 500);
        }
    }

    /**
     * Single inline status update for member (by ID)
     */
    public function updateSingleMemberStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:belum_tes,sudah_training,belum_training,sudah_kerja,gagal'
        ]);

        try {
            $member = Member::findOrFail($id);
            $member->update([
                'status' => $request->status
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            
            return back()->with('success', 'Status member berhasil diperbarui!');
        } catch (\Exception $e) {
            \Log::error('Error updating single member status: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengupdate status'], 500);
            }
            return back()->withErrors(['error' => 'Gagal mengupdate status: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk update the status of members (by ID or Email)
     */
    public function bulkUpdateMemberStatus(Request $request)
    {
        if ($request->has('ids') || $request->has('ids[]')) {
            // New: Bulk update by IDs (for inline bulk selection)
            $ids = $request->input('ids', $request->input('ids[]', []));
            $request->merge(['ids' => $ids]);
            
            $request->validate([
                'ids' => 'required|array|min:1',
                'ids.*' => 'integer|exists:members,id',
                'status' => 'required|in:belum_tes,sudah_training,belum_training,sudah_kerja,gagal'
            ]);

            try {
                Member::whereIn('id', $request->ids)->update([
                    'status' => $request->status
                ]);

                if ($request->wantsJson() || $request->acceptsJson()) {
                    return response()->json(['success' => true]);
                }
                
                return back()->with('success', count($request->ids) . ' status member berhasil diperbarui!');
            } catch (\Exception $e) {
                \Log::error('Error bulk updating member status by IDs: ' . $e->getMessage());
                if ($request->wantsJson() || $request->acceptsJson()) {
                    return response()->json(['success' => false, 'message' => 'Gagal mengupdate status'], 500);
                }
                return back()->withErrors(['error' => 'Gagal mengupdate status: ' . $e->getMessage()]);
            }
        } else {
            // Old: Bulk update by emails (for MemberStatus table)
            $request->validate([
                'emails' => 'required|array',
                'emails.*' => 'required|email',
                'status' => 'required|in:sudah_kerja,gagal,belum_tes,sudah_training,belum_training',
            ], [
                'emails.required' => 'Pilih setidaknya satu member.',
            ]);

            try {
                foreach ($request->emails as $email) {
                    MemberStatus::updateOrCreate(
                        ['email' => $email],
                        ['status' => $request->status]
                    );
                }

                return back()->with('success', count($request->emails) . ' status member berhasil diperbarui.');
            } catch (\Exception $e) {
                \Log::error('Error bulk updating member status by emails: ' . $e->getMessage());
                return back()->withErrors(['error' => 'Gagal memperbarui status masal: ' . $e->getMessage()])->withInput();
            }
        }
    }



    /**
     * Search members for broadcast addition.
     * Returns JSON response.
     */
    public function searchMembers(Request $request, GoogleSheetService $googleSheetService)
    {
        $search = $request->input('search');
        
        // Fetch members with search filter
        // Limit to 50 results for performance in the modal
        $paginator = $googleSheetService->getMembers(['search' => $search], 50);
        
        $results = collect($paginator->items())->map(function($member) {
            return [
                'name' => $member->name,
                'phone' => $member->phone,
                'email' => $member->email
            ];
        });

        return response()->json($results);
    }

    /**
     * Export members data to Excel file.
     */
    public function exportMembers(Request $request, GoogleSheetService $googleSheetService)
    {
        // Build query - Optimized: use select to get only needed columns
        $query = Member::select([
            'id', 'reference', 'tanggal_daftar', 'name', 'email', 'phone', 
            'nik_ktp', 'tempat_lahir', 'tanggal_lahir', 'gender', 
            'perusahaan_favorite', 'status', 'created_at'
        ]);

        // Apply filters
        $filters = $request->only(['search', 'reference', 'gender', 'status', 'age', 'perusahaan_favorite']);
        
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                  ->orWhere('nik_ktp', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['reference'])) {
            $references = is_array($filters['reference']) ? $filters['reference'] : [$filters['reference']];
            $query->where(function ($q) use ($references) {
                foreach ($references as $ref) {
                    $q->orWhereRaw('LOWER(reference) LIKE ?', ["%" . strtolower($ref) . "%"]);
                }
            });
        }

        if (!empty($filters['gender'])) {
            $genderMap = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
            $query->where('gender', $genderMap[$filters['gender']] ?? $filters['gender']);
        }

        if (!empty($filters['status'])) {
            $statuses = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $query->whereIn('status', $statuses);
        }

        if (!empty($filters['age'])) {
            if ($filters['age'] === 'under_25') {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 25');
            } elseif ($filters['age'] === 'above_25') {
                $query->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 25');
            }
        }
        
        if (!empty($filters['perusahaan_favorite'])) {
            $perusahaanFavorites = is_array($filters['perusahaan_favorite']) ? $filters['perusahaan_favorite'] : [$filters['perusahaan_favorite']];
            $query->where(function ($q) use ($perusahaanFavorites) {
                foreach ($perusahaanFavorites as $pf) {
                    $q->orWhereRaw('LOWER(perusahaan_favorite) LIKE ?', ["%" . strtolower($pf) . "%"]);
                }
            });
        }

        $members = $query->orderBy('tanggal_daftar', 'desc')->orderBy('id', 'desc')->get();

        // Generate filename with timestamp
        $filename = 'Data_Member_' . date('Y-m-d_His') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ];

        $response = response()->stream(function() use ($members) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Excel file structure with proper formatting
            echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
            echo "<head>";
            echo "<meta charset='UTF-8'>";
            echo "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Member</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
            echo "<style>";
            echo "td { mso-number-format:\\@; }"; // Preserve text format
            echo "</style>";
            echo "</head>";
            echo "<body>";
            echo "<table border='1' cellspacing='0' cellpadding='5'>";
            
            // Table Header
            echo "<tr style='background-color: #4a90e2; color: white;'>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>No</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Referensi</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Tanggal Daftar</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Nama Lengkap</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>NIK</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Tempat Lahir</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Tanggal Lahir</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Jenis Kelamin</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Perusahaan Favorite</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Email Aktif</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Nomor WA</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Status</th>";
            echo "</tr>";
            
            // Table Body
            $no = 1;
            foreach ($members as $member) {
                $statusLabel = '';
                switch ($member->status) {
                    case 'sudah_kerja':
                        $statusLabel = 'Sudah Kerja';
                        break;
                    case 'gagal':
                        $statusLabel = 'Gagal';
                        break;
                    case 'sudah_training':
                        $statusLabel = 'Sudah Training';
                        break;
                    case 'belum_training':
                        $statusLabel = 'Belum Training';
                        break;
                    default:
                        $statusLabel = 'Belum Tes';
                }

                echo "<tr>";
                echo "<td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>" . $no++ . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($member->reference ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($member->tanggal_daftar ? $member->tanggal_daftar->format('d/m/Y') : ($member->created_at ? $member->created_at->format('d/m/Y') : '-')) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($member->name) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($member->nik_ktp ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($member->tempat_lahir ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($member->tanggal_lahir ? $member->tanggal_lahir->format('d/m/Y') : '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($member->gender ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($member->perusahaan_favorite ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($member->email ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($member->phone ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($statusLabel) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            echo "</body>";
            echo "</html>";
            
            fclose($file);
        }, 200, $headers);
        
        \Log::channel('activity')->info('Export members to Excel', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'member_count' => count($members),
            'filters' => $filters,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        return $response;
    }

    /**
     * Show the form for changing password.
     */
    public function showChangePasswordForm()
    {
        return view('company.change-password');
    }

    /**
     * Update the user's password.
     */
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
            \Log::warning('Failed password change attempt (company)', [
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

        \Log::channel('activity')->info('Password changed (company)', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return redirect()->route('company.change-password')->with('success', 'Password berhasil diubah.');
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
            \Log::warning('Failed email change attempt (company)', [
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

        \Log::channel('activity')->info('Email changed (company)', [
            'user_id' => $user->id,
            'old_email' => $oldEmail,
            'new_email' => $user->email,
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);

        return redirect()->route('company.change-password')->with('success', 'Email berhasil diubah dari ' . $oldEmail . ' ke ' . $user->email);
    }

    /**
     * Export participants data to Excel file.
     */
    public function export(Request $request)
    {
        $query = Participant::where('user_id', Auth::id());

        // Apply same filters as index method
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('nama_perusahaan')) {
            $query->where('nama_perusahaan', 'like', '%' . $request->nama_perusahaan . '%');
        }

        $now = Carbon::now();
        $oneMonthFromNow = $now->copy()->addMonth();
        
        if ($request->filled('status_kontrak') && $request->status_kontrak != 'all') {
            switch ($request->status_kontrak) {
                case 'expired':
                    $query->where('kontrak_habis', '<', $now);
                    break;
                case 'expiring_soon':
                    $query->where('kontrak_habis', '>=', $now)
                          ->where('kontrak_habis', '<=', $oneMonthFromNow);
                    break;
                case 'active':
                    $query->where('kontrak_habis', '>', $oneMonthFromNow);
                    break;
            }
        }

        if ($request->filled('status_followup') && $request->status_followup != 'all') {
            if ($request->status_followup == 'followed_up') {
                $query->where('is_followed_up', true);
            } elseif ($request->status_followup == 'not_followed_up') {
                $query->where('is_followed_up', false);
            }
        }

        // Get participants with same ordering as index
        $participants = $query->orderBy('kontrak_habis', 'asc')->get();

        // Generate filename with timestamp
        $filename = 'Data_Peserta_Magang_' . date('Y-m-d_His') . '.xls';
        
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ];

        $response = response()->stream(function() use ($participants) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Excel file structure with proper formatting
            echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
            echo "<head>";
            echo "<meta charset='UTF-8'>";
            echo "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Peserta Magang</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
            echo "<style>";
            echo "td { mso-number-format:\\@; }"; // Preserve text format
            echo "</style>";
            echo "</head>";
            echo "<body>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            
            // Header row with styling
            echo "<tr style='background-color: #457b9d; color: white; font-weight: bold; text-align: center;'>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>No</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Nama</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>NIK</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Tanggal Lahir</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Alamat</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Nama Perusahaan</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Mulai Kontrak</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Kontrak Habis</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Durasi Kontrak</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Status Followup</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Tanggal Followup</th>";
            echo "<th style='padding: 10px; border: 1px solid #ddd;'>Catatan Followup</th>";
            echo "</tr>";
            
            $no = 1;
            foreach ($participants as $participant) {
                // Calculate contract duration
                $mulaiKontrak = Carbon::parse($participant->mulai_kontrak);
                $kontrakHabis = Carbon::parse($participant->kontrak_habis);
                $totalMonths = $mulaiKontrak->diffInMonths($kontrakHabis);
                $tempDate = $mulaiKontrak->copy()->addMonths($totalMonths);
                $remainingDays = $tempDate->diffInDays($kontrakHabis, false);
                if ($remainingDays < 0) {
                    $totalMonths = max(0, $totalMonths - 1);
                    $tempDate = $mulaiKontrak->copy()->addMonths($totalMonths);
                    $remainingDays = $tempDate->diffInDays($kontrakHabis, false);
                }
                
                $durasi = '';
                if ($totalMonths > 0 && $remainingDays > 0) {
                    $durasi = $totalMonths . ' bulan, ' . $remainingDays . ' hari';
                } elseif ($totalMonths > 0 && $remainingDays == 0) {
                    $durasi = $totalMonths . ' bulan';
                } elseif ($totalMonths == 0 && $remainingDays > 0) {
                    $durasi = $remainingDays . ' hari';
                } else {
                    $durasi = '0 hari';
                }
                
                $statusFollowup = $participant->is_followed_up ? 'Sudah Difollowup' : 'Belum Difollowup';
                $tanggalFollowup = $participant->followed_up_at ? $participant->followed_up_at->format('m/d/Y H:i') : '-';
                $catatanFollowup = $participant->followup_notes ?? '-';
                
                echo "<tr>";
                echo "<td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>" . htmlspecialchars($no) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($participant->nama) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($participant->nik ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($participant->tanggal_lahir ? htmlspecialchars($participant->tanggal_lahir->format('m/d/Y')) : '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($participant->alamat ?? '-') . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($participant->nama_perusahaan) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($participant->mulai_kontrak->format('m/d/Y')) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($participant->kontrak_habis->format('m/d/Y')) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($durasi) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($statusFollowup) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($tanggalFollowup) . "</td>";
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($catatanFollowup) . "</td>";
                echo "</tr>";
                
                $no++;
            }
            
            // Summary row
            echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>";
            echo "<td colspan='11' style='padding: 10px; border: 1px solid #ddd; text-align: right;'>Total Data:</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $participants->count() . " Data</td>";
            echo "</tr>";
            
            echo "</table>";
            echo "</body>";
            echo "</html>";
            
            fclose($file);
        }, 200, $headers);
        
        \Log::channel('activity')->info('Export participants to Excel', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'participant_count' => $participants->count(),
            'filters' => $request->all(),
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        return $response;
    }

    /**
     * Show the form for importing participants from Excel/CSV file.
     */
    public function showImportForm()
    {
        return view('company.participants.import');
    }

    /**
     * Download template file for import.
     */
    public function downloadTemplate()
    {
        $filename = 'template_import_peserta_magang.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Add BOM for UTF-8 to ensure Excel displays correctly
        $output = "\xEF\xBB\xBF";
        
        // Add header row
        $output .= "Nama,Tanggal Lahir,Alamat,Nama Perusahaan,Mulai Kontrak,Kontrak Habis\n";
        
        // Add example data
        $output .= "John Doe,01/01/2000,Jl. Contoh No. 123," . Auth::user()->name . ",01/01/2024,12/31/2024\n";
        $output .= "Jane Smith,05/15/1999,Jl. Contoh No. 456," . Auth::user()->name . ",02/01/2024,01/31/2025\n";

        return response($output, 200, $headers);
    }

    /**
     * Import participants from Excel/CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240', // Max 10MB
        ], [
            'file.required' => 'File wajib diupload.',
            'file.file' => 'File tidak valid.',
            'file.mimes' => 'File harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max' => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->storeAs('temp', 'import_' . time() . '_' . $file->getClientOriginalName());
            $fullPath = storage_path('app/' . $filePath);

            // Read file data
            $rows = ExcelReader::readFile($fullPath);
            
            if (empty($rows)) {
                Storage::delete($filePath);
                return back()->withErrors(['file' => 'File kosong atau tidak dapat dibaca.'])->withInput();
            }

            // Detect if first row is header
            $skipHeader = $request->has('skip_header');
            
            // Map data intelligently
            $mappedData = ExcelReader::mapData($rows, $skipHeader);

            if (empty($mappedData)) {
                Storage::delete($filePath);
                return back()->withErrors(['file' => 'Tidak ada data yang valid ditemukan. Pastikan format file sesuai.'])->withInput();
            }

            // Get existing participants for duplicate checking
            $existingParticipants = Participant::where('user_id', Auth::id())->get();

            // Validate and import data
            $successCount = 0;
            $errorCount = 0;
            $duplicateCount = 0;
            $errors = [];
            $duplicates = [];

            foreach ($mappedData as $index => $row) {
                $rowNumber = $skipHeader ? $index + 2 : $index + 1; // +2 because header is row 1, +1 if no header

                // Convert date formats (only if provided)
                if (!empty($row['tanggal_lahir'])) {
                    $row['tanggal_lahir'] = self::convertDate($row['tanggal_lahir']);
                } else {
                    $row['tanggal_lahir'] = null;
                }
                $row['mulai_kontrak'] = self::convertDate($row['mulai_kontrak'] ?? '');
                $row['kontrak_habis'] = self::convertDate($row['kontrak_habis'] ?? '');

                // Validate data
                $validator = Validator::make($row, [
                    'nama' => 'required|string|max:255',
                    'nik' => 'nullable|string|max:20',
                    'tanggal_lahir' => 'nullable|date',
                    'alamat' => 'nullable|string',
                    'nama_perusahaan' => 'nullable|string|max:255',
                    'mulai_kontrak' => 'required|date',
                    'kontrak_habis' => 'required|date',
                ], [
                    'nama.required' => 'Nama wajib diisi (Baris ' . $rowNumber . ')',
                    'tanggal_lahir.date' => 'Format tanggal lahir tidak valid (Baris ' . $rowNumber . ')',
                    'mulai_kontrak.required' => 'Mulai kontrak wajib diisi (Baris ' . $rowNumber . ')',
                    'mulai_kontrak.date' => 'Format mulai kontrak tidak valid (Baris ' . $rowNumber . ')',
                    'kontrak_habis.required' => 'Kontrak habis wajib diisi (Baris ' . $rowNumber . ')',
                    'kontrak_habis.date' => 'Format kontrak habis tidak valid (Baris ' . $rowNumber . ')',
                ]);

                // Custom validation: kontrak_habis harus setelah mulai_kontrak
                $validator->after(function ($validator) use ($row, $rowNumber) {
                    if (!empty($row['mulai_kontrak']) && !empty($row['kontrak_habis'])) {
                        try {
                            $mulaiKontrak = Carbon::parse($row['mulai_kontrak']);
                            $kontrakHabis = Carbon::parse($row['kontrak_habis']);
                            if ($kontrakHabis->lte($mulaiKontrak)) {
                                $validator->errors()->add('kontrak_habis', 'Kontrak habis harus setelah mulai kontrak (Baris ' . $rowNumber . ')');
                            }
                        } catch (\Exception $e) {
                            // Date parsing error will be caught by date validation
                        }
                    }
                });

                if ($validator->fails()) {
                    $errorCount++;
                    foreach ($validator->errors()->all() as $error) {
                        $errors[] = $error;
                    }
                    continue;
                }

                // Set default nama_perusahaan if empty
                if (empty($row['nama_perusahaan'])) {
                    $row['nama_perusahaan'] = Auth::user()->name;
                }

                // Check for exact duplicates (100% match on nama, tanggal_lahir, alamat, nama_perusahaan)
                $isDuplicate = self::checkDuplicate($row, $existingParticipants);
                
                if ($isDuplicate) {
                    $duplicateCount++;
                    $duplicates[] = 'Data baris ' . $rowNumber . ' (Nama: ' . $row['nama'] . ', Tanggal Lahir: ' . ($row['tanggal_lahir'] ?? 'N/A') . ', Alamat: ' . ($row['alamat'] ?? 'N/A') . ') gagal di-upload karena data 100% sama dengan data yang sudah ada';
                    continue;
                }

                // Save to database
                try {
                    // Prepare data for insertion (handle nullable fields)
                    $participantData = [
                        'nama' => $row['nama'],
                        'nik' => $row['nik'] ?? '',
                        'tanggal_lahir' => !empty($row['tanggal_lahir']) ? $row['tanggal_lahir'] : null,
                        'alamat' => !empty($row['alamat']) ? $row['alamat'] : null,
                        'nama_perusahaan' => $row['nama_perusahaan'] ?? Auth::user()->name,
                        'mulai_kontrak' => $row['mulai_kontrak'],
                        'kontrak_habis' => $row['kontrak_habis'],
                        'user_id' => Auth::id(),
                    ];
                    
                    Participant::create($participantData);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = 'Error menyimpan data baris ' . $rowNumber . ': ' . $e->getMessage();
                }
            }

            // Delete temporary file
            Storage::delete($filePath);

            $message = "Import selesai: {$successCount} data berhasil";
            if ($duplicateCount > 0) {
                $message .= ", {$duplicateCount} data duplikasi di-skip";
            }
            if ($errorCount > 0) {
                $message .= ", {$errorCount} data gagal";
            }
            $message .= ".";
            
            if ($errorCount > 0 || $duplicateCount > 0) {
                return back()->with('import_result', [
                    'success' => $successCount,
                    'error' => $errorCount,
                    'duplicate' => $duplicateCount,
                    'errors' => $errors,
                    'duplicates' => $duplicates,
                    'message' => $message
                ])->withInput();
            }

            return redirect()->route('company.participants')->with('success', $message);

        } catch (\Exception $e) {
            if (isset($filePath) && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
            return back()->withErrors(['file' => 'Error membaca file: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Convert various date formats to Y-m-d format
     * Handles Excel serial numbers, Indonesian date formats, and various text formats
     */
    protected static function convertDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        // Trim whitespace
        $dateString = trim($dateString);
        
        // Store original numeric value if it's numeric (for Excel date detection)
        $isNumeric = is_numeric($dateString);
        $numericValue = $isNumeric ? (float)$dateString : null;

        // If already in Y-m-d format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            return $dateString;
        }

        // Try Excel date serial number (if it's a pure number)
        // Excel serial numbers are typically between 1 and 100000 (for dates from 1900-2174)
        if ($isNumeric && $numericValue !== null) {
            // Check if it looks like an Excel serial date (typically 1-100000 for dates)
            if ($numericValue >= 1 && $numericValue < 1000000) {
                try {
                    // Excel epoch: January 1, 1900 is serial number 1
                    // But Excel incorrectly treats 1900 as a leap year, so we need to adjust
                    $baseDate = Carbon::create(1899, 12, 30, 0, 0, 0);
                    $days = (int)floor($numericValue);
                    $date = $baseDate->copy()->addDays($days);
                    
                    // Validate the date is reasonable (between 1900-01-01 and 2100-12-31)
                    $year = $date->year;
                    if ($year >= 1900 && $year <= 2100) {
                        return $date->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    // Not a valid Excel date, continue to other methods
                }
            }
        }

        // Map Indonesian month names to English
        $indonesianMonths = [
            'januari' => 'january', 'jan' => 'january',
            'februari' => 'february', 'feb' => 'february',
            'maret' => 'march', 'mar' => 'march',
            'april' => 'april', 'apr' => 'april',
            'mei' => 'may', 'may' => 'may',
            'juni' => 'june', 'jun' => 'june',
            'juli' => 'july', 'jul' => 'july',
            'agustus' => 'august', 'agu' => 'august', 'ags' => 'august',
            'september' => 'september', 'sep' => 'september', 'sept' => 'september',
            'oktober' => 'october', 'okt' => 'october',
            'november' => 'november', 'nov' => 'november',
            'desember' => 'december', 'des' => 'december', 'dec' => 'december',
        ];

        // Replace Indonesian month names with English
        // Convert to lowercase but preserve the original for some formats
        $dateStringLower = mb_strtolower($dateString, 'UTF-8');
        $dateStringOriginal = $dateString; // Keep original for formats that need case sensitivity
        
        foreach ($indonesianMonths as $id => $en) {
            $dateStringLower = str_replace($id, $en, $dateStringLower);
            // Also replace in original (for case-sensitive formats like DD-Mon-YY)
            $dateStringOriginal = str_ireplace(ucfirst($id), ucfirst($en), $dateStringOriginal);
            $dateStringOriginal = str_ireplace($id, $en, $dateStringOriginal);
        }

        // Try to parse common date formats
        $formats = [
            // Standard formats with 4-digit year
            'd/m/Y', 'd-m-Y', 'd.m.Y', 
            'Y/m/d', 'Y-m-d', 'Y.m.d',
            'm/d/Y', 'm-d-Y', 'm.d.Y',
            // Standard formats with 2-digit year
            'd/m/y', 'd-m-y', 'd.m.y',
            'y/m/d', 'y-m-d', 'y.m.d',
            'm/d/y', 'm-d-y', 'm.d.y',
            // Formats with month abbreviations (DD-Mon-YY, DD-Mon-YYYY)
            'd-M-y', 'd-M-Y', 'd/M/y', 'd/M/Y', 'd.M.y', 'd.M.Y',
            'd MMM y', 'd MMM Y', 'd MMMM y', 'd MMMM Y',
            // Formats with month names (full and abbreviated)
            'd M y', 'd M Y', 'd MMM y', 'd MMM Y', 'd MMMM y', 'd MMMM Y',
            'd-M-y', 'd-M-Y', 'd-MMM-y', 'd-MMM-Y', 'd-MMMM-y', 'd-MMMM-Y',
            'd M, y', 'd M, Y', 'd MMM, y', 'd MMM, Y', 'd MMMM, y', 'd MMMM, Y',
            'M d, y', 'M d, Y', 'MMM d, y', 'MMM d, Y', 'MMMM d, y', 'MMMM d, Y',
            'y M d', 'Y M d', 'y MMM d', 'Y MMM d', 'y MMMM d', 'Y MMMM d',
            // Formats with slashes and month names
            'd/M/y', 'd/M/Y', 'd/MMM/y', 'd/MMM/Y', 'd/MMMM/y', 'd/MMMM/Y',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateStringLower);
                // Validate year is reasonable
                if ($date->year >= 1900 && $date->year <= 2100) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Try parsing with flexible format (Carbon's parse) - try both lowercase and original
        foreach ([$dateStringLower, $dateStringOriginal] as $dateToParse) {
            try {
                $date = Carbon::parse($dateToParse);
                // Validate year is reasonable
                if ($date->year >= 1900 && $date->year <= 2100) {
                    return $date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Failed to parse, try next
                continue;
            }
        }

        // Try strtotime as last resort (works with English month names) - try both lowercase and original
        foreach ([$dateStringLower, $dateStringOriginal] as $dateToParse) {
            try {
                $timestamp = strtotime($dateToParse);
                if ($timestamp !== false && $timestamp > 0) {
                    $year = date('Y', $timestamp);
                    if ($year >= 1900 && $year <= 2100) {
                        return date('Y-m-d', $timestamp);
                    }
                }
            } catch (\Exception $e) {
                // Failed to parse, try next
                continue;
            }
        }

        // Return null if can't parse (validation will catch this)
        return null;
    }

    /**
     * Calculate similarity percentage between two data arrays
     * Returns percentage (0-100)
     */
    protected static function calculateSimilarity($data1, $data2)
    {
        $weights = [
            'nama' => 0.35,             // Name has highest weight (fuzzy match)
            'tanggal_lahir' => 0.30,    // Birth date has high weight (exact match)
            'alamat' => 0.20,           // Address has medium weight (fuzzy match)
            'nama_perusahaan' => 0.15,  // Company name has medium-low weight (exact match)
        ];

        $totalSimilarity = 0;
        $totalWeight = 0;

        // NIK comparison (exact match, case-insensitive) - Optional bonus if both have NIK
        $nik1 = strtolower(trim($data1['nik'] ?? ''));
        $nik2 = strtolower(trim($data2['nik'] ?? ''));
        if (!empty($nik1) && !empty($nik2)) {
            $nikSimilarity = ($nik1 === $nik2) ? 100 : 0;
            // Add bonus weight for NIK match if both have NIK (10% bonus)
            $totalSimilarity += $nikSimilarity * 0.10;
            $totalWeight += 0.10;
        }

        // Name comparison (fuzzy match using similar_text)
        $nama1 = strtolower(trim($data1['nama'] ?? ''));
        $nama2 = strtolower(trim($data2['nama'] ?? ''));
        if (!empty($nama1) && !empty($nama2)) {
            similar_text($nama1, $nama2, $namaPercent);
            $totalSimilarity += $namaPercent * $weights['nama'];
            $totalWeight += $weights['nama'];
        }

        // Birth date comparison (exact match)
        $tanggalLahir1 = $data1['tanggal_lahir'] ?? null;
        $tanggalLahir2 = $data2['tanggal_lahir'] ?? null;
        if (!empty($tanggalLahir1) && !empty($tanggalLahir2)) {
            try {
                $date1 = Carbon::parse($tanggalLahir1)->format('Y-m-d');
                $date2 = Carbon::parse($tanggalLahir2)->format('Y-m-d');
                $tanggalSimilarity = ($date1 === $date2) ? 100 : 0;
                $totalSimilarity += $tanggalSimilarity * $weights['tanggal_lahir'];
                $totalWeight += $weights['tanggal_lahir'];
            } catch (\Exception $e) {
                // Date parsing failed, skip this field
            }
        }

        // Address comparison (fuzzy match using similar_text)
        $alamat1 = strtolower(trim($data1['alamat'] ?? ''));
        $alamat2 = strtolower(trim($data2['alamat'] ?? ''));
        if (!empty($alamat1) && !empty($alamat2)) {
            similar_text($alamat1, $alamat2, $alamatPercent);
            $totalSimilarity += $alamatPercent * $weights['alamat'];
            $totalWeight += $weights['alamat'];
        }

        // Company name comparison (exact match, case-insensitive)
        $perusahaan1 = strtolower(trim($data1['nama_perusahaan'] ?? ''));
        $perusahaan2 = strtolower(trim($data2['nama_perusahaan'] ?? ''));
        if (!empty($perusahaan1) && !empty($perusahaan2)) {
            $perusahaanSimilarity = ($perusahaan1 === $perusahaan2) ? 100 : 0;
            $totalSimilarity += $perusahaanSimilarity * $weights['nama_perusahaan'];
            $totalWeight += $weights['nama_perusahaan'];
        }

        // Calculate weighted average
        if ($totalWeight > 0) {
            return $totalSimilarity / $totalWeight;
        }

        return 0;
    }

    /**
     * Check if data is exact duplicate (100% match) with existing participants
     * Returns true if exact duplicate found (all fields match), false otherwise
     * Only rejects if nama, tanggal_lahir, alamat, and nama_perusahaan are all exactly the same
     */
    protected static function checkDuplicate($newData, $existingParticipants)
    {
        foreach ($existingParticipants as $existing) {
            // Normalize and compare nama (case-insensitive, trim whitespace)
            $nama1 = strtolower(trim($newData['nama'] ?? ''));
            $nama2 = strtolower(trim($existing->nama ?? ''));
            
            // Normalize and compare tanggal_lahir (optional)
            $tanggalLahir1 = null;
            $tanggalLahir2 = null;
            try {
                if (!empty($newData['tanggal_lahir'])) {
                    $tanggalLahir1 = Carbon::parse($newData['tanggal_lahir'])->format('Y-m-d');
                }
                if ($existing->tanggal_lahir) {
                    $tanggalLahir2 = $existing->tanggal_lahir->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // Date parsing failed, treat as different
            }
            
            // Normalize and compare alamat (case-insensitive, trim whitespace) - Optional
            $alamat1 = strtolower(trim($newData['alamat'] ?? ''));
            $alamat2 = strtolower(trim($existing->alamat ?? ''));
            
            // Normalize and compare nama_perusahaan (case-insensitive, trim whitespace)
            $perusahaan1 = strtolower(trim($newData['nama_perusahaan'] ?? ''));
            $perusahaan2 = strtolower(trim($existing->nama_perusahaan ?? ''));
            
            // Check if critical fields match exactly (100% duplicate)
            // Nama must always match
            $namaMatch = !empty($nama1) && !empty($nama2) && $nama1 === $nama2;
            
            // Tanggal lahir: only check if both are provided
            $tanggalMatch = true; // Default to true if one or both are empty
            if (!empty($tanggalLahir1) && !empty($tanggalLahir2)) {
                $tanggalMatch = $tanggalLahir1 === $tanggalLahir2;
            } elseif (!empty($tanggalLahir1) || !empty($tanggalLahir2)) {
                // One has tanggal_lahir, the other doesn't - not a duplicate
                $tanggalMatch = false;
            }
            
            // Alamat: only check if both are provided
            $alamatMatch = true; // Default to true if one or both are empty
            if (!empty($alamat1) && !empty($alamat2)) {
                $alamatMatch = $alamat1 === $alamat2;
            } elseif (!empty($alamat1) || !empty($alamat2)) {
                // One has alamat, the other doesn't - not a duplicate
                $alamatMatch = false;
            }
            
            // Nama perusahaan must match (if provided)
            $perusahaanMatch = !empty($perusahaan1) && !empty($perusahaan2) && $perusahaan1 === $perusahaan2;
            
            // Only reject if ALL provided fields match exactly (100% duplicate)
            // At minimum, nama must match, and if tanggal_lahir/alamat are provided in both, they must match
            if ($namaMatch && $tanggalMatch && $alamatMatch && $perusahaanMatch) {
                return true;
            }
        }

        return false;
    }

    // Login Logs
    public function loginLogs()
    {
        $logs = LoginLog::where('role', 'company')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('company.logs.index', compact('logs'));
    }

    /**
     * Pelamar Non Member List
     */
    public function nonMemberApplicants(Request $request)
    {
        $query = \App\Models\NonMemberApplicant::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('posisi_dilamar', 'like', "%{$search}%")
                  ->orWhere('domisili', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('nama_institusi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('pendidikan')) {
            $query->where('pendidikan_terakhir', $request->input('pendidikan'));
        }

        if ($request->filled('pengalaman')) {
            $query->where('tipe_pengalaman', $request->input('pengalaman'));
        }

        $applicants = $query->latest()->paginate(20)->withQueryString();

        // Statistics for summary badges
        $totalApplicants = \App\Models\NonMemberApplicant::count();
        $totalReview = \App\Models\NonMemberApplicant::where('status', 'menunggu_review')->count();
        $totalDihubungi = \App\Models\NonMemberApplicant::where('status', 'dihubungi')->count();
        $totalDiterima = \App\Models\NonMemberApplicant::where('status', 'diterima')->count();

        return view('company.non-member.index', compact('applicants', 'totalApplicants', 'totalReview', 'totalDihubungi', 'totalDiterima'));
    }

    /**
     * Update Pelamar Non Member Status
     */
    public function updateNonMemberApplicantStatus(Request $request, $id)
    {
        $applicant = \App\Models\NonMemberApplicant::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:menunggu_review,diproses,dihubungi,diterima,ditolak',
            'catatan_admin' => 'nullable|string',
        ]);

        $applicant->update([
            'status' => $request->input('status'),
            'catatan_admin' => $request->input('catatan_admin', $applicant->catatan_admin),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status pelamar berhasil diperbarui!']);
        }

        return back()->with('success', 'Status pelamar berhasil diperbarui!');
    }

    /**
     * Delete Pelamar Non Member
     */
    public function destroyNonMemberApplicant($id)
    {
        $applicant = \App\Models\NonMemberApplicant::findOrFail($id);
        
        // Hapus file CV lokal jika ada
        if ($applicant->cv_file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($applicant->cv_file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($applicant->cv_file_path);
        }

        $applicant->delete();

        return back()->with('success', 'Data pelamar non member berhasil dihapus!');
    }
}

