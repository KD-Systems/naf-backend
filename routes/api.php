<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\WareHouseController;

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
// Designation routes
Route::apiResource('designations', DesignationController::class)->middleware("auth:sanctum");
// Login routes
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', [AuthController::class, 'test'])->middleware("auth:sanctum");

//Comapny routes
Route::apiResource('companies.users', CompanyUserController::class);
Route::apiResource('companies', CompanyController::class);

//Contracts routes
Route::apiResource('contracts', ContractController::class);

// Employees routes
Route::apiResource('employees', EmployeeController::class)->middleware("auth:sanctum");

// WareHouse Route
Route::apiResource('ware_houses', WareHouseController::class)->middleware("auth:sanctum");
