<button {{ $attributes->merge(['type' => 'submit', 'class' => 'heian-btn inline-flex items-center text-xs']) }}>
    {{ $slot }}
</button>
