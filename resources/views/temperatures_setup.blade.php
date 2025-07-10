@extends('dashboard')

@section('control-content')
    <div class="container">
        <h1>Temperature Controller Setup</h1>

        <!-- Other content here -->

        <!-- LOOP THROUGH EACH SENSOR -->
        <div class="grid-container">
            <table>
                <thead>
                    <tr>
                        <th>Sensor Name<br><small>(TEMPERATURE_SENSORXX_NAME)</small></th>
                        <th>Sensor Address<br><small>(TEMPERATURE_SENSORXX_ADDRESS)</small></th>
                        <th>Sensor Topic<br><small>(TEMPERATURE_SENSORXX_TOPIC)</small></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sensorData as $sensor)
                        <tr>
                            <td>{{ $sensor['id'] }}</td>
                            <td>{{ $sensor['address'] }}</td>
                            <td>{{ $sensor['topic'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

<form action="{{ route('temperatures_save') }}" method="post">
    @csrf
    <input type="text" name="key" placeholder="Setting Key">
    <input type="text" name="value" placeholder="Setting Value">
    <button type="submit">Add Setting</button>
</form>
</div>
@endsection
@push('head-styles')
<style>

    .grid-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* Equal-width columns */
    gap: 1px; /* Small gap between boxes for gridlines */
    border: 2px solid #ccc; /* Add a border around the entire grid */
    }

    .custom-box {
    background-color: #FFFFFF;
    padding: 5px;
    margin-bottom: 10px;
    border-radius: 5px;
    font-size: 0.9em;
    width: 20ch;
    border: 2px solid #ccc; /* Add a border to each individual box */
    }

    .section-title {
            grid-column: 1 / span 4; /* Span all columns */
            background-color: papayawhip;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border: 4px solid #000000;
    }

    .box-header {
        font-weight: bold;
        margin-bottom: 10px;
    }
    .box-body p {
        margin: 0;
    }
    form {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

   table {
    border-collapse: collapse; /* Merge adjacent borders into a single line */
    width: 100%; /* Adjust as needed */
   }

   th, td {
    border: 1px solid #ddd; /* Add a 1px gray border to each cell */
    padding: 8px; /* Adjust padding as needed */
    text-align: left; /* Align cell content */
   }

</style>
@endpush
