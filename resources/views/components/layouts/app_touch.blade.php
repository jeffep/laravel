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
            display: flex;
            justify-content: space-around;
            background-color: #007B8F;
            padding: 10px 0;
        }
        .top-nav button {
            flex-grow: 1;
            background-color: #FFB07F;
            color: black;
            border: 1px solid black;
            padding: 10px;
            font-size: 1.2em;
            cursor: pointer;
            transition: background 0.3s;
        }
        .top-nav button.active {
            background-color: #40E0D0;
        }
        .top-nav button:hover {
            background-color: #E68A64;
        }
        .control-content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 10px;
        }
    </style>
</head>
<body>
<!--
    <nav class="top-nav">
        <button id="tab-switches" class="active" onclick="showTab('switches')">Switches</button>
        <button id="tab-temperatures" onclick="showTab('temperatures')">Temperatures</button>
        <button id="tab-cameras" onclick="showTab('cameras')">Cameras</button>
        <button id="tab-sounds" onclick="showTab('sounds')">Sounds</button>
    </nav>
-->
<nav class="top-nav">
    <a href="{{ route('touch.switches') }}" class="{{ request()->is('touch-dashboard/switches') ? 'active' : '' }}">Switches</a>
    <a href="{{ route('house') }}">Temperatures</a>
    <a href="{{ route('touch.cameras') }}" class="{{ request()->is('touch-dashboard/cameras') ? 'active' : '' }}">Cameras</a>
    <a href="{{ route('sounds') }}" class="{{ request()->is('sounds') ? 'active' : '' }}">Sounds</a>
</nav>

    <main class="control-content" id="tab-content">
        <!-- Initial content loaded dynamically -->
    </main>

    <script>
/*
        function showTab(tab) {
            let routes = {
                'switches': '/touch-dashboard/switches',  // Local Blade template
                'temperatures': '/house',                // Existing HouseController route
                'cameras': '/touch-dashboard/cameras',   // Will be implemented later
                'sounds': '/sounds'                      // Existing ControlPageController route
            };

            if (routes[tab]) {
                fetch(routes[tab])
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('tab-content').innerHTML = html;
                    });

                // Update active tab styling
                document.querySelectorAll('.top-nav button').forEach(btn => btn.classList.remove('active'));
                document.getElementById(`tab-${tab}`).classList.add('active');
            }
        }
*/
    </script>
</body>
</html>

