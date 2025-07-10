
@extends('dashboard')

@section('control-content')
    <div class="container">


<div class="container">
        <h1>Daily Electricity Usage</h1>
        {!! $chart->container() !!}
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {!! $chart->script() !!}

@endsection

@push('head-styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/larapex-charts/1.2.0/css/larapex-charts.min.css">
@endpush

@push('head-scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush
