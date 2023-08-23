@extends('frontEnd.layouts.master')
@section('title','Contact Us')
@section('content')
<!-- Breadcrumb -->
<div class="breadcrumbs" style="background:#db0022;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <!-- Bread Menu -->
                    <div class="bread-menu">
                        <ul>
                            <li><a href="/">Home</a></li>
                            <li><a href="">Contact  Us</a></li>
                        </ul>
                    </div>
                    <!-- Bread Title -->
                    <!--<div class="bread-title"><h2>Contact Us</h2></div>-->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / End Breadcrumb -->

<!-- Contact Us -->
<section class="contact-us section-space">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-7 col-12">
                <!-- Contact Form -->
                <div class="contact-form-area m-top-30">
                    <h4>Get In Touch</h4>
                    <form class="form" method="post" action="" id="contactForm">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <div class="icon"><i class="fa fa-user"></i></div>
                                    <input type="text" name="first_name" placeholder="First Name" required value="{{ old('first_name') }}">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <div class="icon"><i class="fa fa-user"></i></div>
                                    <input type="text" name="last_name" placeholder="Last Name" required value="{{ old('last_name') }}">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <div class="icon"><i class="fa fa-envelope"></i></div>
                                    <input type="email" name="email" placeholder="Enter your mail address" required value="{{ old('email') }}">
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-group">
                                    <div class="icon"><i class="fa fa-phone"></i></div>
                                    <input type="text" name="phone" placeholder="Enter your phone number" required value="{{ old('phone') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="icon"><i class="fa fa-tag"></i></div>
                                    <input type="text" name="subject" placeholder="Type Subjects" required value="{{ old('subject') }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group textarea">
                                    <div class="icon"><i class="fa fa-pencil"></i></div>
                                    <textarea type="textarea" name="message" rows="5" required>{{ old('message') }}</textarea>
                                </div>
                            </div>
                            @if(config('google_captcha.site_key'))
                                <div class="col-12 mt-3">
                                    <div class="g-recaptcha"
                                         data-sitekey="{{config('google_captcha.site_key')}}">
                                    </div>
                                    @error('g-recaptcha-response')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                    <div class="alert alert-danger" id="gcaptcha-error" style="display: none"></div>
                                </div>
                            @endif
                            <div class="col-12">
                                <div class="form-group button">
                                    <button type="submit" class="quickTech-btn theme-2">Send Now</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!--/ End contact Form -->
            </div>
            <div class="col-lg-5 col-md-5 col-12">
                <div class="contact-box-main m-top-30">
                    <div class="contact-title">
                        <h2>Contact with us</h2>
                        <p>{{ $contact_info->address }}</p>
                    </div>
                    
                    <!-- Single Contact -->
                    
                    <div class="single-contact-box">
                        <div class="c-icon"><i class="fa fa-phone"></i></div>
                        <div class="c-text">
                            <h4>Call Us Now</h4>
                            <p>{{ $contact_info->phone1 }}<br></p>
                        </div>
                    </div>                    
                    
                    <div class="single-contact-box">
                        <div class="c-icon"><i class="fa fa-mobile-phone"></i></div>
                        <div class="c-text">
                            <h4>Call Us Now</h4>
                            <p>{{ $contact_info->phone2 }}<br></p>
                        </div>
                    </div>
                    <!--/ End Single Contact -->
                    <!-- Single Contact -->
                    <div class="single-contact-box">
                        <div class="c-icon"><i class="fa fa-envelope-o"></i></div>
                        <div class="c-text">
                            <h4>Email Us</h4>
                            <p> {{ $contact_info->email }}</p>
                        </div>
                    </div>
                    <!--/ End Single Contact 
                    <iframe class="gmap_iframe" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?width=200&amp;height=400&amp;hl=en&amp;q=536, Shamim Sharani&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>-->
                </div>
            </div>
        </div>
    </div>
</section>  
<!--/ End Contact Us -->

@endsection
@section('custom_js_script')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        {{--@if(config('google_captcha.site_key'))
            $("#contactForm").on('submit', function (e) {
                e.preventDefault();
                let url = "{{ route('frontend.contact-us.validate') }}";
                let captcha = $("#g-recaptcha-response").val();
                let _token = "{{ csrf_token() }}";
                let data  = $("#contactForm").serialize();

                console.log(data);
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function (result) {
                        if(result.status == 200) {
                            e.currentTarget.submit();
                        } else {
                            $("#gcaptcha-error").html('Please complete the captcha');
                            $("#gcaptcha-error").show();
                        }
                    },
                    error: function () {
                        $("#gcaptcha-error").html('Please complete the captcha');
                        $("#gcaptcha-error").show();
                    }
                });
            });
        @endif--}}
    </script>
@endsection
