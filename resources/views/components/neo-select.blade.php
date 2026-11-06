@props([
    'name',
    'id' => null,
    'options' => [],
    'value' => null,
    'placeholder' => 'Pilih opsi...',
    'class' => '',
    'height' => 'h-10',
    'disabled' => false,
    'required' => false,
])

@php
    $elementId = $id ?? ($name . '_' . \Illuminate\Support\Str::random(6));
    $currentValue = (string) ($value !== null ? $value : request($name, ''));
    
    // Normalize options into [value => label]
    $normalizedOptions = [];
    if (is_iterable($options)) {
        foreach ($options as $optKey => $optVal) {
            if (is_array($optVal) && isset($optVal['value']) && isset($optVal['label'])) {
                $normalizedOptions[(string) $optVal['value']] = (string) $optVal['label'];
            } else {
                $normalizedOptions[(string) $optKey] = (string) $optVal;
            }
        }
    }

    // Determine current label
    $selectedLabel = $normalizedOptions[$currentValue] ?? ($normalizedOptions[''] ?? $placeholder);
@endphp

<div class="neo-select-wrapper relative w-full select-none {{ $class }}" data-neo-select-wrapper>
    <!-- Hidden native select for form serialization, browser FormData, and auto-submit -->
    <select name="{{ $name }}" 
            id="{{ $elementId }}" 
            class="sr-only pointer-events-none absolute w-[1px] h-[1px] p-0 -m-[1px] overflow-hidden border-0" 
            tabindex="-1" 
            aria-hidden="true"
            {{ $disabled ? 'disabled' : '' }}
            {{ $required ? 'required' : '' }}
            {{ $attributes }}>
        @foreach($normalizedOptions as $optVal => $optLabel)
            <option value="{{ $optVal }}" {{ (string)$optVal === $currentValue ? 'selected' : '' }}>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>

    <!-- Trigger Button (Rendered directly on server: Frame 0 Zero Layout Shift, Zero Twitch) -->
    <button type="button" 
            class="neo-select-trigger w-full {{ $height }} flex items-center justify-between gap-2 bg-white dark:bg-[#0c1222] border-[1.5px] border-slate-300 dark:border-slate-600 rounded-xl px-3 text-xs font-bold text-slate-800 dark:text-slate-100 shadow-[1.5px_1.5px_0px_0px_#e2e8f0] dark:shadow-[2px_2px_0px_0px_#334155] hover:border-slate-800 dark:hover:border-slate-400 focus:outline-none cursor-pointer text-left transition-colors duration-150"
            aria-haspopup="listbox" 
            aria-expanded="false"
            {{ $disabled ? 'disabled' : '' }}>
        <span class="neo-select-label truncate">{{ $selectedLabel }}</span>
        <span class="neo-select-chevron text-slate-400 dark:text-slate-500 shrink-0">
            <svg class="w-3.5 h-3.5 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6"/>
            </svg>
        </span>
    </button>

    <!-- Dropdown Menu (Soft Neo-Brutal with smooth cubic-bezier animation) -->
    <div class="neo-select-menu absolute top-[calc(100%+6px)] left-0 w-full min-w-full max-h-60 overflow-y-auto bg-white dark:bg-[#0c1222] border-[1.5px] border-slate-800 dark:border-slate-600 rounded-xl shadow-[3.5px_3.5px_0px_0px_#0f172a] dark:shadow-[4px_4px_0px_0px_#334155] py-1.5 z-50"
         role="listbox">
        @foreach($normalizedOptions as $optVal => $optLabel)
            @php $isSelected = ((string)$optVal === $currentValue); @endphp
            <div role="option"
                 data-value="{{ $optVal }}"
                 aria-selected="{{ $isSelected ? 'true' : 'false' }}"
                 class="neo-select-option px-3.5 py-2.5 text-xs {{ $isSelected ? 'font-black border-l-4 border-amber-500 bg-amber-100/90 dark:bg-amber-950/40 text-slate-950 dark:text-amber-300' : 'font-bold border-l-4 border-transparent text-slate-700 dark:text-slate-300 hover:bg-amber-50 dark:hover:bg-slate-800/80 hover:text-slate-950 dark:hover:text-white' }} cursor-pointer flex items-center justify-between gap-2.5 transition-colors">
                <span class="truncate">{{ $optLabel }}</span>
                <span class="neo-select-check shrink-0 text-amber-900 dark:text-amber-400" style="{{ $isSelected ? 'display: inline-flex;' : 'display: none;' }}">
                    <svg class="w-3.5 h-3.5 stroke-[2.5]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </span>
            </div>
        @endforeach
    </div>
</div>
