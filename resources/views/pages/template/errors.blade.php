@if (count($errors))
	
		<div class="alert alert-danger" id="phade" style="position: absolute;z-index: 10; top: 40px; right: 20px; color: #b93535;"><strong>Error! </strong>
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	
@endif