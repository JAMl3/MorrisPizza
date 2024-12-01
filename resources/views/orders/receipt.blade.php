<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order #{{ $order->id }} - Receipt</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 10px;
                font-family: 'Courier New', monospace;
                font-size: 10pt;
                width: 80mm; /* Standard thermal receipt width */
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
            @page {
                size: 80mm auto; /* Width of standard thermal receipt */
                margin: 0;
            }
        }
        body {
            font-family: 'Courier New', monospace;
            line-height: 1.2;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            background: #f5f5f5;
        }
        .receipt {
            background: white;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0 0 5px 0;
        }
        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        .info {
            font-size: 9pt;
            margin: 5px 0;
        }
        .items {
            margin: 10px 0;
            font-size: 9pt;
        }
        .item {
            margin: 5px 0;
        }
        .item-details {
            display: flex;
            justify-content: space-between;
        }
        .item-note {
            font-size: 8pt;
            font-style: italic;
            margin-left: 10px;
        }
        .totals {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
            font-size: 9pt;
        }
        .total-line {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .grand-total {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 5px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 9pt;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        .print-button {
            background-color: #dc2626;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-family: Arial, sans-serif;
        }
        .print-button:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="print-button">Print Receipt</button>
    </div>

    <div class="receipt">
        <div class="header">
            <h1>MORRIS PIZZA</h1>
            <p>123 Pizza Street</p>
            <p>London, UK</p>
            <p>Tel: 020 1234 5678</p>
            <p>--------------------------------</p>
            <p>Order #{{ $order->id }}</p>
            <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p>{{ strtoupper($order->order_type) }}</p>
        </div>

        <div class="info">
            <p>Customer: {{ $order->customer_name }}</p>
            <p>Phone: {{ $order->customer_phone }}</p>
            @if($order->order_type === 'delivery')
                <p>Delivery to:</p>
                <p>{{ $order->delivery_address }}</p>
            @else
                <p>Pickup time: {{ $order->pickup_time->format('H:i') }}</p>
            @endif
        </div>

        <div class="divider"></div>

        <div class="items">
            <div class="item">
                <div class="item-details" style="font-weight: bold;">
                    <span>Item</span>
                    <span>Total</span>
                </div>
            </div>
            @foreach($order->items as $item)
                <div class="item">
                    <div class="item-details">
                        <span>{{ $item->quantity }}x {{ $item->menuItem->item_name }}</span>
                        <span>£{{ number_format($item->subtotal, 2) }}</span>
                    </div>
                    <div class="item-details" style="color: #666;">
                        <span style="padding-left: 10px;">@ £{{ number_format($item->unit_price, 2) }} each</span>
                    </div>
                    @if($item->special_instructions)
                        <div class="item-note">* {{ $item->special_instructions }}</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="total-line">
                <span>Subtotal:</span>
                <span>£{{ number_format($order->total_amount - ($order->order_type === 'delivery' ? 2.50 : 0), 2) }}</span>
            </div>
            @if($order->order_type === 'delivery')
                <div class="total-line">
                    <span>Delivery Fee:</span>
                    <span>£2.50</span>
                </div>
            @endif
            <div class="total-line grand-total">
                <span>TOTAL:</span>
                <span>£{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        @if($order->notes)
            <div class="divider"></div>
            <div class="info">
                <p style="font-weight: bold;">Order Notes:</p>
                <p>{{ $order->notes }}</p>
            </div>
        @endif

        <div class="footer">
            <p>Thank you for choosing Morris Pizza!</p>
            <p>--------------------------------</p>
            <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p>Order #{{ $order->id }}</p>
        </div>
    </div>
</body>
</html> 