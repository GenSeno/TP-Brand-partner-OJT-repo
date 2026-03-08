<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quotation</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; color: #333; }
    .container { margin: 20px; }
    .header, .footer { width: 100%; display: flex; justify-content: space-between; }
    .text-end { text-align: right; }
    .fw-bold { font-weight: bold; }
    .text-muted { color: #6c757d; }
    .small { font-size: 12px; }
    .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .bg-light { background-color: #f8f9fa; }
    .border-top { border-top: 1px solid #000; }
    img { max-width: 100%; height: auto; } 
    .table th, .table td { border: 1px solid #ddd; padding: 6px; padding: 5px;vertical-align:middle;white-space:nowrap; }
    .table th { background-color: #f8f9fa; }
    .text-center { text-align:center;}
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
                <h2 style="margin:0; font-weight:bold;">QUOTATION</h2>
                <table style="width:100%; font-size:12px; margin-top:50px; border-collapse:collapse; text-align:left;">
                    <tr>
                        <td style="font-weight:bold; width:40%;">Quotation No.</td>
                        <td>: {{ $quotation->reference }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Date</td>
                        @if (filled($quotation->quoted_at))
                        <td>: {{ $quotation->quoted_at->format('M. d, Y') }}</td>
                        @else
                        <td>: Not Set</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Expected Delivery Date</td>
                        @if (filled($quotation->expected_delivery))
                        <td>: {{ $quotation->expected_delivery->format('M. d, Y') }}</td>
                        @else
                        <td>: Not Set</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Validity</td>
                        @if (filled($quotation->validity_days))
                        <td>: {{ $quotation->validity_days }} Days</td>
                        @else
                        <td>: Not Set</td>
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
    </table>
        <div style="margin-top:20px;line-height:1.5">
            <strong>BILL TO:</strong>
            <div class="small text-muted mt-1" style="margin-top:5px;font-size:12px;">
                <span style="font-size:14px;font-weight:bold;color:#000">{{ $quotation->billingAddress->company_name}}</span><br/>
                {{ $quotation->billingAddress->title}} {{ $quotation->billingAddress->first_name}} {{ $quotation->billingAddress->last_name}}<br>
                {{ $quotation->billingAddress->line1}}, {{ $quotation->billingAddress->line2}}<br>
                {{ $quotation->billingAddress->barangay}}, {{ $quotation->billingAddress->city}}<br>
                {{ $quotation->billingAddress->province}}, {{ $quotation->billingAddress->postcode}}, {{ $quotation->billingAddress->country->name}}<br>
                Email: {{ $quotation->billingAddress->email}} / Mobile: +{{ $quotation->billingAddress->phone}} 
                
            </div>
        </div>
    <div style="width:100%; margin:0 auto; ">
    <table class="table" border="1">
            <thead>
                <tr>
                    <th class="fw-bold" width="5">S/N</th>
                    <th class="fw-bold">Product</th>
                    <th class="fw-bold">Printing Option & Size</th>
                    <th class="fw-bold" width="5">Qty</th>
                    <th class="fw-bold" width="5">UOM</th>
                    <th class="fw-bold" >Price</th>
                    <th class="fw-bold">TOTAL</th>
                </tr>
            </thead>
            <tbody style="font-size:12px;">
                @if($quotation->lines->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">No Item Found</td>
                    </tr>
                @else
                    @php $counter = 0; @endphp
                    @php $t_quantity = 0; @endphp
                    @foreach($lines as $index => $line)
                    @php $counter++; @endphp
                        <tr>
                            <td class="text-center">{{ $counter }}</td>
                            <td style="text-align:left;">
                                <div>
                                    <img src="file://{{ $line['product']['image_url']}}" 
                                        style="width:70px; height:70px; object-fit:contain;">
                                    <div class="small fw-bold">{{ $line['product']['details']['name']  }}</div>
                                </div>
                            </td>
                            <td style="text-align:left;margin-bottom:5px;">
                                @if(!empty($line['withName']) )
                                
                                <div>
                                    <div style="font-weight:bold;"> WITH NAME</div>
                                    @foreach($line['withName'] as $_withName => $withName)
                                        <div style="margin-top:5px;">{{ $_withName }}:{{ $withName['totalQuantity']}} </div>
                                        @if (!empty($withName['namesWithNumbers']))
                                            <div style="margin-bottom:10px;" class="text-muted">
                                            @for ($i = 0; $i < count($withName['namesWithNumbers']); $i++)
                                                <label>{{ $i + 1  }}.)
                                                @if($withName['namesWithNumbers'][$i][0] == null )
                                                    <span><i style="color:red;">Please set name</i></span>
                                                @else
                                                    <span>{{ $withName['namesWithNumbers'][$i][0]}}</span>
                                                @endif
                                                </label><br/>
                                            @endfor
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                @endif
                                @if(!empty($line['noName']) )
                                <div>
                                    <div style="font-weight:bold;margin-top:10px;"> NO NAME</div>
                                    @foreach($line['noName'] as $_noName => $noName)
                                        <div style="margin-bottom:5px;">{{ $_noName }}:{{ $noName['totalQuantity']}} </div>
                                    @endforeach
                                </div>
                                @endif
                            </td>
                            <td class="text-center">
                              @php $t_quantity += $line['totalQuantity']; @endphp   
                            {{ $line['totalQuantity'] }}
                            </td>
                            <td class="text-center">
                                @foreach($line['oum'] as $index => $key )
                                    <span style="font-weight:bold;">{{ $key }}</span><br/>
                                @endforeach
                            </td>
                            <td style="text-align:right;font-family: 'DejaVu Sans', sans-serif;">
                                @if($line['breakdown'] === true )
                                    @for ($i = 0; $i < count($line['prices']); $i++)
                                    <span style="margin-right:5px;">{{ $line['prices'][$i]['size']}}: </span> {{$line['prices'][$i]['price'] }}<br/>
                                    @endfor
                                @else
                                    {{ array_key_first($line['check_prices']) }}
                                @endif
                            </td>
                            <td style="text-align:right; font-family: 'DejaVu Sans', sans-serif;" >
                            {{  $line['price_total_format'] }}
                            </td>
                        </tr>
                    @endforeach 
                    <tr>
                        <td colspan="3" class="fst-italic small mt-1 text-muted"  style="border-left:1px solid white !important;border-bottom:1px solid white !important;">
                            <i>This is a system generated Quotation, no signature is required.</i>
                        </td>
                        <td colspan="1" class="fw-bold bg-light text-center">{{ $t_quantity }}</td>
                        <td colspan="2" class="fw-bold bg-light">Total</td>
                        <td class="text-end fw-bold" style="font-family: 'DejaVu Sans', sans-serif;">{{ $quotation->sub_total->formatted }}</td>
                    </tr>
                    @if(!empty($quotation->shipping_breakdown->items))
                        @if(count($quotation->shipping_breakdown->items) >1  )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td colspan="3" class="bg-light"><b>Shipping Breakdown:</b> <i  style="font-family: 'DejaVu Sans', sans-serif;font-size:10px;">( {{ $quotation->shipping_total->formatted }} )</i></td>
                            <td class="text-end fw-bold"></td>
                        </tr>
                         @endif 
                        @foreach($quotation->shipping_breakdown->items as $index => $key )
                            <tr>
                                <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                <td colspan="3" class="bg-light text-muted">&nbsp;&nbsp;<i>{{ $key->name }}</i></td>
                                <td class="text-end fw-bold"  style="font-family: 'DejaVu Sans', sans-serif;">{{ $key->price->formatted }}</td>
                            </tr>
                        @endforeach 
                        
                    @endif
                    @if(!empty($quotation->tax_breakdown->amounts) )
                        @if(count($quotation->tax_breakdown->amounts) > 1  )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td colspan="3" class="bg-light"><b>Tax Breakdown:</b> <i  style="font-family: 'DejaVu Sans', sans-serif;font-size:10px;">( {{ $quotation->tax_total->formatted }} )</i> </td>
                            <td class="text-end fw-bold"></td>
                        </tr>
                         @endif
                        @foreach($quotation->tax_breakdown->amounts as $index => $key )
                            <tr>
                                <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                <td colspan="3" class="bg-light text-muted">&nbsp;&nbsp;<i>{{ $key->identifier }} @if( $key->percentage )<span class="px-1">({{ $key->percentage }} %)</span>@endif</i></td>
                                <td class="text-end fw-bold"  style="font-family: 'DejaVu Sans', sans-serif;">{{ $key->description }}</td>
                            </tr>
                        @endforeach 
                    @endif
                    @if(!empty($quotation->discount_breakdown['amounts']) )
                        @if(count($quotation->discount_breakdown['amounts']) >1  )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td colspan="3" class="bg-light"><b>Discount Breakdown:</b> <i  style="font-family: 'DejaVu Sans', sans-serif;color:red;font-size:10px;">( -{{ $quotation->discount_total->formatted }} )</i> </td>
                            <td class="text-end fw-bold"></td>
                        </tr>
                         @endif
                        @foreach($quotation->discount_breakdown['amounts'] as $index => $key )
                            <tr>
                                <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                <td colspan="3" class="bg-light text-muted">&nbsp;&nbsp;<i>{{ $key['description'] }}@if( $key['type'] == 'percentage' )<span class="px-1"> ({{ $key['percentage'] }} %)</span>@endif</i></td>
                                <td class="text-end fw-bold" style="color:red;font-family: 'DejaVu Sans', sans-serif;">- {{ $key['price']['formatted'] }}</td>
                            </tr>
                        @endforeach 
                    @endif
                    <tr>
                        <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                        <td colspan="3" class="fw-bold bg-light">Grand Total</td>
                        <td class="text-end fw-bold" style="font-family: 'DejaVu Sans', sans-serif;">{{ $quotation->total->formatted }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <div style="font-size:12px;line-height:1.5;color:#333;margin-top:10px;">
        <span style="font-size:14px;font-weight:bold;">Terms & Conditions</span><br/>
        <strong>Validity:</strong> Prices valid for {{ $quotation->validity_days }} days. By paying the down payment, the client agrees to all terms.<br>
        <strong>Artwork:</strong> Production starts once the final artwork is approved. Revisions after approval may cause delays and extra charges. Slight color variations mayoccur due to 
        screen vs. print differences.<br>
        <strong>Payment:</strong> 50% down payment is required to confirm the order. Remaining 50% must be paid before delivery or pick-up.<br>
        <strong>Lead Time:</strong>  Standard lead time is 7 working days, excluding shipping. Bulk orders may require additional time; an agreed deadline will be set before production.<br>
        <strong>Changes & Cancellation:</strong> No changes allowed once production has started. Downpayment is non-refundable for cancelled orders.<br>
        <strong>Quality:</strong> Issues must be reported within 48 hours of receiving the items.<br>
        <strong>Delivery / Shipping:</strong> Full payment required before shipping or pick-up. Shipping fees are shouldered by the client. TP Ink Lab is not liable for courier delays.<br>
    </div>
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
