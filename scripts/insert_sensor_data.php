<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Bootstrap Eloquent ORM
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/../database/database.sqlite',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Get data from command-line arguments
$location = $argv[1];      // Sensor location (e.g., "den", "garage", "birdbath")
$time = $argv[2];          // Unix timestamp
$temperature = $argv[3];   // Temperature value
$humidity = $argv[4];      // Humidity value

// Validate input
if (empty($location)) {
    die("Error: Sensor location is required.\n");
}
if (!is_numeric($time) || !is_numeric($temperature) || !is_numeric($humidity)) {
    die("Error: Invalid input data.\n");
}

// Insert data into the database
try {
    Capsule::table('sensor_data')->insert([
        'location' => $location,
        'time' => $time,
        'temperature' => $temperature,
        'humidity' => $humidity,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "Data inserted successfully: Location=$location, Time=$time, Temperature=$temperature, Humidity=$humidity\n";
} catch (\Exception $e) {
    echo "Error inserting data for location=$location: " . $e->getMessage() . "\n";
}
