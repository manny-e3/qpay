<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | FMDQ Payment Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1a3668;
            --primary-light: #2a4e8c;
            --gold: #c59333;
            --gold-light: #f5e8c8;
            --success: #1a7c4a;
            --success-bg: #e8f7ef;
            --error: #ba1a1a;
            --error-bg: #fce8e8;
            --pending: #7b5400;
            --pending-bg: #fff8e1;
            --surface: #f8f9fc;
            --card-bg: #ffffff;
            --border: #e8eaf0;
            --text-primary: #1a2742;
            --text-secondary: #556080;
            --text-muted: #8a96ad;
            --radius: 16px;
            --shadow: 0 4px 24px rgba(26, 54, 104, 0.08);
            --shadow-lg: 0 12px 48px rgba(26, 54, 104, 0.14);
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f1e3d 0%, #1a3668 50%, #0d2a5e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: var(--text-primary);
        }

        .checkout-wrapper {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Brand Header */
        .checkout-brand {
            text-align: center;
            color: #ffffff;
        }
        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }
        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--gold), #e8b84b);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .brand-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .brand-tagline {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            font-weight: 400;
        }

        /* Main Card */
        .checkout-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        /* Card Sections */
        .card-section {
            padding: 24px;
            border-bottom: 1px solid var(--border);
        }
        .card-section:last-child { border-bottom: none; }

        .section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 4px;
        }
        .section-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
        }
        .section-value.amount {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.02em;
        }
        .section-value.reference {
            font-size: 13px;
            font-family: 'SFMono-Regular', Consolas, monospace;
            color: var(--text-secondary);
            background: var(--surface);
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 4px;
        }

        .transaction-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Alert / Error */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }
        .alert-error {
            background: var(--error-bg);
            color: var(--error);
            border-left: 3px solid var(--error);
        }

        /* Buttons */
        .btn-primary-checkout {
            display: block;
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary-checkout:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(26, 54, 104, 0.25);
        }
        .btn-primary-checkout:active { transform: translateY(0); }

        .btn-primary-checkout:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        /* Security Footer */
        .checkout-footer {
            text-align: center;
            padding: 16px 24px;
        }
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
        }
        .lock-icon {
            width: 14px;
            height: 14px;
            fill: rgba(255,255,255,0.4);
        }

        @media (max-width: 480px) {
            .transaction-grid { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="checkout-wrapper">
        <!-- Brand Header -->
        <div class="checkout-brand">
            <div class="brand-logo">
                <div class="brand-icon">🏦</div>
                <span class="brand-name">FMDQ Payment Hub</span>
            </div>
            <div class="brand-tagline">Secure & Encrypted Transaction Processing</div>
        </div>

        @if(session('error'))
            <div class="alert alert-error">
                <span>⚠</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Content Card -->
        <div class="checkout-card">
            @yield('content')
        </div>

        <!-- Security Footer -->
        <div class="checkout-footer">
            <span class="security-badge">
                <svg class="lock-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                </svg>
                256-bit SSL Secured · Powered by FMDQ Infrastructure
            </span>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
