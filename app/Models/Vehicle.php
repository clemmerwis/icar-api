<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'joomla_id' => 'integer'
    ];

    public function scopeModelsByMake($query, $make)
    {

        $models = $query->where('make', $make)->orderBy('model', 'desc')->pluck('model');

        $models = $models->unique()->values()->all();

        return $models;
    }
}
