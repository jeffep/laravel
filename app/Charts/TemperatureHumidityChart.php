<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class TemperatureHumidityChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $jsonPath = storage_path('app/public/temperature.json');
        $jsonData = json_decode(file_get_contents($jsonPath), true);

        $dates = array_column($jsonData, 'date');
        $temperatures = array_column($jsonData, 'temperature');
        $humidities = array_column($jsonData, 'humidity');

        $this->labels($dates);
        $this->dataset('Temperature', 'line', $temperatures)->color('rgba(255, 99, 132, 1)');
        $this->dataset('Humidity', 'line', $humidities)->color('rgba(54, 162, 235, 1)');
    }
}
    }
}
