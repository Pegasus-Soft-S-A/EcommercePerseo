@extends('frontend.layouts.app')

@section('content')
<section class="text-center py-6">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 mx-auto">
				<img src="{{ static_asset('assets/img/500.svg') }}" class="img-fluid w-75">
				<h1 class="h2 fw-700 mt-5">Algo salió mal</h1>
				<p class="fs-16 opacity-60">Disculpe las molestias, pero estamos trabajando en ello. </p>
			</div>
		</div>
	</div>
</section>
@endsection