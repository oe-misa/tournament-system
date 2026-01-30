@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#4b463f]']) }}>
    {{ $value ?? $slot }}
</label>
