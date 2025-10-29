@extends('pages/layout')

@section('content')
@if($student->remark == 'PASSED')
<section class="blog-section">
			<div class="container">
				<div class="row">
					<div class="col-lg-10 col-md-10">

						<div class="blog-box">
							<div class="blog-post single-post">
								<div class="post-content">
									<h1>Interview Letter - Congratulations!!!</h1>
									
								</div>
								<a href="single-post.html"><img src="upload/blog/blog-image-1.jpg" alt=""></a>
								<div class="post-content">
									<b>Congratulations<h3>{{ $student->student_name }}</h3>
									Your score:  {{ $student->score }} <br>
									Remark: <strong style="color: green;">{{ $student->remark }}</strong></b><hr>
									<p>You  are to come with the following original/photocopies of your credentials: 
										<ol style="font-size: 14px; line-height: 0.7cm;">
											<li>birth certificate</li>
											<li>FSLC first school leaving certificate</li>
											<li>WAEC/NECO</li>
											<li>Marriage certificate(for married women only)</li>
											<li>local government of origin </li>
											<li>Entrance examination  result print out</li>
											<li>Interview fee receipt</li>
										</ol> 

<i>Note: those who scored from 50 and above are to come for interview on 21st of September 2020<br><br>
           Those who scored from 42 to 49 are to come for interview on 22nd and 23rd respectively of September 2020

<p>pay your interview on this acct no:1020304417
                                               acct name IMO STATE COLLEGE OF NURSING SCIENCES 
                                               bank : UBA</p></i>


CONGRATULATIONS!!!
</p>
<button onClick="window.print()">Print this page</button>
									
									
									
								</div>
							</div>

												

						</div>
					</div>

					

				</div>
						
			</div>
		</section>
	@else 
	<section class="blog-section">
			<div class="container">
				<div class="row">
					<div class="col-lg-10 col-md-10">

						<div class="blog-box">
							<div class="blog-post single-post">
								<div class="post-content">
									<h1>Failed</h1>
									
								</div>
								<a href="single-post.html"><img src="upload/blog/blog-image-1.jpg" alt=""></a>
								<div class="post-content">
									<b>Name: <h3>{{ $student->student_name }}</h3>
									Your score:  {{ $student->score }} <br>
									Remark: <strong style="color: red;">{{ $student->remark }}</strong></b>
									
									
								</div>
							</div>

												

						</div>
					</div>

					

				</div>
						
			</div>
		</section>
		@endif
		@endsection