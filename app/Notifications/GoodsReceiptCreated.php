<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GoodsReceiptCreated extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected $goodsReceipt;

    /**
     * Create a new notification instance.
     */
    public function __construct($goodsReceipt)
    {
        $this->goodsReceipt = $goodsReceipt;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'goods_receipt',
            'title' => 'Goods Receipt Created',
            'message' => 'New Goods Receipt ' . $this->goodsReceipt->gr_number . ' has been created.',
            'url' => route('procurement.po.show', $this->goodsReceipt->purchase_order_id), // Link to PO as GR is mostly accessed there
            'action_text' => 'View Receipt',
            'gr_id' => $this->goodsReceipt->id,
            'po_number' => $this->goodsReceipt->purchaseOrder->po_number ?? 'N/A',
        ];
    }
}
