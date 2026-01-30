<button {{ $attributes->merge(['type' => 'submit', 'class' => 'heian-btn-danger inline-flex items-center text-xs']) }}>
    {{ $slot }}
</button>
