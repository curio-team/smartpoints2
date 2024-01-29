<!doctype html>
<html lang="en" class="h-full w-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if(App::environment() == "staging") TESTOMGEVING @endif
        @isset($title)
            <title>{{ $title }} - SmartPoints 2.0</title>
        @else
            <title>SmartPoints 2.0</title>
        @endisset
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    </head>
    <body class="flex flex-col h-full w-full">
        @if(App::environment() == "staging")
            <div style="position: sticky; top: 0; width: 100%; padding: 4px; font-size: 12px; text-align: center; background: darkorange; z-index: 100;">TESTOMGEVING: data wordt iedere nacht overschreven door kopie uit live-omgeving</div>
        @endif
        <nav class="bg-gray-800 text-white shadow-lg">
            <div class="flex flex-wrap items-center justify-between p-2 md:p-4">
                <div class="flex-grow text-center md:text-start">
                    <a class="text-xl font-semibold" href="{{ route('home') }}">SmartPoints 2.0</a>
                </div>
                <div class="flex md:items-center md:w-auto w-full" id="navbarNav">
                    <ul class="hidden md:flex flex-col md:flex-row list-none md:ml-auto items-center">
                        @auth
                            <li class="nav-item text-sm px-2"><em>Basisdata:</em></li>
                            <li class="nav-item"><x-link href="{{ route('weeks.manage') }}">weken</x-link></li>
                            <li class="nav-item"><x-link href="{{ route('groups.manage') }}">groepen</x-link></li>
                        @else
                            <li class="nav-item"><x-link href="{{ route('login') }}">Login</x-link></li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <div class="flex-grow mt-0 md:first-letter:mt-4 p-0 w-full">
            {{ $slot }}
        </div>

        <x-errors />
    </body>
</html>
