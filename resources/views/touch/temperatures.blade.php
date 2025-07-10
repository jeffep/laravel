@extends(auth()->user()->role === 'fronttouchpanel' ? 'layouts.app_ftouch' : 'layouts.app_gtouch')

@push('head-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/10.3.3/highcharts.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment-timezone@0.5.33/builds/moment-timezone-with-data.min.js"></script>
@endpush

@section('body-inline-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing charts');
            const temperatureData = @json($temperatureData);
            const locations = @json($locations);
            console.log('Temperature Data:', temperatureData);
            console.log('Locations:', locations);

            if (typeof Highcharts === 'undefined') {
                console.error('Highcharts not loaded');
                return;
            }

            function createOrUpdateChart(container, title, seriesData, seriesName) {
                console.log(document.getElementById('bedrooms-temperature-chart'));
                console.log(`Creating/Updating chart for container: ${container}`);
                console.log('Series Data:', seriesData);

                const containerElement = document.getElementById(container);
                if (!containerElement) {
                    console.error(`Container ${container} not found`);
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
                        chart: { type: 'line', height: 200 },
                        time: { timezone: 'America/Denver' },
                        title: { text: title, style: { fontSize: '16px' } },
                        xAxis: {
                            type: 'datetime',
                            title: { text: null },
                            labels: { enabled: false }
                        },
                        yAxis: {
                            title: { text: seriesName, style: { fontSize: '12px' } },
                            labels: { style: { fontSize: '10px' } }
                        },
                        series: [{ name: seriesName, data: seriesData, type: 'line' }],
                        legend: { enabled: false },
                        tooltip: { valueSuffix: '°F' }
                    });
                }
            }

            setTimeout(() => {
                locations.forEach(location => {
                    console.log(`Processing location: ${location}`);
                    const data = temperatureData[location].map(entry => [
                        moment.tz(entry.time, 'America/Denver').valueOf(),
                        entry.temperature
                    ]);
                    createOrUpdateChart(
                        `${location}-temperature-chart`,
                        `${location.charAt(0).toUpperCase() + location.slice(1)} Temp`,
                        data,
                        'Temperature (°F)'
                    );
                });
            }, 100);
        });
    </script>
@endsection

@section('content')
    <h1>Temperature Overview</h1>
    <div class="chart-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, auto); gap: 10px; padding: 10px;">
        <div class="chart-wrapper">
            <div class="chart-container" id="bedrooms-temperature-chart" style="min-height: 200px;"></div>
        </div>
        <div class="chart-wrapper">
            <div class="chart-container" id="den-temperature-chart" style="min-height: 200px;"></div>
        </div>
        <div class="chart-wrapper">
            <div class="chart-container" id="garage-temperature-chart" style="min-height: 200px;"></div>
        </div>
        <div class="chart-wrapper">
            <div class="chart-container" id="birdbath-temperature-chart" style="min-height: 200px;"></div>
        </div>
        <div class="chart-wrapper">
            <div class="chart-container" id="birdcam-temperature-chart" style="min-height: 200px;"></div>
        </div>
        <div class="chart-wrapper">
            <div class="chart-container" id="garagetablet-temperature-chart" style="min-height: 200px;"></div>
        </div>
    </div>
    <!-- Debug: Print temperature data -->
    <pre style="display: none;">{{ print_r($temperatureData, true) }}</pre>
@endsection

@push('styles')
    <style>
        .chart-grid {
            max-width: 1200px;
            margin: 0 auto;
        }
        .chart-wrapper {
            text-align: center;
        }
        .chart-container {
            width: 300px;
            height: 200px;
            margin: 10px auto;
            border: 1px solid #ccc;
            background-color: #fff;
            min-height: 200px;
        }
    </style>
@endpush
