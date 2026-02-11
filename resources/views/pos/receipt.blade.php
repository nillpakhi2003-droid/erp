<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - {{ $transaction->transaction_number }}</title>
    
    <!-- QZ Tray for Direct Printing -->
    <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2/qz-tray.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-sha256@0.9.0/build/sha256.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            height: auto;
        }
        
        body {
            font-family: 'Courier New', 'Courier', monospace;
            font-size: {{ $template->receipt_font_size ?? '12px' }};
            line-height: 1.3;
            color: #000;
            background: #fff;
            width: {{ $template->receipt_paper_size ?? '80mm' }};
            max-width: {{ $template->receipt_paper_size ?? '80mm' }};
            height: auto;
            min-height: auto;
            margin: 0 auto;
            padding: 2mm 3mm;
            page-break-after: avoid;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 3px;
            border-bottom: 2px dashed #000;
            padding-bottom: 3px;
        }
        
        .logo {
            max-width: 100px;
            margin: 0 auto 3px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company-info {
            font-size: 10px;
            line-height: 1.2;
        }
        
        .header-text {
            margin-top: 2px;
            font-size: 11px;
            font-style: italic;
        }
        
        .receipt-meta {
            margin: 3px 0;
            font-size: 10px;
        }
        
        .receipt-meta table {
            width: 100%;
        }
        
        .receipt-meta td {
            padding: 0;
            line-height: 1.1;
        }
        
        .receipt-items {
            margin: 3px 0;
            border-top: 1px dashed #000;
            border-bottom: 2px solid #000;
        }
        
        .receipt-items table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .receipt-items th {
            text-align: left;
            padding: 2px 0;
            border-bottom: 1px solid #000;
            font-weight: bold;
        }
        
        .receipt-items td {
            padding: 1px 0;
            line-height: 1.1;
        }
        
        .text-right {
            text-align: right;
        }
        
        .totals {
            margin: 3px 0;
        }
        
        .totals table {
            width: 100%;
        }
        
        .totals td {
            padding: 1px 0;
            line-height: 1.1;
        }
        
        .grand-total {
            font-size: 13px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 2px !important;
        }
        
        .payment-info {
            margin: 3px 0;
            border-top: 1px dashed #000;
            padding-top: 3px;
        }
        
        .payment-info td {
            padding: 0;
            line-height: 1.1;
        }
        
        .footer {
            text-align: center;
            margin-top: 5px;
            font-size: 10px;
            border-top: 2px dashed #000;
            padding-top: 3px;
            margin-bottom: 0;
            padding-bottom: 0;
            page-break-after: avoid;
        }
        
        @media print {
            html {
                height: auto;
            }
            body {
                width: {{ $template->receipt_paper_size ?? '80mm' }};
                max-width: {{ $template->receipt_paper_size ?? '80mm' }};
                padding: 2mm 2mm 3mm 2mm;
                margin: 0;
                height: auto;
                min-height: auto;
                page-break-after: avoid;
            }
            @page {
                margin: 0;
                padding: 0;
                size: {{ $template->receipt_paper_size ?? '80mm' }} auto;
            }
            .footer {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        @if($template && ($template->receipt_show_logo ?? true) && $template->logo_url)
            <img src="{{ $template->logo_url }}" alt="Logo" class="logo">
        @endif
        
        <div class="company-name">{{ $template->company_name ?? $business->name }}</div>
        
        <div class="company-info">
            @if($template && $template->company_address)
                {{ $template->company_address }}<br>
            @endif
            @if($template && $template->company_phone)
                Tel: {{ $template->company_phone }}
            @endif
        </div>
        
        @if($template && $template->receipt_header_text)
            <div class="header-text">{{ $template->receipt_header_text }}</div>
        @endif
    </div>
    
    <div class="receipt-meta">
        <table>
            <tr>
                <td><strong>Receipt#:</strong></td>
                <td class="text-right">{{ $transaction->transaction_number }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td class="text-right">{{ $transaction->completed_at->format('d/m/Y h:i A') }}</td>
            </tr>
            <tr>
                <td><strong>Cashier:</strong></td>
                <td class="text-right">{{ $transaction->user->name }}</td>
            </tr>
            @if(($template->receipt_show_customer ?? true) && isset($customerName) && $customerName != 'POS Customer')
                <tr>
                    <td><strong>Customer:</strong></td>
                    <td class="text-right">{{ $customerName }}</td>
                </tr>
                @if($customerPhone)
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td class="text-right">{{ $customerPhone }}</td>
                    </tr>
                @endif
            @endif
        </table>
    </div>
    
    <div class="receipt-items">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    @php
                        $product = \App\Models\Product::find($item['product_id']);
                    @endphp
                    <tr>
                        <td>{{ $product->name ?? 'Unknown' }}</td>
                        <td class="text-right">{{ $item['quantity'] }}</td>
                        <td class="text-right">৳{{ number_format($item['price'], 2) }}</td>
                        <td class="text-right">৳{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">৳{{ number_format($transaction->subtotal, 2) }}</td>
            </tr>
            @if($transaction->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-৳{{ number_format($transaction->discount, 2) }}</td>
                </tr>
            @endif
            @if($transaction->tax > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right">৳{{ number_format($transaction->tax, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>TOTAL:</td>
                <td class="text-right">৳{{ number_format($transaction->total, 2) }}</td>
            </tr>
        </table>
    </div>
    
    @if($template->receipt_show_payment_method ?? true)
        <div class="payment-info">
            <table>
                <tr>
                    <td><strong>Payment Method:</strong></td>
                    <td class="text-right">{{ ucfirst($transaction->payment_method) }}</td>
                </tr>
                <tr>
                    <td><strong>Paid:</strong></td>
                    <td class="text-right">৳{{ number_format($transaction->amount_tendered, 2) }}</td>
                </tr>
                @if($transaction->change > 0)
                    <tr>
                        <td><strong>Change:</strong></td>
                        <td class="text-right">৳{{ number_format($transaction->change, 2) }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif
    
    <div class="footer">
        @if($template && $template->receipt_footer_text)
            {{ $template->receipt_footer_text }}<br>
        @endif
        Thank you for your business!<br>
        Please come again
    </div>
    
    <script>
        // Remove any blank space after content
        document.addEventListener('DOMContentLoaded', function() {
            // Set body height to actual content height
            document.body.style.height = 'auto';
            document.documentElement.style.height = 'auto';
        });
        
        // Auto-print with QZ Tray (fallback to browser print)
        setTimeout(function() {
            printReceiptWithQZ();
        }, 500);
        
        function printReceiptWithQZ() {
            if (typeof qz !== 'undefined') {
                if (!qz.websocket.isActive()) {
                    qz.websocket.connect().then(function() {
                        findThermalPrinter();
                    }).catch(function(err) {
                        console.log('QZ Tray not available, using browser print');
                        window.print();
                    });
                } else {
                    findThermalPrinter();
                }
            } else {
                window.print();
            }
        }
        
        function findThermalPrinter() {
            qz.printers.find().then(function(printers) {
                // Look for thermal receipt printer
                let printer = printers.find(p => 
                    p.toLowerCase().includes('thermal') ||
                    p.toLowerCase().includes('receipt') ||
                    p.toLowerCase().includes('pos') ||
                    p.toLowerCase().includes('rongta') ||
                    p.toLowerCase().includes('80mm')
                ) || printers[0];
                
                printReceipt(printer);
            }).catch(function(err) {
                console.error(err);
                window.print();
            });
        }
        
        function printReceipt(printer) {
            let config = qz.configs.create(printer, {
                size: { width: 80, units: 'mm' },
                margins: { top: 2, right: 2, bottom: 2, left: 2, units: 'mm' }
            });
            
            // Get receipt content with proper styling
            let styles = document.querySelector('style').innerHTML;
            
            // Create minimal HTML for thermal printer
            let receiptHTML = `<!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <style>${styles}</style>
            </head>
            <body style="width: 80mm; margin: 0; padding: 2mm 3mm;">
                ${document.body.innerHTML}
            </body>
            </html>`;
            
            let printData = [{
                type: 'pixel',
                format: 'html',
                flavor: 'plain',
                data: receiptHTML
            }];
            
            qz.print(config, printData).then(function() {
                console.log('Receipt printed successfully via QZ Tray');
            }).catch(function(err) {
                console.error(err);
                window.print();
            });
        }
    </script>
</body>
</html>
