<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TrugreenController extends Controller
{
    public function trugreenFiles()
    {
        $baseDir = public_path('TRUGREEN');
        $directories = File::directories($baseDir);

        return view('trugreen', compact('directories'));
    }

    public function showPDF($directory, $file)
    {
        $filePath = public_path("trugreen/{$directory}/{$file}");

        if (!File::exists($filePath)) {
            abort(404);
        }

        return response()->file($filePath);
    }
}

