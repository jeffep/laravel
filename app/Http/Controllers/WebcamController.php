<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebcamController extends Controller
{
    protected $webcams = [
        'webcam1' => 'http://192.168.87.24',
        'birdcam' => 'http://192.168.87.120/index3.php'
    ];

    public function index()
    {
        $webcams = array_map(function($url, $name) {
            return ['name' => $name, 'url' => $url];
        }, $this->webcams, array_keys($this->webcams));

        return view('webcams', ['webcams' => $webcams]);
    }

    public function show($name)
    {
        $url = $this->webcams[$name] ?? null;

        if ($url) {
            $webcams = [['name' => $name, 'url' => $url]];
            return view('webcams', ['webcams' => $webcams]);
        } else {
            return abort(404);
        }
    }
}

