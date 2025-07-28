<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Homesite') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/custom-styles.css', 'resources/js/app.js'])
        @stack('styles')
        @stack('head-styles')
        @stack('head-scripts')
        @yield('head-inline-scripts')
<style>
/* Titles */
.title-color1 {
    background-color: #D3D3D3; /* Light Gray background */
    color: #000000; /* Bold Black text */
    font-weight: bold;
    font-size: 1.2em;
    margin-top: 0; /* Remove top margin */
    padding: 10px; /* Add padding for better appearance */
    display: block; /* Make it a block element to span the width */
    width: 100%; /* Ensure it spans the entire width */
    box-sizing: border-box; /* Include padding in the element's width */
}

/* Buttons */
.button-color2 {
    background-color: #A9A9A9; /* Darker Gray background */
    color: #000000; /* Bold Black text */
    text-decoration: none;
    padding: 5px 10px; /* Adjust padding for reduced height */
    display: block; /* Make it a block element to span the width */
    width: 100%; /* Ensure it spans the entire width */
    box-sizing: border-box; /* Include padding in the element's width */
    font-weight: bold; /* Bold text */
    border: 1px solid #000000; /* Add a black border */
    text-align: left; /* Align text to the left for a button-like appearance */
    margin: 0; /* Remove margin to stack neatly */
    line-height: 1.2; /* Adjust line height to control vertical spacing */
    padding-left: 20px; /* Indent text */
    font-size: 0.85em; /* Reduce font size */
}

.button-color2:hover {
    background-color: #8F8F8F; /* Slightly darker gray for hover effect */
    color: #000000; /* Ensure text remains bold black on hover */
}

.collapsible {
    background-color: #A9A9A9; /* Darker Gray background */
    color: #000000; /* Bold Black text */
    text-decoration: none;
    padding: 5px 10px; /* Adjust padding for reduced height */
    display: block; /* Make it a block element to span the width */
    width: 100%; /* Ensure it spans the entire width */
    box-sizing: border-box; /* Include padding in the element's width */
    font-weight: bold; /* Bold text */
    border: 1px solid #000000; /* Add a black border */
    text-align: left; /* Align text to the left for a button-like appearance */
    margin: 0; /* Remove margin to stack neatly */
    line-height: 1.2; /* Adjust line height to control vertical spacing */
    padding-left: 15px; /* Indent text */
    font-size: 0.85em; /* Reduce font size */
}

.collapsible:hover {
    background-color: #8F8F8F; /* Slightly darker gray for hover effect */
    color: #000000; /* Ensure text remains bold black on hover */
}
/* Hide the content by default */
.content {
    display: none;
}

</style>
        <style>
            hr {
                border-top: 2px solid #333;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
               <div class="flex">
                  <!-- Left Navigation Bar -->
                  <div class="w-1/4 bg-tan p-4">
                      <nav>
<ul>
    <!-- System Functions Section -->
    <li class="title title-color1">System Functions</li>
    <li><a href="{{ route('home') }}" class="button-color2">Home (Jarvis)</a></li>
    <form action="{{ route('generateGoAccessReport') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="button-color2">GoAccess Report</button>
    </form>
    <li><a href="{{ route('calendar') }}" class="button-color2">Calendar</a></li>

    <!-- Sensors Section -->
    <li class="title title-color1">Sensors</li>
    <li><a href="{{ route('house') }}" class="button-color2">House Layout</a></li>
    <li><a href="{{ route('sensor-data') }}" class="button-color2">Temperature Graphs</a></li>

    <!-- Shelly Controllers Section -->
    <li class="title title-color1">Shelly Controllers</li>
    <li>
        <button class="collapsible" data-id="utility-shellys">Utility Shellys ></button>
        <ul class="content">
            <li><a href="{{ route('shelly_status') }}" class="button-color2">Shellys</a></li>
            <li><a href="{{ route('shellyDevice', ['id' => 1]) }}" class="button-color2">Shelly - Den Fan</a></li>
            <li><a href="{{ route('shellyDevice', ['id' => 2]) }}" class="button-color2">Shelly Bdrm Heater</a></li>
            <li><a href="{{ route('shellyDevice', ['id' => 3]) }}" class="button-color2">Shelly Bdrm AC WPump</a></li>
            <li><a href="{{ route('shellyDevice', ['id' => 4]) }}" class="button-color2">Shelly Bdrm AC Blow</a></li>
        </ul>
    </li>
    <li>
        <button class="collapsible" data-id="light-shellys">Light Shellys ></button>
        <ul class="content">
            <li><a href="{{ route('shellyLight', ['id' => 1]) }}" class="button-color2">Breakfast Light</a></li>
            <li><a href="{{ route('shellyLight', ['id' => 2]) }}" class="button-color2">Camper Light</a></li>
            <li><a href="{{ route('shellyLight', ['id' => 3]) }}" class="button-color2">Garage Light</a></li>
            <li><a href="{{ route('shellyLight', ['id' => 4]) }}" class="button-color2">Lamp Post Light</a></li>
            <li><a href="{{ route('shellyLight', ['id' => 5]) }}" class="button-color2">Foyer Light</a></li>
            <li><a href="{{ route('shellyLight', ['id' => 6]) }}" class="button-color2">Front Porch Light</a></li>

        </ul>
    </li>

    <!-- Webcams Section -->
    <li class="title title-color1">Webcams</li>
    <li><a href="{{ route('webcam.show', 'webcam1') }}" class="button-color2">Backyard Cam</a></li>
    <li><a href="{{ route('webcam.show', 'birdcam') }}" class="button-color2">Bird Cam</a></li>

    <!-- Sprinklers Section -->
    <li class="title title-color1">Sprinklers</li>
    <li><a href="{{ route('sprinkler2') }}" class="button-color2">Sprinklers</a></li>

    <!-- Sounds Section -->
    <li class="title title-color1">Sounds</li>
    <li><a href="{{ route('sounds') }}" class="button-color2">Sounds</a></li>

    <!-- House Info Section -->
    <li class="title title-color1">House Info</li>
    <li><a href="{{ route('wattage_chart') }}" class="button-color2">Elec. Bill</a></li>
    <li><a href="{{ route('trugreen') }}" class="button-color2">Trugreen</a></li>
    <li><a href="{{ route('fertilizer') }}" class="button-color2">Fert. Chart</a></li>

    <!-- Automation Section -->
    <li class="title title-color1">Automation</li>
    <li><a href="{{ route('devices.index') }}" class="button-color2">T. Sensor Setup</a></li>
    <li><a href="{{ route('automation_rules.index') }}" class="button-color2">Automate</a></li>


    <li class="title title-color1">Farm</li>
    <li><a href="{{ route('corn.prices') }}">Corn Prices</a></li>
  
</ul>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const collapsibles = document.querySelectorAll('.collapsible');

    // Function to save the state of a collapsible menu
    function saveCollapsibleState(button, isOpen) {
        const id = button.getAttribute('data-id'); // Unique identifier for each collapsible
        localStorage.setItem(id, isOpen ? 'open' : 'closed');
    }

    // Function to load the state of a collapsible menu
    function loadCollapsibleState(button) {
        const id = button.getAttribute('data-id'); // Unique identifier for each collapsible
        return localStorage.getItem(id) === 'open';
    }

    // Loop through each collapsible button
    collapsibles.forEach((button, index) => {
        // Assign a unique ID to each collapsible (if not already assigned)
        if (!button.getAttribute('data-id')) {
            button.setAttribute('data-id', `collapsible-${index}`);
        }

        // Load the saved state
        const isOpen = loadCollapsibleState(button);
        const content = button.nextElementSibling;
        if (isOpen) {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }

        // Add a click event listener to the button
        button.addEventListener('click', function () {
            const content = this.nextElementSibling;
            if (content.style.display === 'block') {
                content.style.display = 'none';
                saveCollapsibleState(this, false); // Save closed state
            } else {
                content.style.display = 'block';
                saveCollapsibleState(this, true); // Save open state
            }
        });
    });
});
</script>

                      </nav>
                  </div>
                  <!-- Right Content Area -->
                  <div class="w-3/4 p-4">
                     {{ $slot }}
                      @stack('body-scripts')
                      @yield('body-inline-scripts')
                  </div>
               </div>
            </main>
        </div>
    </body>
</html>
