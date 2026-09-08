<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FleetNG Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        h1{
            margin-bottom: 5px;
            font-size: 18px;
        }
        h2{
            margin-bottom: 10px;
            font-size: 16px;
        }
        h3{
            margin-bottom: 10px;
            font-size: 14px;
        }
        h4{
            margin-bottom: 10px;
            font-size: 13px;
        }
        .invoice-container {
            max-width: 1000px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details th,
        .invoice-details td {
            padding: 12px 12px 12px 20px;
            border-bottom: 1px solid #ccc;
            font-size: 12px;
        }
        .invoice-details th {
            text-align: left;
            background-color: #f2f2f2;
        }
        .status-paid {
            color: green;
        }
        .status-pending {
            color: orange;
        }
        .status-failed {
            color: red;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="invoice-header">            
            <h2>Payment Invoice</h2>
            <h1>FleetNG Trucking Company Limited</h1>
            <label style="font-size: 12px;">Suite 6, Scapular Plaza KM 17 Lekki-Epe expressway Eti-Osa Lagos Nigeria</label>
        </div>
        <br>
        <div class="invoice-details">
            <h3>Invoice to:</h3>
            <table>
                <tr>
                    <th>Name</th>
                    <td>{{$trip->client_name ?? '--'}}</td>
                    <th>Phone</th>
                    <td>{{$trip->client->phone_number ?? '--'}}</td>
                </tr>
            </table>
        </div>
        <br>
        <div class="invoice-details">
            <h3>Trip Information:</h3>
            <table>
                <tr>
                    <th>Pickup Location</th>
                    <th>Drop Location</th>
                    <th>Trip Cost</th>
                    
                </tr>
                <tr>
                    <td>{{$trip->pickup_location_alias ?? '--'}}</td>
                    <td>{{$trip->drop_location ?? '--'}}</td>
                    <td>{{$trip->total_trip_cost}} NGN</td>
                </tr>
            </table>
        </div>
        <br>
        <div class="invoice-details">
            <h3>Payment Details:</h3>
            <table>
                <tr>
                    <th>Transaction Date</th>
                    <td>{{$payment->created_at ?? '--'}}</td>
                    
                    <th>Transaction ID</th>
                    <td>{{$payment->transaction_id ?? '--'}}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{$payment->amount ?? '--'}} NGN</td>
                    <th>Payment Type</th>
                    <td>{{$payment->payment_type ?? '--'}}</td>
                </tr>
                <tr>
                    <th colspan="1">Reference ID</th>
                    <td colspan="3">{{$payment->reference_code ?? '--'}}</td>
                </tr>
            </table>
        </div>
        <h4 style="text-align: center; margin-bottom: 0;">Thank You!</h4>
    </div>
</body>
</html>