<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gumbo Millennium e-voting</title>

    {{-- Stylesheet and Javascript --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}"></script>
</head>
<body class="bg-grey-secondary">
    {{-- Warning notice --}}
    @if (config('app.debug'))
    <div class="bg-red-primary-3 text-white text-center px-4 py-2 font-bold">
        Applicatie in debug modus: Stemming niet beveiligd.
    </div>
    @endif

    <div class="container container-sm mx-auto">
        {{-- Header --}}
        <div class="flex flex-row items-center m-4">
            <div class="flex flex-col items-center">
                <img src="{{ mix('images/logo.svg') }}" class="h-16 flex-none" />
            </div>
            <div class="flex-grow"></div>
            <div class="text-right">
                @auth
                    Ingelogd als <strong>{{ user()->name }}</strong>
                @else
                    Niet ingelogd<br />
                    <a href="{{ route('login') }}">Inloggen</a>
                @endauth
            </div>
        </div>

        {{-- Messages --}}
        @if (session()->has('message'))
        <div class="rounded p-4 border border-yellow-600 my-4">
            <p>{{ session()->get('message') }}</p>
        </div>
        @endif

        {{-- Debug messages --}}
        @if (Config::get('app.debug') === true && session()->has('debug-message'))
        <div class="rounded p-4 border border-yellow-700 my-4">
            <strong class="text-yellow-700 font-bold block text-sm uppercase">Debug message</strong>
            <p>{{ session()->get('debug-message') }}</p>
        </div>
        @endif

        {{-- Title --}}
        @section('content')
            <div class="p-8 bg-red-100 border border-red-800 rounded text-center">
                <p>Something went wrong</p>
            </div>
        @show
    </div>
</body>
</html>
