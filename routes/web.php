<?php

use App\Http\Controllers\NotificationController;
use App\Modules\Procurement\Presentation\Http\Controllers\DebitNoteController;
use App\Modules\Procurement\Presentation\Http\Controllers\GoodsReceiptController;
use App\Modules\Procurement\Presentation\Http\Controllers\GoodsReturnRequestController;
use App\Modules\Procurement\Presentation\Http\Controllers\InvoiceController;
use App\Modules\Procurement\Presentation\Http\Controllers\OfferController;
use App\Modules\Procurement\Presentation\Http\Controllers\PurchaseOrderController;
use App\Modules\Procurement\Presentation\Http\Controllers\PurchaseRequisitionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Invitation (Public/Auth) - Moved here to allow guest access
Route::get('/invitation/{token}', [\App\Modules\Company\Presentation\Http\Controllers\TeamController::class, 'acceptInvitation'])->name('team.accept-invitation');
Route::post('/invitation/process', [\App\Modules\Company\Presentation\Http\Controllers\TeamController::class, 'processAcceptInvitation'])->name('team.process-acceptance');

Route::middleware('auth')->group(function () {
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatestNotifications'])->name('notifications.latest');
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/select-company/{company}', [\App\Http\Controllers\DashboardController::class, 'selectCompany'])->name('dashboard.select-company');
    Route::get('/company-dashboard', [\App\Modules\Company\Presentation\Http\Controllers\CompanyDashboardController::class, 'index'])->name('company.dashboard');

    Route::get('/companies', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'show'])->name('companies.show');
    Route::get('/companies/{company}/edit', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}', [\App\Modules\Company\Presentation\Http\Controllers\CompanyController::class, 'update'])->name('companies.update');

    Route::prefix('catalogue')->name('catalogue.')->middleware('company.selected')->group(function () {
        Route::get('/import/template', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'downloadTemplate'])->name('download-template');
        Route::post('/import/preview', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'previewImport'])->name('import.preview');
        Route::post('/import', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'import'])->name('import');
        Route::get('/import/status/{id}', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'checkImportStatus'])->name('import.status');

        Route::post('/bulk-delete', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'bulkDelete'])->name('bulk-delete');

        Route::get('/', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'index'])->name('index');
        Route::get('/create', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'create'])->name('create');
        Route::post('/', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'store'])->name('store');
        Route::get('/{product}', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'show'])->name('show');
        Route::post('/{product}/sku', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'storeSku'])->name('store-sku');
        Route::get('/{product}/edit', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'edit'])->name('edit');
        Route::put('/{product}', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'update'])->name('update');
        Route::delete('/{product}', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'destroy'])->name('destroy');
        Route::post('/generate-sku', [\App\Modules\Catalogue\Presentation\Http\Controllers\CatalogueController::class, 'generateSku'])->name('generate-sku');
    });

    // Team Management
    Route::prefix('team')->name('team.')->middleware('company.selected')->group(function () {
        Route::get('/', [\App\Modules\Company\Presentation\Http\Controllers\TeamController::class, 'index'])->name('index');
        Route::post('/invite', [\App\Modules\Company\Presentation\Http\Controllers\TeamController::class, 'invite'])->name('invite');
        Route::post('/{user}/remove', [\App\Modules\Company\Presentation\Http\Controllers\TeamController::class, 'removeMember'])->name('remove');
        Route::put('/{user}/role', [\App\Modules\Company\Presentation\Http\Controllers\TeamController::class, 'updateRole'])->name('update-role');
    });

    // Warehouse Routes - Top Level for Sidebar Access
    Route::middleware('company.selected')->group(function () {
        Route::get('/warehouse', [\App\Modules\Catalogue\Presentation\Http\Controllers\WarehouseController::class, 'index'])->name('warehouse.index');
        Route::get('/warehouse/scan', [\App\Modules\Catalogue\Presentation\Http\Controllers\WarehouseController::class, 'scan'])->name('warehouse.scan');
        Route::post('/warehouse/scan', [\App\Modules\Catalogue\Presentation\Http\Controllers\WarehouseController::class, 'processScan'])->name('warehouse.process-scan');
        Route::post('/warehouse/adjust', [\App\Modules\Catalogue\Presentation\Http\Controllers\WarehouseController::class, 'adjustStock'])->name('warehouse.adjust');
        Route::get('/warehouse/report', [\App\Modules\Catalogue\Presentation\Http\Controllers\WarehouseReportController::class, 'index'])->name('warehouse.report');
        Route::get('/warehouse/qr/{id}', [\App\Modules\Catalogue\Presentation\Http\Controllers\WarehouseController::class, 'generateQr'])->name('warehouse.generate-qr');
    });

    Route::prefix('procurement')->name('procurement.')->middleware('company.selected')->group(function () {

        Route::prefix('pr')->name('pr.')->group(function () {
            Route::get('/', [PurchaseRequisitionController::class, 'index'])->name('index');
            Route::get('/my-requests', [PurchaseRequisitionController::class, 'myRequests'])->name('my-requests');
            Route::get('/public-feed', [PurchaseRequisitionController::class, 'publicFeed'])->name('public-feed');
            Route::get('/create', [PurchaseRequisitionController::class, 'create'])->name('create');
            Route::post('/', [PurchaseRequisitionController::class, 'store'])->name('store');
            Route::get('/public/{purchaseRequisition}', [PurchaseRequisitionController::class, 'showPublic'])->name('show-public');
            Route::get('/{purchaseRequisition}', [PurchaseRequisitionController::class, 'show'])->name('show');
            Route::get('/documents/{document}/download', [PurchaseRequisitionController::class, 'downloadDocument'])->name('download-document');
            Route::post('/{purchaseRequisition}/comment', [PurchaseRequisitionController::class, 'addComment'])->name('add-comment');

            // Approval Routes
            Route::post('/{purchaseRequisition}/submit-approval', [PurchaseRequisitionController::class, 'submitForApproval'])->name('submit-approval');
            Route::post('/{purchaseRequisition}/approve', [PurchaseRequisitionController::class, 'approve'])->name('approve');
            Route::post('/{purchaseRequisition}/reject', [PurchaseRequisitionController::class, 'reject'])->name('reject');
            Route::post('/{purchaseRequisition}/assign', [PurchaseRequisitionController::class, 'assign'])->name('assign');
        });

        // Purchase Orders
        Route::get('/po', [PurchaseOrderController::class, 'index'])->name('po.index');
        Route::get('/po/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('po.show');
        Route::get('/po/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('po.print');
        Route::get('/po/{purchaseOrder}/download-pdf', [PurchaseOrderController::class, 'downloadPdf'])->name('po.download-pdf');
        Route::post('/pr/{purchaseRequisition}/generate-po', [PurchaseOrderController::class, 'generate'])->name('po.generate');

        // Goods Receipts
        Route::get('/po/{purchaseOrder}/receive', [GoodsReceiptController::class, 'create'])->name('gr.create');
        Route::post('/po/{purchaseOrder}/receive', [GoodsReceiptController::class, 'store'])->name('gr.store');
        Route::get('/gr/{id}/print', [GoodsReceiptController::class, 'print'])->name('gr.print');
        Route::get('/gr/{id}/download-pdf', [GoodsReceiptController::class, 'downloadPdf'])->name('gr.download-pdf');

        // Invoice routes
        Route::prefix('invoices')->as('invoices.')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
            Route::get('/{invoice}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('download-pdf');
            Route::post('/{invoice}/issue-tax-invoice', [InvoiceController::class, 'issueTaxInvoice'])->name('issue-tax-invoice');
            Route::get('/{invoice}/tax-invoice-print', [InvoiceController::class, 'printTaxInvoice'])->name('tax-invoice-print');
            Route::get('/{invoice}/tax-invoice-pdf', [InvoiceController::class, 'downloadTaxInvoicePdf'])->name('tax-invoice-pdf');
            Route::get('po/{purchaseOrder}/create-invoice', [InvoiceController::class, 'create'])->name('create');
            Route::post('po/{purchaseOrder}/create-invoice', [InvoiceController::class, 'store'])->name('store');
        });

        // Offers
        Route::prefix('offers')->name('offers.')->group(function () {
            Route::get('/my-offers', [OfferController::class, 'myOffers'])->name('my');
            Route::get('/pr/{purchaseRequisition}', [OfferController::class, 'index'])->name('index');
            Route::post('/pr/{purchaseRequisition}', [OfferController::class, 'store'])->name('store');
            Route::get('/{offer}', [OfferController::class, 'show'])->name('show');
            Route::post('/{offer}/accept', [OfferController::class, 'accept'])->name('accept');
            Route::post('/{offer}/reject', [OfferController::class, 'reject'])->name('reject');
        });

        // Goods Return Requests (GRR) - for damaged/rejected items
        Route::prefix('grr')->name('grr.')->group(function () {
            Route::get('/', [GoodsReturnRequestController::class, 'index'])->name('index');
            Route::post('/', [GoodsReturnRequestController::class, 'store'])->name('store');
            Route::get('/{goodsReturnRequest}', [GoodsReturnRequestController::class, 'show'])->name('show');
            Route::put('/{goodsReturnRequest}/resolution', [GoodsReturnRequestController::class, 'updateResolution'])->name('update-resolution');
            Route::post('/{goodsReturnRequest}/vendor-response', [GoodsReturnRequestController::class, 'vendorResponse'])->name('vendor-response');
            Route::post('/{goodsReturnRequest}/resolve', [GoodsReturnRequestController::class, 'resolve'])->name('resolve');
        });

        // Debit Notes - for price adjustments
        Route::prefix('debit-notes')->name('debit-notes.')->group(function () {
            Route::get('/', [DebitNoteController::class, 'index'])->name('index');
            Route::get('/create/{goodsReturnRequest}', [DebitNoteController::class, 'create'])->name('create');
            Route::post('/store/{goodsReturnRequest}', [DebitNoteController::class, 'store'])->name('store');
            Route::get('/{debitNote}', [DebitNoteController::class, 'show'])->name('show');
            Route::get('/{debitNote}/print', [DebitNoteController::class, 'print'])->name('print');
            Route::post('/{debitNote}/approve', [DebitNoteController::class, 'approve'])->name('approve');
        });

        // Marketplace / Non-Tender Procurement
        Route::prefix('marketplace')->name('marketplace.')->group(function () {
            Route::get('/', [\App\Modules\Catalogue\Presentation\Http\Controllers\MarketplaceController::class, 'index'])->name('index');
            Route::get('/cart', [\App\Modules\Catalogue\Presentation\Http\Controllers\MarketplaceController::class, 'viewCart'])->name('cart');
            Route::post('/cart/add', [\App\Modules\Catalogue\Presentation\Http\Controllers\MarketplaceController::class, 'addToCart'])->name('cart.add');
            Route::post('/cart/remove', [\App\Modules\Catalogue\Presentation\Http\Controllers\MarketplaceController::class, 'removeFromCart'])->name('cart.remove');
            Route::post('/checkout', [\App\Modules\Catalogue\Presentation\Http\Controllers\MarketplaceController::class, 'checkout'])->name('checkout');
            Route::get('/{product}', [\App\Modules\Catalogue\Presentation\Http\Controllers\MarketplaceController::class, 'show'])->name('show');
        });
    });

    Route::get('/profile', [\App\Modules\User\Presentation\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/details', [\App\Modules\User\Presentation\Http\Controllers\ProfileController::class, 'updateDetails'])->name('profile.details.update');
    Route::post('/profile/photo', [\App\Modules\User\Presentation\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [\App\Modules\User\Presentation\Http\Controllers\ProfileController::class, 'deletePhoto'])->name('profile.photo.delete');
});
