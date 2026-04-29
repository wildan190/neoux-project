<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Procurement\Models\PurchaseOrder;

class MidtransController extends Controller
{
    /**
     * Handle the frontend redirect after a Midtrans payment.
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');
        $transactionStatus = $request->query('transaction_status');
        $statusCode = $request->query('status_code');

        $purchaseOrder = null;

        if ($orderId) {
            $purchaseOrder = PurchaseOrder::where('escrow_reference', $orderId)->first();
        }

        return view('procurement::midtrans.finish', compact('purchaseOrder', 'transactionStatus', 'statusCode'));
    }
}
