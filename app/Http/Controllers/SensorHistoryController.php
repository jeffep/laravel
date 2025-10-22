<?php
// app/Http/Controllers/SensorHistoryController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SensorHistoryController extends Controller
{
    public function showSensorHistory()
    {
        // Fetch unique sensor locations
        $locations = DB::table('sensors')->distinct()->pluck('location')->toArray();
        
        // Fetch all unique titles (data types) across all sensors
        $dataTypes = DB::table('sensor_data')->distinct()->pluck('title')->toArray();
        
        return view('sensor-history', compact('locations', 'dataTypes'));
    }

    public function getSensorHistory(Request $request)
    {
        // Get query parameters
        $location = $request->query('location');
        $dataType = $request->query('data_type'); // New parameter for title
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $interval = $request->query('interval', 1);

        // Validate parameters
        if (!$location || !$startDate || !$endDate || !$dataType) {
            Log::error("Missing required parameters", [
                'location' => $location,
                'data_type' => $dataType,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
            return response()->json(['error' => 'Location, data type, start date, and end date are required'], 400);
        }

        try {
            // Parse dates
            $startTime = Carbon::parse($startDate)->startOfDay()->toDateTimeString();
            $endTime = Carbon::parse($endDate)->endOfDay()->toDateTimeString();

            // Get titles (limited to the selected data type)
            $titles = [$dataType]; // Only the selected title

            // Fetch raw data for the specific location and data type
            $rawData = DB::table('sensor_data')
                ->join('sensors', 'sensor_data.sensor_id', '=', 'sensors.id')
                ->select('sensors.location', 'sensor_data.time', 'sensor_data.title', 'sensor_data.value')
                ->where('sensors.location', $location)
                ->where('sensor_data.title', $dataType)
                ->whereBetween('sensor_data.time', [$startTime, $endTime])
                ->orderBy('sensor_data.time', 'asc')
                ->get();

            // Log the raw query for debugging
            $rawQuery = DB::table('sensor_data')
                ->join('sensors', 'sensor_data.sensor_id', '=', 'sensors.id')
                ->select('sensors.location', 'sensor_data.time', 'sensor_data.title', 'sensor_data.value')
                ->where('sensors.location', $location)
                ->where('sensor_data.title', $dataType)
                ->whereBetween('sensor_data.time', [$startTime, $endTime])
                ->orderBy('sensor_data.time', 'asc')
                ->toSql();
            Log::debug("Sensor history query for {$location}, {$dataType}: {$rawQuery}", [
                'bindings' => [$location, $dataType, $startTime, $endTime]
            ]);

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

            Log::debug("Transformed data for {$location}, {$dataType}:", [
                'data_count' => count($filteredData),
                'sample_data' => array_slice($filteredData, 0, 5)
            ]);

            return response()->json([
                'data' => $filteredData,
                'titles' => $titles
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching sensor history for {$location}, {$dataType}: {$e->getMessage()}", ['exception' => $e]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
