@if(request()->is('touch-dashboard*'))
    @extends('layouts.app_touch')
@else
    @extends('dashboard')
@endif

@section('control-content')
    <iframe src="http://192.168.87.102" style="width: 100%; height: 100vh; border: none;"></iframe>
@endsection

