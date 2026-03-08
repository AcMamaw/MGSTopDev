<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }
        .header { width: 100%; margin-bottom: 15px; }
        .store-name { font-size: 14px; font-weight: bold; }
        .receipt-title { font-size: 18px; color: #f5a623; letter-spacing: 4px; font-weight: bold; text-align: right; }
        .receipt-meta { text-align: right; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead tr { background-color: #f5a623; }
        thead th { padding: 6px 8px; font-size: 10px; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .totals-wrap { width: 100%; margin-top: 10px; }
        .totals-table { float: right; width: 200px; font-size: 11px; }
        .totals-table td { padding: 2px 4px; border: none; }
        .total-line { border-top: 1px solid #ccc; font-weight: bold; }
        .notes { margin-top: 40px; font-size: 10px; clear: both; }
        .authorized { margin-top: 30px; text-align: right; font-size: 10px; }
        .signature-line { border-bottom: 1px solid #333; display: inline-block; padding: 0 30px 2px 30px; font-weight: bold; }
        .billed { font-size: 11px; margin-bottom: 10px; }
        .label { font-size: 10px; color: #777; text-transform: uppercase; font-weight: bold; }
        .two-col { width: 100%; }
        .two-col td { vertical-align: top; padding: 0; }
    </style>
</head>
<body>

    @php $logoPath = public_path('images/ace.jpg'); @endphp

    {{-- Header --}}
    <table class="two-col">
        <tr>
            {{-- Left: Store info --}}
            <td style="width:60%; vertical-align:top;">
                <p class="store-name">Mariviles Graphic Studio</p>
                <p>Adopted CO.</p>
                <p>Mati City</p>
                <br>
                <p><strong>Receipt #:</strong> R-{{ str_pad($payment->payment_id, 5, '0', STR_PAD_LEFT) }}</p>
            </td>

            {{-- Right: Logo + RECEIPT title + meta --}}
            <td style="width:40%; text-align:right; vertical-align:top;">
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}"
                         alt="MGS Logo"
                         style="max-width:110px; max-height:55px; display:inline-block; margin-bottom:4px;">
                @endif
                <p class="receipt-title">RECEIPT</p>
                <br>
                <p><strong>Receipt Date:</strong> {{ $payment->payment_date }}</p>
                <p><strong>Status:</strong> {{ $payment->status }}</p>
                <p><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
                @if($payment->reference_number)
                    <p><strong>Reference #:</strong> {{ $payment->reference_number }}</p>
                @endif
            </td>
        </tr>
    </table>

    <br>

    {{-- Billed To --}}
    <p class="label">Billed To</p>
    <p><strong>{{ $customerName }}</strong></p>
    <p>{{ $customerAddress }}</p>

    <br>

    {{-- Items Table --}}
    <table>
        <thead>
            <tr>
                <th class="text-left">Qty</th>
                <th class="text-left">Item</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Customize</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['quantity'] }}</td>
                <td>
                    <strong>{{ $item['product_name'] }}</strong><br>
                    <small style="color:#666;">
                        @if(!empty($item['size'])) Size: {{ $item['size'] }} @endif
                        @if(!empty($item['color'])) | Color: {{ $item['color'] }} @endif
                    </small>
                </td>
                <td class="text-right">{{ number_format($item['unit_price'], 2) }}</td>
                <td class="text-right">{{ number_format($item['custom_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-wrap">
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
            </tr>
            <tr class="total-line">
                <td><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($payment->amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Cash</td>
                <td class="text-right">{{ number_format($payment->cash, 2) }}</td>
            </tr>
            <tr>
                <td>Change</td>
                <td class="text-right">{{ number_format($payment->change_amount, 2) }}</td>
            </tr>
            @if(($payment->balance ?? 0) > 0)
            <tr>
                <td style="color:red;">Balance</td>
                <td class="text-right" style="color:red;">{{ number_format($payment->balance, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Notes --}}
    <div class="notes">
        <strong>Notes</strong><br>
        Thank you for choosing Mariviles Graphic Studio.
        Your positive feedback helps us continue providing quality service.
    </div>

    {{-- Authorized By --}}
    <div class="authorized">
        <p class="signature-line">{{ $authorizedBy }}</p>
        <br>
        <p>Authorized by</p>
    </div>

</body>
</html>