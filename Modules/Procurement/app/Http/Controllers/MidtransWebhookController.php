<?php

namespace Modules\Procurement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Procurement\Models\PurchaseOrder;

class MidtransWebhookController extends Controller
{
    public function handleCallback(Request $request)
    {
        $payload = $request->all();
        
        Log::info('Midtrans Webhook Received', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $serverKey = config('services.midtrans.server_key');
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Verify Signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        
        if ($expectedSignature !== $signatureKey) {
            Log::warning('Midtrans Invalid Signature', ['expected' => $expectedSignature, 'actual' => $signatureKey]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Find the Purchase Order
        $purchaseOrder = PurchaseOrder::where('escrow_reference', $orderId)->first();

        if (!$purchaseOrder) {
            Log::error('Purchase Order not found for Midtrans Order ID: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($purchaseOrder->escrow_status !== 'paid') {
                $purchaseOrder->update([
                    'escrow_status' => 'paid',
                    'escrow_paid_at' => now(),
                ]);

                // Notify Vendor
                try {
                    $recipients = collect();
                    
                    if ($purchaseOrder->vendorCompany && $purchaseOrder->vendorCompany->user) {
                        $recipients->push($purchaseOrder->vendorCompany->user);
                    }
                    
                    if ($purchaseOrder->offer && $purchaseOrder->offer->user) {
                        $recipients->push($purchaseOrder->offer->user);
                    }
                    
                    $recipients->unique('id')->each(function ($user) use ($purchaseOrder) {
                        $user->notify(new \Modules\Procurement\Notifications\PaymentReceived($purchaseOrder));
                    });
                } catch (\Exception $e) {
                    Log::error('Failed to send payment notification: ' . $e->getMessage());
                }
            }
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            // Optional: Handle failed payment
            $purchaseOrder->update([
                'escrow_status' => 'failed',
            ]);
        }

        return response()->json(['message' => 'Callback processed successfully']);
    }
}
