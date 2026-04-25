<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Procurement\Models\Contract;

class ContractProposed extends Notification
{
    use Queueable;

    protected $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Annual Contract Proposal: ' . $this->contract->contract_number)
            ->greeting('Hello, ' . $notifiable->name)
            ->line('A new annual contract has been proposed to your company by ' . $this->contract->buyer->name)
            ->line('Contract Number: ' . $this->contract->contract_number)
            ->action('Review and Sign Contract', route('procurement.contracts.show', $this->contract))
            ->line('Please review the terms and prices before signing.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'contract_proposed',
            'contract_id' => $this->contract->id,
            'contract_number' => $this->contract->contract_number,
            'buyer_company' => $this->contract->buyer->name,
            'message' => 'New annual contract proposal received.',
            'url' => route('procurement.contracts.show', $this->contract),
        ];
    }
}
