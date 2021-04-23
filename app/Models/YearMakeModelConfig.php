<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearMakeModelConfig extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeYearId($query)
    {
        return $query->where('name', 'year')->first()->joomla_id;
    }

    public function scopeMakeId($query)
    {
        return $query->where('name', 'make')->first()->joomla_id;
    }

    public function scopeModelId($query)
    {
        return $query->where('name', 'model')->first()->joomla_id;
    }

    public function scopeSubmodelId($query)
    {
        return $query->where('name', 'submodel')->first()->joomla_id;
    }
}
