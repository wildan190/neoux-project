<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\User\Traits\CheckNotificationSettings;
use Illuminate\Notifications\Notification;

class NegotiationAccepted extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable, CheckNotificationSettings;

    protected $offer;

    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    public function via(object $notifiable): array
    {
        if (!$this->isNotificationEnabled($notifiable, 'negotiation_updates')) {
            return [];
        }

        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'negotiation_accepted',
            'title' => 'Negotiation Accepted',
            'message' => 'Vendor ' . ($this->offer->company->name ?? 'Vendor') . ' accepted your negotiation terms for PR #' . ($this->offer->purchaseRequisition->pr_number ?? ''),
            'url' => route('procurement.offers.show', $this->offer->id),
            'action_text' => 'View Offer',
            'offer_id' => $this->offer->id,
            'pr_id' => $this->offer->purchase_requisition_id,
        ];
    }
}
