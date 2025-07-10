<!-- resources/views/control.blade.php -->
@extends('dashboard')

@section('control-content')
    <div class="container">
        <h1>System Information</h1>
        <div class="info-box">
            <h2>Current Time</h2>
            <p>{{ $currentTime }}</p>
        </div>
        <div class="info-box">
            <h2>Current Temperature</h2>
            <p>{{ $temperature }} °F</p>
        </div>
        <div class="info-box">
            <h2>Disk Space</h2>
            <p>{{ $diskSpace }}</p>
        </div>
        <div class="info-box">
            <h2>System Uptime</h2>
            <p>{{ $uptime }}</p>
        </div>
        <div class="info-box">
            <h2>Top Processes</h2>
            <ul>
                @foreach($topProcesses as $process)
                    <li>{{ $process }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection

<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: Arial, sans-serif;
    }
    .info-box {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .info-box h2 {
        margin-top: 0;
    }
    .info-box p, .info-box ul {
        margin: 0;
    }
    .info-box ul {
        padding-left: 20px;
    }
</style>

