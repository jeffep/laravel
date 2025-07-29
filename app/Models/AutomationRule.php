<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $fillable = [
        'location',
        'condition_type',
        'condition_compare',
        'action_device_id',
        'action',
        'active' // Add active to fillable
    ];

    protected $casts = [
        'active' => 'boolean', // Ensures tinyint is treated as boolean
    ];

    public function actionDevice()
    {
        return $this->belongsTo(Device::class, 'action_device_id');
    }
}
