<?php

namespace Modules\Procurement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Procurement\Models\Invoice;

class FinalInvoiceIssued extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Final Invoice Issued: ' . $this->invoice->invoice_number)
            ->greeting('Hello, ' . $notifiable->name)
            ->line('The final invoice has been generated for Purchase Order: ' . $this->invoice->purchaseOrder->po_number)
            ->line('Invoice Number: ' . $this->invoice->invoice_number)
            ->line('Total Amount: ' . $this->invoice->formatted_total_amount)
            ->action('View Invoice', route('procurement.invoices.show', $this->invoice))
            ->line('The transaction is now ready for finance processing.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'final_invoice_issued',
            'title' => 'Final Invoice Diterbitkan',
            'message' => 'Final invoice ' . $this->invoice->invoice_number . ' has been issued for PO ' . $this->invoice->purchaseOrder->po_number,
            'url' => route('procurement.invoices.show', $this->invoice),
            'action_text' => 'Lihat Invoice',
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'po_number' => $this->invoice->purchaseOrder->po_number,
        ];
    }
}
