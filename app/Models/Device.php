<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name',
        'address',
        'status_endpoint',
        'report_url',
        'action_on',
        'action_off',
    ];
}
