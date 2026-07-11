{{-- resources/views/components/sidebar-section.blade.php --}}
@props(['title'])

<div class="pt-5 first:pt-0">
    <p class="px-3 mb-2 text-xs font-medium tracking-wide text-slate-500 uppercase">{{ $title }}</p>
    <div class="space-y-1">
        {{ $slot }}
    </div>
</div>
