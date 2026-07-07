@props(['title'])

<div class="mt-6">
    <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ $title }}</p>
    <div class="space-y-1">
        {{ $slot }}
    </div>
</div>