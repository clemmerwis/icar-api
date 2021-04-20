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
    public function search($search_type, $make, $model, $year = null)
    {
        // return match ($search_type) {
        //     'OEM-Calibartion-Requirements-Search' => $this->whereVars(80, $make, $model, $year),
        //     'OEM-Partial-Part-Replacement-Search' => $this->whereVars(3, $make, $model, $year),
        //     'OEM-Restraints-System-Part-Replacement-Search' => $this->whereVars(77, $make, $model, $year),
        //     'OEM-Hybrid-And-Electric-Vehicle-Disable-Search' => $this->whereVars(78, $make, $model, $year),
        //     default => 'test'
        // };
    }

    public function whereVars($category, $make, $model, $year = null)
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
     */
    public function taylor(Request $request)
    {
        $year = $request->filled('year') ? $request->input('year') : false;
        $make = $request->filled('make') ? $request->input('make') : false;
        $model = $request->filled('model') ? $request->input('model') : false;
        // This can be a request param
        $perPage = 12;

        $query = Article::query();

        $articles = $query->when($make, function ($q, $make) {
                        $q->where('make', $make);
                    })->when($model, function ($q, $model) {
                        $q->where('model', $model);
                    })->when($year, function ($q, $year) {
                        $q->where('year', $year);
                    })->paginate($perPage)->appends($request->query());

         return ArticleResource::collection($articles);
    }
}
