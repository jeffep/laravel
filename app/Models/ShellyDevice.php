<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ShellyDevice extends Model
{
    public $id;
    public $name;
    public $address;

    public function __construct($id, $name = null, $address = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
    }
}
