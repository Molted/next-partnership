<?php

use App\Http\Controllers\api\PartnershipController;
use Illuminate\Http\Response;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::options('/{routes:.*}', function (Response $response) {
//     $response->header('Access-Control-Allow-Origin', '*');
//     $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
//     $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

//     return $response;
// });

Route::controller(PartnershipController::class)->prefix("/events")->group( function (){
    Route::post('/', 'createEvent');
    Route::post('/chart-data', 'chartData');
});
