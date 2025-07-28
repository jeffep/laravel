@extends('dashboard')

@section('control-content')
        <!-- Inline CSS -->
        <style>
            .custom-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 1rem;
                font-size: 0.9rem;
            }

            .custom-table th,
            .custom-table td {
                border: 1px solid #dee2e6; /* Light gray border */
                padding: 0.75rem; /* Comfortable padding */
                text-align: left;
                vertical-align: middle; /* Center content vertically */
            }

            .custom-table th {
                background-color: #f8f9fa; /* Light gray header background */
                font-weight: bold;
            }

            .custom-table tbody tr:hover {
                background-color: #f1f1f1; /* Hover effect */
            }

            .custom-table .text-center {
                text-align: center;
            }

            /* Checkbox styling */
            .custom-table input[type="checkbox"] {
                width: 1.2rem;
                height: 1.2rem;
                cursor: pointer;
            }

            .custom-table input[type="checkbox"]:hover {
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            }
        </style>
    <div class="container">
        <h1>Shelly Status</h1>

        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        <h1>Automation Settings</h1>


        <!-- Display existing settings -->
        <div class="table-responsive">
            <table class="custom-table">
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
                    </tr>
                </thead>
                <tbody>
                    @forelse ($automationSettings as $setting)
                        <tr>
                            <td>
                                <input type="checkbox" 
                                       class="active-toggle" 
                                       data-id="{{ $setting->id }}" 
                                       {{ $setting->active ? 'checked' : '' }} 
                                       title="Toggle active status">
                            </td>
                            <td>{{ $setting->location ?? 'N/A' }}</td>
                            <td>{{ $setting->condition_type ?? 'N/A' }}</td>
                            <td>{{ $setting->condition_compare ?? 'N/A' }}</td>
                            <td>{{ $setting->action_device_id ?? 'N/A' }}</td>
                            <td>{{ $setting->action ?? 'N/A' }}</td>
                            <td>{{ 'N/A' }}</td> <!-- Placeholder for Device(3) -->
                            <td>{{ 'N/A' }}</td> <!-- Placeholder for Setting(2) -->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No automation settings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Form for creating new settings -->
        <form action="{{ route('automation.store') }}" method="post">
            @csrf
            <!-- Dropdowns and input fields for new settings -->
            <!-- Adjust this part based on your requirements -->
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
