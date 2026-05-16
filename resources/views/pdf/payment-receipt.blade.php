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
                <img src="{{ public_path('/img/logo/logo_pakaras_white.png') }}" alt="Company Logo" style="width:160px; margin-bottom:10px;">
                <div style="font-size:12px; color:#6c757d;">
                    <span style="font-size:16px; font-weight:bold;color:#000">TP Ink Lab Corp.</span><br>
                    109 Roxas Ave, Poblacion District, Davao City<br>
                    8000 Davao del Sur, Philippines<br>
                    Viber / WhatsApp: +63 992 309 0084<br>
                    Email: contact@tpinklab.com . Website: tpinklab.com
                </div>
            </td>

            <!-- Right: Quotation Info -->
            <td style="width:50%; vertical-align:top; text-align:right; line-height:1.5;">
                <h2 style="margin:0;">PAYMENT ACKNOWLEDGEMENT</h2>
                <h3 style="margin:0;" class="text-muted"> {{ $payment->internal_reference   }}</h3>
            </td>
        </tr>
    </table>
      <hr style="border:none; border-top:1px solid #ddd; margin:20px 0;">
       <div style="margin-top:10px; width:80%; margin-left:auto; margin-right:auto; line-height:1.6;">

      <p style="margin:0 0 10px; color:#6c757d; font-size:12px;">
         PAYMENT RECEIVED FROM
      </p>
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
   </div>
   <br/>
    <div style="width:100%; margin:0 auto; ">
       <!-- PAYMENT DETAILS TABLE -->
     <table width="80%" cellpadding="8" cellspacing="0" border="1"
            style="border-collapse:collapse; font-size:13px; margin:0 auto;">
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
            <td style="font-family: 'DejaVu Sans', sans-serif;font-size:10px;">{{ $invoice->amount_due->formatted }}</td>
         </tr>
         <tr>
            <td style="background:#f8f9fa;"><strong>Payment Received</strong></td>
            <td style="font-family: 'DejaVu Sans', sans-serif;font-size:10px;">{{ $payment->amount->formatted }}</td>
         </tr>
      </table>
      <br/>
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
