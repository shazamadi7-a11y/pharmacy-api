<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - Order #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .info { margin-bottom: 20px; }
        .info p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 10px; }
        .footer { text-align: center; margin-top: 40px; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pharmacy Receipt</h1>
        <p>Order #{{ $order->id }}</p>
    </div>

    <div class="info">
        <p><strong>Customer:</strong> {{ $order->user->name }}</p>
        <p><strong>Email:</strong> {{ $order->user->email }}</p>
        <p><strong>Date:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status->value) }}</p>
        @if($order->notes)
            <p><strong>Notes:</strong> {{ $order->notes }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->medicine?->brand_name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                    <td>${{ number_format((float) $item->unit_price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: ${{ number_format((float) $order->total_price, 2) }}
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
