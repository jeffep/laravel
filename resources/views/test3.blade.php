<!-- resources/views/test3.blade.php again!-->

@extends('dashboard')
    <!-- Include (push) Highcharts library -->
    @push('head-scripts')
     <script src="{{ asset('https://code.highcharts.com/highcharts.js') }} "></script>
    @endpush
    <!-- push styles -->
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
    </style>
@endpush

<!-- Content for the page -->

@section('control-content')

    <div class="chart-wrapper">
    <div class="chart-title">Den Environment</div>


    <!-- Container for the temperature chart -->
    <div class="chart-container" id="temperature-chart1"></div>

    <!-- Container for the humidity chart -->
    <div class="chart-container" id="humidity-chart1"></div>


    <div class="chart-wrapper">
    <div class="chart-title">Work Room Environment</div>


    <!-- Container for the temperature chart -->
    <div class="chart-container" id="temperature-chart"></div>

    <!-- Container for the humidity chart -->
    <div class="chart-container" id="humidity-chart"></div>


    <div class="chart-wrapper">
    <div class="chart-title">Garage Environment</div>


    <!-- Container for the temperature chart -->
    <div class="chart-container" id="temperature-chart2"></div>

    <!-- Container for the humidity chart -->
    <div class="chart-container" id="humidity-chart2"></div>

    <div class="chart-wrapper">
    <div class="chart-title">Back Porch Environment</div>


    <!-- Container for the temperature chart -->
    <div class="chart-container" id="temperature-chart3"></div>

    <!-- Container for the humidity chart -->
    <div class="chart-container" id="humidity-chart3"></div>



    <div class="chart-wrapper">
    <div class="chart-title">Bird Bath Environment</div>


    <!-- Container for the temperature chart -->
    <div class="chart-container" id="temperature-chart4"></div>

    <!-- Container for the humidity chart -->
    <div class="chart-container" id="humidity-chart4"></div>

    </div>
@endsection

<!-- Script to do charts -->
@section('body-inline-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to convert epoch timestamps to readable dates
            function convertEpochToReadableDates(epochTimes) {
                return epochTimes.map(epochTime => {
                    const timestamp = new Date(epochTime * 1000); // Convert to milliseconds
                    return timestamp.toLocaleString(); // Adjust format as needed
                });
            }

            // Function to create a Highcharts chart
            function createChart(container, title, categories, seriesData, seriesName) {
                Highcharts.chart(container, {
                    title: { text: title },
                    xAxis: {
                        type: 'datetime',
                        categories: categories,
                    },
                    series: [{ name: seriesName, data: seriesData }]
                });
            }

            // Function to process data and create charts
            function processData(data, tempChartId, humidityChartId, location) {
                const epochTimes = data.map(entry => entry.Time);
                const temperatures = data.map(entry => parseFloat(entry.Temperature));
                const humidities = data.map(entry => parseFloat(entry.Humidity));
                const formattedDates = convertEpochToReadableDates(epochTimes);

                console.log(`Formatted dates for ${location}:`, formattedDates);
                console.log(`Temperatures for ${location}:`, temperatures);
                console.log(`Humidities for ${location}:`, humidities);

                createChart(tempChartId, `${location} Temperature Chart`, formattedDates, temperatures, 'Temperature');
                createChart(humidityChartId, `${location} Humidity Chart`, formattedDates, humidities, 'Humidity');
            }

            // Process data for each location
            const locations = [
                { data: @json($linesDen), tempChartId: 'temperature-chart1', humidityChartId: 'humidity-chart1', location: 'Den' },
                { data: @json($linesWorkRoom), tempChartId: 'temperature-chart', humidityChartId: 'humidity-chart', location: 'Workroom' },
                { data: @json($linesGarage), tempChartId: 'temperature-chart2', humidityChartId: 'humidity-chart2', location: 'Garage' },
                { data: @json($linesBirdBath), tempChartId: 'temperature-chart4', humidityChartId: 'humidity-chart4', location: 'BirdBath'},
                // Uncomment the following lines if you want to include Back Porch data
                // { data: (*at*)json($linesBackPorch), tempChartId: 'temperature-chart3', humidityChartId: 'humidity-chart3', location: 'Back Porch' },
            ];

            locations.forEach(loc => processData(loc.data, loc.tempChartId, loc.humidityChartId, loc.location));
        });
    </script>
@endsection
