@extends('layouts.company')

@section('content')
<div class="container-fluid py-5" style="background: #f8f9fa; min-height: 100vh;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold" style="color: #457b9d;">
                <i class="fas fa-edit me-2"></i>Edit Data Peserta Magang (Bersamaan)
            </h2>
            <a href="{{ route('company.participants') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan!</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Catatan:</strong> Anda akan mengedit <strong>{{ $participants->count() }}</strong> data peserta magang secara bersamaan. 
            Hanya field <strong>Mulai Kontrak</strong>, <strong>Kontrak Habis</strong>, dan <strong>Nama Perusahaan</strong> yang dapat diedit.
            <br><strong>Anda tidak wajib mengisi semua field - cukup isi field yang ingin diubah saja.</strong>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <form action="{{ route('company.participants.bulk-update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <input type="hidden" name="ids" value="{{ $participants->pluck('id')->implode(',') }}">
                            
                            <div class="mb-4">
                                <h5 class="fw-bold mb-3" style="color: #457b9d;">
                                    <i class="fas fa-list me-2"></i>Data Peserta yang Akan Diedit:
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Tanggal Lahir</th>
                                                <th>Alamat</th>
                                                <th>Mulai Kontrak (Lama)</th>
                                                <th>Kontrak Habis (Lama)</th>
                                                <th>Nama Perusahaan (Lama)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($participants as $index => $participant)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><strong>{{ $participant->nama }}</strong></td>
                                                    <td>{{ $participant->tanggal_lahir->format('m/d/Y') }}</td>
                                                    <td>{{ $participant->alamat }}</td>
                                                    <td>{{ $participant->mulai_kontrak->format('m/d/Y') }}</td>
                                                    <td>{{ $participant->kontrak_habis->format('m/d/Y') }}</td>
                                                    <td>{{ $participant->nama_perusahaan }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="fw-bold mb-3" style="color: #457b9d;">
                                <i class="fas fa-edit me-2"></i>Data Baru (Isi hanya field yang ingin diubah):
                            </h5>

                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Penting:</strong> Anda tidak wajib mengisi semua field. Cukup isi field yang ingin diubah saja. 
                                Field yang tidak diisi akan tetap menggunakan data lama.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mulai_kontrak" class="form-label">
                                        <i class="fas fa-calendar-check me-1"></i>Mulai Kontrak <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('mulai_kontrak') is-invalid @enderror" 
                                           id="mulai_kontrak" 
                                           name="mulai_kontrak" 
                                           value="{{ old('mulai_kontrak') }}">
                                    @error('mulai_kontrak')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah tanggal mulai kontrak</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="kontrak_habis" class="form-label">
                                        <i class="fas fa-calendar-times me-1"></i>Kontrak Habis <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('kontrak_habis') is-invalid @enderror" 
                                           id="kontrak_habis" 
                                           name="kontrak_habis" 
                                           value="{{ old('kontrak_habis') }}">
                                    @error('kontrak_habis')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah tanggal kontrak habis</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nama_perusahaan" class="form-label">
                                    <i class="fas fa-building me-1"></i>Nama Perusahaan <span class="text-muted">(Opsional)</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nama_perusahaan') is-invalid @enderror" 
                                       id="nama_perusahaan" 
                                       name="nama_perusahaan" 
                                       value="{{ old('nama_perusahaan') }}" 
                                       placeholder="Kosongkan jika tidak ingin mengubah nama perusahaan">
                                @error('nama_perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah nama perusahaan</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('company.participants') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set minimum date for kontrak_habis based on mulai_kontrak (only if mulai_kontrak is filled)
    document.getElementById('mulai_kontrak').addEventListener('change', function() {
        const mulaiKontrak = new Date(this.value);
        const kontrakHabis = document.getElementById('kontrak_habis');
        
        if (this.value && mulaiKontrak) {
            const nextDay = new Date(mulaiKontrak);
            nextDay.setDate(nextDay.getDate() + 1);
            kontrakHabis.min = nextDay.toISOString().split('T')[0];
            
            // If kontrak_habis is before mulai_kontrak, clear it
            if (kontrakHabis.value && new Date(kontrakHabis.value) <= mulaiKontrak) {
                kontrakHabis.value = '';
            }
        } else {
            // If mulai_kontrak is cleared, remove min restriction
            kontrakHabis.removeAttribute('min');
        }
    });

    // Validate form before submit - ensure at least one field is filled
    document.querySelector('form').addEventListener('submit', function(e) {
        const mulaiKontrak = document.getElementById('mulai_kontrak').value;
        const kontrakHabis = document.getElementById('kontrak_habis').value;
        const namaPerusahaan = document.getElementById('nama_perusahaan').value.trim();
        
        if (!mulaiKontrak && !kontrakHabis && !namaPerusahaan) {
            e.preventDefault();
            alert('Minimal harus mengisi salah satu field: Mulai Kontrak, Kontrak Habis, atau Nama Perusahaan.');
            return false;
        }
    });
</script>
@endsection

