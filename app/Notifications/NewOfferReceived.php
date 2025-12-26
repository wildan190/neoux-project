<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOfferReceived extends Notification
{
    use Queueable;

    protected $offer;

    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_offer',
            'title' => 'New Offer Received',
            'message' => 'Your Purchase Requisition '.($this->offer->purchaseRequisition->pr_number ?? '').' received a new offer from '.($this->offer->company->name ?? 'a vendor'),
            'url' => route('procurement.pr.show', $this->offer->purchase_requisition_id),
            'action_text' => 'View Offer',
            'offer_id' => $this->offer->id,
            'pr_id' => $this->offer->purchase_requisition_id,
        ];
    }
}
