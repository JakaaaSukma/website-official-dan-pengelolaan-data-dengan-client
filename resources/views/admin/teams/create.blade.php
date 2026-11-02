@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Add New Team Member</h2>
            <a href="{{ route('admin.teams') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.teams.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="position" name="position" value="{{ old('position') }}" required>
                        @error('position')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kelompok Team <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Pilih Kelompok</option>
                            <option value="director" {{ old('category') === 'director' ? 'selected' : '' }}>DIREKTUR & CEO</option>
                            <option value="executive" {{ old('category') === 'executive' ? 'selected' : '' }}>EXECUTIVE & MANAGEMENT TEAM</option>
                            <option value="marketing" {{ old('category') === 'marketing' ? 'selected' : '' }}>MARKETING & BUSINESS DEVELOPMENT TEAM</option>
                            <option value="legal" {{ old('category') === 'legal' ? 'selected' : '' }}>LEGAL TEAM</option>
                        </select>
                        @error('category')
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
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        @error('photo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

