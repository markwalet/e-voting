<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gumbo Millennium e-voting</title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="bg-grey-secondary">
    @section('content')
    <div class="container mx-auto">
        {{-- Header --}}
        <div class="text-center mx-4">
            <img src="{{ mix('images/logo.svg') }}" class="h-32 my-4 mx-auto" />
        </div>

        {{-- Message --}}
        @if (session()->has('message'))
        <div class="rounded p-4 border border-brand-primary-1 mb-4">
            {{ session()->get('message') }}
        </div>
        @endif

        {{-- Title --}}
        @section('content-inner')
            <div class="mb-8">
                <h1 class="font-title font-bold text-4xl">Gumbo E-Voting</h1>
            </div>
        @show
    </div>
    @show
</body>
</html>
