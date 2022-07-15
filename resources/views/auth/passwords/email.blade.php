@extends('frontend.layouts.app')

@section('content')

<div class="py-6">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                <div class="bg-white rounded shadow-sm p-4 text-left">
                    <h1 class="h3 fw-600">¿Has olvidado tu contraseña?</h1>
                    <p class="mb-4 opacity-60">Ingrese su identificacion y dirección de correo electrónico para
                        recuperar su contraseña.
                    </p>
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="form-group">
                            <input type="text"
                                class="form-control{{ $errors->has('identificacion') ? ' is-invalid' : '' }}"
                                value="{{ old('identificacion') }}" placeholder="Identificacion" name="identificacion"
                                required autocomplete="off">

                            @if ($errors->has('identificacion'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('identificacion') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                value="{{ old('email') }}" placeholder="Email" name="email" required autocomplete="off">

                            @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group text-right">
                            <button class="btn btn-primary btn-block" type="submit">
                                Enviar Enlace Para Reestablecer Contraseña
                            </button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <a href="{{route('user.login')}}" class="text-reset opacity-60">Volver al Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection