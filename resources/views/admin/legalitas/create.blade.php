@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Add New Legalitas</h2>
            <a href="{{ route('admin.legalitas') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.legalitas.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Document Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="order" class="form-label">Order (Display Order)</label>
                        <input type="number" class="form-control" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                        @error('order')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="document" class="form-label">Document (Image or PDF)</label>
                        <input type="file" class="form-control" id="document" name="document" accept="image/*,application/pdf">
                        @error('document')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Upload image (JPG, PNG, GIF) or PDF file (max 2MB)</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection












