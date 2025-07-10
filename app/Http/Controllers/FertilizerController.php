<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FertilizerController extends Controller
{
    public function index()
    {
        $file = storage_path('app/public/fertilizer_data.csv');
        $data = $this->readCSV($file);

        return view('fertilizer', compact('data'));
    }

    private function readCSV($file)
    {
        $data = [];
        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, "\t")) !== false) { // Change delimiter to tab
                $data[] = [
                    'year' => $row[0],
                    'month' => $row[1],
                    'date' => $row[2],
                    'fertilizer' => $row[3],
                    'amount' => $row[4]
                ];
            }
            fclose($handle);
        }
        return $data;
    }
}

