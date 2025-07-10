<?php

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Bootstrap Eloquent ORM for the primary database
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/../database/database.sqlite',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Bootstrap Eloquent ORM for the archive database
$archiveCapsule = new Capsule;
$archiveCapsule->addConnection([
    'driver' => 'sqlite',
    'database' => '/mnt/nas/archive_database.sqlite', // Path to the archive database on the NAS
    'prefix' => '',
]);
$archiveCapsule->setAsGlobal();
$archiveCapsule->bootEloquent();

// Define the cutoff time (3 months ago)
$cutoffTime = time() - (3 * 30 * 24 * 60 * 60); // 3 months in seconds

// Fetch data older than 3 months
$oldData = Capsule::table('sensor_data')
    ->where('time', '<', $cutoffTime)
    ->get();

// Insert old data into the archive database
foreach ($oldData as $entry) {
    $archiveCapsule::table('sensor_data')->insert((array) $entry);
}

// Delete old data from the primary database
Capsule::table('sensor_data')
    ->where('time', '<', $cutoffTime)
    ->delete();

echo "Archived " . count($oldData) . " records.\n";
