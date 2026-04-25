<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Procurement\Models\Contract;

class ContractSigned extends Notification
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
            ->subject('Contract Signed by Vendor: ' . $this->contract->contract_number)
            ->greeting('Hello, ' . $notifiable->name)
            ->line('The annual contract ' . $this->contract->contract_number . ' has been signed by the vendor: ' . $this->contract->vendor->name)
            ->action('Approve and Activate Contract', route('procurement.contracts.show', $this->contract))
            ->line('You can now perform the final review and activate the contract.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'contract_signed',
            'contract_id' => $this->contract->id,
            'contract_number' => $this->contract->contract_number,
            'vendor_company' => $this->contract->vendor->name,
            'message' => 'Vendor has signed the annual contract.',
            'url' => route('procurement.contracts.show', $this->contract),
        ];
    }
}
