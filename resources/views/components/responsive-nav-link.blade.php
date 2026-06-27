@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-md bg-[#9f3b30] px-3 py-2 text-start text-sm font-semibold text-white transition duration-150 ease-in-out focus:outline-none'
            : 'block w-full rounded-md px-3 py-2 text-start text-sm font-medium text-[#776b64] transition duration-150 ease-in-out hover:bg-[#f4efe6] hover:text-[#34251f] focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
