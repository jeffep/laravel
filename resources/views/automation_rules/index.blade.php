@extends('dashboard')

@section('control-content')
    <div class="rules-container">
        <div class="title-container">
            <h1>Automation Rules for Shelly Controllers</h1>
        </div>
        <div class="button-container">
            <a href="{{ route('automation_rules.create') }}" class="add-rule-btn">Add A New Rule</a>
        </div>
        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif
        <table class="rules-table">
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
                            <a href="{{ route('automation_rules.edit', $rule) }}" class="edit-btn">Edit</a>
                            <form action="{{ route('automation_rules.destroy', $rule) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <style>
        .rules-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .title-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-container h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        .button-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .add-rule-btn {
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
        .add-rule-btn:hover {
            background-color: #0052cc;
            transform: scale(1.05);
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }
        .rules-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .rules-table th, .rules-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .rules-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .edit-btn, .delete-btn {
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .edit-btn {
            background-color: #007bff;
            color: #fff;
            border: 1px solid #0052cc;
            margin-right: 5px;
        }
        .edit-btn:hover {
            background-color: #0052cc;
        }
        .delete-btn {
            background-color: #dc3545;
            color: #fff;
            border: 1px solid #b02a37;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #b02a37;
        }
    </style>
@endsection
