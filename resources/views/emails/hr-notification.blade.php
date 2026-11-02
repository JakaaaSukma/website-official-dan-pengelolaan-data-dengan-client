<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi HR - Gyokai Connect</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f3f4f6; padding: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); overflow: hidden; max-width: 600px;">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%); padding: 28px 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: 0.5px;">
                                🔔 Notifikasi HR Portal
                            </h1>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.8); font-size: 13px;">
                                Gyokai Connect — Sistem HR Terintegrasi
                            </p>
                        </td>
                    </tr>

                    {{-- Priority Banner --}}
                    @if($hrMessage->priority === 'urgent')
                    <tr>
                        <td style="background-color: #fef2f2; padding: 12px 32px; border-left: 4px solid #dc2626;">
                            <p style="margin: 0; color: #dc2626; font-size: 14px; font-weight: 600;">
                                🚨 PRIORITAS URGENT — Memerlukan tindakan segera
                            </p>
                        </td>
                    </tr>
                    @elseif($hrMessage->priority === 'high')
                    <tr>
                        <td style="background-color: #fff7ed; padding: 12px 32px; border-left: 4px solid #f97316;">
                            <p style="margin: 0; color: #ea580c; font-size: 14px; font-weight: 600;">
                                ⚠️ PRIORITAS TINGGI — Perlu segera ditangani
                            </p>
                        </td>
                    </tr>
                    @endif

                    {{-- Body Content --}}
                    <tr>
                        <td style="padding: 32px;">
                            {{-- Greeting --}}
                            <p style="margin: 0 0 20px; color: #374151; font-size: 15px;">
                                Halo <strong>{{ $recipientName }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px; color: #6b7280; font-size: 14px;">
                                Anda menerima notifikasi baru di HR Portal. Berikut detailnya:
                            </p>

                            {{-- Notification Card --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        {{-- Subject --}}
                                        <p style="margin: 0 0 4px; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                            Subjek
                                        </p>
                                        <p style="margin: 0 0 16px; color: #1e293b; font-size: 16px; font-weight: 700;">
                                            {{ $hrMessage->subject }}
                                        </p>

                                        {{-- Message Body --}}
                                        <p style="margin: 0 0 4px; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                            Isi Pesan
                                        </p>
                                        <p style="margin: 0 0 16px; color: #475569; font-size: 14px; line-height: 1.6;">
                                            {{ $hrMessage->body }}
                                        </p>

                                        {{-- Meta Info --}}
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="50%" style="padding: 8px 0;">
                                                    <p style="margin: 0; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Tipe</p>
                                                    <p style="margin: 2px 0 0; color: #475569; font-size: 13px; font-weight: 500;">
                                                        {{ $hrMessage->message_type_label }}
                                                    </p>
                                                </td>
                                                <td width="50%" style="padding: 8px 0;">
                                                    <p style="margin: 0; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Prioritas</p>
                                                    <p style="margin: 2px 0 0; color: #475569; font-size: 13px; font-weight: 500;">
                                                        @switch($hrMessage->priority)
                                                            @case('urgent') 🔴 Urgent @break
                                                            @case('high') 🟠 Tinggi @break
                                                            @case('normal') 🔵 Normal @break
                                                            @case('low') ⚪ Rendah @break
                                                            @default {{ ucfirst($hrMessage->priority) }}
                                                        @endswitch
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" style="padding: 8px 0;">
                                                    <p style="margin: 0; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Pengirim</p>
                                                    <p style="margin: 2px 0 0; color: #475569; font-size: 13px; font-weight: 500;">
                                                        {{ $hrMessage->fromUser?->name ?? 'Sistem' }}
                                                    </p>
                                                </td>
                                                <td width="50%" style="padding: 8px 0;">
                                                    <p style="margin: 0; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Waktu</p>
                                                    <p style="margin: 2px 0 0; color: #475569; font-size: 13px; font-weight: 500;">
                                                        {{ $hrMessage->created_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top: 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/admin/login') }}" 
                                           style="display: inline-block; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #ffffff; padding: 14px 36px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; letter-spacing: 0.3px;">
                                            📋 Buka HR Portal
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 32px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px; text-align: center;">
                                Email ini dikirim secara otomatis oleh sistem Gyokai Connect HR Portal.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center;">
                                Jika Anda merasa tidak seharusnya menerima email ini, abaikan saja.
                            </p>
                            <p style="margin: 12px 0 0; color: #d1d5db; font-size: 11px; text-align: center;">
                                &copy; {{ date('Y') }} Gyokai Connect. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
