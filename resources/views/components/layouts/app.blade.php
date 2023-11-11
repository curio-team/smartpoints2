<!doctype html>
<html lang="en" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @isset($title)
            <title>{{ $title }} - SmartPoints 2.0</title>
        @else
            <title>SmartPoints 2.0</title>
        @endisset
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="grid grid-auto-rows overflow-auto h-full w-full">
        <nav class="bg-gray-800 text-white shadow-lg">
            <div class="flex flex-wrap items-center justify-between p-2 md:p-4">
                <div class="flex-grow text-center md:text-start">
                    <a class="text-xl font-semibold" href="{{ route('home') }}">SmartPoints 2.0</a>
                </div>
                <div class="flex md:items-center md:w-auto w-full" id="navbarNav">
                    <ul class="hidden md:flex flex-col md:flex-row list-none md:ml-auto">
                        @auth
                        <li class="nav-item">
                            {{ auth()->user()->name }}
                        </li>
                        @else
                        <li class="nav-item">
                            <x-link href="{{ route('login') }}">Login</x-link>
                        </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <div class="mt-0 md:first-letter:mt-4 p-0 w-full">
            {{ $slot }}
        </div>
    </body>
</html>
