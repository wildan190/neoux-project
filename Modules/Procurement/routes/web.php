<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalogue\Http\Controllers\MarketplaceController;
use Modules\Procurement\Http\Controllers\DebitNoteController;
use Modules\Procurement\Http\Controllers\DeliveryOrderController;
use Modules\Procurement\Http\Controllers\GoodsReceiptController;
use Modules\Procurement\Http\Controllers\GoodsReturnRequestController;
use Modules\Procurement\Http\Controllers\InvoiceController;
use Modules\Procurement\Http\Controllers\OfferController;
use Modules\Procurement\Http\Controllers\PurchaseOrderController;
use Modules\Procurement\Http\Controllers\PurchaseRequisitionController;

Route::middleware(['auth', 'company.selected'])->prefix('procurement')->name('procurement.')->group(function () {

    // Marketplace Routes
    Route::prefix('marketplace')->name('marketplace.')->group(function () {
        Route::get('/', [MarketplaceController::class, 'index'])->name('index');
        Route::get('/cart', [MarketplaceController::class, 'viewCart'])->name('cart');
        Route::post('/cart/add', [MarketplaceController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/remove', [MarketplaceController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/checkout', [MarketplaceController::class, 'checkout'])->name('checkout');
        Route::get('/{product}', [MarketplaceController::class, 'show'])->name('show');
    });

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
    Route::get('/po/export-template', [PurchaseOrderController::class, 'exportTemplate'])->name('po.export-template');
    Route::get('/po', [PurchaseOrderController::class, 'index'])->name('po.index');
    Route::get('/po/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('po.show');
    Route::get('/po/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('po.print');
    Route::get('/po/{purchaseOrder}/download-pdf', [PurchaseOrderController::class, 'downloadPdf'])->name('po.download-pdf');
    Route::post('/po/{purchaseOrder}/confirm', [PurchaseOrderController::class, 'confirm'])->name('po.confirm');
    Route::post('/po/{purchaseOrder}/vendor-accept', [PurchaseOrderController::class, 'vendorAccept'])->name('po.vendor-accept');
    Route::post('/po/{purchaseOrder}/vendor-reject', [PurchaseOrderController::class, 'vendorReject'])->name('po.vendor-reject');
    Route::post('/po/import-history', [PurchaseOrderController::class, 'importHistory'])->name('po.import-history');
    Route::post('/po/confirm-import', [PurchaseOrderController::class, 'confirmImport'])->name('po.confirm-import');
    Route::post('/pr/{purchaseRequisition}/generate-po', [PurchaseOrderController::class, 'generate'])->name('po.generate');

    // Goods Receipts
    Route::get('/po/{purchaseOrder}/receive', [GoodsReceiptController::class, 'create'])->name('gr.create');
    Route::post('/po/{purchaseOrder}/receive', [GoodsReceiptController::class, 'store'])->name('gr.store');

    // Delivery Orders (Vendor side)
    Route::prefix('do')->name('do.')->group(function () {
        Route::get('/po/{purchaseOrder}/create', [DeliveryOrderController::class, 'create'])->name('create');
        Route::post('/po/{purchaseOrder}', [DeliveryOrderController::class, 'store'])->name('store');
        Route::post('/{deliveryOrder}/ship', [DeliveryOrderController::class, 'markAsShipped'])->name('ship');
    });
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
        Route::post('/{invoice}/vendor-approve', [InvoiceController::class, 'vendorApprove'])->name('vendor-approve');
        Route::post('/{invoice}/purchasing-approve', [InvoiceController::class, 'purchasingApprove'])->name('purchasing-approve');
        Route::post('/{invoice}/finance-approve', [InvoiceController::class, 'financeApprove'])->name('finance-approve');
        Route::post('/{invoice}/reject', [InvoiceController::class, 'reject'])->name('reject');
    });

    // Offers
    Route::prefix('offers')->name('offers.')->group(function () {
        Route::get('/my-offers', [OfferController::class, 'myOffers'])->name('my');
        Route::get('/pr/{purchaseRequisition}', [OfferController::class, 'index'])->name('index');
        Route::post('/pr/{purchaseRequisition}', [OfferController::class, 'store'])->name('store');
        Route::get('/{offer}', [OfferController::class, 'show'])->name('show');
        Route::post('/{offer}/accept', [OfferController::class, 'accept'])->name('accept');
        Route::post('/{offer}/reject', [OfferController::class, 'reject'])->name('reject');
        // Negotiation Routes
        Route::post('/{offer}/submit-negotiation', [OfferController::class, 'submitNegotiation'])->name('submit-negotiation');
        Route::post('/{offer}/vendor-accept-negotiation', [OfferController::class, 'vendorAcceptNegotiation'])->name('vendor-accept-negotiation');
        Route::post('/{offer}/vendor-reject-negotiation', [OfferController::class, 'vendorRejectNegotiation'])->name('vendor-reject-negotiation');

        Route::post('/{offer}/approve-winner', [OfferController::class, 'approveWinner'])->name('approve-winner');
    });

    // Goods Return Requests (GRR)
    Route::prefix('grr')->name('grr.')->group(function () {
        Route::get('/', [GoodsReturnRequestController::class, 'index'])->name('index');
        Route::post('/', [GoodsReturnRequestController::class, 'store'])->name('store');
        Route::get('/{goodsReturnRequest}', [GoodsReturnRequestController::class, 'show'])->name('show');
        Route::put('/{goodsReturnRequest}/resolution', [GoodsReturnRequestController::class, 'updateResolution'])->name('update-resolution');
        Route::post('/{goodsReturnRequest}/vendor-response', [GoodsReturnRequestController::class, 'vendorResponse'])->name('vendor-response');
        Route::post('/{goodsReturnRequest}/resolve', [GoodsReturnRequestController::class, 'resolve'])->name('resolve');
    });

    // Debit Notes
    Route::prefix('debit-notes')->name('debit-notes.')->group(function () {
        Route::get('/', [DebitNoteController::class, 'index'])->name('index');
        Route::get('/create/{goodsReturnRequest}', [DebitNoteController::class, 'create'])->name('create');
        Route::post('/store/{goodsReturnRequest}', [DebitNoteController::class, 'store'])->name('store');
        Route::get('/{debitNote}', [DebitNoteController::class, 'show'])->name('show');
        Route::get('/{debitNote}/print', [DebitNoteController::class, 'print'])->name('print');
        Route::post('/{debitNote}/approve', [DebitNoteController::class, 'approve'])->name('approve');
    });
});
