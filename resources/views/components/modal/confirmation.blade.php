@props([
    'title' => false,
    'footer' => false,
    'cancel' => '$wire.closeModal()',
])
<div {{
    $attributes->class([
        'fixed inset-0 z-50 overflow-y-auto grid place-items-center bg-opacity-75 bg-gray-500',
    ])->merge([
        'x-cloak' => '',
        'x-on:keydown.escape.window' => $cancel,
    ])
}}>
    <div class="flex items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div {{
            $attributes->class([
                'fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75',
            ])
        }} aria-hidden="true"></div>

        <div {{
            $attributes->class([
                'inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full',
            ])->merge([
                'role' => 'dialog',
                'aria-modal' => 'true',
                'aria-labelledby' => 'modal-headline',
                'x-on:click.outside' => $cancel,
            ])
        }}>
            @if($title)
                <div class="bg-gray-200 px-4 py-3 sm:px-6 font-semibold">
                    <h3 class="text-lg leading-6" id="modal-headline">
                        {{ $title }}
                    </h3>
                </div>
            @endif

            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                {{ $slot }}
            </div>

            @if($footer)
                <div class="bg-gray-200 gap-4 px-4 py-3 sm:px-6 sm:flex sm:flex-row justify-between">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
