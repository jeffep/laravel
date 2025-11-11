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
    $location = $request->query('location');
    $hours    = $request->query('hours', 6);
    $interval = max(1, (int) $request->query('interval', 1));

    if (!$location) {
        Log::error("Location parameter is required");
        return response()->json(['error' => 'Location required'], 400);
    }

    $start = Carbon::now('America/Denver')->subHours($hours);

    try {
        // -------------------------------------------------
        // 1. All titles that exist in the requested window
        // -------------------------------------------------
        $titles = DB::table('sensor_data AS sd')
            ->join('sensors AS s', 'sd.sensor_id', '=', 's.id')
            ->where('s.location', $location)
            ->where('sd.time', '>=', $start)
            ->distinct()
            ->pluck('sd.title')
            ->toArray();

        if (empty($titles)) {
            return response()->json(['data' => [], 'titles' => []]);
        }

        // -------------------------------------------------
        // 2. Raw rows (time, title, value)
        // -------------------------------------------------
        $rows = DB::table('sensor_data AS sd')
            ->join('sensors AS s', 'sd.sensor_id', '=', 's.id')
            ->select('sd.time', 'sd.title', 'sd.value')
            ->where('s.location', $location)
            ->where('sd.time', '>=', $start)
            ->orderBy('sd.time')
            ->get();

        // -------------------------------------------------
        // 3. Pivot → one object per timestamp
        // -------------------------------------------------
        $pivoted = [];
        foreach ($rows as $r) {
            $key = strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $r->title));

            // Keep the **exact numeric value** – 0 stays 0, null stays null
            $val = $r->value === null ? null : (float) $r->value;

            if (!isset($pivoted[$r->time])) {
                $pivoted[$r->time] = ['time' => $r->time];
            }
            $pivoted[$r->time][$key] = $val;
        }
        $data = array_values($pivoted);   // re-index

        // -------------------------------------------------
        // 4. Down-sample by interval (keep first point of each bucket)
        // -------------------------------------------------
        if ($interval > 1) {
            $data = collect($data)->filter(function ($_, $i) use ($interval) {
                return $i % $interval === 0;
            })->values()->all();
        }

        Log::debug("API {$location} → " . count($data) . ' points', [
            'sample' => array_slice($data, 0, 3)
        ]);

        return response()->json([
            'data'   => $data,
            'titles' => $titles
        ]);

    } catch (\Exception $e) {
        Log::error("SensorData API error: {$e->getMessage()}", ['exception' => $e]);
        return response()->json(['error' => 'Server error'], 500);
    }
}
}
