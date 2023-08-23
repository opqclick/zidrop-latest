@extends('backEnd.layouts.master')
@section('title', 'Create Parcel')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h5 class="m-0 text-dark">Welcome !! {{ auth::user()->name }}</h5>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="#">Parcel</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="manage-button">
                        <div class="body-title">
                            <h5>Create Parcel</h5>
                        </div>
                        <div class="quick-button">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="box-content">
                        <div class="row">
                            <div class="col-12">
                                <h3 class="card-title">Add Parcel Info</h3>
                            </div>
                            
                            <div class="col-lg-7 col-md-7 col-sm-12">
                                    <!-- /.card-header -->
                                    @if(session()->has('message'))
                                        <div class="alert alert-danger">
                                            
                                            {{ session('message') }}
                                            
                                        </div>
                                    @endif
                                    <!-- form start -->
                                <form role="form" action="{{ url('editor/parcel/store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                     <div class="row">
										<div class="col-sm-12">
										   <div class="form-group">
											 <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') }}" name="name" required placeholder="Customer Name" required="required">
											 @if ($errors->has('name'))
					                            <span class="invalid-feedback">
					                              <strong>{{ $errors->first('name') }}</strong>
					                            </span>
					                          @endif
										    </div>
								       </div>
								       
								       	<div class="col-sm-12">
										   <div class="form-group">
											 <input type="text" class="form-control{{ $errors->has('productName') ? ' is-invalid' : '' }}" value="{{ old('productName') }}" name="productName" placeholder="Product Name" required="required">
											 @if ($errors->has('productName'))
					                            <span class="invalid-feedback">
					                              <strong>{{ $errors->first('productName') }}</strong>
					                            </span>
					                          @endif
										    </div>
								       </div>
		                                
                                        <!-- form group -->
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select
                                                    class="form-control select2{{ $errors->has('merchantId') ? ' is-invalid' : '' }}"
                                                    value="{{ old('merchantId') }}" name="merchantId" required='required'>
                                                    <option value="">Select Merchant</option>
    
                                                    @foreach ($merchants as $value)
                                                        <option value="{{ $value->id }}">{{ $value->lastName }}
                                                            ({{ $value->phoneNumber }}) _ ({{ $value->companyName }}) _
                                                            ({{ $value->firstName }})</option>
                                                    @endforeach
                                                </select>
    
                                                @if ($errors->has('merchantId'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('merchantId') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        
										<div class="col-sm-6">
											 <div class="form-group">
											<select type="text"  class="package form-control{{ $errors->has('package') ? ' is-invalid' : '' }}" value="{{ old('package') }}" name="package" required="required">
											    <option value="" selected="selected">State</option>
											    @foreach($delivery as $key=>$value)
											    <option value="{{$value->id}}" data-deliverycharge="{{$value->deliverycharge}}" extracharge="{{$value->extradeliverycharge}}" cod="{{$value->cod}}">{{$value->title}}</option>
											    @endforeach
											</select>    
											 @if ($errors->has('package'))
					                            <span class="invalid-feedback">
					                              <strong>{{ $errors->first('package') }}</strong>
					                            </span>
					                          @endif
											</div>
										</div>
										<div class="col-sm-6">
											<select type="text"  class="reciveZone select2 form-control{{ $errors->has('reciveZone') ? ' is-invalid' : '' }}" value="{{ old('reciveZone') }}" name="reciveZone" id="reciveZone" placeholder="Delivery Area" required="required">
											    <option value="" selected="selected">Delivery Area...</option>
											    
											</select>    
											 @if ($errors->has('reciveZone'))
					                            <span class="invalid-feedback">
					                              <strong>{{ $errors->first('reciveZone') }}</strong>
					                            </span>
					                          @endif
										</div>
										<div class="col-sm-6">
											<div class="form-group">
											<select type="text"  class="form-control{{ $errors->has('percelType') ? ' is-invalid' : '' }}" value="{{ old('percelType') }}" name="percelType" required="required">
											    <option value="">Select Parcel Type</option>
											    <option value="1">Regular</option>
											    <option value="2">Liquid</option>
											    <option value="3">Fragile</option>
											</select>    
											 @if ($errors->has('percelType'))
					                            <span class="invalid-feedback">
					                              <strong>{{ $errors->first('percelType') }}</strong>
					                            </span>
					                          @endif
											</div>
										</div>
										 <div class="col-sm-6">
									          <div class="form-group">
												<input type="number" class="form-control{{ $errors->has('phonenumber') ? ' is-invalid' : '' }}" value="{{ old('phonenumber') }}" name="phonenumber" placeholder="Customer Phone Number" required="required">
												@if ($errors->has('phonenumber'))
						                            <span class="invalid-feedback">
						                              <strong>{{ $errors->first('phonenumber') }}</strong>
						                            </span>
						                          @endif
											</div>
										</div>

									    <div class="col-sm-6">
											<select type="text"  class="select2 form-control{{ $errors->has('payment_option') ? ' is-invalid' : '' }}" value="{{ old('payment_option') }}" name="payment_option" placeholder="Delivery Area" required="required">
											    <option value="">Payment Option</option>
												<option value="1">Prepaid</option>
												<option value="2">Pay on Delivery</option>
											</select>    
											 @if ($errors->has('payment_option'))
					                            <span class="invalid-feedback">
					                              <strong>{{ $errors->first('payment_option') }}</strong>
					                            </span>
					                          @endif
										</div>
										<div class="col-sm-6">
										    <div class="form-group">
												<input type="number"  class="calculate cod form-control{{ $errors->has('cod') ? ' is-invalid' : '' }}" value="{{ old('cod') }}" name="cod" min="0" placeholder="Cash Collection Amount" required="required">
												@if ($errors->has('cod'))
						                            <span class="invalid-feedback">
						                              <strong>{{ $errors->first('cod') }}</strong>
						                            </span>
						                          @endif
											</div>
										</div>
									    
										<div class="col-sm-6">
								           <div class="form-group">
												<input type="number" class="calculate weight form-control{{ $errors->has('weight') ? ' is-invalid' : '' }}" value="{{ old('weight') }}" name="weight" placeholder="Weight in KG" required="required">
												@if ($errors->has('weight'))
						                            <span class="invalid-feedback">
						                              <strong>{{ $errors->first('weight') }}</strong>
						                            </span>
						                          @endif
										    </div>
								       </div>
								       
								       <div class="col-sm-6">
								           <div class="form-group">
												<input type="number" class="calculate form-control{{ $errors->has('productQty') ? ' is-invalid' : '' }}" min="0" value="{{ old('productQty') }}" name="productQty" placeholder="Product Quantity" required="required">
												@if ($errors->has('productQty'))
						                            <span class="invalid-feedback">
						                              <strong>{{ $errors->first('productQty') }}</strong>
						                            </span>
						                          @endif
											</div>
								       </div>
								       								       
								       <div class="col-sm-6">
								           <div class="form-group">
												<input type="text" class="form-control{{ $errors->has('productColor') ? ' is-invalid' : '' }}" value="{{ old('productColor') }}" name="productColor" placeholder="Product Color" required="required">
													@if ($errors->has('productColor'))
						                            <span class="invalid-feedback">
						                              <strong>{{ $errors->first('productColor') }}</strong>
						                            </span>
						                          @endif
										    </div>
								       </div>


										<div class="col-sm-6">
											<div class="form-group">
												<textarea type="text" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" value="{{ old('address') }}" name="address"  placeholder="Customer Full Address" required="required"></textarea>
												@if ($errors->has('address'))
						                            <span class="invalid-feedback">
						                              <strong>{{ $errors->first('address') }}</strong>
						                            </span>
						                          @endif
											</div>
								        </div>
										<div class="col-sm-6">
								           <div class="form-group">
												<textarea type="text" name="note" value="{{old('note')}}" class="form-control" placeholder="Note" required="required"></textarea>
													@if ($errors->has('note'))
						                            <span class="invalid-feedback">
						                              <strong>{{ $errors->first('note') }}</strong>
						                            </span>
						                          @endif
											</div>
									    </div>
										<div class="col-sm-12">
											<div class="form-group">
												<button type="submit" class="form-control btn btn-primary">Submit</button>
											</div>
										</div>
									
							        </div>
                                </form>
                            </div>
                            <!-- col end -->
                            
                            <div class="col-lg-1 col-md-1 col-sm-0"></div>
        				    <div class="col-lg-4 col-md-4 col-sm-12">
        					    <div class="parcel-details-instance">
            						<h2>Delivery Charge Details</h2>
            						<div class="content calculate_result">
            							<div class="row">
            								<div class="col-sm-8">
            									<p>Cash Collection</p>
            								</div>
            								<div class="col-sm-4">
            									<p><span class="cashCollection">00</span>  N</p>
            								</div>
            							</div>
            							<!-- row end -->
            							<div class="row">
            								<div class="col-sm-8">
            									<p>Delivery Charge</p>
            								</div>
            								<div class="col-sm-4">
            									<p><span class="devlieryCharge">00</span> N</p>
            								</div>
            							</div>
            							<!-- row end -->
            							<div class="row">
            								<div class="col-sm-8">
            									<p>Cod Charge</p>
            								</div>
            								<div class="col-sm-4">
            									<p><span class="codCharge">00</span> N</p>
            								</div>
            							</div>
            							<hr>
            							<!-- row end -->
            							<div class="row total-bar">
            								<div class="col-sm-8">
            									<p>Total Payable Amount</p>
            								</div>
            								<div class="col-sm-4">
            									<p><span class="total">00</span> N</p>
            								</div>
            							</div>
            							<!-- row end -->
            							<div class="row">
            								<div class="col-sm-12">
            									<p class="text-center unbold">Note : <span class="">If you  request for pick up after 5pm, it will be collected on the next day</span></p>
            								</div>
            							</div>
            							<!-- row end -->
            						</div>
        					    </div>    
        				    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- submenu dependency --}}
<script type="text/javascript">
	$(document).ready(function() {
		@if(session()->has('open_url') && (session()->get('open_url') != ''))
			window.open('{{ session()->get('open_url') }}', '_blank');
		@endif

		$('select[name="package"]').on('change', function() {
		    devlieryCharge();
		    codUpdate();
			var orderType = $(this).val();
			if (orderType) {
				$.ajax({
					url: "{{ url('/get-area/') }}/" + orderType,
					type: "GET",
					dataType: "json",
					success: function(data) {
						var d = $('select[name="reciveZone"]').empty();
						$('select[name="reciveZone"]').append('<option value="" selected="selected" disabled>Delivery Area .. </option>');
						$.each(data, function(key, value) {
							$('select[name="reciveZone"]').append(
								'<option extra="'+value.extradeliverycharge+'" value="' + value.id + '">' + value
								.zonename + '</option>');
						});
					},
				});
			} else {
				alert('danger');
			}
		});
		
		$('select[name="reciveZone"]').on("change", function() {
		    devlieryCharge();
		})
		
		$("input[name='weight']").on("keyup", function() {
		    devlieryCharge();
		})
		
		$("input[name='cod']").on("keyup", function() {
		    var cod = $("input[name='cod']").val();
		    var formated = CurrencyFormatted(cod);
		    var formated = checkNan(formated);
		    $(".cashCollection").text(formated);
		    codUpdate();
		    devlieryCharge();
		})
		
		function codUpdate() {
		    var cash = $("input[name='cod']").val();
		    var charge = $('select[name="package"] option:selected').attr("cod");
		    charge = charge ? parseInt(cash) * (parseInt(charge) / 100) : 0;
		    var formated = CurrencyFormatted(charge);
		    formated = checkNan(formated);
		    $(".codCharge").text(formated);
		}
		
		function devlieryCharge() {
		    var stateCharge = $('select[name="package"] option:selected').attr('data-deliverycharge');
		    var extraCharge = $('select[name="package"] option:selected').attr('extracharge');
		    var cod = $('select[name="package"] option:selected').attr('cod');
		    var zoneCharge = $('select[name="reciveZone"] option:selected').attr("extra");
		    var cash = $("input[name='cod']").val();
		    var weight = $("input[name='weight']").val();
		    
		    
		    extraCharge = parseInt(weight) > 1 ? (parseInt(weight) * parseInt(extraCharge)) - parseInt(extraCharge) : 0;

		    stateCharge = stateCharge ? stateCharge : 0;
		    zoneCharge = zoneCharge ? zoneCharge : 0;
		    charge =   parseInt(stateCharge) + parseInt(extraCharge) + parseInt(zoneCharge);
		    var formatCharge = CurrencyFormatted(charge);
		    formatCharge = checkNan(formatCharge);
		    $(".devlieryCharge").text(formatCharge);
		    
		    var codcharge = cod ? parseInt(cash) * (parseInt(cod) /100 ) : 0;
		    var total = charge - parseInt(cash) + codcharge;
		    
		    total = total * -1;
		    total = CurrencyFormatted(total);
		    total = checkNan(total);
		    $(".total").text(total);
		}
		
		function checkNan(total) {
		    var str = total.split(".");
		    if(str[0] == 'NaN') {
		        return '00';
		    }
		    return total;
		}
		
        function CurrencyFormatted(number) {
           var decimalplaces = 2;
           var decimalcharacter = ".";
           var thousandseparater = ",";
           number = parseFloat(number);
           var sign = number < 0 ? "-" : "";
           var formatted = new String(number.toFixed(decimalplaces));
           if( decimalcharacter.length && decimalcharacter != "." ) { formatted = formatted.replace(/\./,decimalcharacter); }
           var integer = "";
           var fraction = "";
           var strnumber = new String(formatted);
           var dotpos = decimalcharacter.length ? strnumber.indexOf(decimalcharacter) : -1;
           if( dotpos > -1 )
           {
              if( dotpos ) { integer = strnumber.substr(0,dotpos); }
              fraction = strnumber.substr(dotpos+1);
           }
           else { integer = strnumber; }
           if( integer ) { integer = String(Math.abs(integer)); }
           while( fraction.length < decimalplaces ) { fraction += "0"; }
           temparray = new Array();
           while( integer.length > 3 )
           {
              temparray.unshift(integer.substr(-3));
              integer = integer.substr(0,integer.length-3);
           }
           temparray.unshift(integer);
           integer = temparray.join(thousandseparater);
           return sign + integer + decimalcharacter + fraction;
        }
		
	});
</script>
  
  <style>
          /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
          -webkit-appearance: none;
          margin: 0;
        }
        
        /* Firefox */
        input[type=number] {
          -moz-appearance: textfield;
        }
      .parcel-details-instance {
            background: #ddd;
        }
        .parcel-details-instance h2 {
            border-bottom: 5px solid #A53D3D;
            font-size: 22px;
            text-align: center;
            padding: 20px 0;
            font-weight: 600;
        }
        .parcel-details-instance .content {
            padding: 25px 25px;
        }
        .parcel-details-instance p {
            font-size: 17px;
            font-weight: 600;
        }
        .hr {
            height: 2px;
            width: 100%;
            background: black;
            margin-bottom: 16px;
        }
        p.unbold {
            font-weight: 500;
        }
  </style>
@endsection
