@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Add New Job Vacancy</h2>
            <a href="{{ route('admin.job-vacancies') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.job-vacancies.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="position" name="position" value="{{ old('position') }}" required>
                        @error('position')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="responsibilities" class="form-label">Responsibilities <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="responsibilities" name="responsibilities" rows="5" required>{{ old('responsibilities') }}</textarea>
                        @error('responsibilities')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="qualifications" class="form-label">Qualifications <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="qualifications" name="qualifications" rows="5" required>{{ old('qualifications') }}</textarea>
                        @error('qualifications')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo">
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












