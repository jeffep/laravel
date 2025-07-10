// app/Http/Controllers/WattageController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SplObjectStorage;

class ShellyDevice
{
    public $id;
    public $name;
    public $address;
    public $statusCommand;

    public function __construct($id, $name = null, $address = null, $statusCommand = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->statusCommand = $statusCommand;
    }
}

class WattageController extends Controller
{

   protected $shellyDeviceStorage;  // = new SplObjectStorage();


   public function __construct()
   {

       $this->shellyDeviceStorage = new SplObjectStorage();
       // Load Shelly IPs from .env and populate the array
       foreach ($_ENV as $key => $value) {
           if ((strpos($key, 'SHELLY') === 0) && (strpos($key, 'SHELLY_') !== 0)) {
                   // Extract the custom identifier (e.g., SHELLY01)
                   $shellyId = substr($key, 0, 8);
                   $foundShellyDevice = $this->findShellyDevice($shellyId);
                   if ($foundShellyDevice === null) {
                       $newDevice = new ShellyDevice($shellyId);
                       $this->shellyDeviceStorage->attach($newDevice);
                       $foundShellyDevice = $newDevice;
                   }
                   // Check if the key represents the name or address
                   if (strpos($key, '_NAME') !== false) {
                       // Store the name
                       $foundShellyDevice->name = $value;
                       \Log::info('stored ' . $value . ' in ' . $foundShellyDevice->id);
                   } elseif (strpos($key, '_ADDRESS') !== false) {
                       // Store the address
                       $foundShellyDevice->address = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundShellyDevice->id);
                   } elseif (strpos($key, '_STATUS') !== false) {
                       // Store the status command
                       $foundShellyDevice->statusCommand = $value;
                       \Log::info('stored ' . $value . 'in ' . $foundShellyDevice->id);
                  }
           }
       }
       // Now loop through the array and log the loaded data
              //$keys = array_keys($this->shellyInfo);
       foreach ($this->shellyDeviceStorage as $object) {
              \Log::info('Loaded: ' . $object->id . ',' . $object->name . ',' . $object->address);
       }
   }

      function findShellyDevice($id)
   {
      foreach ($this->shellyDeviceStorage as $object) {
         if ($object->id === $id) {
            return $object;
         }
      }
      return null;  //Not found
   }



    private function getShellyStatus($shellyIp, $statusCommand)
    {
        $url = "http://$shellyIp/$statusCommand";

        try {
            \Log::info('Trying ' . $url);
            $response = Http::get($url);

            if ($response->successful()) {
                \Log::info('Shelly response: ' . $response);
                return $response->json();
            } else {
                \Log::info('Shelly response: ' . $response);
                return ('error');
            }
        } catch (\Exception $e) {
            return ['error' => 'An error occurred: ' . $e->getMessage()];
        }
    }

       public function shelly_status()
   {

       $shellyStatus = [];
       foreach ($this->shellyDeviceStorage as $object) {
                 \Log::info('Getting status of: ' . $object->address);
                 $status = $this->getShellyStatus($object->address, $object->statusCommand);
                 //\Log::info('Received: ' . $status);
                 if ($object->statusCommand == 'status') $statusType = 1;
                 else if ($object->statusCommand == 'rpc/Shelly.GetStatus') $statusType = 2;
                 $shellyStatus[] = [
                     'id' => $object->name, // Use the stored name
                     'ip' => $object->address,
                     'statusType' => $statusType,
                     'status' => $status,
                 ];
       }

       return view('shelly_status', ['shellyStatus' => $shellyStatus]);
   }


    private function getShellyWattage($shellyIp, $statusCommand)
    {
        $wattage = 0;
        $attempts = 0;
        $maxAttempts = 10;
        $delay = 5; // Delay in seconds
        $url = "http://$shellyIp/$statusCommand";

        while ($wattage == 0 && $attempts < $maxAttempts) {
           try {
               \Log::info('Trying ' . $url);
               $response = Http::get($url);

               if ($response->successful()) {
                   \Log::info('Shelly response: ' . $response);
                   $responseData = $response->json();
                   $wattage = $responseData['meters'][0]['power'];
                   if ($wattage == 0) {
                      sleep($delay);
                      $attempts++;
                   }
               } else {
                   \Log::info('Shelly response: ' . $response);
                   return 'error';
               }
           } catch (\Exception $e) {
               return ['error' => 'An error occurred: ' . $e->getMessage()];
           }
        }
        return $wattage; // Adjust based on your Shelly device's response structure
    }


    private function findDeviceByName($deviceName)
    {
        foreach ($this->shellyDeviceStorage as $object) {
            if ($object->name === $deviceName) {
                return $object;
            }
        }
        return null;
    }

    public function logWattage(Request $request)
    {

        $secretKey = $request->query('secret_key');
        if ($secretKey !== env('SHELLY_SECRET_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $device = $request->input('device');
        $wattage = $request->input('wattage');
        $timestamp = now();

        $device = $this->findDeviceByName($deviceName);

        if ($device) {
            if ($action === 'off') {
                // Log the device off event
                Log::info("Device: $deviceName, Status: off, Timestamp: $timestamp");
                file_put_contents(storage_path('app/public/wattage.log'), "$timestamp $deviceName off\n", FILE_APPEND);
            } else {
                // Query the Shelly device for the current wattage
                $wattage = $this->getShellyWattage($device->address, $device->statusCommand);

                // Log the wattage usage
                Log::info("Device: $deviceName, Wattage: $wattage, Timestamp: $timestamp");
                file_put_contents(storage_path('app/public/wattage.log'), "$timestamp $deviceName $wattage\n", FILE_APPEND);
            }

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['error' => 'Device not found'], 404);
        }
    }

}

