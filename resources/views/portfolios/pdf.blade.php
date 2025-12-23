<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $portfolio->name }} Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f1f1f1; }
        h2 { margin-bottom: 0; }
        .green { color: green; }
        .red { color: red; }
    </style>
</head>
<body>

<h2>{{ $portfolio->name }} – Portfolio Report</h2>
<p>Date: {{ now()->format('d M Y') }}</p>

<h3>Summary</h3>
<ul>
    <li>Total Investment: ₹{{ number_format($totalInvestment, 2) }}</li>
    <li>Current Value: ₹{{ number_format($totalCurrentValue, 2) }}</li>
    <li>
        Profit / Loss:
        <span class="{{ $profitLoss >= 0 ? 'green' : 'red' }}">
            ₹{{ number_format($profitLoss, 2) }}
            ({{ number_format($profitLossPercent, 2) }}%)
        </span>
    </li>
</ul>

<h3>Holdings</h3>
<table>
    <thead>
        <tr>
            <th>Symbol</th>
            <th>Qty</th>
            <th>Buy Price</th>
            <th>Current Price</th>
            <th>Sector</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($holdings as $h)
            <tr>
                <td>{{ strtoupper($h->symbol) }}</td>
                <td>{{ $h->quantity }}</td>
                <td>₹{{ number_format($h->buy_price, 2) }}</td>
                <td>₹{{ number_format($h->current_price, 2) }}</td>
                <td>{{ $h->sector }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
