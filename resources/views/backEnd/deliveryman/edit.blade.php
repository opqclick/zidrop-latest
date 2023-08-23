@extends('backEnd.layouts.master')
@section('title','Edit Deliveryman Info')
@section('content')
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="box-content">
            <div class="row">
              <div class="col-sm-12">
                  <div class="manage-button">
                    <div class="body-title">
                      <h5>Edit Deliveryman Info</h5>
                    </div>
                    <div class="quick-button">
                      <a href="{{url('editor/deliveryman/manage')}}" class="btn btn-primary btn-actions btn-create">
                      Manage Deliveryman Info
                      </a>
                    </div>  
                  </div>
                </div>
              <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card card-primary">
                    <div class="card-header">
                      <h3 class="card-title">Edit Deliveryman Info</h3>
                    </div>
                    <!-- form start -->
                   <form action="{{url('editor/deliveryman/update')}}" method="POST" enctype="multipart/form-data" name="editForm">
                  @csrf
                  <input type="hidden" value="{{$edit_data->id}}" name="hidden_id">

                  <div class="main-body">
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="name">Name</label>
                          <input type="text" name="name" id="name" class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{$edit_data->name}}">
                           @if ($errors->has('name'))
                            <span class="invalid-feedback">
                              <strong>{{ $errors->first('name') }}</strong>
                            </span>
                            @endif
                        </div>
                       
                      </div>
                      <!-- column end -->
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="email">Email</label>
                          <input type="email" name="email" id="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{$edit_data->email}}">
                          @if ($errors->has('email'))
                              <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                              </span>
                            @endif
                        </div>
                      </div>
                      <!-- column end -->
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="phone">Phone</label>
                          <input type="text" name="phone" id="phone" class="form-control {{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{$edit_data->phone}}">
                          @if ($errors->has('phone'))
                              <span class="invalid-feedback">
                                <strong>{{ $errors->first('phone') }}</strong>
                              </span>
                            @endif
                        </div>
                      </div>
                      <!-- column end -->
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="designation">Designation</label>
                          <input type="text" name="designation" id="designation" class="form-control {{ $errors->has('designation') ? ' is-invalid' : '' }}" value="{{$edit_data->designation}}">
                          @if ($errors->has('designation'))
                              <span class="invalid-feedback">
                                <strong>{{ $errors->first('designation') }}</strong>
                              </span>
                            @endif
                        </div>
                      </div>
                      <!-- column end -->
                      <div class="col-sm-6">
                        <div class="form-group">
                            <label>state</label>
                              <select name="state" class="form-control select2 {{ $errors->has('state') ? ' is-invalid' : '' }}" value="{{ $edit_data->state}}">
                                <option value="">Select...</option>
                                @foreach($states as $key=>$value)
                                <option value="{{$value->id}}">{{$value->title}}</option>
                                @endforeach
                              </select>

                              @if ($errors->has('state'))
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('state') }}</strong>
                              </span>
                              @endif
                        </div>
                      </div>
                      <!-- column end -->
                      
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="commisiontype">Commision Type</label>
                          <select name="commisiontype" id="commisiontype" class="form-control {{ $errors->has('commisiontype') ? ' is-invalid' : '' }} select2">
                            <option value="">Select...</option>
                            <option value="1">Flat Rate</option>
                            <option value="2">Percent</option>
                          </select>
                          @if ($errors->has('commisiontype'))
                              <span class="invalid-feedback">
                                <strong>{{ $errors->first('commisiontype') }}</strong>
                              </span>
                            @endif
                        </div>
                      </div>
                      <!-- column end -->
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="commision">Commision</label>
                          <input type="number" name="commision" id="commision" class="form-control {{ $errors->has('commision') ? ' is-invalid' : '' }}" value="{{$edit_data->commision}}">
                          @if ($errors->has('commision'))
                              <span class="invalid-feedback">
                                <strong>{{ $errors->first('commision') }}</strong>
                              </span>
                            @endif
                        </div>
                      </div>
                      <!-- column end -->
                      
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label for="image">Image</label>
                          <input type="file" name="image" id="image" class="form-control {{ $errors->has('image') ? ' is-invalid' : '' }}" value="{{old('image')}}">
                          <img src="{{asset($edit_data->image)}}" class="backend_image" alt="">
                          @if ($errors->has('image'))
                              <span class="invalid-feedback">
                                <strong>{{ $errors->first('image') }}</strong>
                              </span>
                            @endif
                        </div>
                      </div>
                      <!-- column end -->
                      <div class="col-sm-6">
                        <div class="form-group">
                          <div class="custom-label">
                            <label>Publication Status</label>
                          </div>
                          <div class="box-body pub-stat display-inline">
                              <input class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" id="active" name="status" value="1">
                              <label for="active">Active</label>
                              @if ($errors->has('status'))
                              <span class="invalid-feedback">
                                <strong>{{ $errors->first('status') }}</strong>
                              </span>
                              @endif
                          </div>
                          <div class="box-body pub-stat display-inline">
                              <input class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" type="radio" name="status" value="0" id="inactive">
                              <label for="inactive">Inactive</label>
                              @if ($errors->has('status'))
                              <span class="invalid-feedback">
                                <strong>{{ $errors->first('status') }}</strong>
                              </span>
                              @endif
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
                </div>
              </div>
              <!-- col end -->

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script type="text/javascript">
      document.forms['editForm'].elements['state'].value="{{$edit_data->state}}"
      document.forms['editForm'].elements['commisiontype'].value="{{$edit_data->commisiontype}}"
      document.forms['editForm'].elements['status'].value="{{$edit_data->status}}"
    </script>
@endsection