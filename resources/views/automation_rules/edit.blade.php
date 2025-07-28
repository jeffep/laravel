@extends('dashboard')

@section('control-content')
    <h1>Edit Automation Rule</h1>
    @if ($errors->any())
        <ul class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <form action="{{ route('automation_rules.update', $automationRule) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Monitor Location:</label>
            <select name="location" class="form-control" id="location" required>
                @foreach ($locations as $location)
                    <option value="{{ $location }}" {{ $automationRule->location == $location ? 'selected' : '' }}>{{ $location }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Condition Type:</label>
            <select name="condition_type" class="form-control" id="condition_type" required>
                <option value="temperature" {{ $automationRule->condition_type == 'temperature' ? 'selected' : '' }}>Temperature</option>
                <option value="device_status" {{ $automationRule->condition_type == 'device_status' ? 'selected' : '' }}>Device Status</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Condition (e.g., ">84" or "Off"):</label>
            <input type="text" name="condition_compare" class="form-control" value="{{ old('condition_compare', $automationRule->condition_compare) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Action Device:</label>
            <select name="action_device_id" class="form-control" required>
                @foreach ($devices as $device)
                    <option value="{{ $device->id }}" {{ $automationRule->action_device_id == $device->id ? 'selected' : '' }}>{{ $device->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Action:</label>
            <select name="action" class="form-control" required>
                <option value="turn_on" {{ $automationRule->action == 'turn_on' ? 'selected' : '' }}>Turn On</option>
                <option value="turn_off" {{ $automationRule->action == 'turn_off' ? 'selected' : '' }}>Turn Off</option>
            </select>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="active" class="form-check-input" id="active" {{ $automationRule->active ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Active</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Rule</button>
    </form>
    <a href="{{ route('automation_rules.index') }}" class="btn btn-secondary mt-2">Back to Rules</a>
@endsection
