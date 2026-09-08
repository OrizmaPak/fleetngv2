<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>FleetNG - Contact Us</title>
</head>

<body>
    <div class="container">
        <div style="text-align: center; margin-top: 25px;">
            <img src="{{asset('images/logo/fleetng-logo.png')}}" width="80" alt="Logo" style="margin-bottom: 5px; margin-left: auto; margin-right: auto;">
            <h2 style="font-family: 'Poppins', sans-serif;">Contact Us Enquiry</h4>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <table align="center" class="table table-responsive text-center" border="1" width="100%" style="max-width: 500px; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td align="left" style="padding: 4px 14px; font-family: 'Poppins', sans-serif;">
                                <p style="font-weight:700;">Name:</p>
                            </td>
                            <td align="left" style="padding: 4px 14px; font-family: 'Poppins', sans-serif;">
                                <p>{{ $data['name'] }}</p>
                            </td>
                        </tr>

                        <tr>
                            <td align="left" style="padding: 4px 14px; font-family: 'Poppins', sans-serif;">
                                <p style="font-weight:700;">Email ID:</p>
                            </td>
                            <td align="left" style="padding: 4px 14px; font-family: 'Poppins', sans-serif;">
                                <p>{{ $data['email'] }}</p>
                            </td>
                        </tr>

                        <tr>
                            <td align="left" style="padding: 4px 14px; font-family: 'Poppins', sans-serif;">
                                <p style="font-weight:700;">Phone:</p>
                            </td>
                            <td align="left" style="padding: 4px 14px; font-family: 'Poppins', sans-serif;">
                                <p>{{ $data['phone'] }}</p>
                            </td>
                        </tr>

                        <tr>
                            <td align="left" style="padding: 4px 14px; font-family: 'Poppins', sans-serif;">
                                <p style="font-weight:700;">Message:</p>
                            </td>
                            <td align="left" style="padding: 4px 14px; font-family: 'Poppins', sans-serif;">
                                <p>{{ $data['message'] }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>