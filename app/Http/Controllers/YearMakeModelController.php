<?php

namespace App\Http\Controllers;
use App\Models\Article;
use Illuminate\Http\Request;

class YearMakeModelController extends Controller
{
    /**
     * Get all the Makes.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function makes(Request $request)
    {
        $cat = $request->filled('cat_id') ? $request->input('cat_id') : '';
        return Article::makes($cat);
    }

    /**
     * Get all the Makes in category $cat.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function modelsByMake(Request $request)
    {
        $cat = $request->filled('cat_id') ? $request->input('cat_id') : '';
        $make = $request->filled('make') ? $request->input('make') : '';
        return Article::modelsByMake($make, $cat);
    }

    /**
     * Get all the Years.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function years(Request $request)
    {
        $cat = $request->filled('cat_id') ? $request->input('cat_id') : '';
        $make = $request->filled('make') ? $request->input('make') : '';
        $model = $request->filled('model') ? $request->input('model') : false;

        $years = Article::where('category', $cat)
                        ->when($make, function ($query, $make) {
                            $query->where('make', $make);
                        })->when($model, function ($query, $model) {
                            $query->where('model', $model);
                        })->pluck('year');

        return $years->unique()->filter()->sort()->values()->all();
    }
}
