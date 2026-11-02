<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi Gyokai</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #991B1B; margin: 0; }
        .content { margin-bottom: 30px; }
        .otp-box { text-align: center; background-color: #f9f9f9; padding: 15px; border-radius: 5px; font-size: 28px; letter-spacing: 5px; font-weight: bold; color: #991B1B; margin: 20px 0; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #e0e0e0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gyokai Connect</h1>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Anda menerima email ini sebagai bagian dari proses <strong>{{ $purpose }}</strong> pada sistem Gyokai Connect.</p>
            <p>Berikut adalah kode rahasia 6-digit Anda (berlaku selama 5 menit):</p>
            
            <div class="otp-box">
                {{ $code }}
            </div>
            
            <p>Mohon masukkan kode ini pada halaman aplikasi. <strong>Jangan bagikan kode ini kepada siapapun</strong>, termasuk pihak yang mengaku dari tim Gyokai.</p>
            <p>Jika Anda tidak meminta kode ini, Anda dapat mengabaikan email ini dengan aman.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Gyokai Group. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
