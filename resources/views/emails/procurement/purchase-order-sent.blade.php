@component('mail::message')
# New Purchase Order Received

Hello {{ $vendorName }},

You have received a new Purchase Order (**{{ $purchaseOrder->po_number }}**) from **{{ $buyerName }}**.

## Order Details
**Total Amount:** {{ $purchaseOrder->formatted_total_amount }}

@component('mail::button', ['url' => route('procurement.po.show', $purchaseOrder->id)])
View Purchase Order
@endcomponent

Please ensure to acknowledge this order and proceed with the delivery process.

Thanks,<br>
{{ config('app.name') }}
@endcomponent