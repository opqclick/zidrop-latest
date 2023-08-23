<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Balance Added</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100%;
        }
        .header {
            text-align: center;
            background-color: #f5f5f5;
            padding: 20px;
        }
        table {
            /*width: 100%;*/
            border-collapse: collapse;
        }
        th {
            text-align: right;
        }
        th, td {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <img src="{{ asset('logo.png') }}" alt="ZiDrop Logistics" style="width: 150px;">
</div>
<div class="mail-body" style="width: 90%;margin: 0 auto;max-width: 700px;">
    <h1 style="font-size: 20px;">
        Hi {{ $merchant->companyName }},
    </h1>

    <p>
        Your parcel (Tacking Code: {{ $parcel->trackingCode }}) has been updated!
    </p>

    <table border="1" style="text-align: center">
        <tr>
            <th colspan="3"  style="text-align: center">Parcel Updated Status</th>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>:</td>
            <td>Awaiting</td>
        </tr>
        <tr>
            <td><strong>Note</strong></td>
            <td>:</td>
            <td>Arrived Delivery Facility</td>
        </tr>
        <tr>
            <th colspan="3"  style="text-align: center">
                <a href="{{route('merchant.parcel-details', $parcel->id)}}">View Parcel Details</a>
            </th>
        </tr>
    </table>


    <p style="line-height: 0.1;margin: 0;">&nbsp;</p>
    <p>If you have any questions about this, please
        <a href="{{ route('frontend.contact-us') }}">Contact With Us</a>
    </p>


    <p>Have a great day!</p>

    <p><strong>Zidrop Logistics</strong></p>
</div>
<hr>
<p class="footer-content" style="text-align: center;margin-top: 0;margin-bottom: 50px;">
    Zidrop Logistics. All rights reserved.
</p>



</body>
</html>