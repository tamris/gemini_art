@php
    $defaultMessage = 'Sistem sedang dalam proses perbaikan & pemeliharaan server. Data transaksi, order desain, dan arsip file Anda tetap aman tersimpan.';
    $rawExceptionMessage = (isset($exception) && method_exists($exception, 'getMessage'))
        ? trim((string) $exception->getMessage())
        : '';

    $resolvedMessage = $maintenanceMessage
        ?? config('app.maintenance_message')
        ?: ($rawExceptionMessage !== '' && $rawExceptionMessage !== 'Service Unavailable'
            ? $rawExceptionMessage
            : $defaultMessage);

    $resolvedEta = $maintenanceEta ?? config('app.maintenance_eta');

    if (empty($resolvedEta) && !empty($retryAfter ?? null)) {
        if (is_numeric($retryAfter) && (int) $retryAfter > 0) {
            $seconds = (int) $retryAfter;
            if ($seconds >= 3600) {
                $hours = intdiv($seconds, 3600);
                $mins = (int) ceil(($seconds % 3600) / 60);
                $resolvedEta = $mins > 0 ? "± {$hours} jam {$mins} menit" : "± {$hours} jam";
            } elseif ($seconds >= 60) {
                $resolvedEta = '± ' . (int) ceil($seconds / 60) . ' menit';
            } else {
                $resolvedEta = "± {$seconds} detik";
            }
        } elseif (is_string($retryAfter)) {
            $resolvedEta = $retryAfter;
        }
    }

    $resolvedEta = $resolvedEta ?: 'Segera Kembali Online';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="color-scheme" content="light dark">
    <script>
        (function () {
            var meta = document.querySelector('meta[name="color-scheme"]');
            if (!meta) return;
            try {
                var saved = localStorage.getItem('theme');
                if (saved === 'dark' || saved === 'light') {
                    meta.content = saved;
                } else {
                    meta.content = 'light dark';
                }
            } catch (e) {
                meta.content = 'light dark';
            }

            window.addEventListener('storage', function (event) {
                if (event.key === 'theme') {
                    meta.content = (event.newValue === 'dark' || event.newValue === 'light')
                        ? event.newValue
                        : 'light dark';
                }
            });
        })();
    </script>
    <title>Pemeliharaan Sistem (503) - Gemini Art</title>
    <style>
        :root {
            color-scheme: light dark;

            --bg-light: #f8fafc;
            --bg-dark: #0b0f19;
            --surface-light: #ffffff;
            --surface-dark: #0c1222;
            --surface-muted-light: #f1f5f9;
            --surface-muted-dark: #111827;
            --border-light: #1e293b;
            --border-dark: #64748b;
            --border-soft-light: #cbd5e1;
            --border-soft-dark: #334155;
            --shadow-light: #0f172a;
            --shadow-dark: #334155;
            --text-light: #0f172a;
            --text-dark: #f1f5f9;
            --text-muted-light: #475569;
            --text-muted-dark: #94a3b8;

            --amber-solid: #fcd34d;
            --amber-solid-hover: #fbbf24;
            --amber-pill-light: #fef08a;
            --amber-pill-dark: rgba(250, 204, 21, 0.16);
            --amber-pill-text-light: #78350f;
            --amber-pill-text-dark: #fde68a;
            --amber-dot-light: #d97706;
            --amber-dot-dark: #fbbf24;

            --note-bg-light: #fffbeb;
            --note-bg-dark: rgba(245, 158, 11, 0.08);
            --note-border-light: #f59e0b;
            --note-border-dark: rgba(245, 158, 11, 0.45);

            --safe-bg-light: #ecfdf5;
            --safe-bg-dark: rgba(16, 185, 129, 0.12);
            --safe-text-light: #065f46;
            --safe-text-dark: #6ee7b7;

            --bg: var(--bg-light);
            --surface: var(--surface-light);
            --surface-muted: var(--surface-muted-light);
            --border: var(--border-light);
            --border-soft: var(--border-soft-light);
            --shadow: var(--shadow-light);
            --text: var(--text-light);
            --text-muted: var(--text-muted-light);
            --amber-pill: var(--amber-pill-light);
            --amber-pill-text: var(--amber-pill-text-light);
            --amber-dot: var(--amber-dot-light);
            --note-bg: var(--note-bg-light);
            --note-border: var(--note-border-light);
            --safe-bg: var(--safe-bg-light);
            --safe-text: var(--safe-text-light);
        }

        :root:has(> head > meta[name="color-scheme"][content="dark"]) {
            color-scheme: dark;
        }

        :root:has(> head > meta[name="color-scheme"][content="light"]) {
            color-scheme: light;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: var(--bg-dark);
                --surface: var(--surface-dark);
                --surface-muted: var(--surface-muted-dark);
                --border: var(--border-dark);
                --border-soft: var(--border-soft-dark);
                --shadow: var(--shadow-dark);
                --text: var(--text-dark);
                --text-muted: var(--text-muted-dark);
                --amber-pill: var(--amber-pill-dark);
                --amber-pill-text: var(--amber-pill-text-dark);
                --amber-dot: var(--amber-dot-dark);
                --note-bg: var(--note-bg-dark);
                --note-border: var(--note-border-dark);
                --safe-bg: var(--safe-bg-dark);
                --safe-text: var(--safe-text-dark);
            }
        }

        @supports (color: light-dark(white, black)) {
            :root {
                --bg: light-dark(var(--bg-light), var(--bg-dark));
                --surface: light-dark(var(--surface-light), var(--surface-dark));
                --surface-muted: light-dark(var(--surface-muted-light), var(--surface-muted-dark));
                --border: light-dark(var(--border-light), var(--border-dark));
                --border-soft: light-dark(var(--border-soft-light), var(--border-soft-dark));
                --shadow: light-dark(var(--shadow-light), var(--shadow-dark));
                --text: light-dark(var(--text-light), var(--text-dark));
                --text-muted: light-dark(var(--text-muted-light), var(--text-muted-dark));
                --amber-pill: light-dark(var(--amber-pill-light), var(--amber-pill-dark));
                --amber-pill-text: light-dark(var(--amber-pill-text-light), var(--amber-pill-text-dark));
                --amber-dot: light-dark(var(--amber-dot-light), var(--amber-dot-dark));
                --note-bg: light-dark(var(--note-bg-light), var(--note-bg-dark));
                --note-border: light-dark(var(--note-border-light), var(--note-border-dark));
                --safe-bg: light-dark(var(--safe-bg-light), var(--safe-bg-dark));
                --safe-text: light-dark(var(--safe-text-light), var(--safe-text-dark));
            }
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            position: relative;
            overflow-x: hidden;
        }

        .glow-top {
            position: fixed;
            top: -8rem;
            left: -8rem;
            width: 22rem;
            height: 22rem;
            border-radius: 9999px;
            background: rgba(251, 191, 36, 0.16);
            filter: blur(72px);
            pointer-events: none;
            z-index: 0;
        }

        .glow-bottom {
            position: fixed;
            bottom: -8rem;
            right: -8rem;
            width: 22rem;
            height: 22rem;
            border-radius: 9999px;
            background: rgba(56, 189, 248, 0.14);
            filter: blur(72px);
            pointer-events: none;
            z-index: 0;
        }

        .preview-bar {
            position: relative;
            z-index: 10;
            max-width: 36rem;
            width: 100%;
            margin-bottom: 0.9rem;
            padding: 0.65rem 1rem;
            background-color: var(--amber-solid);
            color: #0f172a;
            border: 1.5px solid #0f172a;
            border-radius: 0.85rem;
            box-shadow: 2.5px 2.5px 0 0 #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .preview-bar a {
            color: #0f172a;
            background: #ffffff;
            border: 1.5px solid #0f172a;
            padding: 0.3rem 0.65rem;
            border-radius: 0.5rem;
            text-decoration: none;
            white-space: nowrap;
        }

        .card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 36rem;
            background-color: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 1.25rem;
            box-shadow: 4px 4px 0 0 var(--shadow);
            padding: 1.75rem;
        }

        @media (min-width: 640px) {
            .card {
                padding: 2.25rem;
            }
        }

        .brand-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 1.5px solid var(--border-soft);
            margin-bottom: 1.5rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-logo {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.85rem;
            background-color: var(--amber-solid);
            border: 1.5px solid var(--border);
            box-shadow: 2px 2px 0 0 var(--shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
            font-weight: 900;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .brand-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            line-height: 1.15;
        }

        .brand-sub {
            margin: 0.15rem 0 0;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.38rem 0.75rem;
            border-radius: 9999px;
            background-color: var(--amber-pill);
            color: var(--amber-pill-text);
            border: 1.5px solid var(--border);
            box-shadow: 1.5px 1.5px 0 0 var(--shadow);
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .status-dot {
            width: 0.52rem;
            height: 0.52rem;
            border-radius: 9999px;
            background-color: var(--amber-dot);
            flex-shrink: 0;
        }

        @media (prefers-reduced-motion: no-preference) {
            .status-dot {
                animation: gentle-pulse 2.2s ease-in-out infinite;
            }
        }

        @keyframes gentle-pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.45;
                transform: scale(0.82);
            }
        }

        .hero {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .hero-icon {
            width: 3.25rem;
            height: 3.25rem;
            border-radius: 1rem;
            background-color: var(--amber-solid);
            border: 1.5px solid var(--border);
            box-shadow: 2.5px 2.5px 0 0 var(--shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
            flex-shrink: 0;
        }

        .hero h1 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 900;
            letter-spacing: -0.025em;
            line-height: 1.2;
        }

        @media (min-width: 640px) {
            .hero h1 {
                font-size: 1.6rem;
            }
        }

        .hero-tag {
            margin: 0.3rem 0 0;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        .message-box {
            background-color: var(--note-bg);
            border: 1.5px solid var(--note-border);
            border-radius: 0.9rem;
            padding: 1rem 1.1rem;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            line-height: 1.6;
            font-weight: 600;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 520px) {
            .info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .info-card {
            background-color: var(--surface-muted);
            border: 1.5px solid var(--border-soft);
            border-radius: 0.85rem;
            padding: 0.85rem 0.95rem;
        }

        .info-card.safe {
            background-color: var(--safe-bg);
            border-color: var(--border-soft);
        }

        .info-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.65rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 0.3rem;
        }

        .info-value {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--text);
        }

        .info-card.safe .info-value {
            color: var(--safe-text);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
        }

        .btn-reload {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.72rem 1.25rem;
            border-radius: 0.8rem;
            border: 1.5px solid #0f172a;
            background-color: var(--amber-solid);
            color: #0f172a;
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 2.5px 2.5px 0 0 var(--shadow);
            transition: background-color 150ms ease, transform 120ms ease, box-shadow 120ms ease;
        }

        .btn-reload:hover {
            background-color: var(--amber-solid-hover);
        }

        .btn-reload:active {
            transform: translate(1.5px, 1.5px);
            box-shadow: none;
        }

        .btn-reload:focus-visible {
            outline: 2.5px solid #f59e0b;
            outline-offset: 2px;
        }

        .refresh-note {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .footer {
            position: relative;
            z-index: 10;
            margin-top: 1.25rem;
            text-align: center;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        .code-pill {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            padding: 0.12rem 0.4rem;
            border-radius: 0.35rem;
            border: 1px solid var(--border-soft);
            background-color: var(--surface);
            font-size: 0.68rem;
            margin-left: 0.35rem;
        }
    </style>
</head>
<body>
    <div class="glow-top" aria-hidden="true"></div>
    <div class="glow-bottom" aria-hidden="true"></div>

    @if(!empty($isPreview))
    <div class="preview-bar" role="status">
        <span>PRATINJAU HALAMAN MAINTENANCE (503) — Hanya terlihat oleh Owner</span>
        <a href="{{ route('maintenance.index') }}">Kembali ke Panel</a>
    </div>
    @endif

    <main class="card" role="main" aria-labelledby="maintenance-heading">
        <header class="brand-row">
            <div class="brand">
                <div class="brand-logo" aria-hidden="true">GA</div>
                <div>
                    <p class="brand-title">GEMINI ART</p>
                    <p class="brand-sub">Studio &amp; Percetakan</p>
                </div>
            </div>

            <div class="status-badge" role="status">
                <span class="status-dot" aria-hidden="true"></span>
                <span>Sedang Maintenance</span>
            </div>
        </header>

        <section class="hero">
            <div class="hero-icon" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
                </svg>
            </div>
            <div>
                <h1 id="maintenance-heading">Sistem Sedang Dalam Pemeliharaan</h1>
                <p class="hero-tag">Tim teknis sedang memperbarui atau memperbaiki server.</p>
            </div>
        </section>

        <div class="message-box">
            {{ $resolvedMessage }}
        </div>

        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" /><path d="M12 6v6l4 2" />
                    </svg>
                    <span>Estimasi Selesai</span>
                </div>
                <div class="info-value">{{ $resolvedEta }}</div>
            </div>

            <div class="info-card safe">
                <div class="info-label">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                    <span>Keamanan Data</span>
                </div>
                <div class="info-value">Order, Kas &amp; Arsip Aman</div>
            </div>
        </div>

        <div class="actions">
            <button type="button" class="btn-reload" onclick="window.location.reload()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
                    <path d="M21 3v5h-5" />
                    <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
                    <path d="M8 16H3v5" />
                </svg>
                <span>Cek Status Sekarang</span>
            </button>
            <p class="refresh-note">Muat ulang setelah perbaikan server selesai.</p>
        </div>
    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} Gemini Art Studio &amp; Percetakan
        <span class="code-pill">HTTP 503</span>
    </footer>
</body>
</html>

