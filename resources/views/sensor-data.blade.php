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
            const result = await response.json();
            console.log(`Fetched Data for ${location}:`, result);
            return result;
        } catch (error) {
            console.error(`Error fetching data for ${location}:`, error);
            return { data: [], titles: [] };
        }
    }

    // Function to create chart container if it doesn't exist
    function ensureChartContainer(location, title) {
        const containerId = `${location}-${title.toLowerCase().replace(/[^a-z0-9]/g, '_')}-chart`;
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'chart-container';
            document.getElementById(`${location}-charts`).appendChild(container);
        }
        return containerId;
    }

    // Function to create or update a Highcharts chart
    function createOrUpdateChart(containerId, title, seriesData, seriesName) {
    console.log(`Updating chart: ${containerId}`, seriesData);

    if (!seriesData || seriesData.length === 0) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `<p style="text-align: center; color: #666;">No data available for ${seriesName}</p>`;
        }
        const oldChart = Highcharts.charts.find(c => c && c.renderTo.id === containerId);
        if (oldChart) oldChart.destroy();
        return;
    }

    // Extract sensor type from the end of the title (e.g., "Temperature")
    const sensorType = title.trim().split(' ').pop();
    const units = { 'Temperature': '°F', 'Humidity': '%', 'FanSpeed': 'RPM' };
    const unit = units[sensorType] || '';

    const chartConfig = {
        chart: { renderTo: containerId },
        time: { timezone: 'America/Denver' },
        title: { text: title },
        xAxis: { type: 'datetime', title: { text: 'Time' } },
        yAxis: { title: { text: `${seriesName} (${unit})` } },
        series: [{ name: seriesName, data: seriesData }]
    };

    let chart = Highcharts.charts.find(c => c && c.renderTo.id === containerId);
    if (chart) {
        chart.update(chartConfig, true);
    } else {
        Highcharts.chart(chartConfig);
    }
}

// Function to update charts for a location
    async function updateCharts(location, hours, interval) {
        console.log('updateCharts called with:', { location, hours, interval });
        const result = await fetchData(location, hours, interval);
        const data = result.data || [];
        const titles = result.titles || [];

        if (!data || data.length === 0 || !titles || titles.length === 0) {
            console.warn(`No data or titles returned for ${location}`);
            return;
        }

titles.forEach(title => {
    const columnKey = title.toLowerCase().replace(/[^a-z0-9]/g, '_');

    const seriesData = data
        .map(entry => {
            const raw = entry[columnKey];
            if (raw === undefined || raw === null) return null;
            const val = parseFloat(raw);
            return isNaN(val) ? null : [
                moment.tz(entry.time, 'America/Denver').valueOf(),
                val
            ];
        })
        .filter(point => point !== null);

    // Build full title HERE (location is in scope)
    const fullTitle = `${location.charAt(0).toUpperCase() + location.slice(1)} ${title}`;
    const containerId = ensureChartContainer(location, title);

    // Pass fullTitle — NO location inside createOrUpdateChart
    createOrUpdateChart(containerId, fullTitle, seriesData, title);
});
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
    const locations = ['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'backporch', 'front'];
    locations.forEach(location => {
        updateCharts(location, 6, 1);
    });
    initializeButtons();
});
    </script>
@endsection
@section('control-content')
<!-- Chart Wrappers -->
@foreach(['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'backporch', 'front'] as $location)
    <div class="chart-wrapper">
        <div class="chart-title">{{ ucfirst($location) }} Environment</div>
        <!-- Chart containers will be populated dynamically by JavaScript -->
        <div class="chart-containers" id="{{ $location }}-charts"></div>
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
    .chart-containers {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
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
