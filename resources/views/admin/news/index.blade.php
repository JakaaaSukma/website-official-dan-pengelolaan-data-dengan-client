@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">News</h2>
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary">Add News</a>
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
                            <th>Headline</th>
                            <th>Published</th>
                            <th>Views</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>
                                    <span class="badge {{ $item->is_headline ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $item->is_headline ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $item->is_published ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $item->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>{{ $item->views }}</td>
                                <td>{{ $item->created_at?->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.news.delete', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this news?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada berita.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection






