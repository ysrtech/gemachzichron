<button {{ $attributes->merge([
    'type' => 'submit',
    'x-bind:disabled' => "processing",
    'class' => 'inline-flex items-center px-4 py-2 bg-primary-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-900 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:shadow-outline-primary disabled:opacity-25 transition ease-in-out duration-150'
    ]) }}>
    <div x-show="processing" class="btn-spinner mr-2"></div>
    {{ $slot }}
</button>
