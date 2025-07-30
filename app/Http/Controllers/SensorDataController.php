<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SensorDataController extends Controller
{
    public function showSensorData()
    {
        return view('sensor-data');
    }

    public function getSensorData(Request $request)
    {
        // Get query parameters
        $location = $request->query('location');
        $hours = $request->query('hours', 6);
        $interval = $request->query('interval', 1);

        // Validate the location parameter
        if (!$location) {
            Log::error("Location parameter is required");
            return response()->json(['error' => 'Location parameter is required'], 400);
        }

        // Calculate the start time
        $startTime = Carbon::now()->subHours($hours)->toDateTimeString();

        try {
            // Get all unique titles for the location
            $titles = DB::table('sensor_data')
                ->join('sensors', 'sensor_data.sensor_id', '=', 'sensors.id')
                ->where('sensors.location', $location)
                ->where('sensor_data.time', '>=', $startTime)
                ->distinct()
                ->pluck('sensor_data.title')
                ->toArray();

            if (empty($titles)) {
                Log::warning("No titles found for location: {$location}, startTime: {$startTime}");
                return response()->json(['data' => [], 'titles' => []]);
            }

            // Fetch raw data
            $rawData = DB::table('sensor_data')
                ->join('sensors', 'sensor_data.sensor_id', '=', 'sensors.id')
                ->select('sensors.location', 'sensor_data.time', 'sensor_data.title', 'sensor_data.value')
                ->where('sensors.location', $location)
                ->where('sensor_data.time', '>=', $startTime)
                ->orderBy('sensor_data.time', 'asc')
                ->get();

            // Log the raw query for debugging
            $rawQuery = DB::table('sensor_data')
                ->join('sensors', 'sensor_data.sensor_id', '=', 'sensors.id')
                ->select('sensors.location', 'sensor_data.time', 'sensor_data.title', 'sensor_data.value')
                ->where('sensors.location', $location)
                ->where('sensor_data.time', '>=', $startTime)
                ->orderBy('sensor_data.time', 'asc')
                ->toSql();
            Log::debug("Sensor data query for {$location}: {$rawQuery}", ['bindings' => [$location, $startTime]]);

            // Transform data into pivoted format
            $data = [];
            $currentTime = null;
            $currentEntry = null;

            foreach ($rawData as $row) {
                if ($row->time !== $currentTime) {
                    if ($currentEntry) {
                        $data[] = $currentEntry;
                    }
                    $currentEntry = [
                        'location' => $row->location,
                        'time' => $row->time
                    ];
                    $currentTime = $row->time;
                }
                $columnKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $row->title));
                $currentEntry[$columnKey] = (float) $row->value;
            }
            if ($currentEntry) {
                $data[] = $currentEntry;
            }

            // Apply interval filtering
            $filteredData = array_filter($data, function ($entry, $index) use ($interval) {
                return $index % $interval === 0;
            }, ARRAY_FILTER_USE_BOTH);

            // Convert to indexed array
            $filteredData = array_values($filteredData);

            // Log the transformed data
            Log::debug("Transformed data for {$location}:", ['data' => $filteredData]);

            return response()->json([
                'data' => $filteredData,
                'titles' => $titles
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching sensor data for {$location}: {$e->getMessage()}", ['exception' => $e]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
