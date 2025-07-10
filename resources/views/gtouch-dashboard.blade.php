@extends('layouts.app_gtouch')

@section('content')
    <div id="tab-content">
        @include('touch.gswitches') {{-- Default tab content on load --}}
    </div>

    <script>
        function showTab(tab) {
            fetch(`/gtouch-dashboard/${tab}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('tab-content').innerHTML = html;
                });

            // Highlight the active tab
            document.querySelectorAll('.top-nav button').forEach(btn => btn.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
        }
    </script>
@endsection

