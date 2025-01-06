<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Moje aplikace')</title> 
    <link rel="stylesheet" href="{{ asset('css/board.css') }}"> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <header>
        @include('components/layouts/Nav') 
    </header>
    <main>
        @yield('content') 
    </main>
    <footer>
        @include('components/layouts/Footer') 
    </footer>
    <script src="{{ asset('js/AllGames.js') }}"></script> 
</body>
</html>
