@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#b08b3a] text-sm font-semibold leading-5 text-[#2b2a27] focus:outline-none focus:border-[#c1483c] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[#6b645e] hover:text-[#2b2a27] hover:border-[#dbcbb0] focus:outline-none focus:text-[#2b2a27] focus:border-[#dbcbb0] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
