<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TouchController extends Controller
{
   public function switches()
    {
        $user = Auth::user();
        if ($user->role === 'fronttouchpanel') {
            return view('touch.fswitches');
        } elseif ($user->role === 'garagetouchpanel') {
            return view('touch.gswitches');
        }

        // Fallback view or redirect if the role is unexpected
        return view('touch.switches'); // Or redirect to a default route
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
        $locations = ['bedrooms', 'den', 'garage', 'birdbath', 'birdcam', 'garagetablet', 'fronttablet'];
        $hours = 12; // Last 12 hours
        $startTime = Carbon::now()->subHours($hours)->timestamp;

        $temperatureData = [];
        foreach ($locations as $location) {
            $data = DB::table('sensor_data')
                ->select('time', 'temperature')
                ->where('location', $location)
                ->where('time', '>=', $startTime)
                ->orderBy('time', 'asc')
                ->get()
                ->map(function ($entry) {
                    return [
                        'time' => $entry->time * 1000, // Convert to milliseconds for Highcharts
                        'temperature' => $entry->temperature
                    ];
                })
                ->values()
                ->toArray();

            $temperatureData[$location] = $data;
        }

        return view('touch.temperatures', compact('temperatureData', 'locations'));
    }

   public function slideshow()
    {
        return view('touch.slideshow');
    }
} 
