@extends('layouts.admin')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #e63946;">Add New Facility</h2>
            <a href="{{ route('admin.facilities') }}" class="btn btn-secondary">Back</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.facilities.store') }}" method="POST" enctype="multipart/form-data" id="facilityForm">
                    @csrf
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            <option value="fasilitas" {{ old('category', 'fasilitas') === 'fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                            <option value="galeri" {{ old('category') === 'galeri' ? 'selected' : '' }}>Galeri</option>
                        </select>
                        @error('category')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="nameField">
                        <label for="name" class="form-label">Facility Name <span class="text-danger" id="nameRequired">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3" id="orderField">
                        <label for="order" class="form-label">Order (Display Order)</label>
                        <input type="number" class="form-control" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                        @error('order')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Photo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                        @error('photo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

                <script>
                    document.getElementById('category').addEventListener('change', function() {
                        const category = this.value;
                        const nameField = document.getElementById('nameField');
                        const nameInput = document.getElementById('name');
                        const nameRequired = document.getElementById('nameRequired');
                        const orderField = document.getElementById('orderField');
                        const orderInput = document.getElementById('order');

                        if (category === 'galeri') {
                            nameField.style.display = 'none';
                            nameInput.removeAttribute('required');
                            nameInput.value = '';
                            orderField.style.display = 'none';
                            orderInput.value = 0;
                        } else {
                            nameField.style.display = 'block';
                            nameInput.setAttribute('required', 'required');
                            nameRequired.style.display = 'inline';
                            orderField.style.display = 'block';
                        }
                    });

                    // Trigger on page load
                    document.getElementById('category').dispatchEvent(new Event('change'));
                </script>
            </div>
        </div>
    </div>
</div>
@endsection








