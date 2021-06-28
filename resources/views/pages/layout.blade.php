<!doctype html>


<html lang="en" class="no-js">
<head>
	<title>Imo State College of Nursing Sciences Orlu </title>

	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,400i,500,500i,600,700&display=swap" rel="stylesheet">
	
	<link rel="stylesheet" href="{{URL::asset('assets/css/studiare-assets.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/fonts/font-awesome/font-awesome.min.css')}}" media="screen">
	<link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/fonts/elegant-icons/style.css')}}" media="screen">
	<link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/fonts/iconfont/material-icons.css')}}" media="screen">
	<link rel="stylesheet" type="text/css" href="{{URL::asset('assets/css/style.css')}}">

</head>
<body>
<div><img width="100%" src="{{URL::asset('assets/images/banner.png')}}" /></div>
	<!-- Container -->
	<div id="container">
		<!-- Header
		    ================================================== -->
		
		<!-- End Header -->

		<!-- page-banner-section 
			================================================== -->
		
		<!-- End page-banner-section -->

		<!-- single-course-section 
			================================================== -->
			@include('pages.template.errors')
			@yield('content')
		
		<!-- End single-course section -->

		<!-- footer 
			================================================== -->
		<footer>
			

			<div class="footer-copyright copyrights-layout-default">
				<div class="container">
					<div class="copyright-inner">
						<div class="copyright-cell"> &copy; 2020 <span class="highlight">Imo State College of Nursing Sciences Orlu</span>. Developed by <a href="https://xclusivea.com/"> XlusiveA Networks </a>.</div>
						<div class="copyright-cell">
							<ul class="studiare-social-links">
								<li><a href="#" class="facebook"><i class="fa fa-facebook-f"></i></a></li>
								<li><a href="#" class="twitter"><i class="fa fa-twitter"></i></a></li>
								<li><a href="#" class="google"><i class="fa fa-google-plus"></i></a></li>
								<li><a href="#" class="linkedin"><i class="fa fa-linkedin"></i></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

		</footer>
		<!-- End footer -->

	</div>
	<!-- End Container -->

	
	<script src="{{URL::asset('assets/js/studiare-plugins.min.js')}}"></script>
	<script src="{{URL::asset('assets/js/jquery.countTo.js')}}"></script>
	<script src="{{URL::asset('assets/js/popper.js')}}"></script>
	<script src="{{URL::asset('assets/js/bootstrap.min.js')}}"></script>
    
	<script src="{{URL::asset('assets/js/gmap3.min.js')}}"></script>
	<script src="{{URL::asset('assets/js/script.js')}}"></script>
	
</body>
</html>