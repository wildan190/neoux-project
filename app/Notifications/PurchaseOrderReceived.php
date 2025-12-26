<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PurchaseOrderReceived extends Notification
{
    use Queueable;

    protected $purchaseOrder;

    public function __construct($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'purchase_order',
            'title' => 'New Purchase Order Received',
            'message' => 'You have received a new Purchase Order: '.$this->purchaseOrder->po_number,
            'url' => route('procurement.po.show', $this->purchaseOrder->id),
            'action_text' => 'View Order',
            'po_id' => $this->purchaseOrder->id,
            'company_name' => $this->purchaseOrder->purchaseRequisition->company->name ?? 'Buyer',
        ];
    }
}
