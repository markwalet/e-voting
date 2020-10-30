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

    {{-- Livewire --}}
    @livewireStyles
</head>

<body class="bg-grey-secondary">
    {{-- Warning notice --}}
    @if (Config::get('app.beta'))
    <div class="bg-red-700 text-white text-center px-4 py-2 font-bold">
        Applicatie in beta modus: Stemming en gegevens niet beveiligd.
    </div>
    @endif

    {{-- Deploy warning --}}
    @if (Cache::get('sys.in-deploy') === true)
    <div class="bg-orange-700 text-white text-center px-4 py-2 font-bold">
        Er wordt een nieuwe versie uitgerold, dit kan voor downtime zorgen.
    </div>
    @endif

    {{-- Header --}}
    <header class="container container--md header">
        {{-- Logo --}}
        <a href="/" class="flex flex-col items-center">
            <img src="{{ mix('images/logo.svg') }}" class="h-16 flex-none" />
        </a>

        {{-- Spacer --}}
        <div class="flex-grow"></div>

        {{-- User --}}
        <div class="text-right">
            @auth
            Ingelogd als <strong>{{ Auth::user()->name }}</strong><br />
            @can('monitor')
            <a href="{{ route('monitor.index') }}">Controle</a>&nbsp;
            @endcan
            @can('admin')
            <a href="{{ route('admin.index') }}">Admin</a>&nbsp;
            @endcan
            <button type="submit" form="logout" class="appearance-none underline hover:no-underline">Uitloggen</button>
            @else
            Niet ingelogd<br />
            <a href="{{ route('login') }}">Inloggen</a>
            @endauth
        </div>
    </header>

    <main>
        @yield('content-before')

        <div class="container mx-auto">
            {{-- Errors --}}
            @if ($errors->any())
            <div class="notice notice--danger my-4">
                <p class="mb-4">Er is wat fout gegaan bij het versturen van de data:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Messages --}}
            @if (session()->has('message'))
            <div class="notice notice--warning my-4">
                <p>{{ session()->pull('message') }}</p>
            </div>
            @endif

            {{-- Debug messages --}}
            @if (Config::get('app.beta') && session()->has('debug-message'))
            <div class="notice notice--warning my-4">
                <strong class="text-yellow-700 font-bold block text-sm uppercase">Debug message</strong>
                <p>{{ session()->pull('debug-message') }}</p>
            </div>
            @endif

            {{-- Title --}}
            @section('content')
            <div class="p-8 bg-red-100 border border-red-800 rounded text-center">
                <p>Something went wrong</p>
            </div>
            @show
        </div>
        @yield('content-after')
    </main>

    <footer class="container container--md mx-auto mb-4">
        <hr class="mt-8 mb-4 border-gray-200">

        <div class="flex items-top justify-between text-sm text-gray-600">
            <div>
                © Gumbo Millennium, <a target="_blank" rel="noopener license"
                    href="https://github.com/gumbo-millennium/e-voting/blob/master/LICENSE">AGPL v3</a>.
            </div>
            <div class="text-right">
                Versie <a href="@version(link)" target="_blank" rel="noopener source">@version(short)</a>
                (<a href="{{ route('audit') }}">controleer</a>)
            </div>
        </div>
    </footer>

    {{-- Logout form --}}
    <form action="{{ route('logout') }}" method="post" id="logout">@csrf</form>

    {{-- Livewire --}}
    @livewireScripts
</body>

</html>
