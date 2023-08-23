@extends('frontEnd.layouts.pages.merchant.merchantmaster')
@section('title', 'Dashboard')
@section('content')

    <style>
        @media screen and (min-width: 320px) and (max-width: 767px) {
            .mobile-men {
                margin-top: 52px;
            }
        }
    </style>
    <div class="container-fluide mobile-men">
        <div class="row">
            <div class="col-sm-12" style="background-color:#af251b;padding-top:7px;">
                <marquee style="font-weight: bold;color:white;">
                    {{ $notice->title }}
                </marquee>
            </div>
        </div>
    </div>












    <section class="section-padding">
        <b style="text-decoration: underline;text-transform:uppercase;">Transaction count for {{ date('F') }},
            {{ date('Y') }}</b>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/pending?month=' . true) }}">
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#1d2941">
                            <p class="text-center text-light">Pending</p>
                            <p class="text-center text-light"><b>{{ $m_pending == 0 ? null : $m_pending }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/in transit?month=' . true) }}">
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#5f45da">
                            <p class="text-center text-light">In Transit</p>
                            <p class="text-center text-light"><b>{{ $m_pick == 0 ? null : $m_pick }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/awaiting?month=' . true) }}">
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#670a91">
                            <p class="text-center text-light">Awaiting</p>
                            <p class="text-center text-light"><b>{{ $m_await == 0 ? null : $m_await }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/deliverd?month=' . true) }}">
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#096709">
                            <p class="text-center text-light">Delivered</p>
                            <p class="text-center text-light"><b>{{ $m_deliver == 0 ? null : $m_deliver }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/partial-delivery?month=' . true) }}">
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#28a745;">
                            <p class="text-center text-light">Partial Delivery</p>
                            <p class="text-center text-light">
                                <b>{{ $m_partial_deliver == 0 ? null : $m_partial_deliver }}</b>
                            </p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/return-to-merchant?month=' . true) }}">
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#9a8309">
                            <p class="text-center text-light">Returned</p>
                            <p class="text-center text-light"><b>{{ $m_return == 0 ? null : $m_return }}</b></p>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/Collected-amount-from-DA?month=' . true) }}">
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#1F6E91">
                            <p class="text-center text-light">Collected amount from DA</p>
                            <p class="text-center text-light"><b>{{ $m_da == 0 ? null : $m_da }}</b></p>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/hold?month=' . true) }}">
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#FFAC0E">
                            <p class="text-center text-light">Hold</p>
                            <p class="text-center text-light"><b>{{ $m_hold == 0 ? null : $m_hold }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-4">
                    <a>
                        <div class="p-2 m-1" style="height:90px;text-transform:uppercase;background-color:#f012be">
                            <p class="text-center text-light">Wallet Usage</p>
                            <p class="text-center text-light"><b>N{{ number_format($m_wallet ?? 0, 2) }}</b></p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <b style="text-decoration: underline">TRANSACTION COUNT FROM INCEPTION</b>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/pending') }}">
                        <div class="p-2 m-1" style="text-transform:uppercase;height: 90px;background-color:#1d2941;">
                            <p class="text-center text-light">Pending</p>
                            <p class="text-center text-light"><b>{{ $t_pending == 0 ? null : $t_pending }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/in transit') }}">
                        <div class="p-2 m-1" style="text-transform:uppercase;height: 90px;background-color:#5f45da;">
                            <p class="text-center text-light">In Transit</p>
                            <p class="text-center text-light"><b>{{ $t_pick == 0 ? null : $t_pick }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/awaiting') }}">
                        <div class="p-2 m-1" style="text-transform:uppercase;height: 90px;background-color:#670a91;">
                            <p class="text-center text-light">Awaiting</p>
                            <p class="text-center text-light"><b>{{ $t_await == 0 ? null : $t_await }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/deliverd') }}">
                        <div class="p-2 m-1" style="text-transform:uppercase;height: 90px;background-color:#096709;">
                            <p class="text-center text-light">Delivered</p>
                            <p class="text-center text-light"><b>{{ $t_deliver == 0 ? null : $t_deliver }}</b></p>
                        </div>
                    </a>
                </div>

                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/partial-delivery') }}">
                        <div class="p-2 m-1" style="text-transform:uppercase;height: 90px;background-color:#28a745;">
                            <p class="text-center text-light">Partial Delivery</p>
                            <p class="text-center text-light">
                                <b>{{ $t_partial_deliver == 0 ? null : $t_partial_deliver }}</b>
                            </p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/return-to-merchant') }}">
                        <div class="p-2 m-1" style="text-transform:uppercase;height: 90px;background-color:#9a8309;">
                            <p class="text-center text-light">Returned</p>
                            <p class="text-center text-light"><b>{{ $t_return == 0 ? null : $t_return }}</b></p>
                        </div>
                    </a>
                </div>
                
                
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/Collected-amount-from-DA') }}">
                        <div class="p-2 m-1 m-1" style="height:90px;text-transform:uppercase;background-color:#1F6E91">
                            <p class="text-center text-light">Collected amount from DA</p>
                            <p class="text-center text-light"><b>{{ $t_da == 0 ? null : $t_da }}</b></p>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-4 mb-3">
                    <a href="{{ url('/merchant/parcel/hold') }}">
                        <div class="p-2 m-1 m-1" style="height:90px;text-transform:uppercase;background-color:#FFAC0E">
                            <p class="text-center text-light">Hold</p>
                            <p class="text-center text-light"><b>{{ $t_hold == 0 ? null : $t_hold }}</b></p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a>
                        <div class="p-2 m-1" style="text-transform:uppercase;height: 90px;background-color:#f012be;">
                            <p class="text-center text-light">Available Wallet</p>
                            <p class="text-center text-light"><b>N{{ number_format($merchant->balance ?? 0, 2) }}</b>
                            </p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-padding">
        <b style="text-decoration: underline">RECENT SHIPMENT STATUS UPDATES</b>
        <div class="container-fluid">
            <div class="row">
                <!-- column end -->
                <div class="col-sm-12">
                    <div class="stats-reportList-inner">
                        <div class="row">

                            <table class="table table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th scope="col">S/N</th>
                                        <th scope="col">View</th>
                                        <th scope="col">Recipient Name</th>
                                        <th scope="col">Recipient Address</th>
                                        <th scope="col">Tracking Code</th>
                                        <th scope="col">Parcel Weight</th>
                                        <th scope="col">Admin Status Note</th>
                                        <th>COD</th>
                                        <th scope="col">Status Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($parcels as $key => $parcel)
                                        <tr>
                                            <th>{{ ++$key }}</th>
                                            <td scope="row">
                                                <button class="edit_icon" href="#" data-toggle="modal"
                                                    data-target="#merchantParcel{{ $parcel->id }}" title="View"><i
                                                        class="fa fa-eye"></i></button>
                                                <div id="merchantParcel{{ $parcel->id }}" class="modal fade"
                                                    role="dialog">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Parcel Details</h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <table class="table table-bordered">
                                                                    <tr>
                                                                        <td>Merchant Name</td>
                                                                        <td>{{ $parcel->merchant->firstName }}
                                                                            {{ $parcel->merchant->lastName }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Merchant Phone</td>
                                                                        <td>{{ $parcel->merchant->phoneNumber }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Merchant Email</td>
                                                                        <td>{{ $parcel->merchant->emailAddress }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Company</td>
                                                                        <td>{{ $parcel->merchant->companyName }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Recipient Name</td>
                                                                        <td>{{ $parcel->recipientName }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Recipient Phone</td>
                                                                        <td>{{ $parcel->recipientPhone }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Recipient Address</td>
                                                                        <td>{{ $parcel->recipientAddress }}</td>
                                                                    </tr>
                                                                     <tr>
                                                                        <td>Area/State</td>
                                                                        <td>{{App\Nearestzone::find($parcel->reciveZone)->zonename}} / {{App\Deliverycharge::find($parcel->orderType)->title}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>COD</td>
                                                                        <td>{{ $parcel->cod }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>C. Charge</td>
                                                                        <td>{{ $parcel->codCharge }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>D. Charge</td>
                                                                        <td>N{{ number_format($parcel->deliveryCharge, 2) }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Sub Total</td>
                                                                        <td>{{ $parcel->merchantAmount }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Paid</td>
                                                                        <td>{{ $parcel->merchantPaid }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Due</td>
                                                                        <td>{{ $parcel->merchantDue }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Create Date</td>
                                                                        <td>{{ $parcel->created_at }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Last Update</td>
                                                                        <td>{{ date('F d, Y', strtotime($parcel->updated_at)) }}
                                                                            <br>
                                                                            {{ date('g:i a', strtotime($parcel->updated_at)) }}
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $parcel->recipientName }}</td>
                                            <td>{{ $parcel->recipientAddress }}</td>
                                            <td>{{ $parcel->trackingCode }}</td>
                                            <td>{{ $parcel->productWeight }}</td>
                                            <td>{{ $parcel->parcelnote->note ?? 'Empty Note' }}</td>
                                            <td>{{ $parcel->cod }}</td>
                                            <td>{{ $parcel->updated_at->format('d/m/Y') }}<br>{{ $parcel->updated_at->format('H:i:s') }}

                                            </td>

                                        </tr>


                                        <!-- Modal end -->
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
