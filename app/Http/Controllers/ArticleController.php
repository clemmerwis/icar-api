<?php

namespace App\Http\Controllers;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Article::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'joomla_id' => 'required',
            'joomla_url' => 'required',
            'category' => 'required',
            'year' => 'nullable',
            'make' => 'required',
            'model' => 'nullable',
            'submodel' => 'nullable',
        ]);

        return Article::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Article::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $article = Article::find($id);
        $article->update($request->all());
        return $article;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Article::destroy($id);
    }

    /**
     * Search articles.
     *
     * @param  str  $search_type
     * @param  str  $make
     * @param  str  $model
     * @param  str  $year
     * @return \Illuminate\Http\Response
     */
    public function search_OLD($search_type, $make, $model, $year = null)
    {
        // return match ($search_type) {
        //     'OEM-Calibartion-Requirements-Search' => $this->whereVars(80, $make, $model, $year),
        //     'OEM-Partial-Part-Replacement-Search' => $this->whereVars(3, $make, $model, $year),
        //     'OEM-Restraints-System-Part-Replacement-Search' => $this->whereVars(77, $make, $model, $year),
        //     'OEM-Hybrid-And-Electric-Vehicle-Disable-Search' => $this->whereVars(78, $make, $model, $year),
        //     default => 'test'
        // };
    }

    public function whereVars_OLD($category, $make, $model, $year = null)
    {
        // return match (true) {
        //     $model !== 'model' && isset($year) => Article::where(['category' => $category, 'make' => $make, 'model' => $model, 'year' => $year])->get(),
        //     $model !== 'model' => Article::where(['category' => $category, 'make' => $make, 'model' => $model])->get(),
        //     isset($year) => Article::where(['category' => $category, 'make' => $make, 'year' => $year])->get(),
        //     default => 'none'
        // };
    }

    /**
     * Alt search function
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $category
     */
    public function search(Request $request, $category)
    {
        $year = $request->filled('year') ? $request->input('year') : false;
        $make = $request->filled('make') ? $request->input('make') : false;
        $model = $request->filled('model') ? $request->input('model') : false;
        $sortBy = $request->filled('sort_by') ? $request->input('sort_by') : 'year';
        $sortOrder = $request->filled('sort_order') ? $request->input('sort_order') : 'asc';
        // This can be a request param
        $perPage = 12;

        $query = Article::query();
        if(!$make) {
            return ArticleResource::collection([]);
        }

        $articles = $query->where('category', $category)
                    ->when(!$year, function ($q) {
                        $q->whereNotNull('year');
                    })->when(!$model, function ($q) {
                        $q->whereNotNull('model');
                    })->when($make, function ($q, $make) {
                        $q->where('make', $make);
                    })->when($model, function ($q, $model) {
                        $q->where('model', $model);
                    })->when($year, function ($q, $year) {
                        $q->where('year', $year);
                    })->orderBy($sortBy, $sortOrder);
        
        $yMin = $articles->min('year');
        $yMax = $articles->max('year');

        return (new ArticleResource($articles->paginate($perPage)->appends($request->query())))
                ->additional(['extra_meta' => [
                    'year_min' => $yMin,
                    'year_max' => $yMax
                ]]);

        //  return ArticleResource::collection($articles);
    }
}
