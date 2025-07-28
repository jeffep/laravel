@extends('dashboard')

@section('control-content')
    <div class="container">
        <h1>Shelly Status</h1>

        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h1>Automation Rules</h1>

        <!-- Inline CSS -->
        <style>
            .automation-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 1rem;
                font-size: 0.9rem;
            }

            .automation-table th,
            .automation-table td {
                border: 1px solid #dee2e6; /* Light gray border */
                padding: 0.75rem; /* Comfortable padding */
                text-align: left;
                vertical-align: middle; /* Center content vertically */
            }

            .automation-table th {
                background-color: #f8f9fa; /* Light gray header background */
                font-weight: bold;
            }

            .automation-table tbody tr:hover {
                background-color: #f1f1f1; /* Hover effect */
            }

            .automation-table .text-center {
                text-align: center;
            }

            /* Checkbox styling */
            .automation-table input[type="checkbox"] {
                width: 1.2rem;
                height: 1.2rem;
                cursor: pointer;
            }

            .automation-table input[type="checkbox"]:hover {
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            }

            .btn-primary, .btn-danger {
                margin-right: 10px;
            }
        </style>

        <!-- Add New Rule Button -->
        <a href="{{ route('automation_rules.create') }}" class="btn btn-primary mb-3">Add New Rule</a>

        <!-- Display existing rules -->
        <div class="table-responsive">
            <table class="table automation-table">
                <thead>
                    <tr>
                        <th>Active</th>
                        <th>Location</th>
                        <th>Condition Type</th>
                        <th>Condition</th>
                        <th>Action Device</th>
                        <th>Action</th>
                        <th>Device(3)</th>
                        <th>Setting(2)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rules as $rule)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="active-toggle"
                                       data-id="{{ $rule->id }}"
                                       {{ $rule->active ? 'checked' : '' }}
                                       title="Toggle active status">
                            </td>
                            <td>{{ $rule->location ?? 'N/A' }}</td>
                            <td>{{ $rule->condition_type ?? 'N/A' }}</td>
                            <td>{{ $rule->condition_compare ?? 'N/A' }}</td>
                            <td>{{ $rule->actionDevice->name ?? 'N/A' }}</td>
                            <td>{{ $rule->action ?? 'N/A' }}</td>
                            <td>{{ 'N/A' }}</td> <!-- Placeholder for Device(3) -->
                            <td>{{ 'N/A' }}</td> <!-- Placeholder for Setting(2) -->
                            <td>
                                <a href="{{ route('automation_rules.edit', $rule) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('automation_rules.destroy', $rule) }}" method="POST" style="display:inline;" onclick="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No automation rules found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Form for creating new settings -->
        <form action="{{ route('automation_rules.store') }}" method="post">
            @csrf
            <!-- Add dropdowns and input fields for new rules -->
            <!-- Example: -->
            <!--
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control" required>
            </div>
            -->
        </form>

        <!-- JavaScript for active toggle -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.active-toggle').on('change', function() {
                    var ruleId = $(this).data('id');
                    var isActive = $(this).is(':checked') ? 1 : 0;

                    $.ajax({
                        url: '{{ route("automation.toggle-active") }}',
                        method: 'POST',
                        data: {
                            id: ruleId,
                            active: isActive,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert('Rule updated successfully!');
                        },
                        error: function(xhr) {
                            alert('Error updating rule: ' + (xhr.responseJSON?.message || 'Unknown error'));
                            $(this).prop('checked', !isActive); // Revert on error
                        }
                    });
                });
            });
        </script>
    </div>
@endsection
