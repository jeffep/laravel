@extends('dashboard')

@section('control-content')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border: 1px solid #ddd;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    a, button {
        margin-right: 10px;
        text-decoration: none;
        color: #007bff;
    }

    button {
        background: none;
        border: none;
        cursor: pointer;
        color: #dc3545;
    }

    button:hover {
        text-decoration: underline;
    }

    .success-message {
        color: green;
        margin-bottom: 15px;
    }

    .add-device-link {
        display: inline-block;
        margin-bottom: 15px;
        padding: 8px 12px;
        background-color: #007bff;
        color: white;
        border-radius: 4px;
        text-decoration: none;
    }

    .add-device-link:hover {
        background-color: #0056b3;
    }
</style>

<h1>Devices</h1>
@if (session('success'))
    <p class="success-message">{{ session('success') }}</p>
@endif
<a href="{{ route('devices.create') }}" class="add-device-link">Add New Device</a>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>IP Address</th>
            <th>Status Endpoint</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($devices as $device)
            <tr>
                <td>{{ $device->name }}</td>
                <td>{{ $device->address }}</td>
                <td>{{ $device->status_endpoint }}</td>
                <td>
                    <a href="{{ route('devices.edit', $device) }}">Edit</a>
                    <form action="{{ route('devices.destroy', $device) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
