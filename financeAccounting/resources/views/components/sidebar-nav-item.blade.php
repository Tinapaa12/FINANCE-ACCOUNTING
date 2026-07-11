{{-- resources/views/components/sidebar-nav-item.blade.php --}}
@props(['href' => '#', 'active' => false, 'icon' => ''])

<a href="{{ $href }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold text-[15px] transition-colors
          {{ $active ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-white hover:bg-slate-800' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        {!! $icon !!}
    </svg>
    {{ $slot }}
</a>
