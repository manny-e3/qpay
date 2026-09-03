<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-top: 5px solid #1e3a60;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .header h1 {
            color: #1e3a60;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .details-box {
            background-color: #f8f9fa;
            border-left: 4px solid #1e3a60;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .details-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .details-box td {
            padding: 6px 0;
            font-size: 14px;
        }
        .details-box td.label {
            font-weight: bold;
            color: #6c757d;
            width: 35%;
        }
        .details-box td.value {
            color: #212529;
        }
        .footer {
            text-align: center;
            color: #888888;
            font-size: 12px;
            margin-top: 30px;
            border-top: 1px solid #eeeeee;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Initiated</h1>
        </div>
        
        <div class="content">
            <p>Dear Customer,</p>
            <p>Your payment transaction for <strong>{{ $transaction->app->appName ?? 'Central Hub' }}</strong> has been initiated.</p>
            
            <p>Here is a summary of the invoice details:</p>
            
            <div class="details-box">
                <table>
                    <tr>
                        <td class="label">Reference:</td>
                        <td class="value"><code>{{ $transaction->reference }}</code></td>
                    </tr>
                    <tr>
                        <td class="label">Amount Due:</td>
                        <td class="value"><strong>{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Date:</td>
                        <td class="value">{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
            
            <p>Your payment invoice has been attached to this email as a PDF document. Please use the checkout URL sent to you to complete your payment.</p>
            
            <p>Best regards,<br>
            The {{ $transaction->app->appName ?? 'Central Hub' }} Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated notification. Please do not reply directly to this email.</p>
        </div>
    </div>
</body>
</html>
