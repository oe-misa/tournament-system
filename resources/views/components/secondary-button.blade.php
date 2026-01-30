<button {{ $attributes->merge(['type' => 'button', 'class' => 'heian-btn-secondary inline-flex items-center text-xs']) }}>
    {{ $slot }}
</button>
