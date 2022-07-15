@extends('frontend.layouts.app')

@section('content')
<div class="py-6">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-md-8 mx-auto">
                <div class="bg-white rounded shadow-sm p-4 text-left">
                    <h1 class="h3 fw-600">Restablecer la contraseña</h1>
                    <p class="mb-4 opacity-60">
                        Ingrese su indentificacion, dirección de correo electrónico, la nueva contraseña y confirme la
                        contraseña.</p>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf


                        <div class="form-group">
                            <input id="code" type="text"
                                class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code"
                                value="{{ $email ?? old('code') }}" placeholder="Codigo" required autofocus
                                autocomplete="off">

                            @if ($errors->has('code'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('code') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="password" type="password"
                                class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                                placeholder="Nueva Contraseña" required>

                            @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>La confirmación de la contraseña no coincide</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="password-confirm" type="password" class="form-control"
                                name="password_confirmation" placeholder="Confirmar Contraseña" required>
                        </div>

                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary btn-block">
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection