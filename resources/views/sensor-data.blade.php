@extends('dashboard')
@push('head-scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-timezone@0.5.33/builds/moment-timezone-with-data.min.js"></script>
@endpush
@section('body-inline-scripts')
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to fetch data from the server
    async function fetchData(location, hours, interval) {
        try {
            const response = await fetch(`/api/sensor-data?location=${location}&hours=${hours}&interval=${interval}`);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            console.log(`Fetched Data for ${location}:`, data);
            return data;
        } catch (error) {
            console.error(`Error fetching data for ${location}:`, error);
            return [];
        }
    }
    // Function to create or update a Highcharts chart
    function createOrUpdateChart(container, title, seriesData, seriesName) {
        console.log(`Creating/Updating chart for container: ${container}`);
        console.log(`Series Data for ${seriesName}:`, seriesData);
        if (!seriesData || seriesData.length === 0) {
            console.warn(`No data to plot for ${container}`);
            return;
        }
        if (Highcharts.charts[container]) {
            const chart = Highcharts.charts[container];
            chart.update({
                title: { text: title },
                series: [{ name: seriesName, data: seriesData }]
            });
        } else {
            Highcharts.chart(container, {
                time: {
                    timezone: 'America/Denver'
                },
                title: { text: title },
                xAxis: {
                    type: 'datetime',
                    title: { text: 'Time' }
                },
                yAxis: {
                    title: { text: seriesName }
                },
                series: [{ name: seriesName, data: seriesData }]
            });
        }
    }
    // Function to update charts for a location
    async function updateCharts(location, hours, interval) {
        console.log('updateCharts called with:', { location, hours, interval });
        const data = await fetchData(location, hours, interval);
        if (!data || data.length === 0) {
            console.warn(`No data returned for ${location}`);
            return;
        }
        // Parse datetime string directly
        const temperatureData = data.map(entry => [
            moment.tz(entry.time, 'America/Denver').valueOf(),
            parseFloat(entry.temperature) || null
        ]).filter(entry => entry[1] !== null);
        const humidityData = data.map(entry => [
            moment.tz(entry.time, 'America/Denver').valueOf(),
            parseFloat(entry.humidity) || null
        ]).filter(entry => entry[1] !== null);
        console.log(`Temperature Data for ${location}:`, temperatureData);
        console.log(`Humidity Data for ${location}:`, humidityData);
        createOrUpdateChart(
            `${location}-temperature-chart`,
            `${location.charAt(0).toUpperCase() + location.slice(1)} Temperature`,
            temperatureData,
            'Temperature (°F)'
        );
        createOrUpdateChart(
            `${location}-humidity-chart`,
            `${location.charAt(0).toUpperCase() + location.slice(1)} Humidity`,
            humidityData,
            'Humidity (%)'
        );
    }
    // Function to initialize buttons
    function initializeButtons() {
        const buttons = document.querySelectorAll('.time-range-button');
        buttons.forEach(button => {
            button.addEventListener('click', async () => {
                const location = button.getAttribute('data-location');
                const hours = parseInt(button.getAttribute('data-hours'));
                const interval = parseInt(button.getAttribute('data-interval'));
                await updateCharts(location, hours, interval);
            });
        });
    }
    // Load default data for all locations
    const locations = ['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'garagetablet', 'frontdoortablet', 'backporch'];
    locations.forEach(location => {
        updateCharts(location, 6, 1);
    });
    initializeButtons();
});
    </script>
@endsection
@section('control-content')
<!-- Chart Wrappers -->
@foreach(['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'garagetablet', 'frontdoortablet', 'backporch'] as $location)
    <div class="chart-wrapper">
        <div class="chart-title">{{ ucfirst($location) }} Environment</div>
        <div class="chart-container" id="{{ $location }}-temperature-chart"></div>
        <div class="chart-container" id="{{ $location }}-humidity-chart"></div>
        <div class="time-range-buttons">
            <button class="time-range-button" data-location="{{ $location }}" data-hours="6" data-interval="1">6h</button>
            <button class="time-range-button" data-location="{{ $location }}" data-hours="12" data-interval="1">12h</button>
            <button class="time-range-button" data-location="{{ $location }}" data-hours="24" data-interval="5">24h</button>
            <button class="time-range-button" data-location="{{ $location }}" data-hours="168" data-interval="30">1w</button>
        </div>
    </div>
@endforeach
@endsection
@push('styles')
<style>
    .chart-wrapper {
        text-align: center;
        margin: 20px;
    }
    .chart-title {
        font-size: 24px;
        margin-bottom: 20px;
    }
    .chart-container {
        display: inline-block;
        width: 45%;
        height: 300px;
        margin: 10px;
    }
    .time-range-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }
    .time-range-button {
        padding: 8px 16px;
        border: 2px solid #6c757d;
        border-radius: 15px;
        background-color: #6c757d;
        color: white;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
    }
    .time-range-button:hover {
        background-color: #5a6268;
        color: white;
    }
    .time-range-button:focus {
        outline: none;
        box-shadow: 0 0 5px #6c757d;
    }
</style>
@endpush
