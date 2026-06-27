@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-md bg-[#9f3b30] px-3 py-1.5 text-sm font-semibold leading-5 text-white transition duration-150 ease-in-out hover:bg-[#9f3b30] focus:outline-none'
            : 'inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium leading-5 text-[#776b64] transition duration-150 ease-in-out hover:bg-[#f4efe6] hover:text-[#34251f] focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
