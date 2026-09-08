<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FleetNG - Privacy Policy</title>
    <link rel="icon" href="{{ asset('front/images/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('front/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('front/css/responsive.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
</head>
<body>
    <!-- Navbar Area Start -->
    <nav class="nav-main position-sticky top-0" style="background-color: #EFF2F4;">
        <div class="container common-container">
            <div class="navbar-inner-div">
                <div class="left-div"> <a href="{{url('/')}}"><img src="{{ asset('front/images/logo/fleetng-logo.svg') }}" alt="Logo" width="60" class="img-fluid"></a></div>
                <div class="nav-btns">
                    <a href="{{url('user')}}" class="nav-btn btn me-2">User Login</a>
                    <a href="{{ config('app.front_url') }}" class="nav-btn btn customer">Customer Login</a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar Area End -->
    <div class="container col-xl-10 offset-xl-1 py-5">
    {!! $data ? $data->contents : '' !!}
    </div>

    <footer class="bg-light p-3">
        <ul class="nav justify-content-center gap-4">
            <li class="nav-item">
                <a class="nav-link text-normal fs-14" href="{{url('contact-us')}}">Contact Us</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-normal fs-14" href="{{url('refund-policy')}}">Refund Policy</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-normal fs-14" href="{{url('terms-conditions')}}">Terms and Conditions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-normal fs-14" href="{{url('/')}}">Back to Homepage</a>
            </li>
        </ul>
    </footer>

    <button class="btn btn-primary back-to-top" id="backToTopBtn">
        <svg width="22" height="18" viewBox="0 0 22 18" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M11 12.9235C11.2084 12.9235 11.4082 12.8384 11.5556 12.6869C11.7029 12.5355 11.7857 12.33 11.7857 12.1158V2.75868L15.158 6.22663C15.2311 6.30172 15.3178 6.36129 15.4132 6.40192C15.5087 6.44256 15.611 6.46348 15.7143 6.46348C15.8176 6.46348 15.9199 6.44256 16.0153 6.40192C16.1108 6.36129 16.1975 6.30172 16.2706 6.22663C16.3436 6.15154 16.4016 6.0624 16.4411 5.96429C16.4806 5.86618 16.501 5.76102 16.501 5.65483C16.501 5.54864 16.4806 5.44349 16.4411 5.34538C16.4016 5.24727 16.3436 5.15812 16.2706 5.08303L11.5563 0.237271C11.4833 0.162059 11.3966 0.102387 11.3011 0.0616725C11.2057 0.0209577 11.1033 0 11 0C10.8967 0 10.7943 0.0209577 10.6989 0.0616725C10.6034 0.102387 10.5167 0.162059 10.4437 0.237271L5.72943 5.08303C5.58189 5.23468 5.49901 5.44037 5.49901 5.65483C5.49901 5.8693 5.58189 6.07498 5.72943 6.22663C5.87696 6.37828 6.07707 6.46348 6.28571 6.46348C6.49436 6.46348 6.69446 6.37828 6.842 6.22663L10.2143 2.75868V12.1158C10.2143 12.33 10.2971 12.5355 10.4444 12.6869C10.5918 12.8384 10.7916 12.9235 11 12.9235ZM0 16.9616C0 16.7474 0.0827803 16.542 0.23013 16.3905C0.37748 16.2391 0.57733 16.154 0.785714 16.154H21.2143C21.4227 16.154 21.6225 16.2391 21.7699 16.3905C21.9172 16.542 22 16.7474 22 16.9616C22 17.1758 21.9172 17.3812 21.7699 17.5327C21.6225 17.6841 21.4227 17.7692 21.2143 17.7692H0.785714C0.57733 17.7692 0.37748 17.6841 0.23013 17.5327C0.0827803 17.3812 0 17.1758 0 16.9616V16.9616Z" fill="#ffffff"></path>
            </svg>
    </button>

    <script src="{{ asset('front/js/jquery.min.js') }}"></script>
    <script src="{{ asset('front/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript">
      $(function(){
        $(window).scroll(function(){
            if($(window).scrollTop() > 125){
                $('#backToTopBtn').addClass("d-flex").removeClass("d-none");
            }
            else{
                $('#backToTopBtn').addClass("d-none").removeClass("d-flex");
            }
        });

        $("#backToTopBtn").on("click", function(){
            $("html, body").animate({ scrollTop: 0 });
            return false;
        });
    });
  </script>
</body>
</html>