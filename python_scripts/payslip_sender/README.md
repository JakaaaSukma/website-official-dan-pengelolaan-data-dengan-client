# Sistem Pengiriman Slip Gaji Otomatis

Sistem ini digunakan untuk mengirimkan slip gaji secara otomatis kepada karyawan berdasarkan alamat email yang diekstrak dari file PDF slip gaji.

## Persyaratan Sistem

- Python 3.8 atau lebih baru
- Akun Google Cloud Platform dengan Gmail API diaktifkan
- File `credentials.json` dari Google Cloud Console

## Instalasi

1.  Buka terminal atau command prompt.
2.  Masuk ke direktori script:
    ```bash
    cd c:\xampp\htdocs\Gyokai01\python_scripts\payslip_sender
    ```
3.  Install library yang dibutuhkan:
    ```bash
    pip install -r requirements.txt
    ```

## Konfigurasi

1.  **Credentials**: Letakkan file `credentials.json` (OAuth 2.0 Client ID) di folder:
    `c:\xampp\htdocs\Gyokai01\database\gmail\`
    
    *Catatan: Saat pertama kali dijalankan, sistem akan meminta login via browser dan kemudian mengenkripsi file credentials.json untuk keamanan.*

2.  **PDF Files**: Letakkan semua file PDF slip gaji di folder:
    `c:\xampp\htdocs\Gyokai01\storage\app\public\payslips\`

## Cara Menjalankan

Jalankan script utama menggunakan Python:

```bash
python send_slip.py
```

Sistem akan:
1.  Membaca semua file PDF di folder target.
2.  Mengekstrak teks dan mencari alamat email.
3.  Mengirim email dengan lampiran PDF ke alamat yang ditemukan.
4.  Menyimpan log pengiriman di folder `logs`.

## Keamanan

- File `credentials.json` akan dienkripsi menjadi `credentials.json.enc` setelah dijalankan pertama kali.
- Token akses disimpan di `token.json`.
- Pastikan file `secret.key` tidak dibagikan.

## Struktur Folder

- `config.py`: Pengaturan direktori dan template email.
- `security.py`: Modul enkripsi/dekripsi.
- `send_slip.py`: Script utama.
- `requirements.txt`: Daftar dependency.
- `logs/`: Folder log hasil pengiriman.
