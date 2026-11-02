@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Edit Team Member</h2>
            <a href="{{ route('admin.teams') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if($team->photo)
                    <div class="mb-3">
                        <label class="form-label">Current Photo</label><br>
                                                <img src="{{ App\Helpers\AppHelper::storageUrl($team->photo) }}" alt="{{ $team->name }}" style="max-width: 200px; border-radius: 10px;" onerror="this.src='{{ asset('img/blog-1.png') }}'">
                    </div>
                @endif

                <form action="{{ route('admin.teams.update', $team->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $team->name) }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="position" name="position" value="{{ old('position', $team->position) }}" required>
                        @error('position')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Kelompok Team <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Pilih Kelompok</option>
                            <option value="director" {{ old('category', $team->category) === 'director' ? 'selected' : '' }}>DIREKTUR & CEO</option>
                            <option value="executive" {{ old('category', $team->category) === 'executive' ? 'selected' : '' }}>EXECUTIVE & MANAGEMENT TEAM</option>
                            <option value="marketing" {{ old('category', $team->category) === 'marketing' ? 'selected' : '' }}>MARKETING & BUSINESS DEVELOPMENT TEAM</option>
                            <option value="legal" {{ old('category', $team->category) === 'legal' ? 'selected' : '' }}>LEGAL TEAM</option>
                        </select>
                        @error('category')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="order" class="form-label">Order (Display Order)</label>
                        <input type="number" class="form-control" id="order" name="order" value="{{ old('order', $team->order) }}" min="0">
                        @error('order')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo (Leave empty to keep current)</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        @error('photo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection








