@extends('dashboard')

@section('control-content')
<style>
.table {
    margin-top: 20px;
}

.table th, .table td {
    padding: 12px 15px;
    vertical-align: middle;
}

.alert-success {
    margin-bottom: 20px;
}

.btn-sm {
    padding: 5px 10px;
}
</style>
<h1>Automation Rules</h1>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <a href="{{ route('automation_rules.create') }}" class="btn btn-primary mb-3">Add New Rule</a>
    <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
            <tr>
                <th scope="col">Monitor</th>
                <th scope="col">Condition</th>
                <th scope="col">Action Device</th>
                <th scope="col">Action</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rules as $rule)
                <tr>
                    <td>{{ $rule->location ?? 'N/A' }}</td>
                    <td>{{ $rule->condition_type }} {{ $rule->condition_compare }}</td>
                    <td>{{ $rule->actionDevice->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $rule->action == 'turn_on' ? 'bg-success' : 'bg-danger' }}">
                            {{ $rule->action == 'turn_on' ? 'Turn On' : 'Turn Off' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('automation_rules.edit', $rule) }}" class="btn btn-sm btn-outline-warning me-2">Edit</a>
                        <form action="{{ route('automation_rules.destroy', $rule) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No automation rules found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
