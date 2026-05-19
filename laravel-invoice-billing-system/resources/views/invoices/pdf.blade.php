<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }

        .company-info {
            float: left;
            width: 50%;
        }

        .company-info h1 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .company-info p {
            color: #666;
            margin: 3px 0;
        }

        .invoice-title {
            float: right;
            width: 50%;
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 32px;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .invoice-title p {
            font-size: 14px;
            font-weight: bold;
            color: #4b5563;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .invoice-details {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .invoice-info,
        .client-info {
            float: left;
            width: 48%;
        }

        .client-info {
            float: right;
        }

        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }

        .info-row {
            margin: 8px 0;
        }

        .info-label {
            color: #6b7280;
            display: inline-block;
            width: 100px;
        }

        .info-value {
            font-weight: bold;
            color: #1f2937;
        }

        .client-box {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
        }

        .client-name {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .client-company {
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-unpaid {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .items-table {
            width: 100%;
            margin-top: 30px;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .items-table thead {
            background-color: #f3f4f6;
        }

        .items-table th {
            padding: 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
            border-bottom: 2px solid #e5e7eb;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            color: #1f2937;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #e5e7eb;
        }

        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }

        .totals-box {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 5px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            color: #4b5563;
        }

        .total-row.grand-total {
            border-top: 2px solid #d1d5db;
            padding-top: 12px;
            margin-top: 12px;
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }

        .notes {
            margin-top: 80px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .notes-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .notes-content {
            color: #4b5563;
            line-height: 1.8;
        }

        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
        }

        .footer p {
            margin: 5px 0;
        }

        .thank-you {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header clearfix">
        <div class="company-info">
            @if ($invoice->logo_path)
                <img src="{{ public_path('storage/' . $invoice->logo_path) }}" alt="Logo"
                    style="max-height: 60px; margin-bottom: 10px;">
            @elseif ($invoice->user && $invoice->user->logo_path)
                <img src="{{ public_path('storage/' . $invoice->user->logo_path) }}" alt="Logo"
                    style="max-height: 60px; margin-bottom: 10px;">
            @endif
            <h1>{{ $invoice->user->name ?? 'iCreativez Technologies' }}</h1>
            <p>Karachi, Pakistan</p>
            <p>+92 300 5000248</p>
            <p>hello@icreativez.info</p>
            <p>www.icreativez.com</p>
        </div>
        <div class="invoice-title">
            <h2>INVOICE</h2>
            <p>{{ $invoice->invoice_number }}</p>
        </div>
    </div>

    <!-- Invoice Details -->
    <div class="invoice-details clearfix">
        <div class="invoice-info">
            <div class="section-title">Invoice Information</div>
            <div class="info-row">
                <span class="info-label">Invoice Date:</span>
                <span class="info-value">{{ $invoice->invoice_date->format('d M, Y') }}</span>
            </div>
            @if ($invoice->due_date)
                <div class="info-row">
                    <span class="info-label">Due Date:</span>
                    <span class="info-value">{{ $invoice->due_date->format('d M, Y') }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Status:</span>
                @if ($invoice->status == 'paid')
                    <span class="status-badge status-paid">✓ Paid</span>
                @elseif($invoice->status == 'unpaid')
                    <span class="status-badge status-unpaid">✗ Unpaid</span>
                @else
                    <span class="status-badge status-pending">⏱ Pending</span>
                @endif
            </div>
        </div>

        <div class="client-info">
            <div class="section-title">Bill To</div>
            <div class="client-box">
                <div class="client-name">{{ $invoice->client->name }}</div>
                @if ($invoice->client->company)
                    <div class="client-company">{{ $invoice->client->company }}</div>
                @endif
                @if ($invoice->client->email)
                    <p>{{ $invoice->client->email }}</p>
                @endif
                @if ($invoice->client->phone)
                    <p>{{ $invoice->client->phone }}</p>
                @endif
                @if ($invoice->client->address)
                    <p style="margin-top: 5px;">{{ $invoice->client->address }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 15%;" class="text-right">Quantity</th>
                <th style="width: 15%;" class="text-right">Price</th>
                <th style="width: 20%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $currencySymbol }} {{ number_format($item->price, 2) }}</td>
                    <td class="text-right"><strong>{{ $currencySymbol }} {{ number_format($item->total, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <div class="totals-box">
            <div class="total-row">
                <span>Subtotal:</span>
                <span><strong>{{ $currencySymbol }} {{ number_format($invoice->subtotal, 2) }}</strong></span>
            </div>
            <div class="total-row">
                <span>Tax:</span>
                <span><strong>{{ $currencySymbol }} {{ number_format($invoice->tax, 2) }}</strong></span>
            </div>
            <div class="total-row grand-total">
                <span>Total:</span>
                <span>{{ $currencySymbol }} {{ number_format($invoice->total, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if ($invoice->notes)
        <div class="notes clearfix">
            <div class="notes-title">Notes / Terms</div>
            <div class="notes-content">{{ $invoice->notes }}</div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p class="thank-you">Thank you for your business!</p>
        <p style="font-size: 10px;">This is a computer-generated invoice and does not require a signature.</p>
    </div>

</body>

</html>
