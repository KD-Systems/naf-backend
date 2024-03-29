<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdvancePaymentController;
use App\Http\Controllers\DuePaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoxHeadingController;
use App\Http\Controllers\ClaimRequisitionController;
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
use App\Http\Controllers\Client\ClientRequiredRequisitionController;
use App\Http\Controllers\Client\ClientClaimRequistionController;
use App\Http\Controllers\Client\ClientQuotationController;
use App\Http\Controllers\Client\ClientInvoiceController;
use App\Http\Controllers\Client\ClientDeliveryNoteController;
use App\Http\Controllers\Client\ClientMachineController;
use App\Http\Controllers\Client\ClientContractController;
use App\Http\Controllers\Client\ClientDashboardController;
use App\Http\Controllers\Client\ClientUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuotationCommentController;
use App\Http\Controllers\RequiredPartRequisitionController;
use App\Http\Controllers\ReturnPartController;
use App\Http\Controllers\TransactionSummeryController;
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

Route::middleware(['auth:sanctum', 'throttle:500,1'])->group(function () {
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
    Route::get('/companies/machines/requisition/{id}', [CompanyMachineController::class, 'getCompanyMachineForRequisition']);

    //Company filse store routes start here
    Route::post('companies/{company}/files', [CompanyController::class, 'uploadFiles']);
    Route::get('companies/{company}/files', [CompanyController::class, 'getFiles']);
    Route::delete('companies/{company}/files/{media:uuid}/delete', [CompanyController::class, 'deleteFiles']);

    //Contracts routes
    Route::apiResource('contracts', ContractController::class);

    //Contracts filse store routes start here
    Route::post('contracts/{contract}/files', [ContractController::class, 'uploadFiles']);
    Route::get('contracts/{contract}/files', [ContractController::class, 'getFiles']);
    Route::delete('contracts/{contract}/files/{media:uuid}/delete', [ContractController::class, 'deleteFiles']);

    //Machines routes
    Route::apiResource('machines', MachineController::class);
    Route::apiResource('machines/{machine}/models', MachineModelController::class);
    Route::apiResource('machines/{machine}/part-headings', PartHeadingController::class);
    Route::get('machines/part-headings', [PartHeadingController::class, 'filtered']);
    Route::get('/all_machines', [MachineController::class, 'allMachines']);

    //Parts
    Route::apiResource('parts', PartController::class);
    Route::apiResource('parts/{part}/aliases', PartAliasController::class);
    Route::apiResource('parts/{part}/stocks', PartStockController::class);
    Route::post('parts-import', [PartController::class, 'import']);
    Route::get('/gate-pass-parts', [PartController::class, 'GatePassPart']);

    //Foc parts
    Route::get('/foc-parts', [PartController::class, 'getFocPart']);
    //Sellable parts
    Route::get('/sellable-parts', [PartController::class, 'sellableParts']);

    // Employees routes
    Route::apiResource('employees', EmployeeController::class);

    // WareHouse Route
    Route::apiResource('warehouses', WarehouseController::class);

    // Box Headings Route
    Route::apiResource('box-headings', BoxHeadingController::class);
    Route::get('box-headings/{box}/parts', [BoxHeadingController::class, 'parts']);
    Route::get('all-box-headings', [BoxHeadingController::class, 'allBoxHeadings']);

    /**
     * Sales Part
     */
    //Requisition route
    Route::get('requisitions/engineers', [RequisitionController::class, 'engineers']);
    Route::get('requisitions/part-headings', [RequisitionController::class, 'partHeadings']);
    Route::get('requisitions/part-items', [RequisitionController::class, 'partItems']); //get Part Items
    Route::apiResource('requisitions', RequisitionController::class);
    Route::post('requisition/{requisition}/files', [RequisitionController::class, 'uploadFiles']);
    Route::get('requisition/{requisition}/files', [RequisitionController::class, 'getFiles']);
    Route::delete('requisition/{requisition}/files/{media:uuid}/delete', [RequisitionController::class, 'deleteFiles']);
    Route::post('requisition/info/{id}', [RequisitionController::class, 'reqInfo']);
    Route::post('requisitions/{requisition}/add-items', [RequisitionController::class, 'addItems']);
    //required requisition
    Route::apiResource('required-part/requisitions', RequiredPartRequisitionController::class);
    Route::post('required-part/requisitions/status/{id}', [RequiredPartRequisitionController::class, 'RequiredRequisitionStatus']);
    Route::post('required-part/requisitions/info/{id}', [RequiredPartRequisitionController::class, 'RequiredRequisitionInfo']);
    Route::get('required-part/requisitions/get-request-parts/{id}', [RequiredPartRequisitionController::class, 'getRequestedPart']);
    Route::post('required-part/requisitions/update-request-parts/{id}', [RequiredPartRequisitionController::class, 'updateRequestedPart']);
    //required files
    Route::post('required-part-req/{id}/files', [RequiredPartRequisitionController::class, 'requiredUploadFiles']);
    Route::get('required-part-req/{id}/files', [RequiredPartRequisitionController::class, 'requiredGetFiles']);
    Route::delete('required-part-req/{id}/files/{media:uuid}/delete', [RequiredPartRequisitionController::class, 'requiredDeleteFiles']);


    // Route::get('client-required-part/requisitions', [RequiredPartRequisitionController::class,'ClientRequiredRequisition']);

    //FOC Management Routes
    Route::get('claim-request', [RequiredPartRequisitionController::class, 'ClaimRequest']);

    //Contracts filse store routes start here
    Route::post('claim-request/{id}/files', [RequiredPartRequisitionController::class, 'uploadFiles']);
    Route::get('claim-request/{id}/files', [RequiredPartRequisitionController::class, 'getFiles']);
    Route::delete('claim-request/{id}/files/{media:uuid}/delete', [RequiredPartRequisitionController::class, 'deleteFiles']);

    Route::get('/claim-requisitions', [ClaimRequisitionController::class, 'index']);
    Route::post('/claim-requisitions', [ClaimRequisitionController::class, 'store']);
    Route::post('/claim-requisitions/update-part-info/{id}', [ClaimRequisitionController::class, 'updateFocPartInfo']);
    Route::get('/claim-requisitions/{id}', [ClaimRequisitionController::class, 'show']);


    //approve requisition
    Route::post('requisitions/approve/{requisition}', [RequisitionController::class, 'approve']);
    //reject requisition
    Route::post('requisitions/reject/{requisition}', [RequisitionController::class, 'reject']);

    // Quotation Route
    Route::apiResource('quotations', QuotationController::class);
    Route::post('/quotations/locked', [QuotationController::class, 'Locked']);
    //approve quotation
    Route::post('quotations/approve/{id}', [QuotationController::class, 'approve']);
    //reject quotation
    Route::post('quotations/reject/{id}', [QuotationController::class, 'reject']);

    //Quotation filse store routes start here
    Route::post('quotations/{quotation}/files', [QuotationController::class, 'uploadFiles']);
    Route::get('quotations/{quotation}/files', [QuotationController::class, 'getFiles']);
    Route::delete('quotations/{quotation}/files/{media:uuid}/delete', [QuotationController::class, 'deleteFiles']);
    Route::post('quotations/{quotation}/add-items', [QuotationController::class, 'addItems']);


    //search invoice
    Route::get('/invoices/search', [InvoiceController::class, 'Search']);
    Route::get('/invoices-part-search', [InvoiceController::class, 'PartSearch']);
    //Invoice Route
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('invoices-delete/{id}', [InvoiceController::class, 'destroy']);
    Route::post('invoice/return-parts', [InvoiceController::class, 'returnParts']);
    Route::post('invoice-update/{id}', [InvoiceController::class, 'InvoiceUpdate']);

    //Invoice filse store routes start here
    Route::post('invoices/{invoice}/files', [InvoiceController::class, 'uploadFiles']);
    Route::get('invoices/{invoice}/files', [InvoiceController::class, 'getFiles']);
    Route::delete('invoices/{invoice}/files/{media:uuid}/delete', [InvoiceController::class, 'deleteFiles']);

    //Delivery Notes Route
    Route::apiResource('delivery-notes', DeliveryNotesController::class);
    Route::get('/delivered-foc-parts', [DeliveryNotesController::class, 'deliveredFocPart']);

    //return part
    Route::apiResource('return-parts', ReturnPartController::class);

    //Delivery Notes filse store routes start here
    Route::post('delivery-notes/{deliveryNote}/files', [DeliveryNotesController::class, 'uploadFiles']);
    Route::get('delivery-notes/{deliveryNote}/files', [DeliveryNotesController::class, 'getFiles']);
    Route::delete('delivery-notes/{deliveryNote}/files/{media:uuid}/delete', [DeliveryNotesController::class, 'deleteFiles']);

    // Activities Route
    Route::apiResource('activities', ActivityController::class);
    // Activities Route
    Route::apiResource('payment-histories', PaymentHistoryController::class);

    //Report route
    Route::get('/report/sales', [ReportsController::class, 'sales']);
    Route::get('/report/sales/export', [ReportsController::class, 'salesExport']);
    Route::get('/report/stock/export', [ReportsController::class, 'StockHistoryExport']);
    Route::get('/report/monthly/sales', [ReportsController::class, 'MonthlySales']);
    Route::get('/report/weekly/sales', [ReportsController::class, 'WeeklySales']);
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

    ////////////////////////////////////// ClienRoutes  /////////////////////////////////////////////////

    Route::get('/client-info', [ClientUserController::class, 'CompanyInfo']);
    Route::get('/client-advance-amount', [ClientUserController::class, 'CompanyAdvanceAmount']);

    Route::get('/clientmachines/{company}', [ClientMachineController::class, 'show']);
    Route::get('/getmachines/{machine}', [ClientMachineController::class, 'getMachine']);
    Route::get('/clientcontracts/{company}', [ClientContractController::class, 'show']);
    // client
    Route::apiResource('company-user', ClientUserController::class);
    // client machines
    Route::apiResource('client-company-machines', ClientMachineController::class);
    // client contract
    Route::apiResource('client-contract', ClientContractController::class);

    /////////////////////// client requisition start ///////////////////////////
    Route::apiResource('client-requisitions', ClientRequisitionController::class);
    //for uploading files
    Route::post('client-requisitions/{requisition}/files', [ClientRequisitionController::class, 'uploadFiles']);
    Route::get('client-requisitions/{requisition}/files', [ClientRequisitionController::class, 'getFiles']);
    Route::delete('client-requisitions/{requisition}/files/{media:uuid}/delete', [ClientRequisitionController::class, 'deleteFiles']);
    Route::get('/client-company', [CompanyController::class, 'getClientCompany']);
    Route::get('/client-company-contract', [CompanyController::class, 'getClientCompanyContract']);
    Route::get('/client-machines', [CompanyController::class, 'getClientMachines']);
    Route::get('/client-parts', [PartController::class, 'getClientPart']);
    //create client req
    Route::post('/create-client-requisitions', [RequisitionController::class, 'storeClientReqisition']);
    //client Required requisition
    Route::apiResource('client-required-part/requisitions', ClientRequiredRequisitionController::class);
    //For Client foc management requisition
    Route::get('/client-claim-request', [ClientClaimRequistionController::class, 'ClientClaimRequest']);
    Route::post('/client-claim-request-create', [ClientClaimRequistionController::class, 'ClientClaimRequestCreate']);
    Route::apiResource('/client-claim-requisition', ClientClaimRequistionController::class);


    /////////////////////// client requisition end ///////////////////////////

    // client quotation
    Route::apiResource('client-quotation', ClientQuotationController::class);
    Route::post('/client-quotation/lock', [ClientQuotationController::class, 'quotationLock']);
    // client invoice
    Route::apiResource('client-invoice', ClientInvoiceController::class);
    // client delivery Notes
    Route::apiResource('client-delivery-notes', ClientDeliveryNoteController::class);
    // quotation comment
    Route::apiResource('quotation-comment', QuotationCommentController::class);
    Route::get('/quotation-comment/index/{id}', [QuotationCommentController::class, 'quotationComment']);

    ///Notification
    Route::apiResource('/notification', NotificationController::class);
    Route::get('/notification/read/{id}', [NotificationController::class, 'notificationRead']);

    Route::get('/permission/get', [ClientUserController::class, 'permission']);

    //for admin dashboard
    Route::get('/sell-purchase', [DashboardController::class, 'sellPurchase']);
    Route::get('/top-selling-product-monthly', [DashboardController::class, 'TopSellingProductMonthly']);
    Route::get('/top-selling-product-yearly', [DashboardController::class, 'TopSellingProductYearly']);
    Route::get('/stock-alert', [DashboardController::class, 'stockAlert']);
    Route::get('/recent-sales', [DashboardController::class, 'RecentSales']);
    Route::get('/top-customers', [DashboardController::class, 'TopCustomers']);

    //for client dashboard
    Route::get('/customer-payment-info', [DashboardController::class, 'CustomerPloayment']);
    Route::get('/all-notification', [NotificationController::class, 'getAll']);

    //advance payment in company
    Route::apiResource('advance-payment', AdvancePaymentController::class);
    Route::apiResource('due-payment', DuePaymentController::class)->only([
        'index', 'store', 'destroy'
    ]);
    //transaction summery
    Route::apiResource('transaction-summery', TransactionSummeryController::class);
    Route::get('transaction-summery-export', [TransactionSummeryController::class, 'TransactionExport']);
    Route::get('/download/stock-alert/parts', [PartStockController::class, 'exportStockAlertParts']);
    // Route::get('com-due', [CompanyController::class, 'ComDue']);

    // Company API
    Route::get('company/dashboard', [ClientDashboardController::class, 'companyDashboard']);
});
