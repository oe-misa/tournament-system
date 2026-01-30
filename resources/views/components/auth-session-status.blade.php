@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-[#3e5b47]']) }}>
        {{ $status }}
    </div>
@endif
