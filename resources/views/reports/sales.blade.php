<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        h1,
        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Sales Report</h1>
    <h2>Summary</h2>
    <table>
        <thead>
            <tr>
                <th>Period</th>
                <th>Total Revenue</th>
                <th>Total Orders</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData['sales_summary'] as $summary)
                <tr>
                    <td>{{ $summary->period }}</td>
                    <td>${{ number_format($summary->total_revenue, 2) }}</td>
                    <td>{{ $summary->total_orders }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Product Popularity</h2>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Sold</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData['product_popularity'] as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->total_sold }}</td>
                    <td>${{ number_format($product->total_revenue, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
