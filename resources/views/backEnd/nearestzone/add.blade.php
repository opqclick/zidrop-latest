@extends('backEnd.layouts.master')
@section('title', 'Nearestzone Add')
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
                                        <h5>Nearestzone Add</h5>
                                    </div>
                                    <div class="quick-button">
                                        <a href="{{ url('admin/nearestzone/manage') }}"
                                            class="btn btn-primary btn-actions btn-create">
                                            Manage Nearestzone
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Nearestzone Add Instructions</h3>
                                    </div>
                                    <!-- /.card-header -->
                                    <!-- form start -->
                                    <form action="{{ url('admin/nearestzone/save') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="main-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlSelect1">State</label>
                                                        <select class="form-control" id="exampleFormControlSelect1" name="state" required>
                                                          @foreach($state as $value)
                                                          <option value="{{ $value->id }}">{{ $value->title }}</option>
                                                          @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="zonename">Nearestzone Name</label>
                                                        <input type="text" name="zonename" id="zonename"
                                                            class="form-control {{ $errors->has('zonename') ? ' is-invalid' : '' }}"
                                                            value="{{ old('zonename') }}">
                                                        @if ($errors->has('zonename'))
                                                            <span class="invalid-feedback">
                                                                <strong>{{ $errors->first('zonename') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

                                                </div>
                                                <div class="col-sm-6">
                                                  <div class="form-group">
                                                      <label for="extradeliverycharge">Extra delivery charge</label>
                                                      <input type="text" name="extradeliverycharge" id="extradeliverycharge"
                                                          class="form-control {{ $errors->has('extradeliverycharge') ? ' is-invalid' : '' }}"
                                                          value="{{ old('extradeliverycharge') }}">
                                                      @if ($errors->has('extradeliverycharge'))
                                                          <span class="invalid-feedback">
                                                              <strong>{{ $errors->first('extradeliverycharge') }}</strong>
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
                                                            <input
                                                                class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}"
                                                                type="radio" id="active" name="status" value="1">
                                                            <label for="active">Active</label>
                                                            @if ($errors->has('status'))
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $errors->first('status') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="box-body pub-stat display-inline">
                                                            <input
                                                                class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}"
                                                                type="radio" name="status" value="0" id="inactive">
                                                            <label for="inactive">Inactive</label>
                                                            @if ($errors->has('status'))
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $errors->first('status') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 mrt-15">
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-primary">Submit</button>
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
@endsection
