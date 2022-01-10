<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DesignationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::apiResource('users', UserController::class);
Route::apiResource('designations', DesignationController::class);
// Login
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', [AuthController::class, 'test'])->middleware("auth:sanctum");

//Comapny routes
Route::apiResource('companies', CompanyController::class);
Route::post('companies/{company}/users', [CompanyController::class, 'addUser']);
