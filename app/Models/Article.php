<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'joomla_data' => 'collection'
    ];

    protected $appends = ['joomla_category'];

    public function scopeModelsByMake($query, $make, $cat)
    {
        $models = $query->where(['make' => $make, 'category' => $cat])->pluck('model');
        return $models->unique()->filter()->sort()->values()->all();
    }

    public function scopeMakes($query, $cat)
    {
        $makes = $query->where('category', $cat)->pluck('make');
        return $makes->unique()->filter()->sort()->values()->all();
    }

    public function getJoomlaCategoryAttribute()
    {
        $cat = ArticleCategory::where('joomla_id', $this->category);
        if($cat->exists()) {
            return $cat->first();
        }
        return null;
    }
}
