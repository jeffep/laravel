@extends('dashboard')

@section('control-content')
    <h1>Automation Rules</h1>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <a href="{{ route('automation_rules.create') }}" class="btn btn-primary mb-3">Add New Rule</a>
    <table class="table">
        <thead>
            <tr>
                <th>Location</th>
                <th>Condition Type</th>
                <th>Condition</th>
                <th>Action Device</th>
                <th>Action</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rules as $rule)
                <tr>
                    <td>{{ $rule->location }}</td>
                    <td>{{ $rule->condition_type }}</td>
                    <td>{{ $rule->condition_compare }}</td>
                    <td>{{ $rule->actionDevice->name }}</td>
                    <td>{{ $rule->action }}</td>
                    <td>{{ $rule->active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('automation_rules.edit', $rule) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('automation_rules.destroy', $rule) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
