@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Video Management</h2>
            <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">Add Video</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Youtube Link</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($videos as $video)
                            <tr>
                                <td>{{ $video->title }}</td>
                                <td>
                                    <a href="{{ $video->youtube_link }}" target="_blank" class="text-decoration-none">
                                        {{ Str::limit($video->youtube_link, 50) }}
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('admin.videos.toggle-status', $video->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $video->is_active ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $video->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td>{{ $video->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.videos.delete', $video->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this video?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No videos found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    {{ $videos->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
