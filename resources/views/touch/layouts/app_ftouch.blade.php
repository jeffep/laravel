<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Homesite') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/css/custom-styles.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
               /* Ensure the nav stays at the top */
        .top-nav {
            position: sticky;
            top: 0;
            background-color: #333; /* Add a background color */
            color: white;
            padding: 10px;
            z-index: 1000; /* Ensure it stays above other content */
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .top-nav a {
            color: white;
            text-decoration: none;
            font-size: 1.2em;
        }

        .top-nav a.active {
            font-weight: bold;
            text-decoration: underline;
        }

        /* Ensure the main content is below the nav */
        .control-content {
            margin-top: 60px; /* Add margin to avoid overlap with the nav */
            padding: 20px;
        }
        .top-nav button:hover {
            background-color: #E68A64;
        }
        /* Your existing .control-content styles */
        .control-content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 10px;
            margin-top: 60px; /* Add margin to avoid overlap with the nav */
        }
</style>
</head>
<body>
<nav class="top-nav">
    <a href="{{ route('touch.fswitches') }}" class="{{ request()->is('touch-dashboard/fswitches') ? 'active' : '' }}">Switches</a>
    <a href="{{ route('house') }}">Temperatures</a>
    <a href="{{ route('touch.cameras') }}" class="{{ request()->is('touch-dashboard/cameras') ? 'active' : '' }}">Cameras</a>
    <a href="{{ route('sounds') }}" class="{{ request()->is('sounds') ? 'active' : '' }}">Sounds</a>
</nav>

    <main class="control-content" id="tab-content">
        <!-- Initial content loaded dynamically -->
        @yield('content')
        @yield('switch-container')
    </main>

</body>
</html>

