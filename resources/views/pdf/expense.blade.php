<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Voucher</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; color: #333; }
    .container { margin: 20px; }
    .header, .footer { width: 100%; display: flex; justify-content: space-between; }
    .text-end { text-align: right; }
    .fw-bold { font-weight: bold; }
    .text-muted { color: #6c757d; }
    .text-capitalize { text-transform: capitalize; }
    .small { font-size: 12px; }
    .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .bg-light { background-color: #f8f9fa; }
    .border-top { border-top: 1px solid #000; }
    img { max-width: 100%; height: auto; } 
    .table th, .table td { border: 1px solid #ddd; padding: 6px; padding: 5px;vertical-align:middle;white-space:nowrap; }
    .table th { background-color: #f8f9fa; }
    .text-center { text-align:center;}
    .top-border-text {
        display: block;
        border-top: 1px solid #dee2e6;
        padding-top: 6px;
        margin-top: 8px;
        font-weight: 600;
    }

</style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
            <tr>
                <!-- Left: Logo & Company Info -->
                <td style="width:50%; vertical-align:top; line-height:1.5;">
                    <img src="{{ public_path('/img/logo/logo-pdf.png') }}" alt="Company Logo" style="width:160px; margin-bottom:10px;">
                    <div style="font-size:12px; color:#6c757d;">
                        <span style="font-size:16px; font-weight:bold;color:#000">TP Ink Lab Corp.</span><br>
                        109 Roxas Ave, Poblacion District, Davao City<br>
                        8000 Davao del Sur, Philippines<br>
                        Viber / WhatsApp: +63 992 309 0084<br>
                        Email: contact@tpinklab.com / Website: tpinklab.com
                    </div>
                </td>

                <!-- Right: Quotation Info -->
                <td style="width:50%; vertical-align:top; text-align:right; line-height:1.5;">
                    <h2 style="margin:0; font-weight:bold;">PAYMENT VOUCHER</h2>
                    <h3 style="margin:0; font-weight:bold;" class="text-muted">{{ $expense->reference }}</h3>
                </td>
            </tr>
        </table>
        <table style="width:100%; border-collapse:collapse; margin-top:20px;line-height:1.5">
            <tr>
                <!-- Left: Logo & Company Info -->
                <td style="width:50%; vertical-align:top;">
                    <strong>PAY TO:</strong>
                    <div class="small text-muted mt-1" style="margin-top:5px;font-size:12px;">
                        <!-- Supplier Name (required) -->
                        <span style="font-size:14px;font-weight:bold;color:#000">
                            {{ $expense->supplier->name }}
                        </span><br/>

                        <!-- Optional fields -->
                        @if(!empty($expense->supplier->contact_person))
                            {{ $expense->supplier->contact_person }}<br/>
                        @endif

                        @if(!empty($expense->supplier->address))
                            {{ $expense->supplier->address }}<br/>
                        @endif

                        @if(!empty($expense->supplier->city))
                            {{ $expense->supplier->city }}<br/>
                        @endif

                        @if(!empty($expense->supplier->province) || !empty($expense->supplier->postcode) || !empty($expense->supplier->country->name))
                            {{ $expense->supplier->province ?? '' }} 
                            {{ $expense->supplier->postcode ?? '' }} 
                            {{ $expense->supplier->country->name ?? '' }}<br/>
                        @endif

                        @if(!empty($expense->supplier->email) || !empty($expense->supplier->phone))
                            Email: {{ $expense->supplier->email ?? '-' }} / Mobile: {{ !empty($expense->supplier->phone) ? '+' . $expense->supplier->phone : '-' }}
                        @endif
                    </div>
                </td>
                <td style="width:50%; vertical-align:top;">
                    <table class="medium text-muted table-responsive">
                        <tbody>
                            <tr>
                                <td class="fw-bold text-black pr-4">Date</td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td>:  {{ \Carbon\Carbon::parse($expense->expense_date)->format('M. d, Y') }} </td>
                            </tr>
                           <tr>
                                <td class="fw-bold text-black pr-4">Mode of Payment</td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td class="text-capitalize">
                                    : {{ $expense->payment_method === 'bank' ? 'Bank Transfer' : ($expense->payment_method ?? '-') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-black pr-4">Payment Reference</td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td>: {{ $expense->reference_no ?? '-' }} </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-black pr-4">Payment Date</td>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td>:
                                   {{ $expense->payment_date 
                                        ? \Carbon\Carbon::parse($expense->payment_date)->format('M. d, Y') 
                                        : '-' 
                                    }}

                                </td>
                            </tr>
                        </tbody>
                     </table>
                </td>
            </tr>
        </table>
     </table>
    <div style="width:100%; margin:0 auto; ">
    <table class="table" border="1">
            <thead>
                <tr>
                    <th class="fw-bold" width="5%">S/N</th>
                    <th class="fw-bold" width="60%">Item Description</th>
                    <th class="fw-bold" width="5%">Qty</th>
                    <th class="fw-bold" width="15%">Price</th>
                    <th class="fw-bold" width="25%">Total</th>
                </tr>
            </thead>
            <tbody style="font-size:12px;">
                 @if($expense->lines->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center">No Item Found</td>
                    </tr>
                @else
                    @php $counter = 0; @endphp
                    @foreach($expense->lines as $index => $line)
                    @php $counter++; @endphp
                    <tr>
                        <td class="text-center">{{ $counter }}</td>
                        <td class="text-start">
                            {{ $line->expenseAccount->name }}<br/>
                            {{ $line->description }}
                        </td>
                        <td class="text-center">{{ $line->quantity}}</td>
                        <td class="text-end"  style="font-family: 'DejaVu Sans', sans-serif;">{{ $line->price->formatted}}</td>
                        <td class="text-end"  style="font-family: 'DejaVu Sans', sans-serif;">{{ $line->total->formatted}}</td>
                    </tr>
                    @endforeach 
                    <tr>
                        <td colspan="3" class="fst-italic small mt-1 text-muted"  style="border-left:1px solid white !important;border-bottom:1px solid white !important;">
                            <i>This is a system generated, no signature is required.</i>
                        </td>
                        <td class="fw-bold bg-light">Total</td>
                        <td class="text-end fw-bold" style="font-family: 'DejaVu Sans', sans-serif;">{{ $expense->sub_total->formatted }}</td>
                    </tr>
                     @if(!empty($expense->discount_breakdown ) )
                        @if( count($expense->discount_breakdown ) >1 )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td class="bg-light"><b>Discount Breakdown:</b> ( <i  style="font-family: 'DejaVu Sans', sans-serif;font-size:10px;">- {{ $expense->discount_total->formatted }} ) </i> </td>
                            <td class="text-end fw-bold"></td>
                        </tr>
                         @endif
                        @foreach($expense->discount_breakdown as $index => $key )
                            <tr>
                                <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                <td class="bg-light">&nbsp;&nbsp;<i>{{ $key['label'] }}@if( $key['method'] == 'percentage' )<span class="px-1"> ({{ $key['value'] }} %)</span>@endif</i></td>
                                <td class="text-end fw-bold" style="color:red;font-family: 'DejaVu Sans', sans-serif;">- {{ $key['format'] }}</td>
                            </tr>
                        @endforeach 
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td class="fw-bold bg-light">Total Amount Due</td>
                            <td class="text-end fw-bold" style="font-family: 'DejaVu Sans', sans-serif;">{{ $expense->total_amount->formatted }}</td>
                        </tr>
                   @endif
                    <tr>
                        <td colspan="3" class="small mt-1 text-muted"  style="border-left:1px solid white !important;border-bottom:1px solid white !important;border-right:1px solid white !important;">
                            Received the amount stated above:
                        </td>
                        <td colspan="2" style="border-right:1px solid white !important;border-left:1px solid white !important;border-bottom:1px solid white !important;"></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border:1px solid white !important;"></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border:1px solid white !important;"></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border:1px solid white !important;"></td>
                    </tr>
                    <tr>
                        <td colspan="3"  class="text-center small text-muted fs-12" style="border-left:1px solid white !important;border-left:1px solid white !important;border-bottom:1px solid white !important;border-right:1px solid white !important;"> <span class="top-border-text">Date, Full Name & Signature </span> </td>
                        <td colspan="2"  style="border:1px solid white !important;"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    </div>
</body>
</html>
