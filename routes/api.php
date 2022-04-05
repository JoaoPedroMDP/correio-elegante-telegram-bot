<?php
declare(strict_types=1);

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

Route::get('update','CorAnteController@updateMessages');
Route::post('register', 'CorAnteController@register');

Route::get("updateMessages", [App\Domains\Message\Handlers\GetUpdates::class, 'handle']);
Route::post("webHook", [App\Domains\Update\Handlers\UpdatesListener::class, 'handle']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
