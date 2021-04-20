<?php
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('/vehicles', [VehicleController::class, 'index']);
// Route::post('/vehicles', [VehicleController::class, 'store']);
Route::resource('vehicles', VehicleController::class);
Route::resource('articles', ArticleController::class);

Route::get('articles/search/{searchtype}/{make}/{model}/{year?}', [ArticleController::class, 'search']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
