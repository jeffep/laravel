
@section('control-content')
<h1>House Temperature Overlay</h1>
    <div class="layout-container">
        <img src="7905Top.png" alt="House Layout">
        <div class="temp-overlay garage" style="background-color: {{ getTemperatureColor($garage) }}">{{ $garage }}°F</div>
        <div class="temp-overlay den" style="background-color: {{ getTemperatureColor($den) }}">{{ $den }}°F</div>
        <div class="temp-overlay birdbath" style="background-color: {{ getTemperatureColor($birdbath) }}">{{ $birdbath }}°F</div>
        <div class="temp-overlay bedrooms" style="background-color: {{ getTemperatureColor($bedrooms) }}">{{ $bedrooms }}°F</div>
    </div>

    @php
        function getTemperatureColor($temp) {
            if ($temp === 'N/A') return 'gray';
            if ($temp <= 50) return 'blue';
            if ($temp <= 70) return 'lightgreen';
            if ($temp <= 85) return 'yellow';
            if ($temp <= 100) return 'orange';
            return 'red';
        }
    @endphp

    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .layout-container {
            position: relative;
            display: inline-block;
        }
        .temp-overlay {
            position: absolute;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            color: black;
        }
        .garage { left: 50px; top: 250px; }
        .den { left: 190px; top: 150px; }
        .birdbath { left: 300px; top: 40px; }
        .bedrooms { left: 630px; top: 200px; }

    </style>
