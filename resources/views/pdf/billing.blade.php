<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing Statement - {{ $invoice->reference }}</title>
    <style>
        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f8f9fa; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #6c757d; }
        .bg-light { background: #f8f9fa; }
        .no-border { border-left:1px solid white !important;border-bottom:1px solid white !important }
        .no-border-all { border:1px solid white !important; }
        .no-border-side { border-right:1px solid white !important;border-left:1px solid white !important;}
        .footer-powered { 
            display: flex;
            align-items: center;      /* vertical center */
            justify-content: center;  /* horizontal center */
            gap: 8px;                 /* space between text and logo */
            font-size: 12px;    
            float:right;
        }
        .footer-divider span { background: #fff; padding: 0 10px; position: relative; z-index:1; text-align:center }
        .footer-divider {
            position: relative;
            height: 20px; /* adjust spacing */
            text-align:center;
            margin-top:10px;
        }

        .divider-line {
            border-top: 1px solid #ccc;
            width: 100%;
            position: absolute;
            top: 50%;
            left: 0;
        }
        .text-danger {
            color:red;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
        <tr>
            <td width="50%" valign="top" class="no-border-all">
                <img src="{{ public_path('img/logo/logo-pdf.png') }}" width="160" style="display:block; margin-bottom:10px;">
            </td>
            <td width="50%" valign="top" align="right" class="no-border-all">
                <h2 style="margin:0;">BILLING STATEMENT</h2>
                <p style="color:#6c757d; margin:5px 0 0;">{{ $invoice->reference }}</p>
            </td>
        </tr>
    </table>

    <!-- COMPANY INFO & DETAILS -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 15px;">
        <tr>
            <td width="50%" valign="top" style="border:none;">
                <strong style="font-size:14px;">TP Ink Lab Corp.</strong><br>
                109 Roxas Ave, Poblacion District, Davao City<br>
                8000 Davao del Sur, Philippines<br>
                Viber / WhatsApp: +63 992 309 0084<br>
                Email: contact@tpinklab.com · Website: tpinklab.com
            </td>
            <td width="50%" valign="top" style="border:none;">
                <table width="100%" cellpadding="4" cellspacing="0" border="0">
                    <tr>
                        <td style="border:none;"><strong>Bill Date</strong></td>
                        <td style="border:none;">: {{ optional($invoice->invoiced_at)->format('d M Y') ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="border:none;"><strong>Due Date</strong></td>
                        <td style="border:none;">: {{ optional($invoice->due_at)->format('d M Y') ?? '—' }}</td>
                    </tr>
                    @if($invoice->order?->reference)
                    <tr>
                        <td style="border:none;"><strong>Sales Order No.</strong></td>
                        <td style="border:none;">: {{ $invoice->order->reference }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- ADDRESSES -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 15px;">
        <tr>
            <td width="50%" valign="top" style="border:none;">
                <p style="color:#6c757d; margin:0 0 5px;">BILL TO:</p>
                @if($invoice->billingAddress)
                <strong>{{ $invoice->billingAddress->company_name }}</strong><br>
                {{ $invoice->billingAddress->full_name }}<br>
                @if($invoice->billingAddress->line1)
                {{ $invoice->billingAddress->line1 }}@if($invoice->billingAddress->line2), {{ $invoice->billingAddress->line2 }}@endif<br>
                @endif
                @if($invoice->billingAddress->city)
                {{ $invoice->billingAddress->barangay }}, {{ $invoice->billingAddress->city }}@if($invoice->billingAddress->province), {{ $invoice->billingAddress->province }}@endif<br>
                @endif
                @else
                <span style="color:#6c757d;">No billing address</span>
                @endif
            </td>
            <td width="50%" valign="top" style="border:none;">
                <p style="color:#6c757d; margin:0 0 5px;">SHIP TO:</p>
                @if($invoice->shippingAddress)
                <strong>{{ $invoice->shippingAddress->company_name }}</strong><br>
                {{ $invoice->shippingAddress->full_name }}<br>
                @if($invoice->shippingAddress->line1)
                {{ $invoice->shippingAddress->line1 }}@if($invoice->shippingAddress->line2), {{ $invoice->shippingAddress->line2 }}@endif<br>
                @endif
                @if($invoice->shippingAddress->city)
                {{ $invoice->shippingAddress->barangay }}, {{ $invoice->shippingAddress->city }}@if($invoice->shippingAddress->province), {{ $invoice->shippingAddress->province }}@endif<br>
                @endif
                @elseif($invoice->billingAddress)
                <em style="color:#6c757d;">(Same as billing address)</em>
                @endif
            </td>
        </tr>
    </table>

    <!-- LINE ITEMS TABLE -->
    <table width="100%" cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse; margin-bottom:10px;">
        <thead>
            <tr style="background:#f8f9fa;">
                <th style="width:40px;">S/N</th>
                <th>Product</th>
                <th>Printing Option & Size</th>
                <th style="width:60px;">Qty</th>
                <th style="width:60px;">UOM</th>
                <th style="width:80px;">Price</th>
                <th style="width:100px;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $index => $line)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $line->product_name }}</strong></td>
                <td>
                    @if($line->options_payload)
                    @foreach($line->options_payload as $option)
                    <strong>{{ $option['printing_option'] ?? '' }}</strong><br>
                    @foreach($option['items'] ?? [] as $item)
                    {{ $item['size'] ?? '' }}: {{ $item['quantity'] ?? '' }}<br>
                    @endforeach
                    @endforeach
                    @endif
                </td>
                <td class="text-center">{{ $line->quantity }}</td>
                <td class="text-center">{{ $line->uom_code ?? '-' }}</td>
                <td class="text-end" style="font-family: 'DejaVu Sans', sans-serif;">{{ $line->unit_price->formatted ?? 'N/A' }}</td>
                <td class="text-end" style="font-family: 'DejaVu Sans', sans-serif;">{{ $line->total->formatted ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $hasShipping = $invoice->order?->shipping_total && $invoice->order->shipping_total->value > 0;
            @endphp

           <tr>
                <td colspan="4" class="no-border" style="font-style:italic; color:#6c757d; font-size:11px;">
                    This is a system generated billing statement, no signature is required.
                </td>
                <td colspan="2" class="bg-light fw-bold">TOTAL</td>
                <td class="text-end fw-bold" style="font-family: 'DejaVu Sans', sans-serif;">{{ $invoice->total->formatted }}</td>
            </tr>
            <tr>
                <td colspan="4" class="no-border no-border-side"></td>
                <td colspan="3" class="border-0 no-border-side">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="4" class="no-border"></td>
                <td colspan="2" class="bg-light">{{ $invoice->description }}</td>
                <td class="text-end" style="font-family: 'DejaVu Sans', sans-serif;">{{ $invoice->amount_due->formatted }}</td>
            </tr>
            @if($invoice->shipping_breakdown && $invoice->shipping_breakdown->items->isNotEmpty())
                    @foreach($invoice->shipping_breakdown->items as $item)
                        <tr>
                            <td colspan="4" class="no-border"></td>

                            <td colspan="2" class="bg-light">
                                Add on: {{ $item->name }}
                            </td>

                            <td class="text-end" style="font-family: 'DejaVu Sans', sans-serif;">
                                {{ $item->price->formatted }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            <tr>
                <td colspan="4" class="no-border"></td>
                <td colspan="2" class="bg-light">Total Amount Due</td>
                <td class="text-end" style="font-family: 'DejaVu Sans', sans-serif;">{{ $invoice->summary['with_shipping']->formatted }}</td>
            </tr>

            <tr>
                <td colspan="4" class="no-border no-border-side"></td>
                <td colspan="3" class="border-0 no-border-side">
                    &nbsp;
                </td>
            </tr>

            @if(!empty($billing) && count($billing) > 0)
                <tr>
                    <td colspan="4" class="no-border"></td>
                    <td colspan="3" class="border-0">
                       Billing Breakdown
                    </td>
                </tr>
                @foreach($billing as $bill)
                    <tr>
                        
                        <td colspan="4" class="no-border"></td>
                        <td colspan="2" class="bg-light">
                            Billing No.:  {{ $bill->reference }}
                        </td>
                        <td class="text-end" style="font-family: 'DejaVu Sans', sans-serif;">
                            {{ $bill->summary['amount_paid']->formatted ?? '' }}
                        </td>
                    </tr>
                @endforeach
                @if($invoice->type->value !== 'down-payment')
                    <tr>
                        <td colspan="4" class="no-border"></td>
                        <td colspan="2" class="fw-bold bg-light">
                            Total Amount Billed
                        </td>
                        <td class="text-end fw-bold" style="font-family: 'DejaVu Sans', sans-serif;">
                            {{ $totalBilled->formatted ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="no-border"></td>
                        <td colspan="2" class="bg-light text-danger">
                            Unbilled Amount
                        </td>
                        <td class="text-end text-danger" style="font-family: 'DejaVu Sans', sans-serif;">
                            {{ $unbilledAmount->formatted ?? '' }}
                        </td>
                    </tr>
                @endif
             @endif
        </tfoot>
    </table>

    <!-- PAYMENT INFO -->
    <div style="font-size:12px; line-height:1.6; margin-top:15px;">
        <strong>HOW TO MAKE PAYMENT?</strong><br>
        TP Ink Lab uses Xendit, a secure and trusted payment platform, to provide multiple convenient payment options for our clients.<br><br>
        <strong>Step 1:</strong> Click the Payment Link: LINKHERE.COM<br>
        <strong>Step 2:</strong> Choose Your Preferred Payment Method<br>
        <strong>Step 3:</strong> Follow the Instructions on the Screen<br>
        <strong>Step 4:</strong> Payment Confirmation<br><br>
        Once payment is completed, you will automatically receive a confirmation email or SMS from Xendit. TP Ink Lab will also be notified, and we will begin processing your order once payment is verified.<br><br>

        <strong>Terms & Conditions</strong><br>
        1. Start of Production: We begin working on your order once your payment or down payment has been confirmed.<br>
        2. Down Payments: Down payments are non-refundable once materials are prepared or production has started.<br>
        3. Remaining Balance: Any balance must be settled before pickup or delivery of your finished items.<br>
        4. Late Payments: Unpaid invoices beyond the due date may be subject to service charges or interest fee as applicable.<br>
        5. Order Cancellation: Orders cancelled after artwork approval or material preparation may be subject to charges for labor and materials already used.
    </div>

    <!-- FOOTER -->
    <div class="footer-divider">
                <div class="divider-line"></div>
                <span>Thank you for your business, we hope to work with you again!</span>
                <div class="divider-line"></div>
        </div>
        <div class="footer-powered">
                <span style="vertical-align: middle;">Powered by</span>
                <img src="{{public_path('/img/tech-hive-logo-black-font.png')}}" alt="Tech Hive" class="footer-logo" style="width:100px;margin-top:10px;" />
            </div>
    </div>
</body>
</html>
