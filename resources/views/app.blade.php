<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="/favicon.png">

        <!-- Fonts -->
{{--        <link rel="preconnect" href="https://fonts.gstatic.com">--}}
{{--        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">--}}
        <link rel="stylesheet" href="/fonts/icon/style.css">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
