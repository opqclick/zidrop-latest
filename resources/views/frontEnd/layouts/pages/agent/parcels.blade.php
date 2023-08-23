@extends('frontEnd.layouts.pages.agent.agentmaster')
@section('title','Dashboard')
@section('content')
<style>
@media screen {
  #printSection {
      display: none;
  }
}

@media print {
  body * {
    visibility:hidden;
  }
  #printSection, #printSection * {
    visibility:visible !important;
  }
  #printSection {
    position:absolute !important;
    left:0;
    top:0;
  }
}
</style>
<div class="container-fluid">
  <div class="box-content">
    <div class="row">
      <div class="col-sm-12 col-md-12 col-lg-12">
        <div class="card custom-card">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <form action="" class="filte-form">
            @csrf
            <div class="row">
              <input type="hidden" value="1" name="filter_id">
              <div class="col-sm-2">
                <input type="text" class="form-control" placeholder="Track Id" name="trackId">
              </div>
              <!-- col end -->
              <div class="col-sm-2">
                <input type="number" class="form-control" placeholder="Phone Number" name="phoneNumber">
              </div>
              <!-- col end -->
              <div class="col-sm-2">
                <input type="date" class="flatDate form-control" placeholder="Date Form" name="startDate">
              </div>
              <!-- col end -->
              <div class="col-sm-2">
                <input type="date" class="flatDate form-control" placeholder="Date To" name="endDate">
              </div>
              <!-- col end -->
              <div class="col-sm-2">
                <button type="submit" class="btn btn-success">Submit </button>
              </div>
              <!-- col end -->
            </div>
          </form>
        </div>
        <div class="card-body">
                      <form action="{{url('agent/dliveryman-asign/bulk-option')}}" method="POST" id="myform" class="bulk-status-form">
                      @csrf
                      <select name="deliverymanId" class="bulkselect" form="myform" required>
                      <option value="">Select all & Asign</option>
                         @foreach($deliverymen as $key=>$dman)
                         <option value="{{$dman->id}}">{{$dman->name}}</option>
                         @endforeach
                           
                      </select>
                      <select class="bulkselect" name="asigntype">
                          <option value="1">Pickup</option>
                          <option value="2">Delivery</option>
                      </select>

                     <button type="submit" class="bulkbutton bulk-status-btn">Apply</button>

                        @if(request()->segment(3) === 'in transit')
                          <button id="receiveParcel" type="button" class="bulkbutton btn-success">Receive Parcel</button>
                        @endif




               <table id="example333" class="table table-bordered table-striped custom-table table-responsive">
                 <thead>
                     <tr>
                       <th><input type="checkbox"  id="My-Button"></th>
                       <th>Sl ID</th>
                       <th>Tracking ID</th>
                       <th>Date</th>
                       <th>Shop Name</th>
                       <th>Phone</th>
                       <th>Delivery Man</th>
                       <th>Status</th>
                       <th>Total</th>
                       <th>Charge</th>
                       <th>Sub Total</th>
                       <th>L. Update</th>
                       <th>Payment Status</th>
                       <th>Note</th>
                       <th>More</th>
                     </tr>
                 </thead>
                 <tbody>
                @foreach($allparcel as $key=>$value)
                 <tr>
                  @php
                    $deliverymanInfo = App\Deliveryman::find($value->deliverymanId);
                    $merchantInfo = App\Merchant::find($value->merchantId);
                  @endphp
                   <td>
                     <input type="checkbox" class="selectItemCheckbox" value="{{$value->id}}" name="parcel_id[]" form="myform">
                      </form>
                          </td>
                   <td>{{$loop->iteration}}</td>
                   <td>{{$value->trackingCode}}</td>
                   <td>{{$value->created_at}}</td>
                   <td>{{$value->companyName}}</td>
                   <td>{{$value->recipientPhone}}</td>
                   <td>
                     @if($value->deliverymanId) {{$deliverymanInfo->name}} @else <button class="btn btn-primary" data-toggle="modal" data-target="#asignModal{{$value->id}}">Asign</button> @endif
                     <div id="asignModal{{$value->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                          <!-- Modal content-->
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Delivery Man Asign</h5>
                            </div>
                            <div class="modal-body">
                              <form action="{{url('agent/deliveryman/asign')}}" method="POST">
                                @csrf
                                <input type="hidden" name="hidden_id" value="{{$value->id}}">
                                <input type="hidden" name="merchant_phone" value="{{$merchantInfo->phoneNumber}}">
                                <div class="form-group">
                                  <select name="deliverymanId" class="form-control" id="">
                                    <option value="">Select..</option>
                                    @foreach($deliverymen as $key=>$deliveryman)
                                    <option value="{{$deliveryman->id}}">{{$deliveryman->name}}</option>
                                    @endforeach
                                  </select>
                                </div>
                                <!-- form group end -->
                                <div class="form-group mrt-15">
                                  <textarea name="note" class="form-control" cols="30" placeholder="Note" ></textarea>
                                </div>
                                <div class="form-group">
                                  <button class="btn btn-success">Update</button>
                                </div>
                                <!-- form group end -->
                              </form>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Modal end -->
                   </td>
                   <td>
                     @php
                        $parcelstatus = App\Parceltype::find($value->status);
                     @endphp
                     {{$parcelstatus->title}}
                   </td>
                   <td> {{$value->cod}}</td>
                   <td> {{$value->deliveryCharge+$value->codCharge}}</td>
                   <td> {{$value->cod-($value->deliveryCharge+$value->codCharge)}}</td>
                    <td>{{date('F d, Y', strtotime($value->updated_at))}}</td>
                   <td>@if($value->merchantpayStatus==NULL) NULL @elseif($value->merchantpayStatus==0) Processing @else Paid @endif</td>
                  <td>
                    @php 
                        $parcelnote = App\Parcelnote::where('parcelId',$value->id)->orderBy('id','DESC')->first();

                        //dd($parcelnote);
                    @endphp

                    @if(!empty($parcelnote))
                    {{$parcelnote->note}}
                    @endif
                 </td>
                   <td>
                     <li>
                       <button class="btn btn-info" href="#"  data-toggle="modal" data-target="#merchantParcel{{$value->id}}" title="View"><i class="fa fa-eye"></i></button>
                   </li>
                          <div id="merchantParcel{{$value->id}}" class="modal fade" role="dialog">
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
                                <td>{{$value->firstName}} {{$value->lastName}}</td>
                              </tr>
                              <tr>
                                <td>Merchant Phone</td>
                                <td>{{$value->phoneNumber}}</td>
                              </tr>
                              <tr>
                                <td>Merchant Email</td>
                                <td>{{$value->emailAddress}}</td>
                              </tr>
                              <tr>
                                <td>Company</td>
                                <td>{{$value->companyName}}</td>
                              </tr>
                              <tr>
                                <td>Recipient Name</td>
                                <td>{{$value->recipientName}}</td>
                              </tr>
                              <tr>
                                <td>Recipient Phone</td>
                                <td>{{$value->recipientPhone}}</td>
                              </tr>
                              <tr>
                                <td>Recipient Address</td>
                                <td>{{$value->recipientAddress}}</td>
                              </tr>
                              <tr>
                                <td>Area/State</td>
                                <td>{{$value->zonename}} / {{$value->title}}</td>
                              </tr>
                              <tr>
                                <td>COD</td>
                                <td>{{$value->cod}}</td>
                              </tr>
                              <tr>
                                <td>C. Charge</td>
                                <td>{{$value->codCharge}}</td>
                              </tr>
                              <tr>
                                <td>D. Charge</td>
                                <td>{{$value->deliveryCharge}}</td>
                              </tr>
                              <tr>
                                <td>Sub Total</td>
                                <td>{{$value->merchantAmount}}</td>
                              </tr>
                              <tr>
                                <td>Paid</td>
                                <td>{{$value->merchantPaid}}</td>
                              </tr>
                              <tr>
                                <td>Due</td>
                                <td>{{$value->merchantDue}}</td>
                              </tr>
                            </table>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- Modal end -->
                    <li>
                      @if($value->status != 8)
                        <button class="btn btn-danger" title="Action" data-toggle="modal" data-target="#sUpdateModal{{$value->id}}">
                          <i class="fa fa-sync-alt"></i>
                        </button>
                      @endif


                    </li>

                     <li><a class="btn btn-primary" a href="{{url('agent/parcel/invoice/'.$value->id)}}"  title="Invoice"><i class="fas fa-list"></i></a></li>
                    <!-- Modal -->
                      <div id="sUpdateModal{{$value->id}}" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                          <!-- Modal content-->
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Parcel Status Update</h5>
                            </div>
                            <div class="modal-body">
                              <form action="{{url('agent/parcel/status-update')}}" method="POST">
                                @csrf
                                <input type="hidden" name="hidden_id" value="{{$value->id}}">
                                <input type="hidden" name="customer_phone" value="{{$value->recipientPhone}}">
                                <div class="form-group">
                                    <select name="status"  onchange="percelDelivery(this)" class="form-control" id="">
                                        @foreach($parceltypes as $key=>$ptvalue)
                                        @php
                                            if(in_array($ptvalue->id, [8,9]))
                                            {
                                              continue;
                                            }
                                        @endphp
                                          <option value="{{$ptvalue->id}}"@if($value->status==$ptvalue->id) selected="selected" @endif @if($value->status > $ptvalue->id) disabled @endif>{{$ptvalue->title}}</option>
                                          @endforeach
                                  </select>
                                </div>        
                                <style>
                                .partialpayment{
                                  display: none;
                                }  
                                </style>                            
                                <!-- form group end -->
                                <div class="form-group mrt-15">
                                  <select name="note" class="form-control" id="">
                                    <option value="">Select</option>
                                    @foreach($allnotelist as $key=>$notelist)
                                    <option value="{{$notelist->title}}">{{$notelist->title}}</option>
                                    @endforeach
                                  </select>
                                </div>
                                 <!-- form group end -->
                                <div class="form-group">
                                  <div id="customerpaid" class="partialpayment">
                                      <input type="text" class=" form-control" value="{{old('customerpay')}}" id="customerpay" name="partial_payment"  placeholder="customer pay" /><br />
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
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- Modal end -->
                      <!--@if($value->status >= 2) -->
                      <!--<li><a class="btn btn-primary" a href="{{url('agent/parcel/invoice/'.$value->id)}}"  title="Invoice"><i class="fas fa-list"></i></a></li>-->
                      <!--@endif-->
                  </td>
                 </tr>
                 @endforeach
                 </tbody>
               </table>
             </div>
           </div>
        </div>
    </div>
    <!-- row end -->
</div>
<!-- Modal -->
@endsection
@section('custom_js_scripts')
  <script>
    $(document).ready(function() {
      $('#receiveParcel').click(function (){
        console.log('clicked');
        var parcels = [];

        $(':checkbox:checked').each(function(i){
          parcels[i] = $(this).val();
        });

        console.log(parcels.length);

        if (parcels.length === 0) {
          alert('Alert:: Please select minimum 1 parcel');
        }else {
          $.ajax({
            type: "POST",
            url: '{{route('agent.parcel.receive')}}',
            data: {
              "_token": "{{ csrf_token() }}",
              "parcels": parcels
            },
            success: function(response){
              if(response.success == 'success'){
                window.location.reload();
              }else {
                console.log(response);
              }

            }
          });
        }

      });
    });

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