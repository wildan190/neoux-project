<?php

namespace Modules\User\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Modules\User\Traits\CheckNotificationSettings;

class NewCommentAdded extends Notification implements ShouldBroadcast, ShouldQueue
{
    use CheckNotificationSettings, Queueable;

    protected $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        if (! $this->isNotificationEnabled($notifiable, 'comments')) {
            return [];
        }

        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_comment',
            'title' => 'New Comment on PR',
            'message' => 'New comment from '.($this->comment->user->name ?? 'someone').' on PR '.($this->comment->purchaseRequisition->pr_number ?? ''),
            'url' => route('procurement.pr.show', $this->comment->purchase_requisition_id),
            'action_text' => 'View Comment',
            'pr_id' => $this->comment->purchase_requisition_id,
        ];
    }

    /**
     * The unique identifier for the notification.
     */
    public function broadcastAs(): string
    {
        return 'Illuminate\Notifications\Events\BroadcastNotificationCreated';
    }
}
