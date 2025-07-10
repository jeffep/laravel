@extends('dashboard')

@section('control-content')
    <div class="container">

<div class="container">
    @foreach($webcams as $webcam)
        <div class="webcam-frame">
            <h3>{{ $webcam['name'] }}</h3>
            <iframe src="{{ $webcam['url'] }}" width="100%" height="500px" frameborder="0"></iframe>
        </div>
    @endforeach
</div>
@endsection

