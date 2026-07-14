@props([
    'label',
    'value',
    'change' => null,
    'changeType' => 'neutral',
    'period' => 'vs May - June',
    'iconBg' => 'bg-gray-100',
    'iconColor' => 'text-gray-600',
    'iconPath'
])

@php
$changeColors = [
    'positive' => 'text-green-600',
    'negative' => 'text-red-500',
    'neutral' => 'text-gray-500'
];
$arrow = $changeType === 'positive' 
    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>' 
    : ($changeType === 'negative' 
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>' 
        : '');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-full {{ $iconBg }} flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $iconPath !!}
        </svg>
    </div>
    <div>
        <p class="text-xs text-gray-500 font-medium">{{ $label }}</p>
        <p class="text-xl font-bold text-gray-900">{{ $value }}</p>
        @if($change)
            <p class="text-xs {{ $changeColors[$changeType] }} font-medium flex items-center gap-1">
                @if($arrow)
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $arrow !!}</svg>
                @endif
                {{ $change }}
            </p>
        @endif
        <p class="text-xs text-gray-400">{{ $period }}</p>
    </div>
</div>