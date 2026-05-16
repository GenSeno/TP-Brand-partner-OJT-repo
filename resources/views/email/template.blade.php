<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title>Quotation</title>
   </head>
   <body style="margin:0; padding:0; background:#aeaeae; font-family:Arial, sans-serif;">
      <!-- OUTER WRAPPER -->
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#aeaeae;">
         <tr>
            <td align="center" style="padding:20px;">
               <!-- EMAIL CONTAINER -->
               <table width="700" cellpadding="0" cellspacing="0" border="0"
                  style="background:#ffffff; color:#333; font-size:12px;">
                  <tr>
                     <td style="padding:20px;">
                        <!-- HEADER -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                           <tr>
                              <td width="50%" valign="top">
                                 <img src="{{ asset('img/logo/logo_pakaras_white.png') }}" width="160" style="display:block; margin-bottom:10px;">
                                 <div style="font-size:12px; color:#6c757d; line-height:1.5;">
                                    <strong style="font-size:16px;">TP Ink Lab Corp.</strong><br>
                                    109 Roxas Ave, Poblacion District, Davao City<br>
                                    8000 Davao del Sur, Philippines<br>
                                    Viber / WhatsApp: +63 992 309 0084<br>
                                    Email: contact@tpinklab.com / Website: tpinklab.com
                                 </div>
                              </td>
                              <td width="50%" valign="top" align="right">
                                 <h2 style="margin:0;">QUOTATION</h2>
                                 <table width="100%" cellpadding="4" cellspacing="0" border="0" style="margin-top:10px; text-align:left;">
                                    <tr>
                                       <td width="110"><strong>Quotation No.</strong></td>
                                       <td>: {{ $quotation->reference }}</td>
                                    </tr>
                                    <tr>
                                       <td><strong>Date</strong></td>
                                       <td>: {{ optional($quotation->quoted_at)->format('M. d, Y') ?? 'Not Set' }}</td>
                                    </tr>
                                    <tr>
                                       <td><strong>Expected Delivery</strong></td>
                                       <td>: {{ optional($quotation->expected_delivery)->format('M. d, Y') ?? 'Not Set' }}</td>
                                    </tr>
                                    <tr>
                                       <td><strong>Validity</strong></td>
                                       <td>: {{ $quotation->validity_days ?? 'Not Set' }} Days</td>
                                    </tr>
                                 </table>
                              </td>
                           </tr>
                        </table>
                        <!-- BILL TO -->
                        <div style="margin-top:20px;">
                           <strong>BILL TO:</strong>
                           <div style="font-size:12px; color:#6c757d; margin-top:5px; line-height:1.5;">
                              <strong style="font-size:14px;">{{ $quotation->billingAddress->company_name }}</strong><br>
                              {{ $quotation->billingAddress->title }}
                              {{ $quotation->billingAddress->first_name }}
                              {{ $quotation->billingAddress->last_name }}<br>
                              {{ $quotation->billingAddress->line1 }},
                              {{ $quotation->billingAddress->line2 }}<br>
                              {{ $quotation->billingAddress->barangay }},
                              {{ $quotation->billingAddress->city }}<br>
                              {{ $quotation->billingAddress->province }},
                              {{ $quotation->billingAddress->postcode }},
                              {{ $quotation->billingAddress->country->name }}<br>
                              Email: {{ $quotation->billingAddress->email }} /
                              Mobile: +{{ $quotation->billingAddress->phone }}
                           </div>
                        </div>
                        <!-- ITEMS TABLE -->
                        <table width="100%" cellpadding="6" cellspacing="0" border="1"
                           style="border-collapse:collapse; margin-top:20px; font-size:13px;">
                           <thead style="background:#f8f9fa;">
                              <tr>
                                 <th>S/N</th>
                                 <th>Product</th>
                                 <th>Printing Option & Size</th>
                                 <th>Qty</th>
                                 <th>UOM</th>
                                 <th>Price</th>
                                 <th>Total</th>
                              </tr>
                           </thead>
                           <tbody>
                              @php $counter = 0; @endphp
                              @foreach($lines as $line)
                              @php $counter++; @endphp
                              <tr>
                                 <td align="center">{{ $counter }}</td>
                                 <td>
                                    <img src="{{ asset($line['product']['image_url_email']) }}" width="70" height="70"
                                       style="display:block; object-fit:contain; margin-bottom:5px;">
                                    <strong>{{ $line['product']['details']['name'] }}</strong>
                                 </td>
                                 <td>
                                    @if(!empty($line['withName']))
                                    <strong>WITH NAME</strong><br>
                                    @foreach($line['withName'] as $_withName => $withName)
                                    {{ $_withName }}: {{ $withName['totalQuantity'] }}<br>
                                    @endforeach
                                    @endif
                                    @if(!empty($line['noName']))
                                    <br><strong>NO NAME</strong><br>
                                    @foreach($line['noName'] as $_noName => $noName)
                                    {{ $_noName }}: {{ $noName['totalQuantity'] }}<br>
                                    @endforeach
                                    @endif
                                 </td>
                                 <td align="center">{{ $line['totalQuantity'] }}</td>
                                 <td align="center">
                                    @foreach($line['oum'] as $uom)
                                    <strong>{{ $uom }}</strong><br>
                                    @endforeach
                                 </td>
                                 <td align="right">
                                    @if($line['breakdown'])
                                    @foreach($line['prices'] as $price)
                                    {{ $price['size'] }}: {{ $price['price'] }}<br>
                                    @endforeach
                                    @else
                                    {{ $line['check_prices'][0] }}
                                    @endif
                                 </td>
                                 <td align="right"><strong>{{ $line['price_total_format'] }}</strong></td>
                              </tr>
                              @endforeach
                              <tr>
                                <td colspan="3" class="fst-italic small mt-1 text-muted"  style="border-left:1px solid white !important;border-bottom:1px solid white !important;">
                                    <i>This is a system generated Quotation, no signature is required.</i>
                                </td>
                                <td colspan="3" class="fw-bold" style="background-color: #f8f9fa;">Sub Total</td>
                                <td class="text-end fw-bold">{{ $quotation->sub_total->formatted }}</td>
                            </tr>
                            @if(!empty($quotation->shipping_breakdown->items))
                                  @if(count($quotation->shipping_breakdown->items) >1  )
                                <tr>
                                    <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                    <td colspan="3" style="background-color: #f8f9fa;"><b>Shipping Breakdown:</b> <i style="font-size:10px;"> ({{ $quotation->shipping_total->formatted }})</i></td>
                                    <td class="text-end fw-bold"></td>
                                </tr>
                                @endif
                                @foreach($quotation->shipping_breakdown->items as $index => $key )
                                    <tr>
                                        <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                        <td colspan="3" style="background-color: #f8f9fa;">&nbsp;&nbsp;<i>{{ $key->name }}</i></td>
                                        <td class="text-end fw-bold">{{ $key->price->formatted }}</td>
                                    </tr>
                                @endforeach 
                            @endif
                            @if(!empty($quotation->tax_breakdown->amounts))
                                @if(count($quotation->tax_breakdown->amounts) > 1  )
                                <tr>
                                    <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                    <td colspan="3" style="background-color: #f8f9fa;"><b>Tax Breakdown: </b> <i style="font-size:10px;"> ( {{ $quotation->tax_total->formatted }} )</i> </td>
                                    <td class="text-end fw-bold"></td>
                                </tr>
                                 @endif
                                @foreach($quotation->tax_breakdown->amounts as $index => $key )
                                    <tr>
                                        <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                        <td colspan="3" style="background-color: #f8f9fa;">&nbsp;&nbsp;<i>{{ $key->identifier }} @if( $key->percentage )<span class="px-1">({{ $key->percentage }} %)</span>@endif</i></td>
                                        <td class="text-end fw-bold">{{ $key->description }}</td>
                                    </tr>
                                @endforeach 
                            @endif
                            @if(!empty($quotation->discount_breakdown['amounts']))
                               @if(count($quotation->discount_breakdown['amounts']) >1  )
                                <tr>
                                    <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                    <td colspan="3" style="background-color: #f8f9fa;"><b>Discount Breakdown:</b><i  style="color:red;font-size:10px;"> (-{{ $quotation->discount_total->formatted }})</i></td>
                                    <td class="text-end fw-bold"></td>
                                </tr>
                                 @endif
                                @foreach($quotation->discount_breakdown['amounts'] as $index => $key )
                                    <tr>
                                        <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                        <td colspan="3" style="background-color: #f8f9fa;">&nbsp;&nbsp;<i>{{ $key['description'] }}@if( $key['type'] == 'percentage' )<span class="px-1"> ({{ $key['percentage'] }} %)</span>@endif</i></td>
                                        <td class="text-end fw-bold" style="color:red;">- {{ $key['price']['formatted'] }}</td>
                                    </tr>
                                @endforeach 
                            @endif
                            <tr>
                                <td colspan="3" class="fw-bold"  style="border-left:1px solid white !important;border-bottom:1px solid white !important"></td>
                                <td colspan="3" class="fw-bold bg-light">Grand Total</td>
                                <td class="text-end fw-bold">{{ $quotation->total->formatted }}</td>
                            </tr>
                           </tbody>
                        </table>
                        <!-- TERMS -->
                        <div style="margin-top:20px; font-size:12px; line-height:1.6;">
                           <strong style="font-size:14px;">Terms & Conditions</strong><br>
                            <strong>Validity:</strong> Prices valid for {{$quotation->validity_days}} days. By paying the down payment, the client agrees to all terms.<br>
                            <strong>Artwork:</strong> Production starts once the final artwork is approved. Revisions after approval may cause delays and extra charges. Slight color variations mayoccur due to 
                            screen vs. print differences.<br>
                            <strong>Payment:</strong> 50% down payment is required to confirm the order. Remaining 50% must be paid before delivery or pick-up.<br>
                            <strong>Lead Time:</strong>  Standard lead time is 7 working days, excluding shipping. Bulk orders may require additional time; an agreed deadline will be set before production.<br>
                            <strong>Changes & Cancellation:</strong> No changes allowed once production has started. Downpayment is non-refundable for cancelled orders.<br>
                            <strong>Quality:</strong> Issues must bereported within 48 hours of receiving the items.<br>
                            <strong>Delivery / Shipping:</strong> Full payment required before shipping or pick-up. Shipping fees are shouldered by the client. TP Ink Lab is not liable for courier delays.<br>
                        </div>
                        <!-- FOOTER -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:20px;">
                           <tr>
                              <td align="center" style="font-size:12px; color:#666;">
                                 <hr style="border:none; border-top:1px solid #ccc;">
                                 <span style="background: #fff; padding: 0 10px; position: relative; z-index:1;">Thank you for your business, we hope to work with you again!</span>
                              </td>
                           </tr>
                           <tr>
                              <td align="right" style="padding-top:10px;">
                                 Powered by
                                 <img src="{{ asset('img/tech-hive-logo-black-font.png') }}" width="100" style="vertical-align:middle;">
                              </td>
                           </tr>
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>
   </body>
</html>