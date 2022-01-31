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
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MachineModelController;
use App\Http\Controllers\PartAliasController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PartHeadingController;
use App\Http\Controllers\PartStockController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WarehouseController;

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

// Login routes
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', [AuthController::class, 'test'])->middleware("auth:sanctum");


Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    Route::apiResource('users', UserController::class);

    // Profile routes

    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::post('/password-update', [ProfileController::class, 'changePassword']);
    Route::post('/profile-update', [ProfileController::class, 'updateProfile']);

    // Designation routes
    Route::apiResource('designations', DesignationController::class);


    // Role Routes
    Route::apiResource('roles', RoleController::class);


    //Comapny routes
    Route::apiResource('companies.users', CompanyUserController::class);
    Route::apiResource('companies', CompanyController::class);

    //Contracts routes
    Route::apiResource('contracts', ContractController::class);

    //Machines routes
    Route::apiResource('machines', MachineController::class);
    Route::apiResource('machines/{machine}/models', MachineModelController::class);
    Route::apiResource('machines/{machine}/part-headings', PartHeadingController::class);

    //Parts
    Route::apiResource('parts', PartController::class);
    Route::apiResource('parts/{part}/aliases', PartAliasController::class);
    Route::apiResource('parts/{part}/stocks', PartStockController::class);

    // Employees routes
    Route::apiResource('employees', EmployeeController::class);

    // WareHouse Route
    Route::apiResource('warehouses', WarehouseController::class);
});

