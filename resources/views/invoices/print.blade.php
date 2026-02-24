<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            color: #333;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header .invoice-number {
            font-size: 14px;
            color: #666;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .info-section .info-block {
            width: 45%;
        }

        .info-section .info-block h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }

        .info-section .info-block p {
            margin-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
        }

        table th.text-right {
            text-align: right;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table td.text-right {
            text-align: right;
        }

        .totals {
            width: 300px;
            margin-left: auto;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }

        .totals .row.total {
            font-weight: bold;
            font-size: 18px;
            border-bottom: 2px solid #333;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .notes h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Close</button>
    </div>

    <div class="header">
        <div>
            <h1>INVOICE</h1>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
        </div>
        <div style="text-align: right;">
            <p><strong>Date:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</p>
            @if($invoice->due_date)
                <p><strong>Due Date:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>
            @endif
            <p><strong>Status:</strong> {{ $invoice->status->label() }}</p>
        </div>
    </div>

    <div class="info-section">
        <div class="info-block">
            <h3>From</h3>
            <p><strong>{{ Auth::user()->name }}</strong></p>
            <p>{{ Auth::user()->email }}</p>
        </div>
        <div class="info-block">
            <h3>Bill To</h3>
            <p><strong>{{ $invoice->customer->name }}</strong></p>
            <p>{{ $invoice->customer->email }}</p>
            @if($invoice->customer->phone)
                <p>{{ $invoice->customer->phone }}</p>
            @endif
            @if($invoice->customer->address)
                <p>{{ $invoice->customer->address }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">${{ $item->formatted_unit_price }}</td>
                    <td class="text-right">${{ $item->formatted_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>Subtotal</span>
            <span>${{ $invoice->formatted_subtotal }}</span>
        </div>
        <div class="row">
            <span>Tax</span>
            <span>${{ $invoice->formatted_tax }}</span>
        </div>
        <div class="row total">
            <span>Total</span>
            <span>${{ $invoice->formatted_total }}</span>
        </div>
    </div>

    @if($invoice->notes)
        <div class="notes">
            <h3>Notes</h3>
            <p>{{ $invoice->notes }}</p>
        </div>
    @endif
</body>

</html>