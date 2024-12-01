<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyer Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        h1,
        h2,
        h3 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .summary-table {
            margin-bottom: 30px;
        }

        .summary-table td {
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>

<body>
    <h1>Buyer Report</h1>
    <h2>From {{ $start_date }} to {{ $end_date }}</h2>


    <h3>Spending Summary</h3>
    <table class="summary-table">
        <tr>
            <td>Total Spent:</td>
            <td>${{ number_format($reportData['spending_summary']->total_spent, 2) }}</td>
        </tr>
        <tr>
            <td>Total Orders:</td>
            <td>{{ $reportData['spending_summary']->total_orders }}</td>
        </tr>
    </table>


    <h3>Purchasing Patterns</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Purchase Count</th>
                <th>Total Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData['purchases'] as $purchase)
                <tr>
                    <td>{{ $purchase->product_name }}</td>
                    <td>{{ $purchase->purchase_count }}</td>
                    <td>${{ number_format($purchase->total_spent, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('Y-m-d H:i:s') }}.
    </div>
</body>

</html>
