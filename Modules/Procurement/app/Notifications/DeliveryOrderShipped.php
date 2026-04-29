<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Procurement\Models\DeliveryOrder;

class DeliveryOrderShipped extends Notification implements ShouldQueue
{
    use Queueable;

    protected $deliveryOrder;

    public function __construct(DeliveryOrder $deliveryOrder)
    {
        $this->deliveryOrder = $deliveryOrder;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Delivery Order Shipped: ' . $this->deliveryOrder->do_number)
            ->greeting('Hello, ' . $notifiable->name)
            ->line('The vendor has shipped your order and issued a Delivery Order: ' . $this->deliveryOrder->do_number)
            ->line('Purchase Order: ' . $this->deliveryOrder->purchaseOrder->po_number)
            ->line('Tracking Number: ' . $this->deliveryOrder->tracking_number)
            ->action('Sign Delivery Order', route('procurement.po.show', $this->deliveryOrder->purchaseOrder))
            ->line('Please sign the delivery order once you have received and inspected the items.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'do_shipped',
            'title' => 'Delivery Order Siap Ditandatangani',
            'message' => 'Pesanan ' . $this->deliveryOrder->do_number . ' telah dikirim. Silakan tanda tangani setelah barang diterima.',
            'url' => route('procurement.po.show', $this->deliveryOrder->purchaseOrder),
            'action_text' => 'Tanda Tangani DO',
            'do_id' => $this->deliveryOrder->id,
            'do_number' => $this->deliveryOrder->do_number,
            'po_number' => $this->deliveryOrder->purchaseOrder->po_number,
        ];
    }
}
