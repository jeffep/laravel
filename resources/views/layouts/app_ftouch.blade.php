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
        .top-nav {
            position: sticky;
            top: 0;
            background-color: #333;
            color: white;
            padding: 10px;
            z-index: 1000;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        .top-nav a {
            color: white;
            text-decoration: none;
            font-size: 1.2em;
            padding: 8px 12px;
            transition: background-color 0.3s;
        }
        .top-nav a.active,
        .top-nav a:hover {
            background-color: #E68A64;
            border-radius: 4px;
        }
        .control-content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <nav class="top-nav">
        <a href="{{ route('touch.dashboard') }}" class="{{ request()->routeIs('touch.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('touch.switches') }}" class="{{ request()->routeIs('touch.switches') ? 'active' : '' }}">Switches</a>
        <a href="{{ route('touch.cameras') }}" class="{{ request()->routeIs('touch.cameras') ? 'active' : '' }}">Cameras</a>
        <a href="{{ route('touch.calendar') }}" class="{{ request()->routeIs('touch.calendar') ? 'active' : '' }}">Calendar</a>
        <a href="{{ route('touch.clock') }}" class="{{ request()->routeIs('touch.clock') ? 'active' : '' }}">Clock</a>
        <a href="{{ route('touch.corn-futures') }}" class="{{ request()->routeIs('touch.corn-futures') ? 'active' : '' }}">Corn Futures</a>
        <a href="{{ route('touch.temperatures') }}" class="{{ request()->routeIs('touch.temperatures') ? 'active' : '' }}">Temperatures</a>
        <a href="{{ route('touch.slideshow') }}" class="{{ request()->routeIs('touch.slideshow') ? 'active' : '' }}">Slideshow</a>
    </nav>

    <main class="control-content" id="tab-content">
        @yield('content')
        @yield('switch-container')
    </main>

    <script>
        const cycleRoutes = [
            '{{ route('touch.switches') }}',
            '{{ route('touch.clock') }}',
            '{{ route('touch.corn-futures') }}',
            '{{ route('touch.temperatures') }}'
        ];
        let currentIndex = 0;
        let cycleTimer = null;
        const cycleInterval = 5 * 60 * 1000; // 5 minutes in milliseconds
        const inactivityTimeout = 5 * 60 * 1000; // 5 minutes inactivity

        function startCycle() {
            cycleTimer = setInterval(() => {
                currentIndex = (currentIndex + 1) % cycleRoutes.length;
                window.location.href = cycleRoutes[currentIndex];
            }, cycleInterval);
        }

        function resetCycle() {
            clearInterval(cycleTimer);
            setTimeout(startCycle, inactivityTimeout);
        }

        // Start cycling
        startCycle();

        // Pause on interaction
        document.addEventListener('touchstart', resetCycle);
        document.addEventListener('click', resetCycle);
    </script>
</body>
</html>
