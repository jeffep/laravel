@extends(auth()->user()->role === 'fronttouchpanel' ? 'layouts.app_ftouch' : 'layouts.app_gtouch')

@section('content')
      <div id="error-log" style="position: fixed; top: 10px; left: 10px; color: red; background: white; padding: 10px; max-height: 200px; overflow-y: auto; z-index: 1000;"></div>
    <div class="full-screen bg-dark text-black">
      <!--  <a href="{{ route('touch.dashboard') }}" class="back-arrow">⬅️</a>  -->
        <h1 id="current-time" style="font-size: 4rem;"></h1>
      <!--  <a href="{{ route('touch.corn-futures') }}" class="next-arrow">➡️</a>  -->
    </div>
@endsection

@section('styles')
    <style>
        body {
            background-color: #000000; /* Black background */
        }
    </style>
@endsection

@section('scripts')
    <script>
        function updateTime() {
            try {
                const element = document.getElementById('current-time');
                if (element) {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString();
                    element.textContent = timeString;
                    console.log('Formatted time:', timeString);
                } else {
                    console.error('Element #current-time not found');
                }
            } catch (error) {
                console.error('Error in updateTime:', error);
            }
        }
        try {
            updateTime();
            const intervalId = setInterval(updateTime, 1000);
            window.addEventListener('unload', () => clearInterval(intervalId));
        } catch (error) {
            console.error('Error setting up clock:', error);
        }
    </script>
@endsection
