<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Monthly Task Report</title>
</head>
<body>
    <h2>Hello, your Monthly Task Report is attached</h2>
    <p>Period: {{ \Carbon\Carbon::now()->subMonth()->format('F Y') }}</p>
    <p>Please find the Excel file with your completed and ongoing tasks for the last month.</p>
    <p>Regards,<br>Task Manager System</p>
</body>
</html>
