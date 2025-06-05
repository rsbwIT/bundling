<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Antrian Farmasi</title>
    @livewireStyles
</head>
<body>

    {{ $slot }} {{-- This is where your Livewire component's content will be rendered --}}

    @livewireScripts
    <script src="{{ asset('js/app.js') }}"></script> {{-- Include your main JS file if you have one --}}
    @stack('scripts') {{-- This is where the script from @push('scripts') will be included --}}
</body>
</html>
