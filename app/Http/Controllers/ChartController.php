<?php

namespace App\Http\Controllers;

use App\Charts\TemperatureHumidityChart;

class ChartController extends Controller
{
    public function showChart(TemperatureHumidityChart $chart)
    {
        return view('chart', ['chart' => $chart]);
    }
    public function index()
    {
        $chart = new TemperatureHumidityChart;
        return view('landing', compact('chart'));
    }
}
