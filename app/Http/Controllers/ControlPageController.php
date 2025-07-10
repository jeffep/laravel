<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ControlPageController extends Controller
{
    public function home()
    {
        $currentTime = date('Y-m-d H:i:s');
        $temperature = $this->getCurrentTemperature();
        $diskSpace = $this->getDiskSpace();
        $uptime = $this->getUptime();
        $topProcesses = $this->getTopProcesses();

        //return view('control-content',compact('currentTime', 'temperature', 'diskSpace', 'uptime', 'topProcesses'));
        //return view([
        //'currentTime' => $currentTime,
        //'temperature' => $temperature,
        //'diskSpace' => $diskSpace,
        //'uptime' => $uptime,
        //'topProcesses' => $topProcesses,
        //]);
        return view('home',compact('currentTime', 'temperature', 'diskSpace', 'uptime', 'topProcesses'));
    }

/* OLD VERSION
    public function readLastSixHoursEntries($filePath) {
       // Initialize an empty array
       $lines = [];

       // Open the file for reading
       $fileHandle = fopen($filePath, 'r');

       if ($fileHandle) {
           // Move the file pointer to the end
           fseek($fileHandle, 0, SEEK_END);

           // Read the last 6 hours of entries into the array
           $now = time();
           $sixHoursAgo = $now - 6 * 60 * 60;

           while (ftell($fileHandle) > 0) {
               $buffer = '';
               $char = '';
               while ($char !== "\n") {
                  fseek($fileHandle, -2, SEEK_CUR);
                  $char = fread($fileHandle, 1);
                  //Log::info('Char=' . $char);
                  $buffer = $char . $buffer;
               }
               Log:info('Grabbed:' . $buffer);
               $entry = json_decode($buffer);
               if ($entry !== null && isset($entry->Time) && $entry->Time >= $sixHoursAgo) {
                   $lines[] = $entry;
                   //Log::info('Decoded line=' . $entry);
               } else if (isset($entry->Time) && $entry->Time < $sixHoursAgo) break;
           }

           // Close the file
           fclose($fileHandle);
       } else {
           echo "Error opening the file.";
       }

       return array_reverse($lines);
    }
*/

public function readLastSixHoursEntries($filePath) {
    $lines = [];
    $now = time();
    $sixHoursAgo = $now - 6 * 60 * 60;

    try {
        // Open the file
        $file = new \SplFileObject($filePath, 'r');

        // Move to the end of the file
        $file->seek(PHP_INT_MAX);
        $lineCount = $file->key(); // Total number of lines

        // Read lines in reverse order
        for ($i = $lineCount - 1; $i >= 0; $i--) {
            $file->seek($i);
            $buffer = $file->current();

            // Decode the JSON line
            $entry = json_decode($buffer);

            // Check if the entry is valid and within the last 6 hours
            if ($entry !== null && isset($entry->Time)) {
                if ($entry->Time >= $sixHoursAgo) {
                    $lines[] = $entry;
                } else {
                    // Stop reading if the timestamp is older than 6 hours
                    break;
                }
            }
        }
    } catch (\Exception $e) {
        // Log any errors
        Log::error("Error reading file: " . $e->getMessage());
        return [];
    }

    // Reverse the array to restore chronological order
    return array_reverse($lines);
}

    public function temperatures()
    {
        //WORKROOM TEMPERATURES
        $filePath = storage_path('app/workroomtemperatures.json');
        $linesWorkRoom = $this->readLastSixHoursEntries($filePath);
        //Log::info('lines=:', $linesWorkRoom);

        //GARAGE TEMPERATURES
        $filePath2 = storage_path('app/garagetemperatures.json');
        $linesGarage = $this->readLastSixHoursEntries($filePath2);
/*
        //BACK PORCH TEMPERATURES
        $filePath3 = storage_path('app/backporchtemperatures.json');
        $linesBackPorch = $this->readLastSixHoursEntries($filePath3);
*/
        //DEN TEMPERATURES

        $filePath4 = storage_path('app/dentemperatures.json');
        $linesDen = $this->readLastSixHoursEntries($filePath4);

        //BIRDBATH TEMPERATURES
        $filePath = storage_path('app/birdbathtemperatures.json');
        $linesBirdBath = $this->readLastSixHoursEntries($filePath);

        //Garage Tablet TEMPERATURES
        $filePath = storage_path('app/garagetablettemperatures.json');
        $linesGarageT = $this->readLastSixHoursEntries($filePath);

        //Frontdoor Tablet TEMPERATURES
        $filePath = storage_path('app/frontdoortablettemperatures.json');
        $linesFrontdoorT = $this->readLastSixHoursEntries($filePath);


        return view('test3', compact('linesDen','linesWorkRoom','linesGarage', 'linesBirdBath', 'linesGarageT', 'linesFrontdoorT'));
//        return view('test3', compact('linesDen','linesWorkRoom','linesGarage','linesBackPorch'));
    }


    public function shelly_status()
    {
        return view('shelly_status');
        //return view('control-content')->with('control-content', 'shelly_status');
    }

    private function getCurrentTemperature()
    {
        // Example API call to get the current temperature
        //$response = Http::get('http://api.weatherapi.com/v1/current.json?key=YOUR_API_KEY&q=London');
        //return $response->json()['current']['temp_c'];
        $client = new Client();
        $response = $client->get('https://api.open-meteo.com/v1/forecast', [
            'query' => [
            'latitude' => 31.7619, // Latitude for El Paso, Texas
            'longitude' => -106.4850, // Longitude for El Paso, Texas
            'current_weather' => true
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $fahrenheitTemp = ($data['current_weather']['temperature'] * 9/5) + 32;
        //return $data['current_weather']['temperature'];
        return $fahrenheitTemp;
    }

    private function getDiskSpace()
    {
        return disk_free_space("/");
    }

    private function getUptime()
    {
        return shell_exec('uptime');
    }

    private function getTopProcesses()
    {
        //return shell_exec('ps -eo pid,comm,%mem,%cpu --sort=-%mem | head -n 6');
        $output = shell_exec('ps -eo pid,comm,%mem,%cpu --sort=-%mem | head -n 6');
        $lines = explode("\n", trim($output)); // Split the output into lines
        array_shift($lines); // Remove the header line

        $processes = [];
        foreach ($lines as $line) {
           $processes[] = preg_replace('/\s+/', ' ', $line); // Normalize whitespace
        }

       return $processes;
    }

    public function showControlPage()
    {
        return view('control'); // Assuming control.blade.php exists in resources/views
    }

    public function sounds()
    {
        return view('sounds');
    }

    public function sprinkler2()
    {
       return view('sprinkler2');
    }

    public function shellyDevice()   // Den Fan
    {
       return view('shellyDevice');
    }

    public function shellyDevice2()
    {
       return view('shellyDevice2');
    }

    public function shellyDevice3()
    {
       return view('shellyDevice3');
    }
    public function shellyDevice4()
    {
       return view('shellyDevice4');
    }


    public function generateGoAccessReport(Request $request)
    {
        // Define the command to run GoAccess
        $command = 'goaccess /var/log/nginx/access.log -o ' . storage_path('../public/goaccess-report.html') . ' --log-format=COMBINED';

        // Run the command
        exec($command, $output, $return_var);

        // Check if the process was successful
        if ($return_var !== 0) {
            return back()->with('error', 'Failed to generate GoAccess report.');
        }

        // Redirect to the GoAccess report view
        return view('goaccess');
    }
}
