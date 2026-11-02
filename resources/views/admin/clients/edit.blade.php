@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Edit Client</h2>
            <a href="{{ route('admin.clients') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if($client->company_logo)
                    <div class="mb-3">
                        <label class="form-label">Current Logo</label><br>
                                               <img src="{{ App\Helpers\AppHelper::storageUrl($client->company_logo) }}" alt="{{ $client->company_name }}" style="max-width: 200px; border-radius: 10px;" onerror="this.src='{{ asset('img/blog-1.png') }}'">
                    </div>
                @endif

                <form action="{{ route('admin.clients.update', $client->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $client->company_name) }}" required>
                        @error('company_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="about_company" class="form-label">About Company <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="about_company" name="about_company" rows="5" required>{{ old('about_company', $client->about_company) }}</textarea>
                        @error('about_company')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="qualifications" class="form-label">Qualifications <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="qualifications" name="qualifications" rows="5" required>{{ old('qualifications', $client->qualifications) }}</textarea>
                        @error('qualifications')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="company_logo" class="form-label">Company Logo (Leave empty to keep current)</label>
                        <input type="file" class="form-control" id="company_logo" name="company_logo" accept="image/*">
                        @error('company_logo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status Partnership</label>
                        <select class="form-select" id="is_active" name="is_active" required>
                            <option value="1" {{ old('is_active', $client->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Open (Active)</option>
                            <option value="0" {{ old('is_active', $client->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Close (Archive)</option>
                        </select>
                        @error('is_active')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="is_hidden" class="form-label">Visibility on Website</label>
                        <select class="form-select" id="is_hidden" name="is_hidden" required>
                            <option value="0" {{ old('is_hidden', $client->is_hidden ? '1' : '0') == '0' ? 'selected' : '' }}>Show on Website</option>
                            <option value="1" {{ old('is_hidden', $client->is_hidden ? '1' : '0') == '1' ? 'selected' : '' }}>Hide from Website</option>
                        </select>
                        @error('is_hidden')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jika dipilih "Hide", data client tidak akan muncul di halaman "Our Clients" atau beranda.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection







