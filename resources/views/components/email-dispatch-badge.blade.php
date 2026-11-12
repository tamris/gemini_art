@props([
    'state' => null,
    'compact' => false,
    'variant' => 'pill',
    'short' => false,
])

@php
    $status = $state['status'] ?? 'none';
@endphp

@if($status !== 'none')
    @php
        $meta = match ($status) {
            'sent' => [
                'pill' => 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-900 dark:text-emerald-200 border-emerald-700 dark:border-emerald-700',
                'inline' => 'text-emerald-700 dark:text-emerald-400 font-bold',
                'icon' => 'check-circle',
                'short' => 'Terkirim',
            ],
            'needs_resend' => [
                'pill' => 'bg-amber-100 dark:bg-amber-950/60 text-amber-900 dark:text-amber-200 border-amber-700 dark:border-amber-700',
                'inline' => 'text-amber-700 dark:text-amber-300 font-bold',
                'icon' => 'alert-triangle',
                'short' => 'Kirim Ulang',
            ],
            default => [
                'pill' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-500 dark:border-slate-600',
                'inline' => 'text-slate-500 dark:text-slate-400 font-semibold',
                'icon' => 'mail',
                'short' => 'Belum Email',
            ],
        };

        $displayLabel = $short ? $meta['short'] : ($state['label'] ?? '');

        $tooltipParts = array_filter([
            $state['detail'] ?? null,
            !empty($state['sent_to']) ? 'Tujuan: ' . $state['sent_to'] : null,
            !empty($state['sent_at']) ? 'Terakhir: ' . $state['sent_at']->format('d M Y, H:i') : null,
            !empty($state['sent_by']) ? 'Oleh: ' . $state['sent_by'] : null,
        ]);
        $tooltip = implode(' · ', $tooltipParts);

        if ($variant === 'inline') {
            $containerClasses = "inline-flex items-center gap-1 text-[11px] leading-tight whitespace-nowrap {$meta['inline']}";
            $iconClasses = 'w-3 h-3 shrink-0';
        } else {
            $sizeClasses = $compact
                ? ($short ? 'px-2 py-1 text-[10px] border gap-1' : 'px-2 py-0.5 text-[10px] border gap-1')
                : 'px-2.5 py-0.5 text-xs border-[1.5px] gap-1.5 shadow-[1px_1px_0px_0px_#0f172a] dark:shadow-[1px_1px_0px_0px_#000000]';
            $containerClasses = "inline-flex items-center rounded-full font-black whitespace-nowrap {$sizeClasses} {$meta['pill']}";
            $iconClasses = $compact ? 'w-3 h-3 shrink-0' : 'w-3.5 h-3.5 shrink-0';
        }
    @endphp
    <span {{ $attributes->merge([
        'class' => $containerClasses,
        'title' => $tooltip,
    ]) }}>
        <x-icon :name="$meta['icon']" class="{{ $iconClasses }}" />
        <span>{{ $displayLabel }}</span>
    </span>
@endif

