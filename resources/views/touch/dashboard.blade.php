@extends(auth()->user()->role === 'fronttouchpanel' ? 'layouts.app_ftouch' : 'layouts.app_gtouch')

@section('content')
    <h1>Touch Panel Dashboard</h1>
    <div class="dashboard-buttons" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; padding: 20px;">
        <a href="{{ route('touch.switches') }}" class="btn btn-primary">Switches</a>
        <a href="{{ route('touch.calendar') }}" class="btn btn-primary">Calendar</a>
        <a href="{{ route('touch.cameras') }}" class="btn btn-primary">Cameras</a>
        <a href="{{ route('touch.clock') }}" class="btn btn-primary">Clock</a>
        <a href="{{ route('touch.corn-futures') }}" class="btn btn-primary">Corn Futures</a>
        <a href="{{ route('touch.temperatures') }}" class="btn btn-primary">Temperatures</a>
        <a href="{{ route('touch.slideshow') }}" class="btn btn-primary">Slideshow</a>
    </div>
@endsection
