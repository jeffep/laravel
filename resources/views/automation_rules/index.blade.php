    @extends('dashboard')

@section('control-content')
<h1>Automation Rules</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <a href="{{ route('automation_rules.create') }}" class="btn btn-primary mb-3">Add New Rule</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Monitor</th>
                <th>Condition</th>
                <th>Action Device</th>
                <th>Action</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rules as $rule)
                <tr>
                    <td>{{ $rule->location ?? 'N/A' }}</td>
                    <td>{{ $rule->condition_type }} {{ $rule->condition_compare }}</td>
                    <td>{{ $rule->actionDevice->name }}</td>
                    <td>{{ $rule->action == 'turn_on' ? 'Turn On' : 'Turn Off' }}</td>
                    <td>
                        <a href="{{ route('automation_rules.edit', $rule) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('automation_rules.destroy', $rule) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
