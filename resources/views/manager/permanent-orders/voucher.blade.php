<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>স্থায়ী অর্ডার ভাউচার - {{ $permanentOrder->voucher_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .voucher {
            background: white;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin-bottom: 5px;
            font-size: 28px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .voucher-title {
            text-align: center;
            background: #2563eb;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: bold;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .info-box {
            flex: 1;
        }
        .info-box h3 {
            color: #2563eb;
            margin-bottom: 10px;
            font-size: 16px;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            padding: 5px 0;
        }
        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .order-details {
            margin: 30px 0;
            border: 2px solid #eee;
            padding: 20px;
            background: #f9fafb;
        }
        .order-details h3 {
            color: #2563eb;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
            text-align: right;
        }
        .total-section {
            margin-top: 30px;
            border-top: 3px solid #333;
            padding-top: 15px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 18px;
        }
        .total-row.grand-total {
            font-size: 22px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #333;
            padding-top: 15px;
            margin-top: 10px;
        }
        .due-amount {
            color: #dc2626;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-active { background: #dbeafe; color: #1e40af; }
        .status-partial { background: #fef3c7; color: #92400e; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #f3f4f6; color: #374151; }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .signature-box {
            text-align: center;
            flex: 1;
        }
        .signature-line {
            border-top: 2px solid #333;
            margin: 50px 20px 10px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .voucher {
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="voucher">
        <div class="header">
            <h1>{{ $permanentOrder->business->name }}</h1>
            @if($permanentOrder->business->address)
                <p>{{ $permanentOrder->business->address }}</p>
            @endif
            @if($permanentOrder->business->phone)
                <p>ফোন: {{ $permanentOrder->business->phone }}</p>
            @endif
        </div>

        <div class="voucher-title">স্থায়ী অর্ডার ভাউচার</div>

        <div class="info-section">
            <div class="info-box">
                <h3>গ্রাহকের তথ্য</h3>
                <div class="info-row">
                    <span class="info-label">নাম:</span>
                    <span class="info-value">{{ $permanentOrder->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">ফোন:</span>
                    <span class="info-value">{{ $permanentOrder->customer_phone }}</span>
                </div>
                @if($permanentOrder->customer_address)
                <div class="info-row">
                    <span class="info-label">ঠিকানা:</span>
                    <span class="info-value">{{ $permanentOrder->customer_address }}</span>
                </div>
                @endif
            </div>

            <div class="info-box">
                <h3>অর্ডারের তথ্য</h3>
                <div class="info-row">
                    <span class="info-label">ভাউচার নং:</span>
                    <span class="info-value">{{ $permanentOrder->voucher_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">তারিখ:</span>
                    <span class="info-value">{{ $permanentOrder->order_date->format('d M Y') }}</span>
                </div>
                @if($permanentOrder->delivery_date)
                <div class="info-row">
                    <span class="info-label">ডেলিভারি:</span>
                    <span class="info-value">{{ $permanentOrder->delivery_date->format('d M Y') }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">স্ট্যাটাস:</span>
                    <span class="info-value">
                        @if($permanentOrder->status === 'completed')
                            <span class="status-badge status-completed">সম্পন্ন</span>
                        @elseif($permanentOrder->status === 'partial')
                            <span class="status-badge status-partial">আংশিক</span>
                        @elseif($permanentOrder->status === 'active')
                            <span class="status-badge status-active">সক্রিয়</span>
                        @else
                            <span class="status-badge status-cancelled">বাতিল</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="order-details">
            <h3>অর্ডার বিবরণ</h3>
            <div class="detail-row">
                <span class="detail-label">পণ্যের নাম</span>
                <span class="detail-value">{{ $permanentOrder->product->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">পরিমাণ</span>
                <span class="detail-value">{{ number_format($permanentOrder->quantity, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">একক মূল্য</span>
                <span class="detail-value">৳{{ number_format($permanentOrder->unit_price, 2) }}</span>
            </div>
        </div>

        @if($permanentOrder->notes)
        <div class="order-details">
            <h3>নোট</h3>
            <p>{{ $permanentOrder->notes }}</p>
        </div>
        @endif

        <div class="total-section">
            <div class="total-row">
                <span>মোট টাকা:</span>
                <span>৳{{ number_format($permanentOrder->total_amount, 2) }}</span>
            </div>
            <div class="total-row">
                <span>পরিশোধিত:</span>
                <span>৳{{ number_format($permanentOrder->paid_amount, 2) }}</span>
            </div>
            <div class="total-row grand-total">
                <span>বাকি টাকা:</span>
                <span class="due-amount">৳{{ number_format($permanentOrder->due_amount, 2) }}</span>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>গ্রাহকের স্বাক্ষর</p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p>অনুমোদিত স্বাক্ষর</p>
            </div>
        </div>

        <div class="footer">
            <p>এই ভাউচারটি কম্পিউটার দ্বারা তৈরি এবং স্বাক্ষরের প্রয়োজন নেই।</p>
            <p>মুদ্রণের তারিখ: {{ now()->format('d M Y, h:i A') }}</p>
        </div>

        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" style="background: #2563eb; color: white; padding: 10px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                প্রিন্ট করুন
            </button>
            <button onclick="window.close()" style="background: #6b7280; color: white; padding: 10px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-left: 10px;">
                বন্ধ করুন
            </button>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
