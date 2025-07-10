<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;

class WattageChartController extends Controller
{
    public function index()
    {
        $filePath = storage_path('app/public/wattage.log');
        $data = $this->processLogFile($filePath);

        $chartData = [];
        foreach ($data as $date => $events) {
            $times = array_column($events, 'time');
            $wattages = array_column($events, 'wattage');

            $chartData[] = [
                'name' => $date,
                'data' => array_map(function ($time, $wattage) {
                    return ['x' => $time, 'y' => $wattage];
                }, $times, $wattages)
            ];
        }

        $chart = (new LarapexChart)->lineChart()
            ->setTitle('Daily Electricity Usage')
            ->setXAxis(array_column($chartData[0]['data'], 'x')) // Assuming all days have the same time intervals
            ->setDataset($chartData);

/*
        $dates = array_keys($data['wattage']);
        $wattages = array_values($data['wattage']);
        $costs = array_values($data['cost']);

        $chart = (new LarapexChart)->barChart()
            ->setTitle('Daily Electricity Usage')
            ->setXAxis($dates) // Set x-axis to dates
            ->addData('Wattage (W)', $wattages)
            ->addData('Cost ($)', $costs)
            ->setLabels($dates); // Set labels to dates
*/
/*
        $chart = (new LarapexChart)->barChart()
           ->setTitle('Daily Electricity Usage')
           ->setXAxis(array_keys($data))
           ->addData('Wattage (W)', array_column($data, 'wattage'))
           ->addData('Cost ($)', array_column($data, 'cost'))
           ->setMarkers(['Wattage (W)', 'Cost ($)'])
           ->setLabels(array_keys($data));
*/
/*
        $chart = (new LarapexChart)->lineChart()
           ->setTitle('Daily Electricity Usage')
           ->setXAxis(array_keys($data[]))
           ->addData('Wattage (W)', array_values($data['wattage']))
           ->addData('Cost ($)', array_values($data['cost']));
*/
/*        $chart = (new LarapexChart)->lineChart()
            ->setTitle('Daily Electricity Usage')
            ->setXAxis(array_keys($data))
            ->setDataset('Wattage (W)', array_values($data['wattage']))
            ->setDataset('Cost ($)', array_values($data['cost']));
*/
        return view('wattage_chart', compact('chart'));
    }

    private function processLogFile($filePath)
    {
       $data = [];
       $costPerWattHour = 0.10203 / 1000; // Cost per watt-hour
       $deviceStatus = []; // To track the status of each device

       if (file_exists($filePath)) {
           $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

           foreach ($lines as $line) {

               $logEntry = json_decode($line, true);
               $timestamp = $logEntry['timestamp'];
               $device = $logEntry['device'];
               $action = $logEntry['action'];
               $wattage = $logEntry['wattage'];
               $date = Carbon::createFromTimestamp($timestamp)->format('Y-m-d');
               $time = Carbon::createFromTimestamp($timestamp)->format('H:i:s');

               if (!isset($data[$date])) {
                  $data[$date] = [];
               }

               if ($action === 'on') {
                   $deviceStatus[$device] = ['timestamp' => $timestamp, 'wattage' => $wattage];
               } elseif ($action === 'off' && isset($deviceStatus[$device])) {
                   $onTimestamp = $deviceStatus[$device]['timestamp'];
                   $onWattage = $deviceStatus[$device]['wattage'];
                   $duration = ($timestamp - $onTimestamp) / 3600; // Duration in hours

                   // Store the on event
                   $data[$date][] = ['time' => Carbon::createFromTimestamp($onTimestamp)->format('H:i:s'), 'wattage' => $onWattage];

                   // Store the off event
                   $data[$date][] = ['time' => $time, 'wattage' => 0];

                   // Remove the device from the status array
                   unset($deviceStatus[$device]);
               }
           }

           // Ensure the data is sorted by time for each day
           foreach ($data as $date => $events) {
               usort($events, function ($a, $b) {
                   return strcmp($a['time'], $b['time']);
               });
           }
/*
               $logEntry = json_decode($line, true);
               $timestamp = $logEntry['timestamp'];
               $device = $logEntry['device'];
               $action = $logEntry['action'];
               $wattage = $logEntry['wattage'];
               $date = Carbon::createFromTimestamp($timestamp)->format('Y-m-d');

               if (!isset($data[$date])) {
                   $data[$date] = ['wattage' => 0, 'cost' => 0];
               }

               if ($action === 'on') {
                   $deviceStatus[$device] = ['timestamp' => $timestamp, 'wattage' => $wattage];
               } elseif ($action === 'off' && isset($deviceStatus[$device])) {
                   $onTimestamp = $deviceStatus[$device]['timestamp'];
                   $onWattage = $deviceStatus[$device]['wattage'];
                   $duration = ($timestamp - $onTimestamp) / 3600; // Duration in hours

                   $data[$date]['wattage'] += $onWattage * $duration;
                   $data[$date]['cost'] += $onWattage * $duration * $costPerWattHour;
   
                   // Remove the device from the status array
                   unset($deviceStatus[$device]);
               }
*/
       }

       // Separate the data into wattage and cost arrays
/*
       $result = ['wattage' => [], 'cost' => []];
       foreach ($data as $date => $values) {
           $result['wattage'][$date] = $values['wattage'];
           $result['cost'][$date] = $values['cost'];
       }

       return $result;
*/
       return $data;
   }



    private function processLogFile_old($filePath)
    {
        $data = [];
        $costPerWatt = 0.10203;

        if (file_exists($filePath)) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                list($timestamp, $device, $action, $wattage) = explode(' ', $line);
                $date = Carbon::parse($timestamp)->format('Y-m-d');

                if (!isset($data[$date])) {
                    $data[$date] = ['wattage' => 0, 'cost' => 0];
                }

                if ($action !== 'off') {
                    $data[$date]['wattage'] += (float) $wattage;
                    $data[$date]['cost'] += (float) $wattage * $costPerWatt;
                }
            }
        }

        return $data;
    }
}

