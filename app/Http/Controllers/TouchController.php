<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TouchController extends Controller
{
    // Helper function to read last N hours of entries from a JSON file
    private function readLastHoursEntries($filePath, $hours = 6, $interval = 1)
    {
        $lines = [];
        $now = time();
        $hoursAgo = $now - $hours * 60 * 60;

        try {
            if (!file_exists($filePath)) {
                Log::error("File not found: {$filePath}");
                return [];
            }

            $file = new \SplFileObject($filePath, 'r');
            $file->seek(PHP_INT_MAX);
            $lineCount = $file->key();

            for ($i = $lineCount - 1; $i >= 0; $i--) {
                $file->seek($i);
                $buffer = $file->current();
                $entry = json_decode($buffer);

                if ($entry !== null && isset($entry->Time)) {
                    if ($entry->Time >= $hoursAgo) {
                        $lines[] = [
                            'time' => date('Y-m-d H:i:s', $entry->Time),
                            'temperature' => $entry->Temperature ?? null
                        ];
                    } else {
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error reading file {$filePath}: " . $e->getMessage());
            return [];
        }

        // Filter by interval (e.g., every 5 minutes if interval=5)
        if ($interval > 1) {
            $lines = array_filter($lines, function ($index) use ($interval) {
                return $index % $interval === 0;
            }, ARRAY_FILTER_USE_KEY);
        }

        return array_reverse($lines);
    }

    public function switches()
    {
        $user = Auth::user();
        if ($user->role === 'fronttouchpanel') {
            return view('touch.fswitches');
        } elseif ($user->role === 'garagetouchpanel') {
            return view('touch.gswitches');
        }
        return view('touch.switches');
    }

    public function index1()
    {
        return view('gtouch-dashboard');
    }

    public function index2()
    {
        return view('ftouch-dashboard');
    }

    public function dashboard()
    {
        return view('touch.dashboard');
    }

    public function clock()
    {
        return view('touch.clock');
    }

    public function cornFutures()
    {
        return view('touch.corn-futures');
    }

    public function calendar()
    {
        return view('touch.calendar');
    }

    public function temperatures()
    {
        $locations = ['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'garagetablet', 'frontdoortablet'];
        $temperatureData = [];

        $filePaths = [
            'bedrooms' => storage_path('app/workroomtemperatures.json'),
            'den' => storage_path('app/dentemperatures.json'),
            'garage' => storage_path('app/garagetemperatures.json'),
            'birdbath' => storage_path('app/birdbathtemperatures.json'),
            'birdcam' => storage_path('app/birdbathtemperatures.json'), // Adjust if birdcam has a separate file
            'garagetablet' => storage_path('app/garagetablettemperatures.json'),
            'frontdoortablet' => storage_path('app/frontdoortablettemperatures.json')
        ];

        foreach ($locations as $location) {
            $filePath = $filePaths[$location] ?? null;
            if ($filePath) {
                $temperatureData[$location] = $this->readLastHoursEntries($filePath, 12, 1);
            } else {
                Log::warning("No file path defined for location: {$location}");
                $temperatureData[$location] = [];
            }
        }

        $locations = array_keys($temperatureData);
        return view('touch.temperatures', compact('temperatureData', 'locations'));
    }

    public function sensorData(Request $request)
    {
        $location = $request->query('location', 'bedrooms');
        $hours = (int) $request->query('hours', 6);
        $interval = (int) $request->query('interval', 1);

        $filePaths = [
            'bedrooms' => storage_path('app/workroomtemperatures.json'),
            'den' => storage_path('app/dentemperatures.json'),
            'garage' => storage_path('app/garagetemperatures.json'),
            'birdbath' => storage_path('app/birdbathtemperatures.json'),
            'birdcam' => storage_path('app/birdbathtemperatures.json'),
            'garagetablet' => storage_path('app/garagetablettemperatures.json'),
            'frontdoortablet' => storage_path('app/frontdoortablettemperatures.json')
        ];

        $filePath = $filePaths[$location] ?? null;
        if (!$filePath) {
            return response()->json(['error' => 'Invalid location'], 404);
        }

        $data = $this->readLastHoursEntries($filePath, $hours, $interval);
        return response()->json([
            'data' => $data,
            'titles' => ['temperature']
        ]);
    }

    public function slideshow()
    {
        return view('touch.slideshow');
    }
}
