<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderConfirmed extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected $purchaseOrder;

    public function __construct($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'purchase_order_confirmed',
            'title' => 'Purchase Order Confirmed',
            'message' => 'Vendor ' . ($this->purchaseOrder->vendorCompany->name ?? 'Vendor') . ' has confirmed Purchase Order ' . $this->purchaseOrder->po_number,
            'url' => route('procurement.po.show', $this->purchaseOrder->id),
            'action_text' => 'View Order',
            'po_id' => $this->purchaseOrder->id,
            'vendor_name' => $this->purchaseOrder->vendorCompany->name ?? 'Vendor',
        ];
    }
}
