<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        /* Base styling */
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
            color: #002E58;
        }
        .invoice-wrapper {
            width: 100%;
            padding: 20px;
        }

        /* Header */
        .header {
            width: 100%;
            display: table;
            margin-bottom: 30px;
        }
        .header .left,
        .header .right {
            display: table-cell;
            vertical-align: middle;
        }
        .header .left {
            width: 60%;
        }
        .header .right {
            width: 40%;
            text-align: right;
        }
        .logo img {
            max-height: 80px;
        }

        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details h2 {
            margin: 0;
            font-size: 24px;
        }
        .invoice-details .small {
            font-size: 12px;
            color: #1976C9;
        }

        /* Info section */
        .section-info {
            margin-bottom: 20px;
        }
        .section-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .section-info table th, .section-info table td {
            text-align: left;
            padding: 8px;
        }
        .section-info table th {
            width: 20%;
            color: #003F7D;
        }

        /* Items table */
        .items {
            margin-top: 20px;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        .items table th, .items table td {
            border: 1px solid #DCEEFF;
            padding: 8px;
            font-size: 14px;
        }
        .items table th {
            background-color: #EAF5FF;
        }

        /* Totals */
        .totals {
            margin-top: 20px;
            width: 100%;
        }
        .totals .right {
            float: right;
            width: 40%;
        }
        .totals .right table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals .right table td {
            padding: 6px 8px;
            font-size: 14px;
        }
        .totals .right table tr:last-child td {
            font-size: 16px;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #1976C9;
        }

        /* Font Awesome embedding */
        @font-face {
            font-family: 'FontAwesomeSolid';
            font-style: normal;
            font-weight: 900;
            src: url('{{ public_path("fonts/fontawesome/fa-solid-900.ttf") }}') format('truetype');
        }
        .fas { 
            font-family: 'FontAwesomeSolid' !important;
            font-weight: 900;
        }

        /* If using other FA variants, embed similarly */
    </style>
</head>
<body>
    <div class="invoice-wrapper" style="margin-left:-15px; border:1px solid #DCEEFF; padding:15px; padding-bottom:50px; box-shadow:0 0 10px rgba(0, 46, 88, 0.12);">
        <div class="header">
            <div class="left logo">
                @if (file_exists(public_path('admin/images/retail-point-of-sale-logo.png')))
                    <img src="{{ public_path('admin/images/retail-point-of-sale-logo.png') }}" alt="Retail Point Of Sale">
                @else
                    <div style="font-size: 24px; font-weight: bold;">{{ optional($sale->store)->name }}</div>
                @endif
            </div>
            <div class="right invoice-details">
                <h2>Invoice #{{ $sale->id }}</h2>
                <div class="small">Date: {{ $sale->created_at->format('Y-m-d H:i') }}</div>
                <div class="small">Cashier: {{ optional($sale->user)->name }}</div>
            </div>
        </div>

        <div class="section-info">
            <table>
                <tr>
                    <th>Store:</th>
                    <td>{{ optional($sale->store)->name }}</td>
                    <th>Customer:</th>
                    <td>{{ optional($sale->customer)->name ?? 'Walk‑in' }}</td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td>{{ optional($sale->store)->address ?? 'Islamabad, Pakistan' }}</td>
                    <th>Email:</th>
                    <td>{{ optional($sale->customer)->email ?? '' }}</td>
                </tr>
            </table>
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Price</th>
                        <th style="text-align:right;">Discount</th>
                        <th style="text-align:right;">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->sale_items as $index => $it)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ optional($it->product)->name }}</td>
                            <td style="text-align:center;">{{ $it->quantity }}</td>
                            <td style="text-align:right;">{{ number_format($it->price ?? 0, 2) }}</td>
                            <td style="text-align:right;">{{ number_format($it->discount ?? 0, 2) }}</td>
                            <td style="text-align:right;">{{ number_format((($it->price ?? 0) * ($it->quantity ?? 0)) - (($it->discount ?? 0) * ($it->quantity ?? 0)), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="right">
                <table>
                    <tr>
                        <td>Subtotal:</td>
                        <td style="text-align:right;">{{ number_format($sale->subtotal ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount:</td>
                        <td style="text-align:right;">{{ number_format($sale->discount ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Tax:</td>
                        <td style="text-align:right;">{{ number_format($sale->tax ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td style="text-align:right;"><strong>{{ number_format($sale->total ?? 0, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>If you have any questions, please contact us at {{ optional($sale->store)->email ?? 'Taylor211@gmail.com' }} or call {{ optional($sale->store)->phone ?? '+55-5555-555-55' }}</p>
        </div>
    </div>
</body>
</html>
