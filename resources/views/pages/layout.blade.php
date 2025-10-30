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
		<header style="background: #f8f9fa; padding: 15px 0; border-bottom: 2px solid #dee2e6;">
			<div class="container">
				<div style="display: flex; justify-content: space-between; align-items: center;">
					<div>
						<strong>Student Result Portal</strong>
					</div>
					<div>
						@auth
							<span style="margin-right: 15px;">Welcome, {{ Auth::user()->name }}</span>
							<form action="{{ route('logout') }}" method="POST" style="display: inline;">
								@csrf
								<button type="submit" style="background: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">
									Logout
								</button>
							</form>
						@else
							<a href="{{ route('login') }}" style="margin-right: 10px; text-decoration: none; color: #007bff;">Login</a>
							<a href="{{ route('register') }}" style="text-decoration: none; color: #007bff;">Register</a>
						@endauth
					</div>
				</div>
			</div>
		</header>
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