<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .title {
            font-size: 18px;
            margin-top: 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
        }
        .value {
            margin-left: 5px;
        }
        .earnings-section, .deductions-section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">HR SYSTEM</div>
        <div class="title">PAYSLIP</div>
        <div>{{ $monthName }} {{ $payroll->year }}</div>
    </div>

    <div class="info-section">
        <div class="section-title">Employee Information</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Employee Name:</span>
                <span class="value">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</span>
            </div>
            <div class="info-item">
                <span class="label">Employee ID:</span>
                <span class="value">{{ $payroll->employee->employee_id }}</span>
            </div>
            <div class="info-item">
                <span class="label">Department:</span>
                <span class="value">{{ $payroll->employee->department->name }}</span>
            </div>
            <div class="info-item">
                <span class="label">Position:</span>
                <span class="value">{{ $payroll->employee->job_title }}</span>
            </div>
        </div>
    </div>

    <div class="earnings-section">
        <div class="section-title">Earnings</div>
        <table>
            <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Basic Salary</td>
                <td>${{ number_format($payroll->basic_salary, 2) }}</td>
            </tr>
            <tr>
                <td>Allowances</td>
                <td>${{ number_format($payroll->allowances, 2) }}</td>
            </tr>
            <tr>
                <td>Overtime</td>
                <td>${{ number_format($payroll->overtime, 2) }}</td>
            </tr>
            <tr>
                <td>Bonus</td>
                <td>${{ number_format($payroll->bonus, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Earnings</td>
                <td>${{ number_format($payroll->basic_salary + $payroll->allowances + $payroll->overtime + $payroll->bonus, 2) }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="deductions-section">
        <div class="section-title">Deductions</div>
        <table>
            <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Tax</td>
                <td>${{ number_format($payroll->tax, 2) }}</td>
            </tr>
            <tr>
                <td>Other Deductions</td>
                <td>${{ number_format($payroll->deductions, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Deductions</td>
                <td>${{ number_format($payroll->tax + $payroll->deductions, 2) }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <table>
            <tbody>
            <tr>
                <td><strong>Total Earnings</strong></td>
                <td>${{ number_format($payroll->basic_salary + $payroll->allowances + $payroll->overtime + $payroll->bonus, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Deductions</strong></td>
                <td>${{ number_format($payroll->tax + $payroll->deductions, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Net Salary</strong></td>
                <td>${{ number_format($payroll->net_salary, 2) }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="payment-info">
        <div class="section-title">Payment Information</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Payment Method:</span>
                <span class="value">{{ $payroll->payment_method ? ucfirst(str_replace('_', ' ', $payroll->payment_method)) : 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Payment Date:</span>
                <span class="value">{{ $payroll->payment_date ? $payroll->payment_date->format('d M, Y') : 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Payment Status:</span>
                <span class="value">{{ ucfirst($payroll->payment_status) }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>For any queries regarding this payslip, please contact the HR department.</p>
    </div>
</div>
</body>
</html>
