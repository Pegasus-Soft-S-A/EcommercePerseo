@extends('frontend.layouts.app')

@section('content')
    <section class="gry-bg py-4">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center pt-4">
                                <h1 class="h4 fw-600">
                                    Crea una cuenta
                                </h1>
                            </div>
                            <div class="px-4 py-3 py-lg-4">
                                <div class="">
                                    <form class="form-default" action="{{ route('register') }}" method="POST" id="form-register">
                                        @csrf

                                        <div class="form-group">
                                            <input type="text" class="form-control {{ $errors->has('identificacion') ? ' is-invalid' : '' }}"
                                                value="{{ old('identificacion') }}" placeholder="Cédula o Ruc" name="identificacion" minlength="10"
                                                maxlength="13" pattern="[0-9]+" onkeypress="return validarNumero(event)" required id="identificacion"
                                                autocomplete="off" onblur="validarIdentificacion()">

                                            @if ($errors->has('identificacion'))
                                                <span class="invalid-feedback" style="font-weight:bold;" role="alert">
                                                    <span>{{ $errors->first('identificacion') }}</span>
                                                </span>
                                            @endif

                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Nombres Completos" name="razonsocial" required
                                                value="{{ old('razonsocial') }}" autocomplete="off" id="razonsocial">
                                            @if ($errors->has('razonsocial'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group email-form-group  ">
                                            <input type="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                value="{{ old('email') }}" placeholder="Email" name="email" autocomplete="off" required id="email"
                                                onblur="validarEmail()">
                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong> {{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group phone-form-group">
                                            <input type="text" class="form-control" value="{{ old('telefono1') }}" placeholder="Celular"
                                                name="telefono1" autocomplete="off" minlength="7" maxlength="10" pattern="[0-9]+"
                                                onkeypress="return validarNumero(event)" required id="telefono">
                                        </div>

                                        <div class="form-group">
                                            <input type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                placeholder="Contraseña" name="password" minlength="6" maxlength="15" required id="inputContraseña">
                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>La confirmación de la contraseña no coincide</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <input type="password" class="form-control" placeholder="Confirme Contraseña" name="password_confirmation"
                                                minlength="6" maxlength="15" required id="inputContraseñaConfirmar">
                                        </div>

                                        <div class="mb-1">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" name="checkbox_example_1" required>
                                                <span class=opacity-60>Al registrarse, acepta nuestros términos y
                                                    condiciones</span>
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>

                                        <div class="mb-1">
                                            <button type="submit" class="btn btn-primary btn-block fw-600" id="buttonCrear">Crear
                                                Cuenta</button>
                                        </div>
                                    </form>

                                </div>
                                <div class="text-center">
                                    <p class="text-muted mb-0">¿Ya tienes una cuenta?</p>
                                    <a href="{{ route('user.login') }}">Iniciar Sesión</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection


@section('script')
    <script type="text/javascript">
        function validarEmail() {
            email = $('#email').val();
            $.post('{{ route('validacionCampos') }}', {
                _token: '{{ csrf_token() }}',
                email: email
            }, function(data) {
                if (data.length > 0) {
                    event.preventDefault();
                    AIZ.plugins.notify('warning', 'El email ya se encuentra registrado');
                    $('#email').focus();
                }
            });
        }

        $("#buttonCrear").click(function() {
            /* VALIDAR QUE LA CONTRASEÑA Y CONFIRMAR LA CONTRASEÑA COINCIDAN */
            var contraseña = document.getElementById("inputContraseña").value;
            var contraseñaConfirmar = document.getElementById("inputContraseñaConfirmar").value;

            if (contraseña != contraseñaConfirmar) {
                event.preventDefault();
                AIZ.plugins.notify('warning', 'La confirmación de la contraseña no coincide');
                $('#inputContraseñaConfirmar').focus();
            }
        });

        function validarNumero(e) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla == 8) return true;
            patron = /[0-9]/;
            te = String.fromCharCode(tecla);
            return patron.test(te);
        }

        function validarIdentificacion() {
            var cad = document.getElementById("identificacion").value.trim();
            if (cad.length > 0) {
                $.post('{{ route('recuperarInformacionPost') }}', {
                    _token: '{{ csrf_token() }}',
                    cedula: cad
                }, function(data) {
                    if (data.identificacion) {
                        $("#razonsocial").val(data.razon_social);
                        $("#email").val(data.correo);
                        $("#telefono").val(data.telefono1);
                    }
                });
            }
        }
    </script>
@endsection
