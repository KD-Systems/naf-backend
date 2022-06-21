<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoxHeadingController;
// use App\Http\Controllers\Client\ContractController as ClientContractController;
// use App\Http\Controllers\Client\MachineController as ClientMachineController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyMachineController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MachineModelController;
use App\Http\Controllers\PartAliasController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PartHeadingController;
use App\Http\Controllers\PartStockController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Resources\EmployeeCollection;
use App\Http\Controllers\DeliveryNotesController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\GatePassController;
use App\Http\Controllers\SettingsController;
// client controller
use App\Http\Controllers\Client\ClientRequisitionController;
use App\Http\Controllers\Client\ClientQuotationController;
use App\Http\Controllers\Client\ClientInvoiceController;
use App\Http\Controllers\Client\ClientDeliveryNoteController;
use App\Http\Controllers\Client\ClientMachineController;
use App\Http\Controllers\Client\ClientContractController;
use App\Http\Controllers\QuotationCommentController;
use App\Models\Requisition;

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

// Login routes
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('user', fn () => auth()->user());
    Route::apiResource('users', UserController::class);

    // Profile routes
    Route::get('profile', [ProfileController::class, 'getProfile']);
    Route::post('password-update', [ProfileController::class, 'changePassword']);
    Route::post('profile-update', [ProfileController::class, 'updateProfile']);

    // Designation routes
    Route::apiResource('designations', DesignationController::class);

    // Role Routes
    Route::apiResource('roles', RoleController::class);
    Route::get('get-permission', [RoleController::class, 'getPermission']);
    Route::post('roles/{role}/permission-update', [RoleController::class, 'updatePermission']);

    //Comapny routes
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('companies.users', CompanyUserController::class);
    Route::apiResource('companies.machines', CompanyMachineController::class);
    Route::post('/companies/due-limit/{company}', [CompanyController::class, 'updateDueLimit']);


    //Contracts routes
    Route::apiResource('contracts', ContractController::class);

    //Machines routes
    Route::apiResource('machines', MachineController::class);
    Route::apiResource('machines/{machine}/models', MachineModelController::class);
    Route::apiResource('machines/{machine}/part-headings', PartHeadingController::class);
    Route::get('machines/part-headings', [PartHeadingController::class, 'filtered']);

    //Parts
    Route::apiResource('parts', PartController::class);
    Route::apiResource('parts/{part}/aliases', PartAliasController::class);
    Route::apiResource('parts/{part}/stocks', PartStockController::class);
    Route::post('parts-import', [PartController::class, 'import']);
    Route::get('/gate-pass-parts', [PartController::class, 'GatePassPart']);

    // Employees routes
    Route::apiResource('employees', EmployeeController::class);

    // WareHouse Route
    Route::apiResource('warehouses', WarehouseController::class);

    // Box Headings Route
    Route::apiResource('box-headings', BoxHeadingController::class);
    Route::get('box-headings/{box}/parts', [BoxHeadingController::class, 'parts']);

    /**
     * Sales Part
     */
    //Requisition route
    Route::get('requisitions/engineers', [RequisitionController::class, 'engineers']);
    Route::get('requisitions/part-headings', [RequisitionController::class, 'partHeadings']);
    Route::get('requisitions/part-items', [RequisitionController::class, 'partItems']); //get Part Items
    Route::apiResource('requisitions', RequisitionController::class);
    Route::post('requisitions/approve/{id}', [RequisitionController::class, 'approve']);

    // Quotation Route
    Route::apiResource('quotations',QuotationController::class);
    Route::post('/quotations/locked', [QuotationController::class, 'Locked']);
    //search invoice
    Route::get('/invoices/search', [InvoiceController::class, 'Search']);
    Route::get('/invoices-part-search', [InvoiceController::class, 'PartSearch']);
    //Invoice Route
    Route::apiResource('invoices',InvoiceController::class);


    //Delivery Notes Route
    Route::apiResource('delivery-notes',DeliveryNotesController::class);

    // Activities Route
    Route::apiResource('activities', ActivityController::class);
    // Activities Route
    Route::apiResource('payment-histories', PaymentHistoryController::class);

    //Report route
    Route::get('/report/sales', [ReportsController::class, 'YearlySales']);
    Route::get('/report/sales/export', [ReportsController::class, 'salesExport']);
    Route::get('/report/stock/export', [ReportsController::class, 'StockHistoryExport']);
    Route::get('/report/monthly/sales', [ReportsController::class, 'MonthlySales']);
    //Stock Histories
    Route::get('/stock-histories', [ReportsController::class, 'StockHistory']);

    //Gate pass
    Route::get('/gate-pass', [GatePassController::class, 'GatePassDetails']);
    //Settings
    Route::apiResource('settings', SettingsController::class)->scoped([
        'only' => ['index', 'store']
    ]);
    //get employees
    Route::get('/get-user', [SettingsController::class, 'getUsers']);


                 ////////////////////////////////////// Client Routes  ////////////////////////////////////////////////////////

    Route::get('/clientmachines/{company}', [ClientMachineController::class, 'show']);
    Route::get('/getmachines/{machine}', [ClientMachineController::class, 'getMachine']);
    Route::get('/clientcontracts/{company}', [ClientContractController::class, 'show']);
    // client machines
    Route::apiResource('client-company-machines', ClientMachineController::class);
    // client contract
    Route::apiResource('client-contract', ClientContractController::class);

    /////////////////////// client requisition start ///////////////////////////
    Route::apiResource('client-requisitions', ClientRequisitionController::class);
    Route::get('/client-company', [CompanyController::class, 'getClientCompany']);
    Route::get('/client-machines', [CompanyController::class, 'getClientMachines']);
    Route::get('/client-parts', [PartController::class, 'getClientPart']);
    //create client req
    Route::post('/create-client-requisitions', [RequisitionController::class, 'storeClientReqisition']);
    /////////////////////// client requisition end ///////////////////////////


    // client quotation
    Route::apiResource('client-quotation', ClientQuotationController::class);
    Route::post('/client-quotation/lock',[ClientQuotationController::class,'quotationLock']);
    // client invoice
    Route::apiResource('client-invoice', ClientInvoiceController::class);
    // client delivery Notes
    Route::apiResource('client-delivery-notes', ClientDeliveryNoteController::class);
    // quotation comment
    Route::apiResource('quotation-comment', QuotationCommentController::class);
    Route::get('/quotation-comment/index/{id}',[QuotationCommentController::class,'quotationComment']);


});



