<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Invoice</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a2332;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
        }
        .outer-frame {
            border: 4px double #1c2d42;
            padding: 20px;
            min-height: 90%;
            background-color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-info {
            font-size: 10px;
            color: #2c3e50;
            line-height: 1.5;
        }
        .company-info strong {
            color: #1c2d42;
            font-size: 11px;
        }
        .logo-container {
            text-align: right;
        }
        .logo-container img {
            max-height: 45px;
            width: auto;
        }
        .banner-title {
            background-color: #1a2e4c;
            color: #ffffff;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            padding: 8px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-grid td {
            border: 1px solid #1c2d42;
            padding: 6px 10px;
            vertical-align: middle;
        }
        .info-grid td.label {
            width: 18%;
            color: #566573;
            font-weight: normal;
        }
        .info-grid td.value {
            width: 32%;
            font-weight: bold;
            color: #1a2332;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            border: 1px solid #1c2d42;
            background-color: #ffffff;
            color: #1c2d42;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            text-transform: uppercase;
        }
        .items-table td {
            border-left: 1px solid #1c2d42;
            border-right: 1px solid #1c2d42;
            padding: 10px;
            vertical-align: top;
        }
        .items-table tr.item-row td {
            height: 180px;
        }
        .items-table tr.total-row td {
            border: 1px solid #1c2d42;
            padding: 6px 10px;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .total-due-label {
            color: #1b365d;
            font-weight: bold;
        }
        .total-due-value {
            color: #1b365d;
            font-weight: bold;
            font-size: 12px;
        }
        .words-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border: 1px solid #1c2d42;
        }
        .words-box td {
            padding: 8px 10px;
            vertical-align: middle;
        }
        .words-box td.label {
            width: 30%;
            font-weight: bold;
            text-transform: uppercase;
            color: #1c2d42;
            border-right: 1px solid #1c2d42;
        }
        .words-box td.value {
            width: 70%;
            font-weight: bold;
            color: #1b365d;
        }
    </style>
</head>
<body>
    <div class="outer-frame">
        <table class="header-table">
            <tr>
                <td>
                    <div class="company-info">
                        <strong>Exchange Place, 35, Idowu Taylor Street, Victoria Island, Lagos</strong><br>
                        Email: info@fmdqgroup.com<br>
                        Website: www.fmdqgroup.com
                    </div>
                </td>
                <td class="logo-container">
                    @if(!empty($logoBase64))
                        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="FMDQ Logo">
                    @else
                        <img src="{{ public_path('assets/FMDQ-Logo.png') }}" alt="FMDQ Logo">
                    @endif
                </td>
            </tr>
        </table>

        <div class="banner-title">
            INVOICE FROM FMDQ GROUP PLC
        </div>

        <table class="info-grid">
            <tr>
                <td class="label">Bill to:</td>
                <td class="value">{{ $transaction->customer_first_name }} {{ $transaction->customer_last_name }}</td>
                <td class="label">Date:</td>
                <td class="value">{{ $transaction->created_at ? $transaction->created_at->format('M d, Y') : date('M d, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Registered Name:</td>
                <td class="value">{{ $transaction->customer_company ?? 'N/A' }}</td>
                <td class="label">Invoice Number:</td>
                <td class="value">{{ $transaction->reference }}</td>
            </tr>
            <tr>
                <td class="label">Registered Address:</td>
                <td class="value">{{ $transaction->metadata['address'] ?? 'N/A' }}</td>
                <td class="label">Our Contact:</td>
                <td class="value">{{ $transaction->metadata['contact'] ?? 'Uju Iwuamadi' }}</td>
            </tr>
            <tr>
                <td class="label">Attention:</td>
                <td class="value">{{ $transaction->customer_first_name }} {{ $transaction->customer_last_name }}</td>
                <td class="label">Tel:</td>
                <td class="value">{{ $transaction->customer_phone ?? 'N/A' }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 8%;">S/N</th>
                    <th style="width: 62%;">DESCRIPTION</th>
                    <th class="text-right" style="width: 30%;">(&#8358;)</th>
                </tr>
            </thead>
            <tbody>
                <tr class="item-row">
                    <td class="text-center">1</td>
                    <td>
                        {{ $transaction->metadata['product'] ?? ($transaction->app->appName ?? 'Online Payment') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($subtotal, 2) }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td style="border: none;"></td>
                    <td class="text-right">SUBTOTAL</td>
                    <td class="text-right">{{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td style="border: none;"></td>
                    <td class="text-right">VAT (7.5%)</td>
                    <td class="text-right">{{ number_format($vat, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td style="border: none;"></td>
                    <td class="text-right total-due-label">TOTAL DUE</td>
                    <td class="text-right total-due-value">{{ number_format($transaction->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="words-box">
            <tr>
                <td class="label">AMOUNT IN WORDS</td>
                <td class="value">{{ $amountInWords }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
