<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkas Cetak Siap Produksi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #0f172a;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 2px solid #0f172a; border-radius: 16px; box-shadow: 4px 4px 0px 0px #0f172a; overflow: hidden;">
        <!-- Header -->
        <tr>
            <td style="background-color: #fef08a; padding: 20px 24px; border-bottom: 2px solid #0f172a;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <span style="font-size: 11px; font-weight: 900; letter-spacing: 0.1em; text-transform: uppercase; color: #854d0e; display: block; margin-bottom: 4px;">
                                ORDER PRODUKSI PERCETAKAN
                            </span>
                            <h1 style="margin: 0; font-size: 20px; font-weight: 900; color: #0f172a;">
                                Gemini Art &bull; {{ $branch->clean_name ?? 'Pusat' }}
                            </h1>
                        </td>
                        <td align="right">
                            <span style="display: inline-block; background-color: #ffffff; border: 1.5px solid #0f172a; border-radius: 8px; padding: 6px 12px; font-family: monospace; font-size: 12px; font-weight: 900; color: #0f172a;">
                                {{ $order->order_number ?? '-' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Content Body -->
        <tr>
            <td style="padding: 24px;">
                <p style="margin-top: 0; font-size: 14px; line-height: 1.6; color: #334155;">
                    Halo Rekanan Percetakan,<br>
                    Berikut kami kirimkan file cetak siap produksi untuk pesanan <strong>#{{ $order->order_number }}</strong>. Mohon diproses sesuai spesifikasi berikut:
                </p>

                <!-- Spec Card -->
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; border: 1.5px solid #0f172a; border-radius: 12px; margin: 16px 0; overflow: hidden;">
                    <tr>
                        <td style="padding: 16px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="6" style="font-size: 13px;">
                                <tr>
                                    <td width="30%" style="color: #64748b; font-weight: 600;">Nama Item:</td>
                                    <td width="70%" style="color: #0f172a; font-weight: 800;">{{ $job->title ?? $order->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">Ukuran / Dimensi:</td>
                                    <td style="color: #0f172a; font-weight: 800; font-family: monospace;">{{ $job->dimensions ?: 'Sesuai file asli' }}</td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">Bahan & Finishing:</td>
                                    <td style="color: #0f172a; font-weight: 800;">{{ $job->material_notes ?: 'Standar Percetakan' }}</td>
                                </tr>
                                @if(!empty($job->brief_notes))
                                <tr>
                                    <td style="color: #64748b; font-weight: 600;">Catatan Tambahan:</td>
                                    <td style="color: #0f172a; font-weight: 700;">{{ $job->brief_notes }}</td>
                                </tr>
                                @endif
                                @if(!empty($notes))
                                <tr style="background-color: #fef9c3;">
                                    <td style="color: #854d0e; font-weight: 800;">Pesan Pengirim:</td>
                                    <td style="color: #713f12; font-weight: 700;">{{ $notes }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- File Box -->
                <div style="background-color: #ffffff; border: 1.5px dashed #0f172a; border-radius: 12px; padding: 14px 16px; margin: 16px 0;">
                    <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">
                        Nama Berkas Cetak:
                    </div>
                    <div style="font-family: monospace; font-size: 13px; font-weight: 800; color: #0f172a; word-break: break-all;">
                        {{ $asset->file_name }}
                    </div>
                    <div style="font-size: 11px; color: #64748b; font-weight: 700; margin-top: 4px;">
                        Ukuran File: <strong>{{ $asset->formatted_size }}</strong> &bull; Tipe: <strong>{{ $asset->file_type_badge }}</strong>
                    </div>
                </div>

                @if($asset->is_image)
                <!-- Image Preview Thumbnail in Email -->
                <div style="margin: 18px 0; background-color: #f8fafc; border: 1.5px solid #0f172a; border-radius: 12px; padding: 12px; text-align: center;">
                    <div style="font-size: 10px; font-weight: 900; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 8px;">
                        Pratinjau Desain:
                    </div>
                    <a href="{{ $previewUrl }}" target="_blank" style="text-decoration: none; display: inline-block; max-width: 100%;">
                        <img src="{{ $previewUrl }}" alt="{{ $asset->file_name }}" style="max-width: 100%; max-height: 280px; height: auto; border-radius: 8px; border: 1px solid #cbd5e1; display: block; margin: 0 auto;" />
                    </a>
                    <div style="margin-top: 8px; font-size: 11px; color: #2563eb; font-weight: 700;">
                        &oplus; Klik gambar untuk memperbesar resolusi tinggi di browser
                    </div>
                </div>
                @endif

                <!-- Big CTA Buttons (Preview & Download) -->
                <div style="text-align: center; margin: 26px 0 16px 0;">
                    <table border="0" cellspacing="0" cellpadding="0" style="margin: 0 auto;">
                        <tr>
                            @if($asset->is_image || str_ends_with(strtolower($asset->file_name), '.pdf'))
                            <td style="padding: 0 6px;">
                                <a href="{{ $previewUrl }}" target="_blank" style="display: inline-block; background-color: #bae6fd; color: #0c4a6e; text-decoration: none; font-size: 13px; font-weight: 900; padding: 12px 20px; border-radius: 10px; border: 2px solid #0f172a; box-shadow: 2.5px 2.5px 0px 0px #0f172a; text-transform: uppercase; letter-spacing: 0.02em;">
                                    &#128065; Buka Pratinjau
                                </a>
                            </td>
                            @endif
                            <td style="padding: 0 6px;">
                                <a href="{{ $downloadUrl }}" style="display: inline-block; background-color: #34d399; color: #064e3b; text-decoration: none; font-size: 13px; font-weight: 900; padding: 12px 24px; border-radius: 10px; border: 2px solid #0f172a; box-shadow: 2.5px 2.5px 0px 0px #0f172a; text-transform: uppercase; letter-spacing: 0.02em;">
                                    &darr; Download Berkas
                                </a>
                            </td>
                        </tr>
                    </table>
                    <div style="margin-top: 10px; font-size: 11px; color: #64748b;">
                        Link aman & langsung dari Cloud Storage (tanpa limit lampiran email).
                    </div>
                </div>

                <!-- Alternative Web Link -->
                <div style="text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 16px; margin-top: 20px;">
                    Atau buka halaman portal produksi resmi:<br>
                    <a href="{{ $shareUrl }}" style="color: #2563eb; font-weight: 700; word-break: break-all;">
                        {{ $shareUrl }}
                    </a>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #f8fafc; border-top: 2px solid #0f172a; padding: 16px 24px; font-size: 11px; color: #64748b; text-align: center;">
                Dikirim oleh <strong>{{ $senderName ?: 'Owner Gemini Art' }}</strong> via Sistem ERP Percetakan Gemini Art.<br>
                Jika ada pertanyaan mengenai file ini, silakan hubungi Gemini Art &bull; {{ $branch->clean_name ?? 'Pusat' }}.
            </td>
        </tr>
    </table>
</body>
</html>

