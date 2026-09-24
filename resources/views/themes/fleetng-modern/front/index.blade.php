<!DOCTYPE html>
<html>

<head>
    <base href="{{ asset('fleetng-view') }}/">
    <title>FleetNG  - Reliable Trucking & Haulage Solutions for Every Sector</title>
    <meta name="description" content="FleetNG  - Specialized trucking and haulage for large quantity goods across all sectors: wholesale, construction, manufacturing, agriculture, and more.">
    <meta name="author" content="FleetNG">
    <meta name="keywords" content="logistics, trucking, haulage, transportation, FleetNG, wholesale delivery, construction delivery, bulk goods, supply chain, Nigeria">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="css/bootstrap.css" />
    <!-- bootstrap grid -->
    <link rel="stylesheet" href="masterslider/style/masterslider.css" />
    <!-- Master slider css -->
    <link rel="stylesheet" href="masterslider/skins/default/style.css" />
    <!-- Master slider default skin -->
    <link rel="stylesheet" href="css/animate.css" />
    <!-- animations -->
    <link rel='stylesheet' href='owl-carousel/owl.carousel.css' />
    <!-- Client carousel -->
    <link rel="stylesheet" href="css/style.css" />
    <!-- template styles -->
    <link rel="stylesheet" href="css/color-default.css" />
    <!-- template main color -->
    <link rel="stylesheet" href="css/retina.css" />
    <!-- retina ready styles -->
    <link rel="stylesheet" href="css/responsive.css" />
    <!-- responsive styles -->

    <!-- Google Web fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,800,700,600" rel='stylesheet' type='text/css'>

    <!-- Font icons -->
    <link rel="stylesheet" href="icon-fonts/font-awesome-4.3.0/css/font-awesome.min.css" />
    <!-- Fontawesome icons css -->

    <style>
        .page-content{
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .fleetng-home-loading {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            pointer-events: none;
            white-space: nowrap;
        }
        .fleetng-home-loading-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: fleetng-home-loading-spin .7s linear infinite;
        }
        @keyframes fleetng-home-loading-spin {
            to { transform: rotate(360deg); }
        }
    </style>
    <!-- styleswitcher -->
</head>

<body><div class="header-wrapper header-transparent">
        <!-- .header.header-style01 start -->
        <header id="header" class="header-style01">
            <!-- .container start -->
            <div class="container">
                <!-- .main-nav start -->
                <div class="main-nav">
                    <!-- .row start -->
                    <div class="row">
                        <div class="col-md-12">
                            <nav class="navbar navbar-default nav-left" role="navigation">

                                <!-- .navbar-header start -->
                                <div class="navbar-header">
                                    <div class="logo">
                                        <a href="{{ url('/') }}">
                                            <img src="img/logo.png" alt="FleetNG - Logistics & Haulage"/>
                                        </a>
                                    </div>
                                    <!-- .logo end -->
                                </div>
                                <!-- .navbar-header start -->

                                <!-- MAIN NAVIGATION -->
                                <div class="collapse navbar-collapse">
                                    <ul class="nav navbar-nav">
                                        <li class="dropdown current-menu-item">
                                            <a href="{{ url('/') }}#masterslider" class="dropdown-toggle scroll">Home</a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="{{ url('/') }}#services" class="dropdown-toggle scroll">Services</a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="{{ url('/') }}#about-us" class="dropdown-toggle scroll">About Us</a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="{{ url('/') }}#contact-us" class="dropdown-toggle scroll">Contact Us</a>
                                        </li>
                                        <!-- Login and Sign Up Buttons inside main menu -->
                                        <li class="dropdown">
                                            <a href="{{ auth()->check() ? url('analytics') : url('user') }}" class="" style="color: rgb(55, 55, 160)">
                                                User Login
                                            </a>
                                        </li>
                                        <li class="dropdown">
                                            <a href="{{ url('customer-portal') }}" class="" style="color: red">
                                                Customer Login
                                            </a>
                                        </li>
                                    </ul>
                                    <!-- .nav.navbar-nav end -->

                                    <!-- RESPONSIVE MENU -->
                                    <div id="dl-menu" class="dl-menuwrapper">
                                        <button class="dl-trigger">Open Menu</button>
                                        <ul class="dl-menu">
                                            <li>
                                                <a href="{{ url('/') }}#masterslider" class="scroll">Home</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/') }}#services" class="scroll">Services</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/') }}#about-us" class="scroll">About Us</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/') }}#contact-us" class="scroll">Contact Us</a>
                                            </li>
                                            <!-- Login and Sign Up in mobile menu -->
                                            <li class="dropdown">
                                                <a href="{{ auth()->check() ? url('analytics') : url('user') }}" class="scroll" style="color: rgb(119, 119, 252)">
                                                    User Login
                                                </a>
                                            </li>
                                            <li class="dropdown">
                                                <a href="{{ url('customer-portal') }}" class="scroll" style="color: red">
                                                    Customer Login
                                                </a>
                                            </li>
                                        </ul>
                                        <!-- .dl-menu end -->
                                    </div>
                                    <div id="search">
                                        <form action="{{ url('/') }}" method="get">
                                            <input class="search-submit" type="submit" />
                                            <input id="m_search" name="s" type="text" placeholder="Type and hit enter..." />
                                        </form>
                                    </div>
                                    <!-- #dl-menu end -->
                                </div>
                                <!-- MAIN NAVIGATION END -->
                            </nav>
                            <!-- .navbar.navbar-default end -->
                        </div>
                        <!-- .col-md-12 end -->
                    </div>
                    <!-- .row end -->
                </div>
                <!-- .main-nav end -->
            </div>
            <!-- .container end -->
        </header>
        <!-- .header.header-style01 -->
    </div>
    <!-- .header-wrapper end -->

    <div id="masterslider" class="master-slider ms-skin-default mb-0">
        <!-- first slide -->
        <div class="ms-slide">
            <!-- slide background -->
            <img src="masterslider/blank.gif" data-src="img/slider/slide04.jpg" alt="Logistics Experts" />

            <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt="" style="left: 50%; top: 310px; transform: translateX(-50%);" data-type="image" data-effect="left(short)" data-duration="300" data-hide-effect="fade" data-delay="0" />

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 340px; transform: translateX(-50%);" data-type="text" data-effect="left(short)" data-duration="300" data-hide-effect="fade" data-delay="300">
                Logistics & Haulage
            </h2>

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 400px; transform: translateX(-50%);" data-type="text" data-effect="left(short)" data-duration="300" data-hide-effect="fade" data-delay="600">
                Reliable Trucking
            </h2>

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 460px; transform: translateX(-50%);" data-type="text" data-effect="left(short)" data-duration="300" data-hide-effect="fade" data-delay="900">
                For Every Sector
            </h2>
        </div>
        <!-- .ms-slide end -->

        <!-- slide 02 start -->
        <div class="ms-slide">
            <!-- slide background -->
            <img src="masterslider/blank.gif" data-src="img/slider/banner-5.png" alt="Start Your Delivery" />

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 390px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="0">
                Start Your Haulage
            </h2>

            <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt="" style="left: 50%; top: 450px; transform: translateX(-50%);" data-type="image" data-effect="bottom(short)" data-duration="300" data-hide-effect="fade" data-delay="300" />

            <p class="ms-layer pi-text" style="left: 50%; top: 470px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="600">
                Hassle-free bulk goods logistics for wholesale, construction, agriculture, and more.
            </p>
        </div>
        <!-- .ms-slide end -->

        <!-- slide 03 start -->
        <div class="ms-slide">
            <!-- slide background -->
            <img src="masterslider/blank.gif" data-src="img/slider/banner-11.png" alt="Easy Logistics Steps" />

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 390px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="0">
                Simple Steps
            </h2>

            <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt="" style="left: 50%; top: 450px; transform: translateX(-50%);" data-type="image" data-effect="bottom(short)" data-duration="300" data-hide-effect="fade" data-delay="300" />

            <p class="ms-layer pi-text" style="left: 50%; top: 470px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="600">
                Request, Track, and Receive Your Goods Nationwide
            </p>
        </div>
        <!-- .ms-slide slide03 end -->

        <!-- slide 04 start -->
        <div class="ms-slide">
            <!-- slide background -->
            <img src="masterslider/blank.gif" data-src="img/slider/slide05.jpg" alt="Choose Your Driver" />

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 390px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="00">
                Choose Your Driver
            </h2>

            <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt="" style="left: 50%; top: 450px; transform: translateX(-50%);" data-type="image" data-effect="bottom(short)" data-duration="300" data-hide-effect="fade" data-delay="300" />

            <p class="ms-layer pi-text" style="left: 50%; top: 470px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="600">
                For Your Bulk Haulage Needs
            </p>
        </div>
        <!-- .ms-slide slide04 end -->

        <!-- slide 05 start -->
        <div class="ms-slide">
            <!-- slide background -->
            <img src="masterslider/blank.gif" data-src="img/slider/banner-4.png" alt="Efficient Delivery" />

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 390px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="0">
                Reliable &
            </h2>

            <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt="" style="left: 50%; top: 450px; transform: translateX(-50%);" data-type="image" data-effect="bottom(short)" data-duration="300" data-hide-effect="fade" data-delay="300" />

            <p class="ms-layer pi-text" style="left: 50%; top: 470px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="600">
                Efficient Logistics for All Sectors
            </p>
        </div>
        <!-- .ms-slide slide05 end -->

        <!-- slide 06 start -->
        <div class="ms-slide">
            <!-- slide background -->
            <img src="masterslider/blank.gif" data-src="img/slider/banner-6.png" alt="Innovative Logistics Solutions" />

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 390px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="0">
                Innovative
            </h2>

            <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt="" style="left: 50%; top: 450px; transform: translateX(-50%);" data-type="image" data-effect="bottom(short)" data-duration="300" data-hide-effect="fade" data-delay="300" />

            <p class="ms-layer pi-text" style="left: 50%; top: 470px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="600">
                Logistics Solutions for Every Industry
            </p>
        </div>
        <!-- .ms-slide slide06 end -->

        <!-- slide 07 start -->
        <div class="ms-slide">
            <!-- slide background -->
            <img src="masterslider/blank.gif" data-src="img/slider/slider06.png" alt="Client Satisfaction" />

            <h2 class="ms-layer pi-caption01" style="left: 50%; top: 390px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="0">
                Client Satisfaction
            </h2>

            <img class="ms-layer" src="masterslider/blank.gif" data-src="img/slider/slider-line.jpg" alt="" style="left: 50%; top: 450px; transform: translateX(-50%);" data-type="image" data-effect="bottom(short)" data-duration="300" data-hide-effect="fade" data-delay="300" />

            <p class="ms-layer pi-text" style="left: 50%; top: 470px; transform: translateX(-50%);" data-type="text" data-effect="top(short)" data-duration="300" data-hide-effect="fade" data-delay="600">
                For All Your Haulage Needs
            </p>
        </div>
        <!-- .ms-slide slide07 end -->
    </div>
    <!-- #masterslider end -->

    <div class="page-content parallax parallax01 mb-70">
        <div class="container">
            <div class="row services-negative-top">
                <div class="col-md-4 col-sm-4">
                    <div class="service-feature-box">
                        <div class="service-media">
                            <img src="img/slider/banner-3.png" alt="">

                            <!-- <a href="{{ config('app.front_url') }}" class="read-more02">
                                    <span>
                                        Read more
                                        <i class="fa fa-chevron-right"></i>
                                    </span>
                                </a> -->
                        </div>
                        <!-- .service-media end -->

                        <div class="service-body">
                            <div class="custom-heading">
                                <h4>CHOOSE YOUR DRIVER</h4>
                            </div>
                            <!-- .custom-heading end -->

                            <p>
                                Select from our pool of experienced logistics drivers to ensure your goods are delivered safely and on time, no matter the sector or cargo size.
                            </p>
                        </div>
                        <!-- .service-body end -->
                    </div>
                    <!-- .service-feature-box-end -->
                </div>
                <!-- .col-md-4 end -->

                <div class="col-md-4 col-sm-4">
                    <div class="service-feature-box">
                        <div class="service-media">
                            <img src="img/slider/truck.png" alt="Logistics Trucking" />

                            <!-- <a href="{{ url('/') }}#services" class="read-more02">
                                    <span>
                                        Read more
                                        <i class="fa fa-chevron-right"></i>
                                    </span>
                                </a> -->
                        </div>
                        <!-- .service-media end -->

                        <div class="service-body">
                            <div class="custom-heading">
                                <h4>TRACK YOUR LOGISTICS</h4>
                            </div>
                            <!-- .custom-heading end -->

                            <p>
                                Monitor your deliveries in real-time. Our advanced tracking system keeps you updated on every stage of your haulage process, from pickup to drop-off.
                            </p>
                        </div>
                        <!-- .service-body end -->
                    </div>
                    <!-- .service-feature-box-end -->
                </div>
                <!-- .col-md-4 end -->

                <div class="col-md-4 col-sm-4">
                    <div class="service-feature-box">
                        <div class="service-media">
                            <img src="img/slider/namer.png" alt="Logistics Solutions" />

                            <!-- <a href="{{ url('/') }}#services" class="read-more02">
                                    <span>  
                                        Read more
                                        <i class="fa fa-chevron-right"></i>
                                    </span>
                                </a> -->
                        </div>
                        <!-- .service-media end -->

                        <div class="service-body">
                            <div class="custom-heading">
                                <h4>LOGISTICS SOLUTIONS</h4>
                            </div>
                            <!-- .custom-heading end -->

                            <p>
                                We provide end-to-end logistics and haulage solutions for wholesale, construction, agriculture, manufacturing, and any sector requiring large quantity delivery.
                            </p>
                        </div>
                        <!-- .service-body end -->
                    </div>
                    <!-- .service-feature-box-end -->
                </div>
                <!-- .col-md-4 end -->
            </div>
            <!-- .row end -->

            <div class="row">
                <div class="col-md-12">
                    <a href="{{ url('customer-portal') }}" class="btn btn-big btn-yellow btn-centered">
                            <span>
                                View Logistics & Haulage Details
                            </span>
                        </a>
                </div>
                <!-- .col-md-12 end -->
            </div>
            <!-- .row end -->
        </div>
        <!-- .container end -->
    </div>
    <!-- .page-content end -->

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="custom-heading02">
                        <h2>Logistics & Haulage Experts</h2>
                        <p>
                            We deliver large quantity goods with accountability, reliability, and convenience for your business, no matter the industry.
                        </p>
                    </div>
                    <!-- .custom-heading02 end -->
                </div>
                <!-- .col-md-12 end -->
            </div>
            <!-- .row end -->

            <div class="row mb-30">
                <div class="col-md-6 col-sm-6">
                    <div class="service-icon-left-boxed">
                        <div class="icon-container animated triggerAnimation" data-animate="zoomIn">
                            <img src="img/svg/pi-checklist-2.svg" alt="checklist icon" />
                        </div>
                        <!-- .icon-container end -->

                        <div class="service-details">
                            <h3>Request Bulk Delivery</h3>

                            <p>
                                Start your logistics journey by requesting delivery of wholesale goods, construction materials, agricultural produce, or any bulk cargo. Our process is simple and efficient for all your needs.
                            </p>
                        </div>
                        <!-- .service-details end -->
                    </div>
                    <!-- .service-icon-left-boxed end -->
                </div>
                <!-- .col-md-6 end -->

                <div class="col-md-6 col-sm-6">
                    <div class="service-icon-left-boxed">
                        <div class="icon-container animated triggerAnimation" data-animate="zoomIn">
                            <img src="img/svg/pi-globe-5.svg" alt="globe icon" />
                        </div>
                        <!-- .icon-container end -->

                        <div class="service-details">
                            <h3>Contact FleetNG </h3>

                            <p>
                                FleetNG : Suite 6, Scapular Plaza KM 17 Lekki-Epe expressway Eti-Osa Lagos Nigeria. Phone: +234 906 493 7788. Email: support@fleetng.com.
                            </p>
                        </div>
                        <!-- .service-details end -->
                    </div>
                    <!-- .service-icon-left-boxed end -->
                </div>
                <!-- .col-md-6 end -->
            </div>
            <!-- .row.mb-30 end -->

            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="service-icon-left-boxed">
                        <div class="icon-container animated triggerAnimation" data-animate="zoomIn">
                            <img src="img/svg/pi-forklift-truck-5.svg" alt="forktruck icon" />
                        </div>
                        <!-- .icon-container end -->

                        <div class="service-details">
                            <h3>Refund Policy</h3>

                            <p>
                                Our refund policy covers all logistics and haulage transactions. Please contact us for details regarding refunds for deliveries and logistics services.
                            </p>
                        </div>
                        <!-- .service-details end -->
                    </div>
                    <!-- .service-icon-left-boxed end -->
                </div>
                <!-- .col-md-6 end -->

                <div class="col-md-6 col-sm-6">
                    <div class="service-icon-left-boxed">
                        <div class="icon-container animated triggerAnimation" data-animate="zoomIn">
                            <img src="img/svg/pi-touch-desktop.svg" alt="touch icon" />
                        </div>
                        <!-- .icon-container end -->

                        <div class="service-details">
                            <h3>Privacy Policy</h3>

                            <p>
                                We value your privacy in all logistics engagements. For our full privacy policy and terms, please contact FleetNG .
                            </p>
                        </div>
                        <!-- .service-details end -->
                    </div>
                    <!-- .service-icon-left-boxed end -->
                </div>
                <!-- .col-md-6 end -->
            </div>
            <!-- .row.mb-30 end -->
        </div>
        <!-- .container end -->
    </div>
    <!-- .page-content end -->

    <div id="about-us" class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-heading">
                        <h2>About FleetNG </h2>
                    </div><!-- .custom-heading end -->

                    <p>
                        FleetNG  specializes in the trucking and haulage of large quantity goods for all sectors: wholesale, construction, agriculture, manufacturing, and more. We are committed to supporting your business with reliable, accountable, and convenient logistics services.
                    </p>

                    <p>
                        Our team of professional drivers and modern fleet ensures your goods arrive safely and on schedule. We are dedicated to providing the highest standards in logistics, making us the trusted partner for your supply chain.
                    </p>
                </div><!-- .col-md-6 end -->

                <div class="col-md-6 animated triggerAnimation" data-animate="zoomIn">
                    <img src="img/slider/trucker.png" alt="FleetNG "/>
                </div><!-- .col-md-6 end -->
            </div><!-- .row end -->
        </div><!-- .container end -->
    </div><!-- .page-content end -->  

    <div class="page-content custom-bkg bkg-light-blue mb-70">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-heading">
                        <h2>Our Mission</h2>
                    </div><!-- .custom-heading end -->

                    <p>
                        Our mission is to provide seamless logistics and haulage services, ensuring timely and safe delivery of all goods to your business or project sites. We strive to be the backbone of your supply chain, regardless of sector.
                    </p>
                </div><!-- .col-md-6 end -->

                <div class="col-md-6">
                    <div class="custom-heading">
                        <h2>Our Promise</h2>
                    </div><!-- .custom-heading end -->

                    <ul class="fa-ul">
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Professional drivers for all types of bulk delivery
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Investment in staff training for safe and efficient logistics
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Environmentally responsible logistics practices
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Safety-first approach for all deliveries
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Advanced technology for real-time tracking and communication
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Commitment to the highest standards in logistics and haulage
                        </li>
                    </ul><!-- .fa-ul end -->
                </div><!-- .col-md-6 end -->
            </div><!-- .row end -->
        </div><!-- .container end -->
    </div><!-- .page-content.custom-bkg end -->

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="custom-heading02">
                    <h2>Logistics & Haulage Services</h2>
                    <p>We deliver large quantity goods with full accountability and convenience for your business, no matter the sector.</p>
                </div>
            </div><!-- .row end -->

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="service-icon-center">
                        <div class="icon-container">
                            <i class="fa fa-truck"></i>
                        </div>

                        <h4>Request Bulk Delivery</h4>

                        <p>
                            Request delivery of wholesale goods, construction materials, agricultural produce, or any bulk cargo. Our process is fast and reliable for all your project needs.
                        </p>
                    </div><!-- .service-icon-center end -->
                </div><!-- .col-md-3 end -->

                <div class="col-md-3 col-sm-6">
                    <div class="service-icon-center">
                        <div class="icon-container">
                            <i class="fa fa-map-marker"></i>
                        </div>

                        <h4>Our Location</h4>

                        <p>
                            FleetNG : Suite 6, Scapular Plaza KM 17 Lekki-Epe expressway Eti-Osa Lagos Nigeria.
                        </p>
                    </div><!-- .service-icon-center end -->
                </div><!-- .col-md-3 end -->

                <div class="col-md-3 col-sm-6">
                    <div class="service-icon-center">
                        <div class="icon-container">
                            <i class="fa fa-phone"></i>
                        </div>

                        <h4>Call Us</h4>

                        <p>
                            Phone: +234 906 493 7788
                        </p>
                    </div><!-- .service-icon-center end -->
                </div><!-- .col-md-3 end -->

                <div class="col-md-3 col-sm-6">
                    <div class="service-icon-center">
                        <div class="icon-container">
                            <i class="fa fa-envelope"></i>
                        </div>

                        <h4>Email Us</h4>

                        <p>
                            Email: support@fleetng.com
                        </p>
                    </div><!-- .service-icon-center end -->
                </div><!-- .col-md-3 end -->
            </div><!-- .row end -->

            <div class="row">
                <div class="col-md-9">
                    <div class="custom-heading">
                        <h2>Top Logistics Drivers</h2>
                    </div><!-- .custom-heading end -->

                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <div class="team-member">
                                <img src="https://static.everypixel.com/ep-pixabay/0329/8099/0858/84037/3298099085884037069-head.png" alt=""/>
                                <div class="team-details">
                                    <h5>John Emenike</h5>
                                    <p class="position">
                                        Logistics & Haulage Driver
                                    </p>
                                </div><!-- .team-details end -->
                            </div><!-- .member end -->
                        </div><!-- .col-md-4 end -->

                        <div class="col-md-4 col-sm-4">
                            <div class="team-member">
                                <img src="https://static.everypixel.com/ep-pixabay/0329/8099/0858/84037/3298099085884037069-head.png" alt=""/>
                                <div class="team-details">
                                    <h5>Tolu Abel</h5>
                                    <p class="position">
                                        Logistics & Haulage Driver
                                    </p>
                                </div><!-- .team-details end -->
                            </div><!-- .member end -->
                        </div><!-- .col-md-4 end -->

                        <div class="col-md-4 col-sm-4">
                            <div class="team-member">
                                <img src="https://static.everypixel.com/ep-pixabay/0329/8099/0858/84037/3298099085884037069-head.png" alt=""/>
                                <div class="team-details">
                                    <h5>Alex Tony</h5>
                                    <p class="position">
                                        Logistics & Haulage Driver
                                    </p>
                                </div><!-- .team-details end -->
                            </div><!-- .member end -->
                        </div><!-- .col-md-4 end -->
                    </div><!-- .row end -->
                </div><!-- .col-md-9 end -->

                <div class="col-md-3">
                    <div class="custom-heading">
                        <h2>Join Our Logistics Team</h2>
                    </div><!-- .custom-heading end -->

                    <div class="promo-box promo-bkg01">
                        <h4>Drivers Needed</h4>
                        <p>
                            We are hiring logistics and haulage drivers. Join our team and help deliver goods to businesses and projects across Nigeria!
                        </p>

                        <a href="{{ url('customer-portal') }}" class="btn btn-medium btn-yellow">
                            <span>Apply Now</span>
                        </a>
                    </div><!-- .promo-box end -->
                </div><!-- .col-md-3 end -->
            </div><!-- .row end -->
        </div><!-- .container end -->
    </div><!-- .page-content end -->

    <div id="services" class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="custom-heading">
                        <h2>Our Logistics & Haulage Services</h2>
                    </div><!-- .custom-heading end -->
                    <p>
                        FleetNG specializes in logistics and haulage, providing reliable delivery of large quantity goods for wholesale, construction, agriculture, manufacturing, and more. Our services are designed to meet the unique needs of businesses, contractors, and project managers in every sector.
                    </p>

                    <ul class="fa-ul">
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            End-to-end logistics for all industries
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Real-time tracking of all deliveries
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Professional drivers and modern trucks for every sector
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            High-quality, timely transportation of bulk goods
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Technology-driven logistics for efficiency and transparency
                        </li>
                        <li>
                            <i class="fa fa-li fa-long-arrow-right"></i>
                            Adherence to safety and industry standards in logistics and haulage
                        </li>
                    </ul><!-- .fa-ul end -->

                    <br>

                    <p>
                        Contact us for your logistics and haulage needs. Location: Suite 6, Scapular Plaza KM 17 Lekki-Epe expressway Eti-Osa Lagos Nigeria. Call +234 906 493 7788 or email support@fleetng.com.
                    </p>

                    <p>
                        For information on our refund policy, privacy policy, and terms and conditions regarding logistics and haulage, please reach out to our team.
                    </p>
                </div><!-- .col-md-8 end -->

                <div class="col-md-4">
                    <img src="img/pics/img32.jpg" alt="Logistics Truck">
                </div><!-- .col-md-4 end -->
            </div><!-- .row end -->
        </div><!-- .container end -->
    </div>

    <div class="page-content parallax parallax01 dark">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="call-to-action clearfix">
                        <div class="text">
                            <h2>Logistics & Haulage You Can Trust.</h2>
                            <p>
                                We deliver large quantity goods for your business with reliability and efficiency. Request your delivery, track your shipment, and receive your goods on time, every time.
                            </p>                              
                        </div><!-- .text end -->

                        <a href="{{ url('customer-portal') }}" class="btn btn-big">
                            <span>Request Bulk Delivery</span>
                        </a>
                    </div><!-- .call-to-action end -->
                </div><!-- .col-md-12 end -->
            </div><!-- .row end -->
        </div><!-- .container end -->
    </div><!-- .page-content.parallax end -->

    <div class="page-content custom-bkg bkg-dark-blue column-img-bkg dark mb-70">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 col-md-offset-2 custom-col-padding-both">
                    <div class="custom-heading">
                        <h3>Logistics & Haulage Solutions</h3>
                    </div>
                    <!-- .custom-heading end -->

                    <p>
                        We make logistics and haulage easy. Request your goods, choose your driver, and track your delivery from dispatch to your destination, for any sector or industry.
                    </p>

                    <ul class="service-list clearfix">
                        <li>
                            <div class="icon-container">
                                <img class="svg-white" src="img/svg/pi-truck-8.svg" alt="icon" />
                            </div>
                            <!-- .icon-container end -->

                            <p>
                                Bulk Goods Transport
                            </p>
                        </li>
                        <li>
                            <div class="icon-container">
                                <img class="svg-white" src="img/svg/pi-cargo-box-2.svg" alt="icon" />
                            </div>
                            <!-- .icon-container end -->

                            <p>
                                Choose Your Logistics Driver
                            </p>
                        </li>
                        <li>
                            <div class="icon-container">
                                <img class="svg-white" src="img/svg/pi-cargo-retail.svg" alt="icon" />
                            </div>
                            <!-- .icon-container end -->

                            <p>
                                Track Your Delivery
                            </p>
                        </li>
                        <li>
                            <div class="icon-container">
                                <img class="svg-white" src="img/svg/pi-mark-energy.svg" alt="icon" />
                            </div>
                            <!-- .icon-container end -->

                            <p>
                                Haulage for All Sectors
                            </p>
                        </li>
                    </ul>
                    <!-- .service-list end -->
                </div>
                <!-- .col-md-6 end -->

                <div class="col-md-6 img-bkg01">
                    <div>&nbsp;</div>
                </div>
            </div>
            <!-- .row end -->
        </div>
        <!-- .container end -->
    </div>
    <!-- .page-content.bkg-dark-blue end -->

    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <div class="custom-heading">
                        <h3>Our Logistics Services</h3>
                    </div>
                    <!-- .custom-heading end -->

                    <ul class="pi-latest-posts clearfix">
                        <li>
                            <div class="post-media">
                                <img src="img/svg/pi-truck-8.svg" alt="icon" />
                            </div>
                            <!-- .post-media end -->

                            <div class="post-details">
                                <div class="post-date">
                                    <p>
                                        <i class="fa fa-calendar"></i> LOGISTICS & HAULAGE
                                    </p>
                                </div>

                                <a href="{{ url('customer-portal') }}">
                                    <h4>
                                        Bulk Goods Transport
                                    </h4>
                                </a>

                                <a href="{{ url('customer-portal') }}" class="read-more">
                                        <span>
                                            Learn more
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                            </div>
                            <!-- .post-details end -->
                        </li>

                        <li>
                            <div class="post-media">
                                <img src="img/svg/pi-cargo-retail.svg" alt="icon" />
                            </div>
                            <!-- .post-media end -->

                            <div class="post-details">
                                <div class="post-date">
                                    <p>
                                        <i class="fa fa-calendar"></i> WHOLESALE DELIVERY
                                    </p>
                                </div>

                                <a href="{{ url('customer-portal') }}">
                                    <h4>
                                        Wholesale & Retail Delivery
                                    </h4>
                                </a>

                                <a href="{{ url('customer-portal') }}" class="read-more">
                                        <span>
                                            Learn more
                                            <i class="fa fa-chevron-right"></i>
                                        </span>
                                    </a>
                            </div>
                            <!-- .post-details end -->
                        </li>
                    </ul>
                    <!-- .pi-latest-posts end -->
                </div>
                <!-- .col-md-4 end -->

                <div class="col-md-4 col-sm-6">
                    <div class="custom-heading">
                        <h3>What Our Clients Say</h3>
                    </div>
                    <!-- .custom-heading end -->

                    <div class="carousel-container">
                        <div id="testimonial-carousel" class="owl-carousel owl-carousel-navigation">
                            <div class="owl-item">
                                <div class="testimonial">
                                    <p>
                                        FleetNG made our wholesale delivery seamless. Their logistics team delivered all our goods on time and kept us updated throughout the process.
                                    </p>
                                    <div class="testimonial-author">
                                        <p>
                                            JAMES ANDERSON, <br /> Wholesale Manager
                                        </p>
                                    </div>
                                    <!-- .testimonial-author end -->
                                </div>
                                <!-- .testimonial end -->
                            </div>
                            <!-- .owl-item end -->

                            <div class="owl-item">
                                <div class="testimonial">
                                    <p>
                                        The logistics service from FleetNG is top-notch. We always receive our bulk materials exactly when we need them, whether for construction or retail.
                                    </p>
                                    <div class="testimonial-author">
                                        <p>
                                            SARAH JOHNSON, <br /> Operations Supervisor
                                        </p>
                                    </div>
                                    <!-- .testimonial-author end -->
                                </div>
                                <!-- .testimonial end -->
                            </div>
                            <!-- .owl-item end -->

                            <div class="owl-item">
                                <div class="testimonial">
                                    <p>
                                        FleetNG's real-time tracking and professional drivers make them our preferred partner for all large quantity deliveries.
                                    </p>
                                    <div class="testimonial-author">
                                        <p>
                                            MICHAEL BROWN, <br /> Logistics Coordinator
                                        </p>
                                    </div>
                                    <!-- .testimonial-author end -->
                                </div>
                                <!-- .testimonial end -->
                            </div>
                            <!-- .owl-item end -->
                        </div>
                        <!-- #testimonial-carousel end -->
                    </div>
                    <!-- .carousel-container end -->
                </div>
                <!-- .col-md-4 end -->

                <div class="col-md-4 col-sm-12 clearfix">
                    <div class="custom-heading">
                        <h3>Our Service Locations</h3>
                    </div>
                    <!-- .custom-heading end -->

                    <img src="img/pics/locations.jpg" alt="locations illustration" />

                    <br />

                    <p>
                        FleetNG  serves over 150 locations, delivering goods to businesses and project sites across Nigeria and beyond.
                    </p>

                    <a href="{{ url('customer-portal') }}" class="read-more">
                            <span>
                                View all locations
                                <i class="fa fa-chevron-right"></i>
                            </span>
                        </a>
                </div>
                <!-- .col-md-4 end -->
            </div>
            <!-- .row end -->
        </div>
        <!-- .container end -->
    </div>
    <!-- .page-content end -->

    <div class="page-content custom-bkg bkg-grey">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="carousel-container">
                        <div id="client-carousel" class="owl-carousel owl-carousel-navigation">
                            <div class="owl-item"><img src="img/pics/client01.png" alt="" /></div>
                            <div class="owl-item"><img src="img/pics/client02.png" alt="" /></div>
                            <div class="owl-item"><img src="img/pics/client03.png" alt="" /></div>
                            <div class="owl-item"><img src="img/pics/client04.png" alt="" /></div>
                            <div class="owl-item"><img src="img/pics/client05.png" alt="" /></div>
                            <div class="owl-item"><img src="img/pics/client06.png" alt="" /></div>
                        </div>
                        <!-- .owl-carousel.owl-carousel-navigation end -->
                    </div>
                    <!-- .carousel-container end -->
                </div>
                <!-- .col-md-12 end -->
            </div>
            <!-- .row end -->
        </div>
        <!-- .container end -->
    </div>
    <!-- .page-content end -->

    <div id="contact-us" class="page-content" style="padding-top:40px">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-heading">
                        <h3>Contact FleetNG </h3>
                    </div><!-- .custom-heading.left end -->

                    <p>
                        For all your logistics and haulage needs, including wholesale, construction, agriculture, and more, contact FleetNG. We provide reliable delivery, real-time tracking, and professional support for your business.
                    </p>

                    <br>

                    <!-- .contact form start -->
                    <form class="wpcf7 clearfix" id="fleetng-home-contact" action="{{ route('submit-contact-us') }}" method="POST">
                        @csrf
                        <div class="alert alert-success" id="fleetng-home-contact-success" style="display:none;"></div>
                        <div class="alert alert-danger" id="fleetng-home-contact-error" style="display:none;"></div>
                        <fieldset>
                            <label>
                                <span class="required">*</span> Your request:
                            </label>

                            <select class="wpcf7-form-control-wrap wpcf7-select" id="contact-inquiry">
                                <option value="Request a Delivery">Request a Delivery</option>
                                <option value="Choose a Driver">Choose a Driver</option>
                                <option value="Track Logistics">Track Logistics</option>
                                <option value="Other Inquiries">Other Inquiries</option>
                            </select>
                        </fieldset>

                        <fieldset>
                            <label>
                                <span class="required">*</span> First Name:
                            </label>

                            <input type="text" class="wpcf7-text" id="contact-name" name="first_name" required>
                        </fieldset>

                        <fieldset>
                            <label>
                                <span class="required">*</span> Last Name:
                            </label>

                            <input type="text" class="wpcf7-text" id="contact-last-name" name="last_name" required>
                        </fieldset>

                        <fieldset>
                            <label>
                                <span class="required">*</span> Email:
                            </label>

                            <input type="email" class="wpcf7-text" id="contact-email" name="email" required>
                        </fieldset>

                        <fieldset>
                            <label>
                                <span class="required">*</span> Phone:
                            </label>

                            <input type="tel" class="wpcf7-text" id="contact-phone" name="phone" placeholder="0801234567" required>
                        </fieldset>

                        <fieldset>
                            <label>
                                <span class="required">*</span> Message:
                            </label>

                            <textarea rows="8" class="wpcf7-textarea" id="contact-message" name="message" required></textarea>
                        </fieldset>

                        <input type="submit" class="wpcf7-submit" value="send">
                    </form><!-- .wpcf7 end -->
                </div><!-- .col-md-6 end -->

                <div class="col-md-6">
                    <div class="custom-heading">
                        <h3>FleetNG  HQ</h3>
                    </div><!-- .custom-heading end -->

                    <div id="map" style="position: relative; overflow: hidden;">
                        <iframe
                            width="100%"
                            height="450"
                            frameborder="0" style="border:0"
                            src="https://www.google.com/maps?q=Suite+6,+Scapular+Plaza+KM+17+Lekki-Epe+expressway,+Eti-Osa,+Lagos,+Nigeria&output=embed" allowfullscreen>
                        </iframe>
                    </div>

                    <div class="custom-heading">
                        <h4>FleetNG Address</h4>
                    </div><!-- .custom-heading end -->

                    <address>
                        Suite 6, Scapular Plaza KM 17 Lekki-Epe expressway, <br>
                        Eti-Osa, Lagos, Nigeria
                    </address>

                    <span class="text-big colored">
                        +234 906 493 7788
                    </span>
                    <br>

                    <a href="mailto:support@fleetng.com">support@fleetng.com</a>
                </div><!-- .col-md-6 end -->
            </div><!-- .row end -->
        </div><!-- .container end -->
    </div>


<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
    <div id="footer-wrapper" class="footer-dark">
        <footer id="footer">
            <div class="container">
                <div class="row" style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                    <ul class="col-md-3 col-sm-6 footer-widget-container clearfix">
                        <!-- .widget.widget_text -->
                        <li class="widget widget_newsletterwidget">
                            <div class="title">
                                <h3>Newsletter Subscribe</h3>
                            </div>

                            <p>
                                Subscribe to our newsletter for updates on logistics, new services, and special offers for your business.
                            </p>

                            <br />

                            <form class="newsletter" action="javascript:void(0)">
                                <input class="email" type="email" placeholder="Your email...">
                                <input type="submit" class="submit" value="">
                            </form>
                        </li>
                        <!-- .widget.widget_newsletterwidget end -->
                    </ul>
                    <!-- .col-md-3.footer-widget-container end -->

                    <ul class="col-md-3 col-sm-6 footer-widget-container">
                        <!-- .widget-pages start -->
                        <li class="widget widget_pages">
                            <div class="title">
                                <h3>Quick Links</h3>
                            </div>

                            <ul>
                                <li><a href="{{ url('/') }}#masterslider" class="scroll">Home</a></li>
                                <li><a href="{{ url('/') }}#services" class="scroll">Services</a></li>
                                <li><a href="{{ url('/') }}#about-us" class="scroll">About us</a></li>
                                <li><a href="{{ url('/') }}#contact-us" class="scroll">Contact us</a></li>
                            </ul>
                        </li>
                        <!-- .widget-pages end -->
                    </ul>
                    <!-- .col-md-3.footer-widget-container end -->

                    <ul style="display:none" class="col-md-3 col-sm-6 footer-widget-container d-none">
                        <!-- .widget-pages start -->
                        <li class="widget widget_pages">
                            <div class="title">
                                <h3>Industry solutions</h3>
                            </div>

                            <ul>
                                <li><a href="{{ url('/') }}#services">Overland transportation</a></li>
                                <li><a href="{{ url('/') }}#services">Air freight</a></li>
                                <li><a href="{{ url('/') }}#services">Ocean freight</a></li>
                                <li><a href="{{ url('/') }}#services">Large projects</a></li>
                                <li><a href="{{ url('/') }}#services">Rail international shipping</a></li>
                                <li><a href="{{ url('/') }}#services">Contract logistics</a></li>
                                <li><a href="{{ url('/') }}#services">Packaging options</a></li>
                            </ul>
                        </li>
                        <!-- .widget-pages end -->
                    </ul>
                    <!-- .col-md-3.footer-widget-container end -->

                    <ul class="col-md-3 col-sm-6 footer-widget-container">
                        <li class="widget widget-text">
                            <div class="title">
                                <h3>Contact Us</h3>
                            </div>

                            <address>
                                Suite 6, Scapular Plaza KM 17 Lekki-Epe expressway, <br />
                                Eti-Osa, Lagos, Nigeria
                            </address>

                            <span class="text-big">
                                +234 906 493 7788
                            </span>
                            <br />

                            <a href="mailto:support@fleetng.com">support@fleetng.com</a>
                            <br />
                            <ul class="footer-social-icons">
                                <li><a href="{{ config('app.front_url') }}" class="fa fa-facebook"></a></li>
                                <li><a href="{{ config('app.front_url') }}" class="fa fa-twitter"></a></li>
                                <li><a href="{{ config('app.front_url') }}" class="fa fa-google-plus"></a></li>
                            </ul>
                            <!-- .footer-social-icons end -->
                        </li>
                        <!-- .widget.widget-text end -->
                    </ul>
                    <!-- .col-md-3.footer-widget-container end -->
                </div>
                <!-- .row end -->
            </div>
            <!-- .container end -->
        </footer>
        <!-- #footer end -->

        <div class="copyright-container">
            <div class="container">
                <div class="row" style="display: flex; justify-content: space-between;">
                    <div class="col-md-6">
                        <p>FLEETNG 2025. All RIGHTS RESERVED.</p>
                    </div>
                    <!-- .col-md-6 end -->

                    <div class="col-md-6">
                        <p class="align-right">POWERED BY <a href="{{ url('/') }}">FLEETNG.</a> .</p>
                    </div>
                    <!-- .col-md-6 end -->
                </div>
                <!-- .row end -->
            </div>
            <!-- .container end -->
        </div>
        <!-- .copyright-container end -->

        <a href="{{ config('app.front_url') }}" class="scroll-up">Scroll</a>
    </div>
    <!-- #footer-wrapper end -->

    <script src="js/jquery-2.1.4.min.js"></script>
    <!-- jQuery library -->
    <script src="js/bootstrap.min.js"></script>
    <!-- .bootstrap script -->
    <script src="js/jquery.srcipts.min.js"></script>
    <!-- modernizr, retina, stellar for parallax -->
    <script src="owl-carousel/owl.carousel.min.js"></script>
    <!-- Carousels script -->
    <script src="masterslider/masterslider.min.js"></script>
    <!-- Master slider main js -->
    <script src="js/jquery.matchHeight-min.js"></script>
    <!-- for columns with background image -->
    <script src="js/jquery.dlmenu.min.js"></script>
    <!-- for responsive menu -->
    <!-- styleswitcher script -->
    <script src="js/include.js"></script>
    <!-- custom js functions -->

    <script>
        /* <![CDATA[ */
        (function ($) {
            function setHomeLoading(control, label) {
                if (!control || control.dataset.loadingText) return;
                var loadingLabel = label || 'Loading...';

                if (control.tagName === 'INPUT') {
                    control.dataset.loadingText = control.value;
                    control.value = loadingLabel;
                } else {
                    control.dataset.loadingText = control.innerHTML;
                    control.innerHTML = '<span class="fleetng-home-loading-spinner" aria-hidden="true"></span><span>' + loadingLabel + '</span>';
                }

                control.classList.add('fleetng-home-loading');
                control.setAttribute('aria-busy', 'true');

                if (control.tagName === 'BUTTON' || control.tagName === 'INPUT') {
                    control.disabled = true;
                }
            }

            function clearHomeLoading(control) {
                if (!control || !control.dataset.loadingText) return;
                control.classList.remove('fleetng-home-loading');
                control.removeAttribute('aria-busy');

                if (control.tagName === 'INPUT') {
                    control.value = control.dataset.loadingText;
                } else {
                    control.innerHTML = control.dataset.loadingText;
                }

                delete control.dataset.loadingText;
                if (control.tagName === 'BUTTON' || control.tagName === 'INPUT') {
                    control.disabled = false;
                }
            }

            $(document).on('click', 'a[href]', function () {
                var href = this.getAttribute('href') || '';
                if ($(this).hasClass('scroll') || href.charAt(0) === '#' || href.indexOf('javascript:') === 0 || this.target === '_blank') {
                    return;
                }

                setHomeLoading(this, 'Loading...');
            });

            $(document).on('submit', 'form', function () {
                if (this.id === 'fleetng-home-contact') return;
                var submitter = this.querySelector('[type="submit"]');
                setHomeLoading(submitter, 'Loading...');
            });

            $('#fleetng-home-contact').on('submit', function (event) {
                event.preventDefault();
                var $form = $(this);
                var submit = $form.find('[type="submit"]').get(0);
                var $success = $('#fleetng-home-contact-success').hide();
                var $error = $('#fleetng-home-contact-error').hide();
                var firstName = $.trim($('#contact-name').val());
                var lastName = $.trim($('#contact-last-name').val());
                var payload = $form.serializeArray().filter(function (field) {
                    return field.name !== 'first_name' && field.name !== 'last_name';
                });

                payload.push({ name: 'name', value: $.trim(firstName + ' ' + lastName) });
                setHomeLoading(submit, 'Loading...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $.param(payload),
                    headers: { 'Accept': 'application/json' },
                    success: function (response) {
                        $success.text(response.message || 'Thank you for contacting us. We will get back to you shortly.').show();
                        $form[0].reset();
                    },
                    error: function (xhr) {
                        var response = xhr.responseJSON || {};
                        $error.text(response.message || 'Something went wrong. Please check the form and try again.').show();
                    },
                    complete: function () {
                        clearHomeLoading(submit);
                    }
                });
            });
        })(jQuery);

        jQuery(document).ready(function($) {
            'use strict';

            function equalHeight() {
                $('.page-content.column-img-bkg *[class*="custom-col-padding"]').each(function() {
                    var maxHeight = $(this).outerHeight();
                    $('.page-content.column-img-bkg *[class*="img-bkg"]').height(maxHeight);
                });
            };

            $(document).ready(equalHeight);
            $(window).resize(equalHeight);

            // Smooth scrolling for navigation links
            $('a.scroll').on('click', function(event) {
                if (this.hash !== "") {
                    event.preventDefault();
                    var hash = this.hash;
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top
                    }, 800, function(){
                        window.location.hash = hash;
                    });
                }
            });

            // MASTER SLIDER START 
            var slider = new MasterSlider();
            slider.setup('masterslider', {
                width: 1140, // slider standard width
                height: 854, // slider standard height
                space: 0,
                speed: 50,
                layout: "fullwidth",
                centerControls: false,
                loop: true,
                autoplay: true
                // more slider options goes here...
                // check slider options section in documentation for more options.
            });
            // adds Arrows navigation control to the slider.
            slider.control('arrows');

            // CLIENTS CAROUSEL START 
            $('#client-carousel').owlCarousel({
                items: 6,
                loop: true,
                margin: 30,
                responsiveClass: true,
                mouseDrag: true,
                dots: false,
                responsive: {
                    0: {
                        items: 2,
                        nav: true,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: true,
                        responsiveClass: true
                    },
                    600: {
                        items: 3,
                        nav: true,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: true,
                        responsiveClass: true
                    },
                    1000: {
                        items: 6,
                        nav: true,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: true,
                        responsiveClass: true,
                        mouseDrag: true
                    }
                }
            });

            // TESTIMONIAL CAROUSELS START
            $('#testimonial-carousel').owlCarousel({
                items: 1,
                loop: true,
                margin: 30,
                responsiveClass: true,
                mouseDrag: true,
                dots: false,
                autoheight: true,
                responsive: {
                    0: {
                        items: 1,
                        nav: true,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: true,
                        responsiveClass: true,
                        autoHeight: true
                    },
                    600: {
                        items: 1,
                        nav: true,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: true,
                        responsiveClass: true,
                        autoHeight: true
                    },
                    1000: {
                        items: 1,
                        nav: true,
                        loop: true,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: true,
                        responsiveClass: true,
                        mouseDrag: true,
                        autoHeight: true
                    }
                }
            });
        });
        /* ]]> */
    </script>
    </body>

</html>



