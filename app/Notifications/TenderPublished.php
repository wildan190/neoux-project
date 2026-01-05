<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Modules\User\Application\Traits\CheckNotificationSettings;
use Illuminate\Notifications\Notification;

class TenderPublished extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable, CheckNotificationSettings;

    protected $requisition;

    public function __construct($requisition)
    {
        $this->requisition = $requisition;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Simple check for now, can be expanded to WA later
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Tender Published: ' . $this->requisition->title)
            ->line('A new tender has been published on the platform.')
            ->line('Tender Title: ' . $this->requisition->title)
            ->line('Company: ' . $this->requisition->company->name)
            ->action('View Tender', route('procurement.pr.show-public', $this->requisition->id))
            ->line('You can submit your bids through the platform.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'tender_published',
            'title' => 'New Tender Published',
            'message' => 'A new tender is available: ' . $this->requisition->title,
            'url' => route('procurement.pr.show-public', $this->requisition->id),
            'action_text' => 'View Tender',
            'requisition_id' => $this->requisition->id,
            'company_name' => $this->requisition->company->name,
        ];
    }
}
