<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HouseController extends Controller
{
    public function index()
    {
        // Query latest temperature data for each location
        $locations = ['garage', 'den', 'birdbath', 'bedrooms'];
        $data = [];

        foreach ($locations as $location) {
            $latest = DB::table('sensor_data')
                ->where('location', $location)
                ->orderBy('time', 'desc')
                ->first();

            $data[$location] = $latest ? $latest->temperature : 'N/A';
        }

        // Get the authenticated user so we can do the proper layout
        $user = Auth::user();
 
        // Determine the layout based on the user's role
        $layout = 'layouts.app'; // Default layout
        if ($user->role === 'fronttouchpanel') {
            $layout = 'layouts.app_ftouch';
        } elseif ($user->role === 'garagetouchpanel') {
            $layout = 'layouts.app_gtouch'; // Add this if you have a garage-specific layout
        }

        // Debugging: Dump the entire request object
        //dd(request(), 'Request URL:', request()->url(), 'Request Path:', request()->path());

        // Pass the temperature data to the Blade view
        return view('house', [
            'garage' => $data['garage'],
            'den' => $data['den'],
            'birdbath' => $data['birdbath'],
            'bedrooms' => $data['bedrooms'],
            'layout' => $layout,  // Pass the layout choice to the view
        ]);
    }
}

