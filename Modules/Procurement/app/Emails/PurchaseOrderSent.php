<?php

namespace Modules\Procurement\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderSent extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseOrder;

    /**
     * Create a new message instance.
     */
    public function __construct($purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Purchase Order Received - '.$this->purchaseOrder->po_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.procurement.purchase-order-sent',
            with: [
                'purchaseOrder' => $this->purchaseOrder,
                'vendorName' => $this->purchaseOrder->vendorCompany->name ?? 'Vendor',
                'buyerName' => $this->purchaseOrder->purchaseRequisition->company->name ?? 'Buyer',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
