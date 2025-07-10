@extends('dashboard')

@section('control-content')
<div class="container">
    <h2 class="text-center my-4">Event Calendar</h2>
    
    <div id="calendar"></div>
</div>

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
            },
            eventDrop: function(info) { // Handles drag-and-drop event updates
                fetch(`/events/${info.event.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        start: info.event.start.toISOString(),
                        end: info.event.end ? info.event.end.toISOString() : null
                    })
                }).then(response => response.json())
                  .then(data => console.log(data.message))
                  .catch(error => console.error('Error:', error));
            }
        });

        calendar.render();
    });
</script>
@endsection
