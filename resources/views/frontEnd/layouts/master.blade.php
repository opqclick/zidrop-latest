<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name='copyright' content='pavilan'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title Tag  -->
    <title>@yield('title','always on time')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon.png') }}">

    <!-- Web Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/animate.min.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/cubeportfolio.min.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/font-awesome.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/bootstrap-icons.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/magnific-popup.min.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/owl-carousel.min.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/slicknav.min.css">
    <link rel="stylesheet" href="{{asset('backEnd/')}}/dist/css/toastr.min.css">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/reset.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/style.css">
    <link rel="stylesheet" href="{{asset('frontEnd/')}}/css/responsive.css">
</head>

<body id="bg">
    <!-- Boxed Layout -->
    <div id="page" class="site boxed-layout">
        <!-- Header -->
        <header class="header">            
            <!-- Middle Header -->
            <div class="middle-header">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="middle-inner">
                                <div class="row">
                                    <div class="col-lg-2 col-md-3 col-12">
                                        <!-- Logo -->
                                        <div class="logo">
                                            <!-- Image Logo -->
                                            <div class="img-logo">
                                                <a href="{{url('/')}}">
                                                @foreach($whitelogo as $wlogo)
                                                    <img src="{{asset($wlogo->image)}}" alt="">
                                                @endforeach
                                                </a>
                                            </div>
                                        </div>
                                        <div class="mobile-nav"></div>
                                    </div>
                                    <div class="col-lg-10 col-md-9 col-12">
                                        <div class="menu-area">
                                            <!-- Main Menu -->
                                            <nav class="navbar navbar-expand-lg">
                                                <div class="navbar-collapse">
                                                    <div class="nav-inner">
                                                        <div class="menu-home-menu-container">
                                                            <!-- Naviagiton -->
                                                            <ul id="nav" class="nav main-menu menu navbar-nav">
                                                                <li class="nav-item"><a href="{{url('/')}}">Home</a></li>
                                                                <li class="nav-item custom-dropdown"><a> Services <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                                                                    <ul class="custom-dropdown-menu">
                                                                    @foreach($services as $key=>$value)
                                                                        <li><a href="{{url('our-service/'.$value->id)}}"><i class="fa {{$value->icon}}"></i>{{$value->title}}</a></li>
                                                                    @endforeach
                                                                    </ul>
                                                                </li>
                                                                <li class="nav-item"><a href="{{url('price')}}">Charges</a></li>
                                                                <!-- <li><a href="{{url('about-us')}}">About Us</a></li> -->
                                                                <!--<li class="nav-item"><a href="{{url('merchant/register')}}">Pick & Drop</a></li>-->
                                                                <!--<li class="nav-item"><a href="{{url('branches')}}">Branches</a></li>-->
                                                                <li class="nav-item"><a href="{{url('gallery')}}">Gallery</a></li>
                                                                <li class="nav-item"><a href="{{url('notice')}}">Notice</a></li>
                                                                <li class="nav-item"><a href="{{url('contact-us')}}">Contact Us</a></li>
                                                                <div class="button">
                                                                    <a href="{{url('merchant/register')}}" class="quickTech-btn register">Register</a>
                                                                    <a href="{{url('merchant/login')}}" class="quickTech-btn login">Login</a>
                                                                </div>
                                                            </ul>
                                                            <!--/ End Naviagiton -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </nav>
                                            <!--/ End Main Menu -->

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ End Middle Header -->

        </header>
        <!--/ End Header -->
        
        
        
        
        @yield('content')
        <!-- Footer -->
        <footer class="footer" style="background-image: url({{asset('frontEnd/images/footer.svg')}});">
            <!-- Footer Top -->
            <div class="footer-top">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <!-- Footer Links -->
                            <div class="single-widget f-link widget">
                                <h3 class="widget-title">Services</h3>
                                <ul>
                                    <li><a href="{{url('/')}}">Home Delivery</a></li>
                                    <li><a href="{{url('/')}}">Warehousing</a></li>
                                    <li><a href="{{url('/')}}">Pick and Drop</a></li>
                                </ul>
                            </div>
                            <!--/ End Footer Links -->
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <!-- Footer Links -->
                            <div class="single-widget f-link widget">
                                <h3 class="widget-title">Earn</h3>
                                <ul>
                                    <li><a href="{{url('/')}}">Become Merchant</a></li>
                                    <li><a href="{{url('/')}}">Become Rider</a></li>
                                    <li><a href="{{url('/')}}">Become Delivery Man</a></li>
                                </ul>
                            </div>
                            <!--/ End Footer Links -->
                        </div>
                        <div class="col-lg-2 col-md-6 col-12">
                            <!-- Footer Links -->
                            <div class="single-widget f-link widget">
                                <h3 class="widget-title">Company</h3>
                                <ul>
                                    <li><a href="{{url('about-us')}}">About Us</a></li>
                                    <li><a href="{{url('contact-us')}}">Contact us</a></li>
                                    <li><a href="{{url('/')}}">Our Goal</a></li>
                                </ul>
                            </div>
                            <!--/ End Footer Links -->
                        </div>

                        <div class="col-lg-4 col-md-6 col-12">
                            <!-- Footer Contact -->
                            <div class="single-widget footer_contact widget">
                                <h3 class="widget-title">Contact</h3>
                                <p>Don’t miss any updates of our Offer</p>
                                <div class="newsletter"  style="border-color: #0a0603;">
                                    <form action="" class="d-flex flex-nowrap">
                                        <div class="form-group h-100 m-0 p-2 w-100">
                                            <input type="email" placeholder="Email Address" class="form-control px-1 bg-transparent h-100 border-0 without-focus"/>
                                        </div>
                                        <button type="button" class=" btn font-20 font-light m-1" style="background-color: #0a0603;color:white">Subscribe</button>
                                    </form>
                                </div>
                            </div>
                            <!--/ End Footer Contact -->
                        </div>
                    </div>

                </div>
            </div>
            
            
            
            
            
        
            <!-- Copyright -->
            <div class="copyright">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="copyright-content">
                                <div class="img-logo text-left">
                                    <a href="{{url('/')}}">
                                    @foreach($whitelogo as $wlogo)
                                        <img src="{{asset($wlogo->image)}}" alt="">
                                    @endforeach
                                    </a>
                                </div>
                                
                                
                                <ul class="address-widget-list">
                                    <li class="footer-mobile-number" style="color: #0a0603;"><i class="fa fa-phone" style="color: #0a0603;"></i>{{ $contact_info->phone1 }}</li>
                                    <li class="footer-mobile-number" style="color: #0a0603;"><i class="fa fa-mobile-phone" style="color: #0a0603;"></i></i>{{ $contact_info->phone2 }}</li>
                                    <li class="footer-mobile-number" style="color: #0a0603;"><i class="fa fa-envelope" style="color: #0a0603;"></i> {{ $contact_info->email }}</li>
                                    <li class="footer-mobile-number" style="color: #0a0603;"><i class="fa fa-map-marker" style="color: #0a0603;"></i>{{ $contact_info->address }}</li>
                                </ul>
                                
                                
                            </div>
                        </div>
                        
                        <div class="col-sm-5">
                            <div class="align-items-center copyright-content d-flex justify-content-center">
                                <!-- Copyright Text -->
                                <p style="color: #0a0603;">© Copyright Zidrop Logistics 2021.</p>
                            </div>
                        </div>
               
                        
                        
                        <div class="col-sm-3">
                            <div class="align-items-center copyright-content d-flex justify-content-end">
                                
                                <ul class="social-widget-list">
                                    @foreach($socialmedia as $key=>$value)
                                    <li class="footer-mobile-number"><a href="{{$value->link}}"><i class="{{$value->icon}}"></i></a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--/ End Copyright -->
            
            
            
            
   

        </footer>
        
 
 

        
        

        <!-- Jquery JS -->
        <script src="{{asset('frontEnd/')}}/js/jquery.min.js"></script>
        <script src="{{asset('frontEnd/')}}/js/jquery-migrate-3.0.0.js"></script>
        <!-- Popper JS -->
        <script src="{{asset('frontEnd/')}}/js/popper.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="{{asset('frontEnd/')}}/js/bootstrap.min.js"></script>
        <!-- Modernizr JS -->
        <script src="{{asset('frontEnd/')}}/js/modernizr.min.js"></script>
        <!-- ScrollUp JS -->
        <script src="{{asset('frontEnd/')}}/js/scrollup.js"></script>
        <!-- FacnyBox JS -->
        <script src="{{asset('frontEnd/')}}/js/jquery-fancybox.min.js"></script>
        <!-- Cube Portfolio JS -->
        <script src="{{asset('frontEnd/')}}/js/cubeportfolio.min.js"></script>
        <!-- Slick Nav JS -->
        <script src="{{asset('frontEnd/')}}/js/slicknav.min.js"></script>
        <!-- Slick Nav JS -->
        <script src="{{asset('frontEnd/')}}/js/slicknav.min.js"></script>
        <!-- Slick Slider JS -->
        <script src="{{asset('frontEnd/')}}/js/owl-carousel.min.js"></script>
        <!-- Easing JS -->
        <script src="{{asset('frontEnd/')}}/js/easing.js"></script>
        <!-- Magnipic Popup JS -->
        <script src="{{asset('frontEnd/')}}/js/magnific-popup.min.js"></script>
        <!-- Active JS -->
        <script src="{{asset('frontEnd/')}}/js/active.js"></script>
        <script src="{{asset('backEnd/')}}/dist/js/toastr.min.js"></script>
        {!! Toastr::message() !!}
@yield('custom_js_script')
</body>


</html>
<!-- Messenger Chat Plugin Code -->
    <div id="fb-root"></div>

    <!-- Your Chat Plugin code -->
    <div id="fb-customer-chat" class="fb-customerchat">
    </div>

    <script>
      var chatbox = document.getElementById('fb-customer-chat');
      chatbox.setAttribute("page_id", "109961004701121");
      chatbox.setAttribute("attribution", "biz_inbox");

      window.fbAsyncInit = function() {
        FB.init({
          xfbml            : true,
          version          : 'v11.0'
        });
      };

      (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));
    </script>
    

    
    
    
    
    
    