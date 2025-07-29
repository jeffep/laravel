@extends('dashboard')

@section('control-content')
    <div class="create-rule-container">
        <h1>Add Automation Rule</h1>
        @if ($errors->any())
            <ul class="error-messages">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <form action="{{ route('automation_rules.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="location">Monitor Location:</label>
                <select name="location" id="location" required>
                    @foreach ($locations as $location)
                        <option value="{{ $location }}">{{ $location }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="condition_type">Condition Type:</label>
                <select name="condition_type" id="condition_type" required>
                    <option value="temperature">Temperature</option>
                    <option value="device_status">Device Status</option>
                </select>
            </div>
            <div class="form-group">
                <label for="condition_compare">Condition (e.g., ">84" or "Off"):</label>
                <input type="text" name="condition_compare" value="{{ old('condition_compare') }}" required>
            </div>
            <div class="form-group">
                <label for="action_device_id">Action Device:</label>
                <select name="action_device_id" required>
                    @foreach ($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="action">Action:</label>
                <select name="action" required>
                    <option value="turn_on">Turn On</option>
                    <option value="turn_off">Turn Off</option>
                </select>
            </div>
            <div class="form-group">
                <div class="checkbox-group">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" id="active" value="1" {{ old('active', 1) ? 'checked' : '' }}>
                    <label for="active">Active</label>
                </div>
            </div>
            <button type="submit" class="save-rule-btn">Save Rule</button>
        </form>
        <a href="{{ route('automation_rules.index') }}" class="back-btn">Back to Rules</a>
    </div>

    <style>
        .create-rule-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .error-messages {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            list-style: none;
        }
        .error-messages li {
            margin-bottom: 5px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .form-group select,
        .form-group input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
        }
        .checkbox-group label {
            font-weight: normal;
        }
        .save-rule-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border: 2px solid #0052cc;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        .save-rule-btn:hover {
            background-color: #0052cc;
            transform: scale(1.05);
        }
        .back-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #6c757d;
            color: #fff;
            text-decoration: none;
            border: 1px solid #5a6268;
            border-radius: 4px;
            font-weight: bold;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
@endsection
