<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CornPrice extends Model
{
    protected $fillable = ['symbol', 'price', 'bid', 'ask', 'updated_at'];
}
