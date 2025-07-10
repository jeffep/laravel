@extends('dashboard')

@section('control-content')
    <div class="container">
        <h1>Shelly Status</h1>

        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

<h1>Automation Settings</h1>

<!-- Display existing settings -->
<table>
    <thead>
        <tr>
            <th>Device</th>
            <th>Test</th>
            <th>Value</th>
            <th>Device(2)</th>
            <th>Setting</th>
            <th>Device(3)</th>
            <th>Setting(2)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($automationSettings as $key => $value)
            <tr>
                <!-- Display settings here -->
                <!-- Adjust this part based on your actual data structure -->
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Form for creating new settings -->
<form action="{{ route('automation.store') }}" method="post">
    @csrf
    <!-- Dropdowns and input fields for new settings -->
    <!-- Adjust this part based on your requirements -->
</form>
