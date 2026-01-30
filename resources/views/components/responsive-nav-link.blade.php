@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-[#b08b3a] text-start text-base font-semibold text-[#2b2a27] bg-[rgba(176,139,58,0.12)] focus:outline-none focus:text-[#2b2a27] focus:bg-[rgba(193,72,60,0.1)] focus:border-[#c1483c] transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#6b645e] hover:text-[#2b2a27] hover:bg-[rgba(219,203,176,0.2)] hover:border-[#dbcbb0] focus:outline-none focus:text-[#2b2a27] focus:bg-[rgba(219,203,176,0.2)] focus:border-[#dbcbb0] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
