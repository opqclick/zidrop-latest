@extends('backEnd.layouts.master')
@section('title', 'All parcel')
@section('content')
    <style>
        @media screen {
            #printSection {
                display: none;
            }
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #printSection,
            #printSection * {
                visibility: visible !important;
            }

            #printSection {
                position: absolute !important;
                left: 0;
                top: 0;
            }
        }
    </style>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="card custom-card">
                            <div class="col-sm-12">
                                <div class="manage-button">
                                    <div class="body-title">
                                        <h4><b>All parcel</b></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <form action="" class="">
                                    @csrf
                                    <div class="row">
                                        <input type="hidden" value="1" name="filter_id">
                                        <div class="col-sm-2 mt-2">
                                            <input type="text" class="form-control" placeholder="Track Id"
                                                name="trackId">
                                        </div>
                                        <!-- col end -->
                                        <div class="col-sm-2 mt-2">
                                            <input type="text" class="form-control" placeholder="Customer name"
                                                name="cname">
                                        </div>

                                        <!-- <div class="col-sm-2 mt-2">
                                <input type="text" class="form-control" placeholder="Address" name="address">
                              </div>-->

                                        <div class="col-sm-2 mt-2">
                                            <input type="number" class="form-control" placeholder="Phone Number"
                                                name="phoneNumber">
                                        </div>
                                        <!-- col end -->
                                        <div class="col-sm-2 mt-2">
                                            <input type="number" class="form-control" placeholder="Merchant Id"
                                                name="merchantId">
                                        </div>

                                        <!-- col end -->
                                        <div class="col-sm-2 mt-2">
                                            <input type="date" class="flatDate form-control"
                                                placeholder="Create Date Form" name="startDate">
                                        </div>
                                        <!-- col end -->
                                        <div class="col-sm-2 mt-2">
                                            <input type="date" class="flatDate form-control" placeholder="Create Date To"
                                                name="endDate">
                                        </div>


                                        <!-- col end -->
                                        <div class="col-sm-2 mt-2">
                                            <input type="date" class="flatDate form-control"
                                                placeholder="Update Date Form" name="upstartDate">
                                        </div>
                                        <!-- col end -->
                                        <div class="col-sm-2 mt-2">
                                            <input type="date" class="flatDate form-control" placeholder="Update Date To"
                                                name="upendDate">
                                        </div>
                                        <!-- col end -->
                                        <div class="col-sm-2 mt-2">
                                            <button type="submit" class="btn btn-success">Submit </button>
                                        </div>
                                        <!-- col end -->
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('editor/dliveryman-asign/bulk-option') }}" method="POST"
                                    id="myform" class="bulk-status-form">
                                    @csrf
                                    <select name="deliverymanId" class="bulkselect" form="myform" required="required">
                                        <option value="">Select all & Asign</option>
                                        @foreach ($deliverymen as $key => $dman)
                                            <option value="{{ $dman->id }}">{{ $dman->name }}</option>
                                        @endforeach

                                    </select>
                                    <select class="bulkselect" name="asigntype">
                                        <option value="1">Pickup</option>
                                        <option value="2">Delivery</option>
                                    </select>
                                    <button type="submit" class="bulkbutton bulk-status-btn">Apply</button>

                                    <table id="example333"
                                        class="table table-bordered table-striped custom-table table-responsive">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="My-Button"></th>
                                                <th>Serial</th>
                                                <th>Merchant Id</th>
                                                <th>Create_Date</th>
                                                <th>Company_Name</th>
                                                <th>Customer</th>
                                                <th>Tracking</th>
                                                <th>Area / State</th>
                                                <th>Full Address</th>
                                                <th>Phone</th>

                                                <th>Pickman</th>
                                                <th>Rider</th>
                                                <th>Agent</th>
                                                <th>Last Update</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th>Charge</th>
                                                <th>Sub Total</th>
                                                <th>Pay Return ?</th>
                                                <th>Pay ?</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($show_data as $key => $value)
                                                <tr>
                                                    <td><input type="checkbox" value="{{ $value->id }}"
                                                            name="parcel_id[]" form="myform" class="selectItemCheckbox">
                                </form>
                                </td>
                                <td>{{ $loop->iteration }}</td>
                                @php
                                    $merchant = App\Merchant::find($value->merchantId);
                                    $agentInfo = App\Agent::find($value->agentId);
                                    
                                    $deliverymanInfo = App\Deliveryman::find($value->deliverymanId);
                                    $pickupmanInfo = App\Deliveryman::find($value->pickupmanId);
                                @endphp

                                <td>{{ $merchant->id }}</td>


                                <td>{{ date('d M Y', strtotime($value->created_at)) }}<br>
                                    {{ date('g:i a', strtotime($value->created_at)) }}</td>
                                <!--Create Date and Time -->
                                <td>{{ $merchant->companyName }}<br>{({{ $merchant->pickLocation }})<br>({{ $merchant->phoneNumber }})}
                                </td>
                                <td>{{ $value->recipientName }}</td>
                                <td>{{ $value->trackingCode }}</td>
                                <td>{{ $value->zonename }} / {{ $value->title }}</td>
                                <td>{{ $value->recipientAddress }}</td>
                                <td>{{ $value->recipientPhone }}</td>

                                <td>
                                    @if ($value->pickupmanId)
                                        <button class="btn btn-primary" data-toggle="modal"
                                            data-target="#pickupmanModal{{ $value->id }}">{{ $pickupmanInfo->name }}</button>
                                    @else
                                        <button class="btn btn-primary" data-toggle="modal"
                                            data-target="#pickupmanModal{{ $value->id }}">Asign</button>
                                    @endif
                                </td>
                                <!-- Modal -->
                                <div id="pickupmanModal{{ $value->id }}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Pickupman Asign</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ url('editor/pickupman/asign') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                                    <input type="hidden" name="merchant_phone"
                                                        value="{{ $merchant->phoneNumber }}">
                                                    <div class="form-group">
                                                        <select name="pickupmanId" class="form-control" id="">
                                                            <option value="">Select</option>
                                                            @foreach ($deliverymen as $key => $deliveryman)
                                                                <option value="{{ $deliveryman->id }}">
                                                                    {{ $deliveryman->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!-- form group end -->
                                                    <div class="form-group">
                                                        <textarea name="note" class="form-control"></textarea>
                                                    </div>
                                                    <!-- form group end -->
                                                    <div class="form-group">
                                                        <button class="btn btn-success">Update</button>
                                                    </div>
                                                    <!-- form group end -->
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal end -->
                                <td>
                                    @if ($value->deliverymanId)
                                        <button class="btn btn-info" data-toggle="modal"
                                            data-target="#asignModal{{ $value->id }}">{{ $deliverymanInfo->name }}</button>
                                    @else
                                        <button class="btn btn-primary" data-toggle="modal"
                                            data-target="#asignModal{{ $value->id }}">Assign</button>
                                    @endif
                                </td>
                                <!-- Modal -->
                                <div id="asignModal{{ $value->id }}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Deliveryman Assign</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ url('editor/deliveryman/asign') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                                    <input type="hidden" name="merchant_phone"
                                                        value="{{ $merchant->phoneNumber }}">
                                                    <div class="form-group">
                                                        <select name="deliverymanId" class="form-control" id="">
                                                            <option value="">Select</option>
                                                            @foreach ($deliverymen as $key => $deliveryman)
                                                                <option value="{{ $deliveryman->id }}"
                                                                    @if ($value->deliverymanId == $deliveryman->id) selected @endif>
                                                                    {{ $deliveryman->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!-- form group end -->
                                                    <div class="form-group">
                                                        <textarea name="note" class="form-control"></textarea>
                                                    </div>
                                                    <!-- form group end -->
                                                    <div class="form-group">
                                                        <button class="btn btn-success">Update</button>
                                                    </div>
                                                    <!-- form group end -->
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal end -->
                                <td>
                                    @if ($value->agentId)
                                        <button class="btn btn-success" data-toggle="modal"
                                            data-target="#agentModal{{ $value->id }}"> {{ $agentInfo->name }}</button>
                                    @else
                                        <button class="btn btn-primary" data-toggle="modal"
                                            data-target="#agentModal{{ $value->id }}">Asign</button>
                                    @endif
                                </td>
                                <!-- Modal -->
                                <div id="agentModal{{ $value->id }}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Agent Asign</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ url('editor/agent/asign') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="hidden_id" value="{{ $value->id }}">
                                                    <input type="hidden" name="merchant_phone"
                                                        value="{{ $merchant->phoneNumber }}">
                                                    <div class="form-group">
                                                        <select name="agentId" class="form-control" id="">
                                                            <option value="">Select</option>
                                                            @foreach ($agents as $key => $agent)
                                                                <option value="{{ $agent->id }}"
                                                                    @if ($value->agentId == $agent->id) selected @endif>
                                                                    {{ $agent->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <textarea name="note" class="form-control"></textarea>
                                                    </div>

                                                    <!-- form group end -->
                                                    <div class="form-group">
                                                        <button class="btn btn-success">Update</button>
                                                    </div>
                                                    <!-- form group end -->
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger"
                                                    data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Modal end -->
                                <td>{{ date('F d, Y', strtotime($value->updated_at)) }}</td>
                                <td>{{ App\Parceltype::find($value->status)->title }}</td>
                                <td> {{ $value->cod }}</td>
                                <td> {{ $value->deliveryCharge + $value->codCharge }}</td>
                                <td> {{ $value->cod - ($value->deliveryCharge + $value->codCharge) }}</td>
                                <td>
                                    @if ($value->pay_return == 0)
                                        <div class="text-danger">{{ $value->deliveryCharge }}</div>
                                    @else
                                        <div class="text-success"> Paid </div>
                                    @endif
                                </td>
                                <td>
                                    @if ($value->merchantpayStatus == null)
                                        <div class="text-danger"> NULL </div>
                                    @elseif ($value->merchantpayStatus == 0)
                                        <div class="text-warning"> Processing </div>
                                    @else
                                        <div class="text-success"> Paid </div>
                                    @endif
                                </td>

                                <td>
                                    <ul class="action_buttons cust-action-btn">

                                        @if (Auth::user()->role_id <= 2)
                                            <li>
                                                @if($value->status == 8 && \Illuminate\Support\Facades\Auth::user()->role_id == 1)
                                                    <a href="{{ url('editor/parcel/edit/' . $value->id) }}"
                                                       class="edit_icon"><i class="fa fa-edit"></i>
                                                    </a>
                                                @elseif($value->status != 8 && \Illuminate\Support\Facades\Auth::user()->role_id <= 3)
                                                    <a href="{{ url('editor/parcel/edit/' . $value->id) }}"
                                                       class="edit_icon"><i class="fa fa-edit"></i>
                                                    </a>
                                                @endif
                                            </li>
                                        @endif

                                        <li>

                                            @if (Auth::user()->role_id <= 3)
                                        <li>

                                            @if($value->status == 8 && \Illuminate\Support\Facades\Auth::user()->role_id == 1)
                                                <button class="thumbs_up" title="Action" data-toggle="modal"
                                                        data-target="#sUpdateModal{{ $value->id }}"><i
                                                            class="fa fa-pencil"></i>
                                                </button>
                                            @elseif($value->status != 8 && \Illuminate\Support\Facades\Auth::user()->role_id <= 3)
                                                <button class="thumbs_up" title="Action" data-toggle="modal"
                                                        data-target="#sUpdateModal{{ $value->id }}"><i
                                                            class="fa fa-pencil"></i>
                                                </button>
                                            @endif


                                            <!-- Modal -->
                                            <div id="sUpdateModal{{ $value->id }}" class="modal fade" role="dialog">
                                                <div class="modal-dialog">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Parcel Status Update</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form action="{{ url('editor/parcel/status-update') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="hidden_id"
                                                                    value="{{ $value->id }}">
                                                                <input type="hidden" name="customer_phone"
                                                                    value="{{ $value->recipientPhone }}">
                                                                <div class="form-group">
                                                                    <select name="status" onchange="percelDelivery(this)"
                                                                        class="form-control" id="">
                                                                        @foreach ($parceltypes as $key => $ptvalue)
                                                                            <option
                                                                                value="{{ $ptvalue->id }}"@if ($value->status == $ptvalue->id) selected="selected" @endif>
                                                                                {{ $ptvalue->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group mrt-15">
                                                                    <select name="note" class="form-control"
                                                                        id="">
                                                                        <option value="">Select</option>
                                                                        @foreach ($allnotelist as $key => $notelist)
                                                                            <option value="{{ $notelist->title }}">
                                                                                {{ $notelist->title }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <!-- form group end -->
                                                                <div class="form-group">
                                                                    <div class="customerpaid" style="display: none;">
                                                                        <input type="text" class="form-control"
                                                                            value="{{ old('customerpay') }}"
                                                                            id="customerpay" name="customerpay"
                                                                            placeholder="customer pay" /><br />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="partialpayment" style="display: none;">
                                                                        <input type="text" class="form-control"
                                                                            value="{{ old('partial_payment') }}"
                                                                            id="partial_payments" name="partial_payment"
                                                                            placeholder="Partial pay" /><br />
                                                                    </div>
                                                                </div>
                                                                <!-- form group end -->
                                                                <div class="form-group">
                                                                    <button class="btn btn-success">Update</button>
                                                                </div>
                                                                <!-- form group end -->
                                                            </form>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal end -->
                                        </li>
                                        @endif
                                        <li>


                                            <button class="edit_icon" href="#" data-toggle="modal"
                                                data-target="#merchantParcel{{ $value->id }}" title="View"><i
                                                    class="fa fa-eye"></i>
                                            </button>


                                            @if (Auth::user()->role_id <= 2)
                                                <button class="edit_icon ml-1" href="#" data-toggle="modal"
                                                    data-target="#merchantParcel{{ $value->id }}h" title="History"><i
                                                        class="fas fa-history"></i></button>
                                            @endif

                                            <div id="merchantParcel{{ $value->id }}" class="modal fade"
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
                                                                    <td>{{ $value->firstName }} {{ $value->lastName }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Merchant Phone</td>
                                                                    <td>{{ $value->phoneNumber }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Merchant Email</td>
                                                                    <td>{{ $value->emailAddress }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Area/State</td>
                                                                    <td>{{ $value->zonename }} / {{ $value->title }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Company</td>
                                                                    <td>{{ $value->companyName }}</td>
                                                                </tr>
                                                                <td>Recipient Name</td>
                                                                <td>{{ $value->recipientName }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Recipient Address</td>
                                                                    <td>{{ $value->recipientAddress }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Note</td>
                                                                    <td>{{ $value->note }}</td>
                                                                </tr>

                                                                @php
                                                                    $last_note = App\Parcelnote::where('parcelId', $value->id)
                                                                        ->latest()
                                                                        ->first();
                                                                @endphp

                                                                @isset($last_note->note)
                                                                    <tr>
                                                                        <td>Parcel Note</td>
                                                                        <td>{{ $last_note->note }}</td>
                                                                    </tr>
                                                                @endisset
                                                                <tr>
                                                                    <td>COD</td>
                                                                    <td>{{ $value->cod }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>C. Charge</td>
                                                                    <td>{{ $value->codCharge }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>D. Charge</td>
                                                                    <td>{{ $value->deliveryCharge }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Sub Total</td>
                                                                    <td>{{ $value->merchantAmount }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Paid</td>
                                                                    <td>{{ $value->merchantPaid }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Due</td>
                                                                    <td>{{ $value->merchantDue }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Create Date</td>
                                                                    <td>{{ $value->created_at }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Last Update</td>
                                                                    <td>{{ date('F d, Y', strtotime($value->updated_at)) }}
                                                                        <br>
                                                                        {{ date('g:i a', strtotime($value->updated_at)) }}
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
                                            <!-- Modal end -->


                                            <!--Model for History Icon -->

                                            <div id="merchantParcel{{ $value->id }}h" class="modal fade"
                                                role="dialog">
                                                <div class="modal-dialog modal-lg">
                                                    <!-- Modal content-->
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Parcel History</h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table table-bordered">

                                                                <tr>
                                                                    <td> <strong class="h5">Customer Name</strong>
                                                                    </td>
                                                                    <td> <strong class="h5">Date</strong> </td>
                                                                    <td> <strong class="h5">Done By</strong> </td>
                                                                    <td> <strong class="h5">Note</strong> </td>
                                                                    <td> <strong class="h5">Status</strong> </td>

                                                                </tr>

                                                                @php
                                                                    
                                                                    $histories = App\History::where('parcel_id', $value->id)->get();
                                                                    
                                                                @endphp
                                                                @foreach ($histories as $history)
                                                                    <tr>


                                                                        <td>{!! $history->name !!}</td>
                                                                        <td>{!! date('d/M/y', strtotime($history->date)) !!}<br>
                                                                            {{ date('g:i a', strtotime($history->date)) }}
                                                                        </td>
                                                                        <td>{{ $history->done_by }}</td>
                                                                        <td>{{ $history->note ?? 'No note added' }}</td>
                                                                        <td>{{ $history->status ?? 'N/A' }}</td>


                                                                    </tr>
                                                                @endforeach


                                                            </table>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger"
                                                                data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal end -->
                                        </li>
                                        <!--@if ($value->status > 1)
    -->
                                        <li>
                                            <a class="edit_icon anchor" target="_blank"
                                                href="{{ url('editor/parcel/invoice/' . $value->id) }}" title="Invoice"><i
                                                    class="fa fa-list"></i></a>
                                        </li>
                                        <!--
    @endif-->
                                        @if (Auth::user()->role_id == 1)
                                            <li>
                                                <form action="{{ url('/editor/parcel/delete/' . $value->id) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Are you sure want to delete this item?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </td>
                                </tr>
                                @endforeach
                                </tbody>
                                </table>

                                {{ $show_data->links() }}





                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Section  -->
@endsection
@section('custom_js_scripts')
    <script>
        $(document).ready(function() {
            var table33 = $('#example333').DataTable( {
                dom: 'Bfrtip',
                "lengthMenu": [[ 200, 500, -1], [ 200, 500, "All"]],
                buttons: [
                    {
                        extend: 'copy',
                        text: 'Copy',
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 ,12,13,14,15,16,17 ],
                            rows: function(idx, data, node) {
                                let found = false;
                                let selectedRowIndexes = table33.rows('.selected').indexes();
                                for (let index = 0; index < selectedRowIndexes.length; index++) {
                                    if (idx == selectedRowIndexes[index]) {
                                        found = true;
                                        break;
                                    }
                                }
                                return found;
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        exportOptions: {
                            columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 ,12,13,14,15,16,17 ],
                            rows: function(idx, data, node) {
                                let found = false;
                                let selectedRowIndexes = table33.rows('.selected').indexes();
                                for (let index = 0; index < selectedRowIndexes.length; index++) {
                                    if (idx == selectedRowIndexes[index]) {
                                        found = true;
                                        break;
                                    }
                                }
                                return found;
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'D_Man',
                        exportOptions: {
                            columns: [ 1,3,4,5,7,8,10,14],
                            rows: function(idx, data, node) {
                                let found = false;
                                let selectedRowIndexes = table33.rows('.selected').indexes();
                                for (let index = 0; index < selectedRowIndexes.length; index++) {
                                    if (idx == selectedRowIndexes[index]) {
                                        found = true;
                                        break;
                                    }
                                }
                                return found;
                            }
                        }
                    },

                    {
                        extend: 'print',
                        text: 'Print',
                        exportOptions: {
                            columns: [ 1,2,3,4,5,6,7,8,9,10],
                            rows: function(idx, data, node) {
                                let found = false;
                                let selectedRowIndexes = table33.rows('.selected').indexes();
                                for (let index = 0; index < selectedRowIndexes.length; index++) {
                                    if (idx == selectedRowIndexes[index]) {
                                        found = true;
                                        break;
                                    }
                                }
                                return found;
                            }
                        }
                    },

                    {
                        extend: 'print',
                        text: 'Print all',
                        exportOptions: {
                            columns: [ 1,2,3,4,5,6,7,8,9,10],
                            rows: function(idx, data, node) {
                                let found = true;
                                let selectedRowIndexes = table33.rows('.selected').indexes();
                                for (let index = 0; index < selectedRowIndexes.length; index++) {
                                    if (idx == selectedRowIndexes[index]) {
                                        found = false;
                                        break;
                                    }
                                }
                                return found;
                            }
                        }
                    },
                    {
                        extend: 'colvis',
                    },

                ]
            } );

            $(".selectItemCheckbox").change(function() {
                var ischecked= $(this).is(':checked');
                if(!ischecked) {
                    $(this).parent().parent().removeClass('selected');
                } else {
                    $(this).parent().parent().addClass('selected');
                }
            });
            $("#My-Button").change(function() {
                var ischecked= $(this).is(':checked');
                if(!ischecked) {
                    $(".selectItemCheckbox").attr('checked');
                    $("#example333 tbody tr").addClass('selected');
                } else {
                    $(".selectItemCheckbox").removeAttr('checked');
                    $("#example333 tbody tr").removeClass('selected');
                }
            });
        });
    </script>
@endsection