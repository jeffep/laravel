@extends('dashboard')
@push('head-scripts')
    <script src="{{ asset('https://code.highcharts.com/highcharts.js') }}"></script>
    <script src="{{ asset('https://code.jquery.com/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js') }}"></script>
    <script src="{{ asset('https://cdn.jsdelivr.net/npm/moment-timezone@0.5.33/builds/moment-timezone-with-data.min.js') }}"></script>

@endpush

@section('body-inline-scripts')
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to fetch data from the server for a specific location
    async function fetchData(location, hours, interval) {
        const response = await fetch(`/api/sensor-data?location=${location}&hours=${hours}&interval=${interval}`);
        return await response.json();
    }

    // Function to create or update a Highcharts chart
    function createOrUpdateChart(container, title, seriesData, seriesName) {
        console.log(`Creating/Updating chart for container: ${container}`);
        console.log('Series Data:', seriesData);

        if (Highcharts.charts[container]) {
            const chart = Highcharts.charts[container];
            chart.update({
                title: { text: title },
                series: [{ name: seriesName, data: seriesData }]
            });
        } else {
            Highcharts.chart(container, {
                time: {
                    timezone: 'America/Denver' // Set the desired time zone
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

    // Function to update charts for a specific location
    async function updateCharts(location, hours, interval) {
        console.log('updateCharts called with:', { location, hours, interval });

        const data = await fetchData(location, hours, interval);
        console.log(`Fetched Data for ${location}:`, data);
/*
        const temperatureData = data.map(entry => [entry.time * 1000, entry.temperature]);
        const humidityData = data.map(entry => [entry.time * 1000, entry.humidity]);
*/
        const temperatureData = data.map(entry => [
                moment.tz(entry.time * 1000, 'America/Denver').valueOf(),
                entry.temperature
        ]);

        const humidityData = data.map(entry => [
                moment.tz(entry.time * 1000, 'America/Denver').valueOf(),
                entry.humidity
        ]);
        console.log(`Temperature Data for ${location}:`, temperatureData);
        console.log(`Humidity Data for ${location}:`, humidityData);

        createOrUpdateChart(
            `${location}-temperature-chart`,
            `${location} Temperature`,
            temperatureData,
            'Temperature (°F)'
        );

        createOrUpdateChart(
            `${location}-humidity-chart`,
            `${location} Humidity`,
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
                const hours = button.getAttribute('data-hours');
                const interval = button.getAttribute('data-interval');
                await updateCharts(location, hours, interval);
            });
        });
    }

    // Load default data for all locations
    const locations = ['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'garagetablet', 'frontdoortablet'];
    locations.forEach(location => {
        updateCharts(location, 6, 1); // Initial load with 6 hours of data for each location
    });

    initializeButtons(); // Set up button event listeners
});

    </script>
@endsection
@section('control-content')
<!-- Chart Wrapper -->

<div class="chart-wrapper">
    <div class="chart-title">Bedrooms Environment</div>
    <div class="chart-container" id="bedrooms-temperature-chart"></div>
    <div class="chart-container" id="bedrooms-humidity-chart"></div>
    <div class="time-range-buttons">
        <button class="time-range-button" data-location="bedrooms" data-hours="6" data-interval="1">6</button>
        <button class="time-range-button" data-location="bedrooms" data-hours="12" data-interval="1">12x1</button>
        <button class="time-range-button" data-location="bedrooms" data-hours="24" data-interval="5">24x5</button>
        <button class="time-range-button" data-location="bedrooms" data-hours="168" data-interval="30">1Wx30</button>
    </div>
</div>

<div class="chart-wrapper">
 <div class="chart-title">Den Environment</div>
    <div class="chart-container" id="den-temperature-chart"></div>
    <div class="chart-container" id="den-humidity-chart"></div>
    <div class="time-range-buttons">
        <button class="time-range-button" data-location="den" data-hours="6" data-interval="1">6</button>
        <button class="time-range-button" data-location="den" data-hours="12" data-interval="1">12x1</button>
        <button class="time-range-button" data-location="den" data-hours="24" data-interval="5">24x5</button>
        <button class="time-range-button" data-location="den" data-hours="168" data-interval="30">1Wx30</button>
    </div>
</div>

<div class="chart-wrapper">
    <div class="chart-title">Garage Environment</div>
    <div class="chart-container" id="garage-temperature-chart"></div>
    <div class="chart-container" id="garage-humidity-chart"></div>
    <div class="time-range-buttons">
        <button class="time-range-button" data-location="garage" data-hours="6" data-interval="1">6</button>
        <button class="time-range-button" data-location="garage" data-hours="12" data-interval="1">12x1</button>
        <button class="time-range-button" data-location="garage" data-hours="24" data-interval="5">24x5</button>
        <button class="time-range-button" data-location="garage" data-hours="168" data-interval="30">1Wx30</button>
    </div>
</div>

<div class="chart-wrapper">
    <div class="chart-title">Bird Bath Environment</div>
    <div class="chart-container" id="birdbath-temperature-chart"></div>
    <div class="chart-container" id="birdbath-humidity-chart"></div>
    <div class="time-range-buttons">
        <button class="time-range-button" data-location="birdbath" data-hours="6" data-interval="1">6</button>
        <button class="time-range-button" data-location="birdbath" data-hours="12" data-interval="1">12x1</button>
        <button class="time-range-button" data-location="birdbath" data-hours="24" data-interval="5">24x5</button>
        <button class="time-range-button" data-location="birdbath" data-hours="168" data-interval="30">1Wx30</button>
    </div>

<div class="chart-wrapper">
    <div class="chart-title">Bird Camera CPU Temp</div>
    <div class="chart-container" id="birdcam-temperature-chart"></div>
    <div class="chart-container" id="birdcam-humidity-chart"></div>
    <div class="time-range-buttons">
        <button class="time-range-button" data-location="birdcam" data-hours="6" data-interval="1">6</button>
        <button class="time-range-button" data-location="birdcam" data-hours="12" data-interval="1">12x1</button>
        <button class="time-range-button" data-location="birdcam" data-hours="24" data-interval="5">24x5</button>
        <button class="time-range-button" data-location="birdcam" data-hours="168" data-interval="30">1Wx30</button>
    </div>
</div>

<div class="chart-wrapper">
    <div class="chart-title">Garage Tablet CPU Temp</div>
    <div class="chart-container" id="garagetablet-temperature-chart"></div>
    <div class="chart-container" id="garagetablet-humidity-chart"></div>
    <div class="time-range-buttons">
        <button class="time-range-button" data-location="garagetablet" data-hours="6" data-interval="1">6</butt
on>
        <button class="time-range-button" data-location="garagetablet" data-hours="12" data-interval="1">12x1</
button>
        <button class="time-range-button" data-location="garagetablet" data-hours="24" data-interval="5">24x5</
button>
        <button class="time-range-button" data-location="garagetablet" data-hours="168" data-interval="30">1Wx3
0</button>
    </div>
</div>

<div class="chart-wrapper">
    <div class="chart-title">Front Door Tablet CPU Temp</div>
    <div class="chart-container" id="frontdoortablet-temperature-chart"></div>
    <div class="chart-container" id="frontdoortablet-humidity-chart"></div>
    <div class="time-range-buttons">
        <button class="time-range-button" data-location="frontdoortablet" data-hours="6" data-interval="1">6</butt
on>
        <button class="time-range-button" data-location="frontdoortablet" data-hours="12" data-interval="1">12x1</
button>
        <button class="time-range-button" data-location="frontdoortablet" data-hours="24" data-interval="5">24x5</
button>
        <button class="time-range-button" data-location="frontdoortablet" data-hours="168" data-interval="30">1Wx3
0</button>
    </div>
</div>

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
        width: 45%; /* Adjust the width as needed */
        height: 300px; /* Adjust the height as needed */
        margin: 10px;
    }

/* Center the buttons on the page */
.time-range-buttons {
    display: flex;
    justify-content: center; /* Center the buttons horizontally */
    gap: 10px; /* Add some space between the buttons */
    margin-top: 20px; /* Add some top margin for spacing */
}

/* Style for individual buttons */
.time-range-button {
    padding: 8px 16px; /* Smaller padding for a more compact look */
    border: 2px solid #6c757d; /* Gray border color */
    border-radius: 15px; /* Rounded corners */
    background-color: #6c757d; /* Gray background color */
    color: white; /* White text color */
    font-size: 14px; /* Smaller font size */
    cursor: pointer; /* Change cursor to pointer on hover */
    transition: background-color 0.3s, color 0.3s; /* Smooth transition for hover effect */
}

/* Hover effect for buttons */
.time-range-button:hover {
    background-color: #5a6268; /* Slightly darker gray on hover */
    color: white; /* Keep text color white on hover */
}

/* Remove outline on focus and add subtle shadow */
.time-range-button:focus {
    outline: none; /* Remove outline on focus */
    box-shadow: 0 0 5px #6c757d; /* Add a subtle shadow on focus */
}

</style>
@endpush
