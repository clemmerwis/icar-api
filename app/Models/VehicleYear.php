<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleYear extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $primaryKey = 'joomla_id';
    public $incrementing = false;
}