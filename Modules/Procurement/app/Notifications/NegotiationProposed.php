<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\User\Traits\CheckNotificationSettings;

class NegotiationProposed extends Notification implements ShouldBroadcast, ShouldQueue
{
    use CheckNotificationSettings, Queueable;

    protected $offer;

    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    public function via(object $notifiable): array
    {
        if (! $this->isNotificationEnabled($notifiable, 'negotiation_updates')) {
            return [];
        }

        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'negotiation_proposed',
            'title' => 'Negotiation Proposal',
            'message' => 'Buyer has proposed new terms for your offer on PR #'.($this->offer->purchaseRequisition->pr_number ?? ''),
            'url' => route('procurement.offers.show', $this->offer->id),
            'action_text' => 'Review Proposal',
            'offer_id' => $this->offer->id,
            'pr_id' => $this->offer->purchase_requisition_id,
        ];
    }
}
