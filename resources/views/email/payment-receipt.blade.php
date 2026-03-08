<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <title>Payment Acknowledgement</title>
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
                                 <img src="{{ asset('img/logo/logo-pdf.png') }}" width="160" style="display:block; margin-bottom:10px;">
                              </td>
                              <td width="50%" valign="top" align="right">
                                 <h2 style="margin:0;">PAYMENT ACKNOWLEDGEMENT</h2>
                                 <p style="margin:5px 0 0; color:#6c757d; font-size:14px;">{{ $payment->internal_reference }}</p>
                              </td>
                           </tr>
                        </table>

                        <!-- COMPANY INFO -->
                        <div style="margin-top:10px; font-size:12px; line-height:1.5;">
                           <strong style="font-size:14px;">TP Ink Lab Corp.</strong><br>
                           109 Roxas Ave, Poblacion District, Davao City<br>
                           8000 Davao del Sur, Philippines<br>
                           Viber / WhatsApp: +63 992 309 0084<br>
                           Email: contact@tpinklab.com Website: tpinklab.com
                        </div>

                        <hr style="border:none; border-top:1px solid #ddd; margin:20px 0;">

                        <!-- PAYMENT RECEIVED FROM -->
                        <div style="margin-bottom:20px;">
                           <p style="margin:0 0 10px; color:#6c757d; font-size:12px;">PAYMENT RECEIVED FROM</p>
                             @if($invoice->billingAddress)
                                 @if($invoice->billingAddress->company_name)
                                       <div style="font-size:14px; font-weight:bold;">
                                          {{ $invoice->billingAddress->company_name }}
                                       </div>
                                 @endif

                                 <div style="font-size:12px;">
                                       {{ $invoice->billingAddress->full_name }}
                                 </div>

                                 @if($invoice->billingAddress->line1)
                                       <div style="font-size:12px;">
                                          {{ $invoice->billingAddress->line1 }}
                                          @if($invoice->billingAddress->line2)
                                             , {{ $invoice->billingAddress->line2 }}
                                          @endif
                                       </div>
                                 @endif

                                 @if($invoice->billingAddress->barangay || $invoice->billingAddress->city)
                                       <div style="font-size:12px;">
                                          {{ $invoice->billingAddress->barangay }}
                                          @if($invoice->billingAddress->barangay && $invoice->billingAddress->city), @endif
                                          {{ $invoice->billingAddress->city }}
                                          @if($invoice->billingAddress->province)
                                             , {{ $invoice->billingAddress->province }}
                                          @endif
                                          @if($invoice->billingAddress->country)
                                             , {{ $invoice->billingAddress->country->name }}
                                          @endif
                                       </div>
                                 @endif
                                    <div style="font-size:12px;">
                                 @if($invoice->billingAddress->email)
                                    
                                          Email: {{ $invoice->billingAddress->email }}
                                       
                                 @endif

                                 @if($invoice->billingAddress->phone)
                                          Mobile: +{{ $invoice->billingAddress->phone }}
                                 @endif
                                    </div>
                              @endif
                        </div>

                        <!-- PAYMENT DETAILS TABLE -->
                        <table width="100%" cellpadding="8" cellspacing="0" border="1"
                           style="border-collapse:collapse; font-size:13px;">
                           <tr>
                              <td style="background:#f8f9fa; width:40%;"><strong>Payment Date</strong></td>
                              <td>{{ optional($payment->paid_at)->format('F d, Y') ?? '—' }}</td>
                           </tr>
                           <tr>
                              <td style="background:#f8f9fa;"><strong>Payment Mode</strong></td>
                              <td style="text-transform:capitalize;">
                                 {{ $payment->method === 'bank' ? 'Bank Transfer' : ($payment->method ?? '—') }}
                              </td>
                           </tr>
                           <tr>
                              <td style="background:#f8f9fa;"><strong>Reference No.</strong></td>
                              <td>{{ $payment->reference ?? '—' }}</td>
                           </tr>
                           <tr>
                              <td style="background:#f8f9fa;"><strong>Billing No.</strong></td>
                              <td>{{ $invoice->reference }}</td>
                           </tr>
                           <tr>
                              <td style="background:#f8f9fa;"><strong>Billing Date</strong></td>
                              <td>{{ optional($invoice->invoiced_at)->format('F d, Y') ?? '—' }}</td>
                           </tr>
                           <tr>
                              <td style="background:#f8f9fa;"><strong>Billing Amount</strong></td>
                              <td>{{ $invoice->amount_due->formatted }}</td>
                           </tr>
                           <tr>
                              <td style="background:#f8f9fa;"><strong>Payment Received</strong></td>
                              <td>{{ $payment->amount->formatted }}</td>
                           </tr>
                        </table>

                        <!-- FOOTER -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:30px;">
                           <tr>
                              <td align="center" style="font-size:12px; color:#666;">
                                 <hr style="border:none; border-top:1px solid #ccc;">
                                 <span style="background:#fff; padding:0 10px;">Thank you for your business, we hope to work with you again!</span>
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
