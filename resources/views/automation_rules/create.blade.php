@extends('dashboard')

@section('control-content')

    <h1>Add Automation Rule</h1>
    @if ($errors->any())
        <ul class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form action="{{ route('automation_rules.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Monitor Location:</label>
            <select name="location" class="form-control" id="location" required>
                @foreach ($locations as $location)
                    <option value="{{ $location }}">{{ $location }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Condition Type:</label>
            <select name="condition_type" class="form-control" id="condition_type" required>
                <option value="temperature">Temperature</option>
                <option value="device_status">Device Status</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Condition (e.g., ">84" or "Off"):</label>
            <input type="text" name="condition_compare" class="form-control" value="{{ old('condition_compare') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Action Device:</label>
            <select name="action_device_id" class="form-control" required>
                @foreach ($devices as $device)
                    <option value="{{ $device->id }}">{{ $device->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Action:</label>
            <select name="action" class="form-control" required>
                <option value="turn_on">Turn On</option>
                <option value="turn_off">Turn Off</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Rule</button>
    </form>
    <a href="{{ route('automation_rules.index') }}" class="btn btn-secondary mt-2">Back to Rules</a>
@endsection
