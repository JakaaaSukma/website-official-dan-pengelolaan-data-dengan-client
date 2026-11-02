<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class AdminVideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $videos = Video::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'youtube_link' => 'required|url',
            'is_active' => 'required|boolean',
        ]);

        Video::create($validated);

        return redirect()->route('admin.videos')->with('success', 'Video berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'youtube_link' => 'required|url',
            'is_active' => 'required|boolean',
        ]);

        $video->update($validated);

        return redirect()->route('admin.videos')->with('success', 'Video berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();

        return redirect()->route('admin.videos')->with('success', 'Video berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $video = Video::findOrFail($id);
        $video->is_active = !$video->is_active;
        $video->save();

        $status = $video->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.videos')->with('success', "Video berhasil {$status}.");
    }
}

