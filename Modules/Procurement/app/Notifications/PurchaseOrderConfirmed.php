<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\User\Traits\CheckNotificationSettings;

class PurchaseOrderConfirmed extends Notification implements ShouldBroadcast, ShouldQueue
{
    use CheckNotificationSettings, Queueable;

    protected $purchaseOrder;

    public function __construct($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    public function via(object $notifiable): array
    {
        if (! $this->isNotificationEnabled($notifiable, 'po_confirmed')) {
            return [];
        }

        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'purchase_order_confirmed',
            'title' => 'Purchase Order Confirmed',
            'message' => 'Vendor '.($this->purchaseOrder->vendorCompany->name ?? 'Vendor').' has confirmed Purchase Order '.$this->purchaseOrder->po_number,
            'url' => route('procurement.po.show', $this->purchaseOrder->id),
            'action_text' => 'View Order',
            'po_id' => $this->purchaseOrder->id,
            'vendor_name' => $this->purchaseOrder->vendorCompany->name ?? 'Vendor',
        ];
    }
}
