@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#dbcbb0] focus:border-[#c1483c] focus:ring-[#c1483c] rounded-md shadow-sm bg-[rgba(246,240,227,0.9)]']) }}>
