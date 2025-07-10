<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    protected $table = 'sensors';

    protected $fillable = [
        'name',
        'location',
        'type',
        'address',
        'topic',
        // Add any other fields your sensors table uses
    ];

    // Relationships
    public function data()
    {
        return $this->hasMany(SensorData::class);
    }
}
