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

// Path to the JSON file
$jsonFilePath = '/var/www/html/garagetemperatures.json';

// Read the JSON file line by line
$fileHandle = fopen($jsonFilePath, 'r');
if ($fileHandle) {
    while (($line = fgets($fileHandle)) !== false) {
        // Decode the JSON line
        $entry = json_decode($line, true);

        if ($entry !== null && isset($entry['Time'], $entry['Temperature'], $entry['Humidity'])) {
            // Insert data into the database
            Capsule::table('sensor_data')->insert([
                'location' => 'garage', // Set the location to "den"
                'time' => $entry['Time'],
                'temperature' => $entry['Temperature'],
                'humidity' => $entry['Humidity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "Inserted: Time={$entry['Time']}, Temperature={$entry['Temperature']}, Humidity={$entry['Humidity']}\n";
        } else {
            echo "Skipping invalid entry: $line\n";
        }
    }

    fclose($fileHandle);
} else {
    echo "Error opening the file: $jsonFilePath\n";
}

echo "Data loading complete.\n";
