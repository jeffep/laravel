<!-- resources/views/sensor-history.blade.php -->
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
            const sensorSelect = document.getElementById('sensor-select');
            const dataTypeSelect = document.getElementById('data-type-select');
            const startDateInput = document.getElementById('start-date');
            const endDateInput = document.getElementById('end-date');
            const granularitySelect = document.getElementById('granularity');
            const fetchDataButton = document.getElementById('fetch-data');
            const chartContainer = document.getElementById('sensor-chart');

            // Function to fetch data from the server
            async function fetchData(location, dataType, startDate, endDate, interval) {
                try {
                    const response = await fetch(`/api/sensor-history?location=${location}&data_type=${encodeURIComponent(dataType)}&start_date=${startDate}&end_date=${endDate}&interval=${interval}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    const result = await response.json();
                    console.log(`Fetched Data for ${location}, ${dataType}:`, result);
                    return result;
                } catch (error) {
                    console.error(`Error fetching data for ${location}, ${dataType}:`, error);
                    chartContainer.innerHTML = `<p style="text-align: center; color: #666;">Error fetching data: ${error.message}</p>`;
                    return { data: [], titles: [] };
                }
            }

            // Function to create or update a Highcharts chart
            function createOrUpdateChart(title, seriesData, seriesName) {
                if (!seriesData || seriesData.length === 0) {
                    chartContainer.innerHTML = `<p style="text-align: center; color: #666;">No data available for ${seriesName} - ${title}</p>`;
                    return;
                }

                const units = {
                    'Temperature': '°F',
                    'Humidity': '%',
                    'Air Quality': '', // Adjust unit as needed for Air Quality
                    'FanSpeed': 'RPM'
                };
                const unit = units[title] || '';

                const chartConfig = {
                    time: {
                        timezone: 'America/Denver'
                    },
                    title: { text: `${seriesName} ${title}` },
                    xAxis: {
                        type: 'datetime',
                        title: { text: 'Time' }
                    },
                    yAxis: {
                        title: { text: `${title} (${unit})` }
                    },
                    series: [{ name: title, data: seriesData }]
                };

                Highcharts.chart('sensor-chart', chartConfig);
            }

            // Function to update the chart based on selections
            async function updateChart() {
                const location = sensorSelect.value;
                const dataType = dataTypeSelect.value;
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;
                const interval = granularitySelect.value;

                if (!location || !dataType || !startDate || !endDate) {
                    chartContainer.innerHTML = '<p style="text-align: center; color: #666;">Please select a sensor, data type, and date range.</p>';
                    return;
                }

                const result = await fetchData(location, dataType, startDate, endDate, interval);
                const data = result.data || [];
                const titles = result.titles || [];

                if (!data || data.length === 0 || !titles || titles.length === 0) {
                    chartContainer.innerHTML = `<p style="text-align: center; color: #666;">No data available for ${location} - ${dataType}.</p>`;
                    return;
                }

                titles.forEach(title => {
                    const columnKey = title.toLowerCase().replace(/[^a-z0-9]/g, '_');
                    const seriesData = data.map(entry => [
                        moment.tz(entry.time, 'America/Denver').valueOf(),
                        parseFloat(entry[columnKey]) || null
                    ]).filter(entry => entry[1] !== null);

                    createOrUpdateChart(title, seriesData, location.charAt(0).toUpperCase() + location.slice(1));
                });
            }

            // Event listener for the fetch data button
            fetchDataButton.addEventListener('click', updateChart);

            // Initialize default date range (last 24 hours)
            const now = moment().tz('America/Denver');
            endDateInput.value = now.format('YYYY-MM-DD');
            startDateInput.value = now.subtract(24, 'hours').format('YYYY-MM-DD');
        });
    </script>
@endsection

@section('control-content')
    <div class="history-wrapper">
        <div class="history-title">Sensor Data History</div>
        <div class="controls">
            <label for="sensor-select">Select Sensor:</label>
            <select id="sensor-select" name="sensor">
                <option value="">Select a sensor</option>
                @foreach($locations as $location)
                    <option value="{{ $location }}">{{ ucfirst($location) }}</option>
                @endforeach
            </select>

            <label for="data-type-select">Data Type:</label>
            <select id="data-type-select" name="data_type">
                <option value="">Select a data type</option>
                @foreach($dataTypes as $dataType)
                    <option value="{{ $dataType }}">{{ ucfirst($dataType) }}</option>
                @endforeach
            </select>

            <label for="start-date">Start Date:</label>
            <input type="date" id="start-date" name="start_date">

            <label for="end-date">End Date:</label>
            <input type="date" id="end-date" name="end_date">

            <label for="granularity">Granularity:</label>
            <select id="granularity" name="granularity">
                <option value="1">1 Minute</option>
                <option value="5">5 Minutes</option>
                <option value="30">30 Minutes</option>
                <option value="60">1 Hour</option>
            </select>

            <button id="fetch-data">Fetch Data</button>
        </div>
        <div id="sensor-chart" class="chart-container"></div>
    </div>
@endsection

@push('styles')
    <style>
        .history-wrapper {
            text-align: center;
            margin: 20px;
        }
        .history-title {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .controls label {
            font-weight: bold;
        }
        .controls select, .controls input[type="date"], .controls button {
            padding: 8px;
            border: 2px solid #6c757d;
            border-radius: 5px;
            font-size: 14px;
        }
        .controls button {
            background-color: #6c757d;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .controls button:hover {
            background-color: #5a6268;
        }
        .chart-container {
            width: 80%;
            height: 400px;
            margin: 0 auto;
        }
    </style>
@endpush
