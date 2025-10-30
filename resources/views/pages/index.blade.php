@extends('pages/layout')

@section('content')

<section class="contact-section">
			<div class="container">
				<center>
				<h2 style="font-size:40px;">Result Checking Portal</h2><p>&nbsp;</p><p>&nbsp;</p>
			</center>
				<div class="row">
					<div class="col-lg-8">
						<div class="contact-box result-box">
					
					<form id="contact-form" method="post" action="{{ route('result.check') }}">
						  @csrf
						<label>Your Registration Number</label>
						<input name="reg_number" type="text">
						<label>Your Card Pin</label>
						<input name="pin" type="text">
						<label>Serial Number</label>
						<input name="serial_number" type="text">
						
						<button type="submit">Check Result</button>
						<div id="msg" class="message"></div><p>&nbsp;</p>
					</form>
				</div>
					</div>
					<div class="col-lg-4">
						<div class="sidebar">
							
							<div class="widget profile-widget">
								<div class="top-part">
									
									<div class="name">
										<h3>Read the following instructions carefully</h3>
										
									</div>
								</div>
								<div class="content" style="font-size: 13px; line-height: 0.8cm;">
									<p><ul>
										
										<li>Do not scratch card with sharp object to avoid loosing the numbers</li>
										<li>Access Card is not transferable after it has been used</li>
										<li>A card can only be used for 5 times</li>
										<li>The Examination Number should be in the following format - </li>
										<li>The Card PIN should be in the following format - 0123456789 </li>
										<li>The serial number should be in the following format - IMSCNM000000 </li>
										
									</ul>





If you have any problems, kindly contact us by sending an email to or put a call through to the following numbers: 
								
							</div>
						</div>
					</div>

				</div>
				</div>
				
			</div>
		</section>

		@endsection