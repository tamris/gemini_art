<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Http\MaintenanceModeBypassCookie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class MaintenanceController extends Controller
{
    private const RESERVED_SECRETS = [
        'login',
        'logout',
        'quick-login',
        'dashboard',
        'orders',
        'pos',
        'workdesk',
        'cash',
        'reports',
        'archive',
        'vendor',
        'branches',
        'users',
        'share',
        'maintenance',
        'up',
    ];

    private function ensureOwner(): User
    {
        $user = Auth::user();
        if (! $user || ! $user->isOwner()) {
            abort(403, 'Hanya Owner yang dapat mengelola mode maintenance.');
        }

        return $user;
    }

    private function metaFilePath(): string
    {
        return storage_path('app/maintenance-meta.json');
    }

    /**
     * @return array<string, mixed>
     */
    private function readMetadata(): array
    {
        $path = $this->metaFilePath();
        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function writeMetadata(array $meta): void
    {
        $path = $this->metaFilePath();
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function deleteMetadata(): void
    {
        $path = $this->metaFilePath();
        if (is_file($path)) {
            @unlink($path);
        }
    }

    public function index(): View
    {
        $this->ensureOwner();

        $isMaintenanceActive = app()->maintenanceMode()->active();
        $activeData = [];
        $meta = [];

        if ($isMaintenanceActive) {
            try {
                $activeData = app()->maintenanceMode()->data();
            } catch (Throwable) {
                $activeData = [];
            }

            $meta = $this->readMetadata();
        }

        $activeSecret = is_string($activeData['secret'] ?? null) ? $activeData['secret'] : null;
        $suggestedSecret = $activeSecret ?? ('gemini-' . Str::lower(Str::random(8)));
        $bypassUrl = $activeSecret ? url('/' . $activeSecret) : null;

        return view('maintenance.index', compact(
            'isMaintenanceActive',
            'activeData',
            'meta',
            'activeSecret',
            'suggestedSecret',
            'bypassUrl'
        ));
    }

    public function preview(): View
    {
        $this->ensureOwner();

        $meta = app()->maintenanceMode()->active() ? $this->readMetadata() : [];

        return view('errors.503', [
            'isPreview' => true,
            'maintenanceMessage' => $meta['message'] ?? null,
            'maintenanceEta' => $meta['eta_label'] ?? null,
        ]);
    }

    public function activate(Request $request): RedirectResponse
    {
        $user = $this->ensureOwner();

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
            'eta_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'secret' => [
                'nullable',
                'string',
                'min:6',
                'max:50',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::notIn(self::RESERVED_SECRETS),
            ],
        ], [
            'message.max' => 'Pesan maintenance maksimal 500 karakter.',
            'eta_minutes.integer' => 'Estimasi selesai harus berupa angka menit.',
            'eta_minutes.min' => 'Estimasi selesai minimal 1 menit.',
            'eta_minutes.max' => 'Estimasi selesai maksimal 1440 menit (24 jam).',
            'secret.min' => 'Kode bypass rahasia minimal 6 karakter.',
            'secret.max' => 'Kode bypass rahasia maksimal 50 karakter.',
            'secret.regex' => 'Kode bypass hanya boleh huruf, angka, tanda hubung (-), atau garis bawah (_).',
            'secret.not_in' => 'Kode bypass tidak boleh memakai nama route sistem.',
        ]);

        $secret = trim((string) ($validated['secret'] ?? ''));
        if ($secret === '') {
            $secret = 'gemini-' . Str::lower(Str::random(8));
        }

        $message = trim((string) ($validated['message'] ?? ''));
        if ($message === '') {
            $message = 'Sistem sedang dalam proses perbaikan & pemeliharaan server. Data transaksi, order desain, dan arsip file Anda tetap aman tersimpan.';
        }

        $etaMinutes = isset($validated['eta_minutes']) ? (int) $validated['eta_minutes'] : null;
        $etaLabel = null;
        if ($etaMinutes !== null) {
            $targetTime = now('Asia/Jakarta')->addMinutes($etaMinutes)->format('H:i');
            if ($etaMinutes >= 60) {
                $hours = intdiv($etaMinutes, 60);
                $rem = $etaMinutes % 60;
                $durationText = $rem > 0 ? "{$hours} jam {$rem} menit" : "{$hours} jam";
            } else {
                $durationText = "{$etaMinutes} menit";
            }
            $etaLabel = "± {$durationText} (sekitar {$targetTime} WIB)";
        }

        $meta = [
            'message' => $message,
            'eta_minutes' => $etaMinutes,
            'eta_label' => $etaLabel,
            'activated_by' => $user->name,
            'activated_at' => now('Asia/Jakarta')->locale('id')->isoFormat('D MMM YYYY, HH:mm') . ' WIB',
        ];
        $this->writeMetadata($meta);

        $previousMessage = config('app.maintenance_message');
        $previousEta = config('app.maintenance_eta');

        config([
            'app.maintenance_message' => $message,
            'app.maintenance_eta' => $etaLabel,
        ]);

        try {
            $commandOptions = [
                '--render' => 'errors::503',
                '--secret' => $secret,
                '--refresh' => 60,
            ];

            if ($etaMinutes !== null) {
                $commandOptions['--retry'] = $etaMinutes * 60;
            }

            $exitCode = Artisan::call('down', $commandOptions);
        } finally {
            config([
                'app.maintenance_message' => $previousMessage,
                'app.maintenance_eta' => $previousEta,
            ]);
        }

        if ($exitCode !== 0) {
            $this->deleteMetadata();

            return back()->with('error', 'Gagal mengaktifkan mode maintenance. Periksa izin tulis folder storage/framework.');
        }

        return redirect()
            ->route('maintenance.index')
            ->with('success', 'Mode maintenance aktif! Pengunjung & staf kini melihat halaman 503, sedangkan browser Anda otomatis mendapat akses bypass.')
            ->withCookie(MaintenanceModeBypassCookie::create($secret));
    }

    public function deactivate(): RedirectResponse
    {
        $this->ensureOwner();

        $exitCode = Artisan::call('up');
        $this->deleteMetadata();

        if ($exitCode !== 0) {
            return back()->with('error', 'Gagal menonaktifkan mode maintenance.');
        }

        return redirect()
            ->back(fallback: route('maintenance.index'))
            ->with('success', 'Mode maintenance dimatikan. Sistem kini kembali ONLINE secara normal!')
            ->withCookie(Cookie::forget('laravel_maintenance'));
    }
}

