<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'SmartPoints 2.0')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="bg-gray-800 text-white">
            <div class="flex flex-wrap items-center justify-between p-4 md:px-6">
                <div class="flex-grow text-center md:text-start">
                    <a class="text-xl font-semibold" href="{{ route('home') }}">SmartPoints 2.0</a>
                </div>
                <div class="flex md:items-center md:w-auto w-full" id="navbarNav">
                    <ul class="flex flex-col md:flex-row list-none md:ml-auto">
                        <li class="nav-item">
                            <a class="text-white px-3 py-2 flex items-center text-xs uppercase font-bold rounded bg-slate-700 hover:bg-slate-500" href="{{ route('home') }}">Home</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        @yield('subnav')

        <div class="@yield('container-class', 'mt-4 p-4 md:px-6')">
            @yield('main')
        </div>
    </body>
</html>
