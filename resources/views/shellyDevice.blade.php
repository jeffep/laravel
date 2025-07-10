<!-- resources/views/shellyDevice.blade.php -->
@extends('dashboard')

@section('control-content')
    <iframe src="http://{{ $ip_address }}" style="width: 100%; height: 100vh; border: none;"></iframe>
@endsection

