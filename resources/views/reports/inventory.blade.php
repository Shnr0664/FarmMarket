<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Report</title>
</head>

<body>
    <h1>Inventory Report</h1>
    <p>Generated on: {{ now() }}</p>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Stock Level</th>
                <th>Turnover Rate</th>
                <th>Restocking Alert</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $data)
                <tr>
                    <td>{{ $data->product_name }}</td>
                    <td>{{ $data->stock_level }}</td>
                    <td>{{ $data->total_sold }}</td>
                    <td>{{ $data->restocking_alert }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
