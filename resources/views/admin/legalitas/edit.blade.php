@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Edit Legalitas</h2>
            <a href="{{ route('admin.legalitas') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if($legalitas->document)
                    <div class="mb-3">
                        <label class="form-label">Current Document</label><br>
                        @if(str_ends_with(strtolower($legalitas->document), '.pdf'))
                            <div class="alert alert-info">
                                <i class="fas fa-file-pdf me-2"></i>PDF Document: {{ $legalitas->name }}
                                 <a href="{{ App\Helpers\AppHelper::storageUrl($legalitas->document) }}" target="_blank" class="btn btn-sm btn-primary ms-2">View PDF</a>
                            </div>
                        @else
                             <img src="{{ App\Helpers\AppHelper::storageUrl($legalitas->document) }}" alt="{{ $legalitas->name }}" style="max-width: 200px; border-radius: 10px;" onerror="this.src='{{ asset('img/blog-1.png') }}'">
                        @endif
                    </div>
                @endif

                <form action="{{ route('admin.legalitas.update', $legalitas->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Document Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $legalitas->name) }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="order" class="form-label">Order (Display Order)</label>
                        <input type="number" class="form-control" id="order" name="order" value="{{ old('order', $legalitas->order) }}" min="0">
                        @error('order')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Leave empty to keep current)</label>
                        <input type="file" class="form-control" id="document" name="document" accept="image/*,application/pdf">
                        @error('document')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Upload image (JPG, PNG, GIF) or PDF file (max 2MB)</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection









