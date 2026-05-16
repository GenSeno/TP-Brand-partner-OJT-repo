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
    .table th, .table td { border: 1px solid #ddd; padding: 6px; padding: 10px 20px;vertical-align:middle;line-height:1.462;white-space:nowrap; }
    .table th { background-color: #f8f9fa; }
    .bg-light { background-color: #f8f9fa; }
    .border-top { border-top: 1px solid #000; }
    img { max-width: 100%; height: auto; } 
    .footer-powered { display: flex; justify-content: flex-end; align-items: center; gap: 10px; font-size: 0.85rem; color: #333; margin-bottom: 10px; }
    .footer-divider { display: flex; align-items: center; justify-content: center; font-size: 0.85rem; color: #333; margin: 20px 0; position: relative; }
    .footer-divider span { background: #fff; padding: 0 10px; position: relative; z-index:1; }
    .footer-divider::before { content: ''; position: absolute; top:50%; left:0; width:100%; border-top:1px solid #ccc; z-index:0; }
    .pdf-preview {
        background: #e5e7eb;       
        min-height: 100vh;
        padding: 10px 0;
        display: flex;
        justify-content: center;
        width: 100%;
    }
    .pdf-page {
    background: #ffffff;
    padding: 20mm;
    font-size: 12px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }
    th {
    border: 1px solid #ddd;
    padding: 8px;
    }
</style>
</head>
<body style="display:block; margin:0 auto;">
      <div class="pdf-preview" >
    <div class="pdf-page" >
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div style="width:50%;line-height:1.5">
                <img src="{{ public_path('/img/logo/logo_pakaras_white.png') }}" alt="Company Logo" style="width:160px;margin-bottom:10px;">
                <div class="small text-muted" >
                    <span style="font-size:16px;font-weight:bold;">TP Ink Lab Corp.</span><br>
                    109 Roxas Ave, Poblacion District, Davao City <br>
                    8000 Davao del Sur, Philippines <br>
                    Viber / WhatsApp: +63 992 309 0084 <br>
                    Email: contact@tpinklab.com / Website: tpinklab.com<br>
                </div>
            </div>
            <div style="width:50%; text-align:right;line-height:1.5">
                <h2 class="fw-bold">QUOTATION</h2>
                <table class="small" style="width:100%; text-align:left;padding-top:20px;">
                    <tr>
                        <td class="fw-bold" width="30%">Quotation No.</td>
                        <td>: {{$quotation->reference}}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Date</td>
                        @if (filled($quotation->quoted_at))
                        <td>: {{$quotation->quoted_at->format('M. d, Y')}}</td>
                        @else
                        <td>: Not Set</td>
                        @endif
                    </tr>
                    <tr>
                        <td class="fw-bold">Expected Delivery Date</td>
                        @if (filled($quotation->expected_delivery))
                        <td>: {{$quotation->expected_delivery->format('M. d, Y')}}</td>
                        @else
                        <td>: Not Set</td>
                        @endif
                    </tr>
                    <tr>
                        <td class="fw-bold">Validity</td>
                        @if (filled($quotation->validity_days))
                        <td>: {{$quotation->validity_days}} Days</td>
                        @else
                        <td>: Not Set</td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>
        <div style="margin-top:20px;line-height:1.5">
            <strong>BILL TO:</strong>
            <div class="small text-muted mt-1" style="margin-top:5px;font-size:12px;">
                <span style="font-size:14px;font-weight:bold;">{{ $quotation->billingAddress->company_name}}</span><br/>
                {{ $quotation->billingAddress->title}} {{ $quotation->billingAddress->first_name}} {{ $quotation->billingAddress->last_name}}<br>
                {{ $quotation->billingAddress->line1}}, {{ $quotation->billingAddress->line2}}<br>
                {{ $quotation->billingAddress->barangay}}, {{ $quotation->billingAddress->city}}<br>
                {{ $quotation->billingAddress->province}}, {{ $quotation->billingAddress->postcode}}, {{ $quotation->billingAddress->country->name}}<br>
                Email: {{ $quotation->billingAddress->email}} / Mobile: +{{ $quotation->billingAddress->phone}} 
                
            </div>
        </div>
        <table class="table">
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
        <tbody style="font-size:14px;">
            @if($quotation->lines->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">No Item Found</td>
                </tr>
            @else
                @php $counter = 0; @endphp
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
                        <td style="text-align:left;">
                            @if(!empty($line['withName']) )
                            
                            <div>
                                <div style="font-weight:bold;"> WITH NAME</div>
                                @foreach($line['withName'] as $_withName => $withName)
                                    <div style="margin-top:5px;">{{ $_withName }}:{{ $withName['totalQuantity']}} </div>
                                    @if (!empty($withName['namesWithNumbers']))
                                        <div>
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
                                <div style="font-weight:bold;margin-top:5px;"> NO NAME</div>
                                @foreach($line['noName'] as $_noName => $noName)
                                    <div>{{ $_noName }}:{{ $noName['totalQuantity']}} </div>
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td>
                        {{ $line['totalQuantity'] }}
                        </td>
                        <td>
                            @foreach($line['oum'] as $index => $key )
                                <span style="font-weight:bold;">{{ $key }}</span><br/>
                            @endforeach
                        </td>
                        <td style="text-align:right;">
                            @if($line['breakdown'] === true )
                                @for ($i = 0; $i < count($line['prices']); $i++)
                                <span style="margin-right:5px;">{{ $line['prices'][$i]['size']}}: </span> {{$line['prices'][$i]['price'] }}<br/>
                                @endfor
                            @else
                                {{ $line['check_prices'][0]; }}

                            @endif
                        </td>
                        <td style="text-align:right;" >
                        {{  $line['price_total_format'] }}
                        </td>
                    </tr>
                @endforeach 
                <tr>
                    <td colspan="3" class="fst-italic small mt-1 text-muted"  style="border-left:1px solid white !important;border-bottom:1px solid white !important;">
                        <i>This is a system generated Quotation, no signature is required.</i>
                    </td>
                    <td colspan="3" class="fw-bold bg-light">Sub Total</td>
                    <td class="text-end fw-bold">{{ $quotation->sub_total->formatted }}</td>
                </tr>
                @if(!empty($quotation->shipping_breakdown->items) && count($quotation->shipping_breakdown->items) >1  )
                    <tr>
                        <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                        <td colspan="3"><b>Shipping Breakdown: (<i>{{ $quotation->shipping_total->formatted }}</i> ) </b></td>
                        <td class="text-end fw-bold"></td>
                    </tr>
                    @foreach($quotation->shipping_breakdown->items as $index => $key )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td colspan="3">&nbsp;&nbsp;<i>{{ $key->name }}</i></td>
                            <td class="text-end fw-bold">{{ $key->price->formatted }}</td>
                        </tr>
                    @endforeach 
                @else 
                    @foreach($quotation->shipping_breakdown->items as $index => $key )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td colspan="3" class="fw-bold">{{ $key->name }}</td>
                            <td class="text-end fw-bold">{{ $key->price->formatted }}</td>
                        </tr>
                    @endforeach 
                @endif
                @if(!empty($quotation->tax_breakdown->amounts) && count($quotation->tax_breakdown->amounts) > 1  )
                    <tr>
                        <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                        <td colspan="3"><b>Tax Breakdown: (<i>{{ $quotation->tax_total->formatted }}</i> ) </b></td>
                        <td class="text-end fw-bold"></td>
                    </tr>
                    @foreach($quotation->tax_breakdown->amounts as $index => $key )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td colspan="3">&nbsp;&nbsp;<i>{{ $key->identifier }} @if( $key->percentage )<span class="px-1">({{ $key->percentage }} %)</span>@endif</i></td>
                            <td class="text-end fw-bold">{{ $key->description }}</td>
                        </tr>
                    @endforeach 
                @else 
                    @foreach($quotation->tax_breakdown->amounts as $index => $key )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td colspan="3" class="fw-bold">{{ $key->identifier }} @if( $key->percentage )<span class="px-1">({{ $key->percentage }} %)</span>@endif</td>
                            <td class="text-end fw-bold">{{ $key->description }}</td>
                        </tr>
                    @endforeach 
                @endif
                @if(!empty($quotation->discount_breakdown['amounts']) && count($quotation->discount_breakdown['amounts']) >1  )
                    <tr>
                        <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                        <td colspan="3"><b>Discount Breakdown: (<i>- {{ $quotation->discount_total->formatted }}</i> ) </b></td>
                        <td class="text-end fw-bold"></td>
                    </tr>
                    @foreach($quotation->discount_breakdown['amounts'] as $index => $key )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                            <td colspan="3">&nbsp;&nbsp;<i>{{ $key['description'] }}@if( $key['type'] == 'percentage' )<span class="px-1"> ({{ $key['percentage'] }} %)</span>@endif</i></td>
                            <td class="text-end fw-bold" style="color:red;">- {{ $key['price']['formatted'] }}</td>
                        </tr>
                    @endforeach 
                @else 
                    @foreach($quotation->discount_breakdown['amounts'] as $index => $key )
                        <tr>
                            <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                        <td colspan="3" class="fw-bold">{{ $key['description'] }}@if( $key['type'] == 'percentage' )<span class="px-1"> ({{ $key['percentage'] }} %)</span>@endif</td>
                            <td class="text-end fw-bold" style="color:red;">- {{ $key['price']['formatted'] }}</td>
                        </tr>
                    @endforeach 
                @endif
                <tr>
                    <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                    <td colspan="3" class="fw-bold bg-light">Grand Total</td>
                    <td class="text-end fw-bold">{{ $quotation->total->formatted }}</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div style="font-size:14px;line-height:1.5;color:#333">
            <span style="font-size:16px;font-weight:bold;">Terms & Conditions</span><br/><br/>
            <strong>Validity:</strong> Prices valid for {{$quotation->validity_days}} days. By paying the down payment, the client agrees to all terms.<br>
            <strong>Artwork:</strong> Production starts once the final artwork is approved. Revisions after approval may cause delays and extra charges. Slight colorvariations mayoccur due to 
            screen vs. print differences.<br>
            <strong>Payment:</strong> 50% down payment is required to confirm the order. Remaining 50% must be paid before delivery or pick-up.<br>
            <strong>Lead Time:</strong>  Standard lead time is 7 working days, excluding shipping. Bulk orders may require additional time; an agreed deadline will be set before production.<br>
            <strong>Changes & Cancellation:</strong> No changes allowed once production has started. Downpayment is non-refundable for cancelled orders.<br>
            <strong>Quality:</strong> Issues must bereported within 48 hours of receiving the items.<br>
            <strong>Delivery / Shipping:</strong> Full payment required before shipping or pick-up. Shipping fees are shouldered by the client.TP Ink Lab is not liable for courier delays.<br>
        </div>
        <div class="footer-divider">
            <span>Thank you for your business, we hope to work with you again!</span>
        </div>
       <div class="footer-powered">
            <span>Powered by</span>
            <img src="{{public_path('/img/tech-hive-logo-black-font.png')}}" alt="Tech Hive" class="footer-logo" style="width:100px;" />
        </div>
    </div>
    </div>
    </div>
</body>
</html>
