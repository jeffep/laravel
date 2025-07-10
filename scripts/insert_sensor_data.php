<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon;

// Bootstrap Eloquent ORM
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'your_database_name',
    'username'  => 'your_db_username',
    'password'  => 'your_db_password',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Get data from command-line arguments
$location = $argv[1];      // Sensor location (e.g., "den", "garage", "birdbath")
$time = $argv[2];          // Unix timestamp
$json_data = $argv[3];     // JSON string, e.g. '{"Temperature":90.00,"Humidity":25.00}'

// Validate input
if (empty($location)) {
    die("Error: Sensor location is required.\n");
}
if (!is_numeric($time)) {
    die("Error: Invalid time input.\n");
}

$data = json_decode($json_data, true);
if (!$data || !is_array($data)) {
    die("Error: Invalid JSON data.\n");
}

// Look up the sensor by location (adjust field as needed)
$sensor = Capsule::table('sensors')->where('location', $location)->first();
if (!$sensor) {
    die("Error: Sensor not found for location '$location'.\n");
}

$records = [];
foreach ($data as $key => $value) {
    if (!is_numeric($value)) continue; // Optionally skip non-numeric data
    $records[] = [
        'sensor_id' => $sensor->id,
        'time' => Carbon::createFromTimestamp($time),
        'title' => $key,
        'value' => $value,
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

if (empty($records)) {
    die("No valid sensor data to insert.\n");
}

try {
    Capsule::table('sensor_data')->insert($records);
    echo "Data inserted successfully for location=$location, Time=$time\n";
} catch (\Exception $e) {
    echo "Error inserting data for location=$location: " . $e->getMessage() . "\n";
}
