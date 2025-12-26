<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OfferAccepted extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    protected $offer;

    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'offer_accepted',
            'title' => 'Bid Won!',
            'message' => 'Your offer for PR '.($this->offer->purchaseRequisition->pr_number ?? '').' has been accepted.',
            'url' => route('procurement.offers.show', $this->offer->id),
            'action_text' => 'View Details',
            'offer_id' => $this->offer->id,
        ];
    }
}
