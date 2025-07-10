<!-- resources/views/sprinkler/control.blade.php -->

@extends('dashboard')

@section('control-content')
    <h1>Wyze Sprinkler Control</h1>
    <form method="POST" action="{{ route('sprinkler.controlZone') }}">
        @csrf
        <div>
            <label for="zone_id">Zone ID:</label>
            <select id="zone_id" name="zone_id" required>
                <option value="1">Zone 1</option>
                <option value="2">Zone 2</option>
                <option value="3">Zone 3</option>
                <option value="4">Zone 4</option>
                <option value="5">Zone 5</option>
                <option value="6">Zone 6</option>
                <option value="7">Zone 7</option>
                <option value="8">Zone 8</option>
            </select>
        </div>
        <div>
            <label for="action">Action:</label>
            <select id="action" name="action" required>
                <option value="on">Turn On</option>
                <option value="off">Turn Off</option>
            </select>
        </div>
        <button type="submit">Submit</button>
    </form>
    @isset($result)
        <div>
            <h3>Result:</h3>
            <pre>{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endisset
@endsection

