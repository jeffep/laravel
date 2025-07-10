<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $location = $request->query('location'); // Get the specific location
        $hours = $request->query('hours', 6); // Default to 6 hours if not provided
        $interval = $request->query('interval', 1); // Default to 1 if not provided

        // Validate the location parameter
        if (!$location) {
            return response()->json(['error' => 'Location parameter is required'], 400);
        }

        // Calculate the start time based on the hours parameter
        $startTime = Carbon::now()->subHours($hours)->timestamp;
//        \Log::info('Start Time (Unix Timestamp): ' . $startTime);

        // Query the database
        $data = DB::table('sensor_data')
            ->select('location', 'time', 'temperature', 'humidity')
            ->where('location', $location) // Filter by the specific location
            ->where('time', '>=', $startTime) // Filter by time
            ->orderBy('time', 'asc') // Order by time
            ->get();

        // Debug the retrieved timestamps
        $data->each(function ($entry) {
//            \Log::info('Retrieved Entry Time: ' . Carbon::createFromTimestamp($entry->time)->toDateTimeString());
        });

        // Apply interval filtering
        $filteredData = $data->filter(function ($entry, $index) use ($interval) {
            return $index % $interval === 0;
        });

        // Return the filtered data as JSON
        return response()->json($filteredData->values());
    }
}

