@extends('backend.layouts.layout')

@section('content')
<section class="align-items-center d-flex h-100 bg-white">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 mx-auto text-center py-4">
				<img src="{{ static_asset('assets/img/maintainance.svg') }}" class="img-fluid w-75">
				<h3 class="fw-600 mt-5">Estamos en Mantenimiento</h3>
				<div class="lead">Volveremos pronto</div>
			</div>
		</div>
	</div>
</section>
@endsection