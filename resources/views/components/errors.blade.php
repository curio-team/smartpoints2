<div>
    <script>
        const messageTypes = {
            'error': '!bg-gradient-to-br from-red-400 to-orange-500 !text-black',
            'info': '!bg-gradient-to-br from-blue-400 to-purple-500 !text-white',
            'success': '!bg-gradient-to-br from-green-400 to-emerald-500 !text-white',
            'warning': '!bg-gradient-to-br from-yellow-400 to-orange-500 !text-black'
        };

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                window.Toastify({
                    text: "{{ $error }}",
                    duration: 3000,
                    className: messageTypes['error'],
                }).showToast();
            @endforeach
        @endif

        document.addEventListener('livewire:init', function () {
            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    const memo = JSON.parse(snapshot).memo;
                    const errors = memo.errors;

                    for (const [key, value] of Object.entries(errors)) {
                        window.Toastify({
                            text: value[0],
                            duration: 3000,
                            className: messageTypes['error'],
                        }).showToast();
                    }
                })

                fail(() => {
                    window.Toastify({
                        text: "Something went wrong",
                        duration: 3000,
                        className: messageTypes['error'],
                    }).showToast();
                })
            });

            Livewire.on('notification', (data) => {
                const { type, message } = data[0];
                window.Toastify({
                    text: message,
                    duration: 3000,
                    className: messageTypes[type],
                }).showToast();
            });
        });
    </script>
</div>
