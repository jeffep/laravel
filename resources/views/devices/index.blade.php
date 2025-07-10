@extends('dashboard')

@section('control-content')

<h1>Devices</h1>
    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    <a href="{{ route('devices.create') }}">Add New Device</a>
    <table border="1">
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
