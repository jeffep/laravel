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
            // Log viewport and screen details for debugging
            console.log('Viewport:', {
                width: window.innerWidth,
                height: window.innerHeight,
                devicePixelRatio: window.devicePixelRatio
            });
            console.log('Screen:', {
                width: screen.width,
                height: screen.height,
                availWidth: screen.availWidth,
                availHeight: screen.availHeight
            });

            // Force scroll to top on load
            window.scrollTo(0, 0);

            // Function to fetch data from the server
            async function fetchData(location, hours = 6, interval = 1) {
                try {
                    const response = await fetch(`/api/sensor-data?location=${location}&hours=${hours}&interval=${interval}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const result = await response.json();
                    console.log(`Fetched Data for ${location}:`, result);
                    return result;
                } catch (error) {
                    console.error(`Error fetching data for ${location}:`, error);
                    return { data: [], titles: [] };
                }
            }

            // Function to create or update a Highcharts chart
            function createOrUpdateChart(container, title, seriesData, seriesName) {
                console.log(`Creating/Updating chart for container: ${container}`);
                console.log('Series Data:', seriesData);

                const containerElement = document.getElementById(container);
                if (!containerElement) {
                    console.error(`Container ${container} not found`);
                    return;
                }

                if (!seriesData || seriesData.length === 0) {
                    console.warn(`No data to plot for ${container}`);
                    containerElement.innerHTML = `<p style="text-align: center; color: #666;">No temperature data available</p>`;
                    return;
                }

                const chartConfig = {
                    chart: { type: 'line', height: 150 },
                    time: { timezone: 'America/Denver' },
                    title: { text: title, style: { fontSize: '12px' } },
                    xAxis: {
                        type: 'datetime',
                        title: { text: null },
                        labels: { enabled: false }
                    },
                    yAxis: {
                        title: { text: `${seriesName} (°F)`, style: { fontSize: '10px' } },
                        labels: { style: { fontSize: '8px' } }
                    },
                    series: [{ name: seriesName, data: seriesData, type: 'line' }],
                    legend: { enabled: false },
                    tooltip: { valueSuffix: '°F' }
                };

                if (Highcharts.charts[container]) {
                    const chart = Highcharts.charts[container];
                    chart.update({
                        title: { text: title },
                        series: [{ name: seriesName, data: seriesData }]
                    });
                } else {
                    Highcharts.chart(container, chartConfig);
                }
            }

            // Function to update charts for a location
            async function updateChart(location) {
                console.log(`Processing location: ${location}`);
                const result = await fetchData(location);
                const data = result.data || [];

                if (!data || data.length === 0) {
                    console.warn(`No data returned for ${location}`);
                    return;
                }

                const seriesData = data
                    .map(entry => [
                        moment.tz(entry.time, 'America/Denver').valueOf(),
                        parseFloat(entry.temperature) || null
                    ])
                    .filter(entry => entry[1] !== null);

                createOrUpdateChart(
                    `${location}-temperature-chart`,
                    `${location.charAt(0).toUpperCase() + location.slice(1)} Temp`,
                    seriesData,
                    'Temperature'
                );
            }

            // Initialize charts for all locations
            const locations = ['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'garagetablet', 'frontdoortablet'];
            locations.forEach(location => {
                updateChart(location);
            });

            // Scroll to top button functionality
            const scrollTopBtn = document.getElementById('scroll-top-btn');
            if (scrollTopBtn) {
                scrollTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                window.addEventListener('scroll', () => {
                    scrollTopBtn.style.display = window.scrollY > 100 ? 'block' : 'none';
                });
            }
        });
    </script>
@endsection

@section('content')
    <h1>Temperature Overview</h1>
    <div class="chart-grid">
        @foreach(['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'garagetablet', 'frontdoortablet'] as $location)
            <div class="chart-wrapper">
                <div class="chart-container" id="{{ $location }}-temperature-chart"></div>
            </div>
        @endforeach
    </div>
    <button id="scroll-top-btn" class="scroll-top-btn">↑ Top</button>
@endsection

@push('styles')
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: auto !important; /* Override 100vh */
            overflow-y: auto !important; /* Force vertical scrollbar */
            overflow-x: hidden !important; /* Prevent horizontal scrollbar */
            box-sizing: border-box;
        }
        .top-nav {
            position: relative !important; /* Override sticky */
            width: 100% !important;
            max-width: 100vw !important;
            flex-wrap: wrap !important; /* Allow menu items to wrap */
            padding: 5px !important;
            gap: 5px;
        }
        .top-nav a {
            font-size: 0.9em !important; /* Smaller font */
            padding: 5px 8px !important;
            flex: 1 0 auto; /* Flexible width */
            text-align: center;
        }
        .control-content {
            margin-top: 0 !important; /* Remove margin to maximize space */
            padding: 10px !important;
            width: 100% !important;
            max-width: 100vw !important;
            overflow: visible !important;
            flex-grow: 0 !important; /* Prevent stretching */
        }
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* Smaller columns */
            gap: 5px;
            padding: 5px;
            width: 100%;
            max-width: 100vw;
            box-sizing: border-box;
        }
        .chart-wrapper {
            text-align: center;
            width: 100%;
        }
        .chart-container {
            width: 100%;
            max-width: 200px; /* Smaller charts */
            height: 150px;
            margin: 5px auto;
            border: 1px solid #ccc;
            background-color: #fff;
            box-sizing: border-box;
        }
        .scroll-top-btn {
            display: none;
            position: fixed;
            bottom: 10px;
            right: 10px;
            padding: 6px 10px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            z-index: 1001; /* Above top-nav */
        }
        .scroll-top-btn:hover {
            background-color: #5a6268;
        }
        h1 {
            font-size: 14px;
            margin: 5px 0;
            text-align: center;
        }
        /* Media query for very small screens */
        @media (max-width: 480px) {
            .chart-grid {
                grid-template-columns: 1fr; /* Single column */
            }
            .chart-container {
                max-width: 100%;
                height: 120px; /* Smaller height */
            }
            .top-nav a {
                font-size: 0.8em !important;
                padding: 4px 6px !important;
            }
            h1 {
                font-size: 12px;
            }
        }
    </style>
@endpush
