<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }

        /* Header */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .header-table td { vertical-align: top; padding: 0; }
        .store-name { font-size: 14px; font-weight: bold; margin-bottom: 2px; }
        .store-sub { font-size: 10px; color: #555; margin-bottom: 2px; }
        .receipt-title { font-size: 18px; color: #f5a623; letter-spacing: 4px; font-weight: bold; text-align: right; }

        /* Receipt # + date row */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .meta-table td { vertical-align: top; padding: 0; font-size: 11px; }
        .meta-right { text-align: right; }

        /* Billed To + Status row */
        .billed-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .billed-table td { vertical-align: top; padding: 0; font-size: 11px; }
        .label { font-size: 10px; color: #777; text-transform: uppercase; font-weight: bold; margin-bottom: 3px; }
        .billed-right { text-align: right; }

        /* Items table */
        .items-table { width: 100%; border-collapse: collapse; margin-top: 0; }
        .items-table thead tr { background-color: #f5a623; }
        .items-table thead th { padding: 6px 8px; font-size: 10px; font-weight: bold; }
        .items-table tbody td { padding: 5px 8px; border-bottom: 1px solid #eee; font-size: 11px; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .item-sub { font-size: 10px; color: #666; margin-top: 2px; }

        /* Totals */
        .totals-wrap { width: 100%; margin-top: 10px; overflow: hidden; }
        .totals-table { float: right; width: 210px; font-size: 11px; border-collapse: collapse; }
        .totals-table td { padding: 2px 4px; border: none; }
        .total-line td { border-top: 1px solid #ccc; font-weight: bold; padding-top: 4px; }
        .balance-row td { color: red; font-weight: bold; }

        /* Notes */
        .notes { margin-top: 40px; font-size: 10px; clear: both; }
        .notes strong { display: block; margin-bottom: 3px; }

        /* Authorized */
        .authorized { margin-top: 30px; text-align: right; font-size: 10px; }
        .signature-line { border-bottom: 1px solid #333; display: inline-block; padding: 0 30px 2px 30px; font-weight: bold; }

        /* Divider */
        .divider { border: none; border-top: 1px solid #eee; margin: 8px 0; }
    </style>
</head>
<body>

    {{-- Header: Store info + RECEIPT title --}}
    <table class="header-table">
        <tr>
            <td style="width:60%">
                <p class="store-name">Mariviles Graphic Studio</p>
                <p class="store-sub">Adopted CO.</p>
                <p class="store-sub">Mati City</p>
            </td>
            <td style="width:40%; text-align:right;">
                <p class="receipt-title">RECEIPT</p>
            </td>
        </tr>
    </table>

    {{-- Receipt # + Date --}}
    <table class="meta-table">
        <tr>
            <td style="width:50%">
                <p><strong>Receipt #:</strong> R-{{ str_pad($payment->payment_id, 5, '0', STR_PAD_LEFT) }}</p>
            </td>
            <td style="width:50%" class="meta-right">
                <p><strong>Receipt Date:</strong> {{ $payment->payment_date }}</p>
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- Billed To + Status --}}
    <table class="billed-table">
        <tr>
            <td style="width:50%">
                <p class="label">Billed To</p>
                <p><strong>{{ $customerName }}</strong></p>
                <p style="font-size:10px; color:#555;">{{ $customerAddress }}</p>
            </td>
            <td style="width:50%" class="billed-right">
                <p><strong>Status:</strong> {{ $payment->status }}</p>
                <p><strong>Payment Method:</strong> {{ $payment->payment_method }}</p>
                @if($payment->reference_number)
                    <p><strong>Reference #:</strong> {{ $payment->reference_number }}</p>
                @endif
            </td>
        </tr>
    </table>

    <hr class="divider">

    {{-- Items Table --}}
    <table class="items-table">
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
                <td><strong>{{ $item['quantity'] }}</strong></td>
                <td>
                    <strong>{{ $item['product_name'] }}</strong>
                    <p class="item-sub">
                        @if(!empty($item['size'])) Size: {{ $item['size'] }} @endif
                        @if(!empty($item['color'])) | Color: {{ $item['color'] }} @endif
                    </p>
                </td>
                <td class="text-right">&#8369;{{ number_format($item['unit_price'], 2) }}</td>
                <td class="text-right">&#8369;{{ number_format($item['custom_amount'] ?? 0, 2) }}</td>
                <td class="text-right"><strong>&#8369;{{ number_format($item['amount'], 2) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-wrap">
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">&#8369;{{ number_format($payment->amount, 2) }}</td>
            </tr>
            <tr class="total-line">
                <td><strong>Total</strong></td>
                <td class="text-right"><strong>&#8369;{{ number_format($payment->amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Cash</td>
                <td class="text-right">&#8369;{{ number_format($payment->cash, 2) }}</td>
            </tr>
            <tr>
                <td>Change</td>
                <td class="text-right">&#8369;{{ number_format($payment->change_amount, 2) }}</td>
            </tr>
            @if(($payment->balance ?? 0) > 0)
            <tr class="balance-row">
                <td>Balance</td>
                <td class="text-right">&#8369;{{ number_format($payment->balance, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Notes --}}
    <div class="notes">
        <strong>Notes</strong>
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