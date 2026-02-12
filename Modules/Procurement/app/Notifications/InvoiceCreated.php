<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\User\Traits\CheckNotificationSettings;

class InvoiceCreated extends Notification implements ShouldBroadcast, ShouldQueue
{
    use CheckNotificationSettings, Queueable;

    protected $invoice;

    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    public function via(object $notifiable): array
    {
        if (!$this->isNotificationEnabled($notifiable, 'invoice_created')) {
            return [];
        }

        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invoice_created',
            'title' => 'New Invoice Received',
            'message' => 'Vendor ' . ($this->invoice->purchaseOrder->vendorCompany->name ?? 'Vendor') . ' has issued Invoice ' . $this->invoice->invoice_number . ' for PO ' . $this->invoice->purchaseOrder->po_number,
            'url' => route('procurement.invoices.show', $this->invoice->id),
            'action_text' => 'View Invoice',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'po_number' => $this->invoice->purchaseOrder->po_number,
            'vendor_name' => $this->invoice->purchaseOrder->vendorCompany->name ?? 'Vendor',
        ];
    }
}
