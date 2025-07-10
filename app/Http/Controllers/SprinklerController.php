<?php

// app/Http/Controllers/SprinklerController.php

namespace App\Http\Controllers;

use App\Services\WyzeService;
use Illuminate\Http\Request;

class SprinklerController extends Controller
{
    protected $wyzeService;

    public function __construct(WyzeService $wyzeService)
    {
        $this->wyzeService = $wyzeService;
    }

    public function controlZone(Request $request)
    {
        $zoneId = $request->input('zone_id');
        $action = $request->input('action'); // 'on' or 'off'

        $result = $this->wyzeService->controlZone($zoneId, $action);

        return view('sprinkler.status', ['result' => $result]);
    }
}

