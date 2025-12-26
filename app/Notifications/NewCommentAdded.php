<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentAdded extends Notification
{
    use Queueable;

    protected $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_comment',
            'title' => 'New Comment on PR',
            'message' => 'New comment from '.($this->comment->user->name ?? 'someone').' on PR '.($this->comment->purchaseRequisition->pr_number ?? ''),
            'url' => route('procurement.pr.show', $this->comment->purchase_requisition_id),
            'action_text' => 'View Comment',
            'comment_id' => $this->comment->id,
            'pr_id' => $this->comment->purchase_requisition_id,
        ];
    }
}
