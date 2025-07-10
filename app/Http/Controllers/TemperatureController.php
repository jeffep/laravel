<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemperatureController extends Controller
{
    public function showChart()
    {
        return view('temperature-chart');
    }
}

use ConsoleTVs\Charts\Facades\Charts;

public function showChart()
{
    $json = file_get_contents(storage_path('app/data/temperature.json'));
    $data = json_decode($json, true);

    $temperature = array_column($data, 'temperature');
    $humidity = array_column($data, 'humidity');
    $labels = array_column($data, 'time');

    $chart = Charts::multi('line', 'chartjs')
        ->title('Temperature and Humidity Levels')
        ->labels($labels)
        ->dataset('Temperature', $temperature)
        ->dataset('Humidity', $humidity);

    return view('temperature-chart', ['chart' => $chart]);
}
