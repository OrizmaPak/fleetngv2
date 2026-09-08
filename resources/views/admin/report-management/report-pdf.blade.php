<!DOCTYPE html>
<html>
<head>
    <title>Report PDF</title>
    <style>
      body{
        font-family: 'Roboto', sans-serif;
      }
      #revenue tr th{
        background-color:#7367F0; 
        color:white;
        border: 1px solid #ddd;
        padding: 8px;
      }
      #revenue tr td{
        border: 1px solid #ddd;
        padding: 8px;
      }
      #tripReport {
        border-collapse: collapse;
        width: 100%;
      }    
      #tripReport td, #tripReport th {
        border: 1px solid #ddd;
        padding: 8px;
      }
      #tripReport tr:nth-child(even){
        background-color: #f2f2f2;
      }
      #tripReport th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #7367F0;
        color: white;
      }
    </style>
</head>
<body>
  <h1 style="color: #7468F0; font-family: 'Roboto', sans-serif; font-weight: 700; text-align: center; margin-top: 0;"><u>FleetNG Report Summary</u></h1>
    <div style="border: 1px solid #ddd">
      <h3 style="margin:5px; text-align: center; font-family: 'Roboto', sans-serif; font-weight: 700;">Revenue Report</h3>
      <table id="revenue" style="width: 100%">
        <thead>
          <tr style="width: 100%">
            <th style="width: 20%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">Completed Trip</th>
            <th style="width: 20%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">Live Trip</th>
            <th style="width: 20%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">New Trip</th>
            <th style="width: 20%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">Cancelled Trip</th>
            <th style="width: 20%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">Paid Trips</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['completed_trips']) }}</td>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['live_trips']) }}</td>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['new_trips']) }}</td>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['cancel_trips']) }}</td>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['paid_trips']) }}</td>
          </tr>
        </tbody>
      </table>
      <table id="revenue" style="width: 100%">
        <thead>
          <tr style="width: 100%">
            <th style="width: 25%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">Total Earning</th>
            <th style="width: 25%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">Profit</th>
            <th style="width: 25%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">Total Commision</th>
            <th style="width: 25%; border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 12px;">Client Due Payment</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['total_earning'] ,2) }}</td>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['profit'] ,2) }}</td>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['total_commission'] ,2) }}</td>
            <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 12px;">{{ number_format($response['data']['trip_due_amount']) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="table " style="border:1px solid #ddd; margin-top:10px">
      <h3 style="margin:5px; text-align: center; font-family: 'Roboto', sans-serif; font-weight: 700;">Trip Details</h3>
        <table class="datatables-ajax table" id="tripReport">
          <thead>
            <tr>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">#</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Date</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Driver</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Client</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Pickup</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Drop off</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Total Cost<br> (NGN)</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Trip Cost<br> (NGN)</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Cost of Sand<br> (NGN)</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Road Money<br> (NGN)</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Commission<br> (NGN)</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Status</th>
              <th style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 600; font-size: 10px;">Payment Status</th>
            </tr>
          </thead>
            @php
            $status = [1=>"New Trip", 2=>"Live Trip", 3=>"Complete Trip", 4=>"Canceled Trip", 5=> "Declined Trip"];
            @endphp
            <tbody>
              @foreach ($response['data']['trips_detail'] as $item)
              <tr>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ $loop->iteration }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ date('d/m/Y', strtotime($item->trip_generated_at)) }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ $item->first_name }} {{ $item->last_name }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ $item->client_name }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ $item->location_name }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ $item->drop_location }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ number_format($item->total_trip_cost , 2) }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ number_format($item->total_cost , 2) }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ number_format($item->cost_of_sand , 2) }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ number_format($item->road_money , 2) }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ number_format($item->driver_commission ,2) }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ $status[$item->status] }}</td>
                <td style="border-collapse: collapse; font-family: 'Roboto', sans-serif; font-weight: 400; font-size: 10px;">{{ $item->payment_status }}</td>
              </tr>
              @endforeach
            </tbody>       
        </table>
    </div>   
</body>
</html>