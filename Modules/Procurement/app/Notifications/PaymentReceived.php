<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Procurement\Models\PurchaseOrder;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $purchaseOrder;

    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Received for PO: ' . $this->purchaseOrder->po_number)
            ->greeting('Hello, ' . $notifiable->name)
            ->line('The buyer has completed the payment for Purchase Order: ' . $this->purchaseOrder->po_number)
            ->line('Total Amount: ' . $this->purchaseOrder->formatted_total_amount)
            ->line('Escrow Reference: ' . $this->purchaseOrder->escrow_reference)
            ->action('View Purchase Order', route('procurement.po.show', $this->purchaseOrder))
            ->line('You can now proceed with arranging the shipment.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'payment_received',
            'title' => 'Pembayaran PO Diterima',
            'message' => 'Buyer telah membayar PO ' . $this->purchaseOrder->po_number . '. Dana kini berada di Escrow, silakan atur pengiriman.',
            'url' => route('procurement.po.show', $this->purchaseOrder),
            'action_text' => 'Lihat PO',
            'purchase_order_id' => $this->purchaseOrder->id,
            'po_number' => $this->purchaseOrder->po_number,
        ];
    }
}
