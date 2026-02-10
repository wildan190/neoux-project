<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\User\Traits\CheckNotificationSettings;

class OfferAccepted extends Notification implements ShouldBroadcast, ShouldQueue
{
    use CheckNotificationSettings, Queueable;

    protected $offer;

    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    public function via(object $notifiable): array
    {
        if (! $this->isNotificationEnabled($notifiable, 'new_offers')) { // Using 'new_offers' category for win status as well or can define new one if needed
            return [];
        }

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
