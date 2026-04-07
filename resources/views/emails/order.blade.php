<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            border-radius: 6px;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
        .order-details {
            margin-top: 20px;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details th, .order-details td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .order-details th {
            background: #f2f2f2;
            text-align: left;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thank you for your order, {{ $order->user->name }}!</h2>
        <p>We’ve received your order and are processing it. Here are the details:</p>

        <div class="order-details">
            <table>
                <tr>
                    <th>Order ID</th>
                    <td>{{ $order->id }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ ucfirst($order->status) }}</td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td>${{ number_format($order->total_price, 2) }}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                </tr>
            </table>
        </div>

        <p>We’ll notify you once your order has been shipped.</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
