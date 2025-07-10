@extends(auth()->user()->role === 'fronttouchpanel' ? 'layouts.app_ftouch' : 'layouts.app_gtouch')

@section('content')
    <h1>Touch Panel Calendar</h1>
    <div class="container">
        <div id="calendar"></div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch(`/events?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`)
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(error => failureCallback(error));
                },
                selectable: true,
                editable: true,
                select: function(info) {
                    var title = prompt("Enter event title:");
                    if (title) {
                        fetch('/events', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                title: title,
                                start: info.startStr,
                                end: info.endStr
                            })
                        }).then(response => response.json())
                          .then(() => calendar.refetchEvents());
                    }
                }
            });

            calendar.render();
        });
    </script>
@endsection
