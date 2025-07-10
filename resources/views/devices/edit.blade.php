@extends('dashboard')

@section('control-content')

<h1>Edit Device: {{ $device->name }}</h1>
    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form action="{{ route('devices.update', $device) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label>Name:</label>
            <input type="text" name="name" value="{{ old('name', $device->name) }}" required>
        </div>
        <div>
            <label>IP Address:</label>
            <input type="text" name="address" value="{{ old('address', $device->address) }}" required>
        </div>
        <div>
            <label>Status Endpoint:</label>
            <input type="text" name="status_endpoint" value="{{ old('status_endpoint', $device->status_endpoint) }}" required>
        </div>
        <div>
            <label>Report URL:</label>
            <input type="text" name="report_url" value="{{ old('report_url', $device->report_url) }}" required>
        </div>
        <div>
            <label>Action On URL:</label>
            <input type="text" name="action_on" value="{{ old('action_on', $device->action_on) }}" required>
        </div>
        <div>
            <label>Action Off URL:</label>
            <input type="text" name="action_off" value="{{ old('action_off', $device->action_off) }}" required>
        </div>
        <button type="submit">Update Device</button>
    </form>
    <a href="{{ route('devices.index') }}">Back to Devices</a>
@endsection
