@extends('frontend.layouts.app')

@section('content')
    @php
    $user = Session::get('user');
    $identificador = 0;
    if ($user) {
        $identificador = 1;
    }
    session(['ruta' => 'login']);

    $almacenes = App\Models\Almacenes::where('disponibleventa', 1)->get();

    @endphp
    <section class="gry-bg py-5">
        <div class="profile">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            <div class="text-center pt-4">
                                <h1 class="h4 fw-600">
                                    Ingrese a su cuenta
                                </h1>
                            </div>

                            <div class="px-4 py-3 py-lg-4">
                                <div class="">
                                    <form class="form-default" role="form" action="{{ route('login.cliente') }}"
                                        method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text" class="form-control " value=""
                                                placeholder="Identificacion" name="identificacion" autocomplete="off">
                                        </div>

                                        <div class="form-group">
                                            <input type="password" class="form-control" placeholder="Contraseña"
                                                name="clave">
                                        </div>
                                        @if (get_setting('controla_stock') == 2)
                                            <div class="form-group">
                                                <select class="form-control fs-16 bg-soft-primary" id="almacenesid"
                                                    name="almacenesid">
                                                    <option value="0">Seleccione Sucursal
                                                    </option>
                                                    @foreach ($almacenes as $almacen)
                                                        <option value="{{ $almacen->almacenesid }}">
                                                            {{ $almacen->descripcion }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        @endif
                                        <div class="row mb-2">
                                            <div class="col-6">
                                                <label class="aiz-checkbox">
                                                    <input type="checkbox" name="remember">
                                                    <span class=opacity-60>Recuerdame</span>
                                                    <span class="aiz-square-check"></span>
                                                </label>
                                            </div>
                                            <div class="col-6 text-right">
                                                <a href="{{ route('password.request') }}"
                                                    class="text-reset opacity-60 fs-14">Olvidó su contraseña</a>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <button type="submit" class="btn btn-primary btn-block fw-600">Entrar</button>
                                            @if (get_setting('registra_clientes') == 'on')
                                                <a href="{{ route('user.registration') }}"
                                                    class="btn btn-primary btn-block fw-600">Registrarse ahora</a>
                                            @endif
                                        </div>

                                    </form>
                                    @if (get_setting('registra_clientes') == 'on')

                                        @if (get_setting('login_google') == 'on' || get_setting('login_facebook') == 'on')
                                            <div class="separator mb-3">
                                                <span class="bg-white px-3 opacity-60">O inicia sesion</span>
                                            </div>
                                            <ul class="list-inline social colored text-center mb-3" id="botones">
                                                @if (get_setting('login_facebook') == 'on')
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('social.login', ['provider' => 'facebook']) }}"
                                                            id="facebook" class="facebook">
                                                            <i class="lab la-facebook-f"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (get_setting('login_google') == 'on')
                                                    <li class="list-inline-item">
                                                        <a href="{{ route('social.login', ['provider' => 'google']) }}"
                                                            id="google" class="google">
                                                            <i class="lab la-google"></i>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')
    <div class="modal fade" id="modalIdentificacion">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Verificar Usuario</h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">

                        @csrf

                        <div class="form-group">

                            <input type="text" class="form-control h-auto form-control-lg" placeholder="Identificacion"
                                name="identificacion" minlength="10" maxlength="13" pattern="[0-9]+"
                                onkeypress="return validarNumero(event)" id="identificacion" autocomplete="off" required>
                        </div>

                        <div class="mb-2">

                            <button type="submit" class="btn btn-primary btn-block fw-600"
                                id="buttonVerificar">Verificar</button>
                        </div>


                        <form class="form-default" role="form" action="{{ route('verificar.identificacion') }}"
                            id="datosred" method="POST">
                            @csrf

                            <div class="form-group">
                                <input type="hidden" value=" " name="identificacion" id="cedula">
                                <input type="hidden" value=" @if (isset($user)) {{ $user->email }} @endif"
                                    name="email" id="email">
                                <input type="hidden"
                                    value=" @if ($identificador == 1) {{ $user->name }} @endif" name="nombre"
                                    id="nombre">

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            @if (isset($user))
                $('#modalIdentificacion').modal()
            @endif
        });



        $('#botones').click(function(e) {
            if ('get_setting("controla_stock") == 2 ') {
                if ($('#almacenesid').val() == 0) {
                    e.preventDefault();
                    AIZ.plugins.notify('warning', 'Seleccione la Sucursal');
                }
            }

        });


        function validarNumero(e) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla == 8) return true;
            patron = /[0-9]/;
            te = String.fromCharCode(tecla);
            return patron.test(te);
        }

        $("#buttonVerificar").click(function(e) {
            var cad = document.getElementById("identificacion").value.trim();
            var total = 0;
            var longitud = cad.length;
            var longcheck = longitud - 1;
            var digitos = cad.split('').map(Number);
            var codigo_provincia = digitos[0] * 10 + digitos[1];
            if (cad !== "" && longitud === 10) {

                if (cad != '2222222222' && codigo_provincia >= 1 && (codigo_provincia <= 24 || codigo_provincia ==
                        30)) {
                    for (i = 0; i < longcheck; i++) {
                        if (i % 2 === 0) {
                            var aux = cad.charAt(i) * 2;
                            if (aux > 9) aux -= 9;
                            total += aux;
                        } else {
                            total += parseInt(cad.charAt(i)); // parseInt o concatenará en lugar de sumar
                        }
                    }
                    total = total % 10 ? 10 - total % 10 : 0;

                    if (cad.charAt(longitud - 1) == total) {
                        recuperacionValidacion(cad);
                    } else {
                        event.preventDefault();
                        AIZ.plugins.notify('warning', 'La Cédula no es válida');
                        $('#identificacion').focus();
                    }
                } else {
                    event.preventDefault();
                    AIZ.plugins.notify('warning', 'La Cédula no es válida');
                    $('#identificacion').focus();
                }
            } else
            if (longitud == 13) {
                var controlador = 1;
                var valor = 0;

                valor = valor + ((cad.substr(0, 1)) * 4);
                valor = valor + ((cad.substr(1, 1)) * 3);
                valor = valor + ((cad.substr(2, 1)) * 2);
                valor = valor + ((cad.substr(3, 1)) * 7);
                valor = valor + ((cad.substr(4, 1)) * 6);
                valor = valor + ((cad.substr(5, 1)) * 5);
                valor = valor + ((cad.substr(6, 1)) * 4);
                valor = valor + ((cad.substr(7, 1)) * 3);
                valor = valor + ((cad.substr(8, 1)) * 2);

                valor = 11 - ((valor % 11) == 0 ? 11 : (valor % 11));

                if (valor == (cad.substr(9, 1)) && (cad.substr(10, 3)) == "001") {
                    controlador = 2;
                    recuperacionValidacion(cad);
                } else {
                    valor = 0;
                    valor = valor + cad.substr(0, 1) * 3;
                    valor = valor + cad.substr(1, 1) * 2;
                    valor = valor + cad.substr(2, 1) * 7;
                    valor = valor + cad.substr(3, 1) * 6;
                    valor = valor + cad.substr(4, 1) * 5;
                    valor = valor + cad.substr(5, 1) * 4;
                    valor = valor + cad.substr(6, 1) * 3;
                    valor = valor + cad.substr(7, 1) * 2;
                    valor = 11 - ((valor % 11) == 0 ? 11 : (valor % 11));

                    if (valor == (cad.substr(8, 1)) && (cad.substr(9, 4)) == "0001") {
                        controlador = 2;
                        recuperacionValidacion(cad);
                    } else {
                        valor = 0;
                        valor = valor + (cad.substr(0, 1) * 2 > 9 ? ((cad.substr(0, 1)) * 2) - 9 : (cad.substr(0,
                            1)) * 2);
                        valor = valor + (cad.substr(1, 1) * 1 > 9 ? ((cad.substr(1, 1)) * 1) - 9 : (cad.substr(1,
                            1)) * 1);
                        valor = valor + (cad.substr(2, 1) * 2 > 9 ? ((cad.substr(2, 1)) * 2) - 9 : (cad.substr(2,
                            1)) * 2);
                        valor = valor + (cad.substr(3, 1) * 1 > 9 ? ((cad.substr(3, 1)) * 1) - 9 : (cad.substr(3,
                            1)) * 1);
                        valor = valor + (cad.substr(4, 1) * 2 > 9 ? ((cad.substr(4, 1)) * 2) - 9 : (cad.substr(4,
                            1)) * 2);
                        valor = valor + (cad.substr(5, 1) * 1 > 9 ? ((cad.substr(5, 1)) * 1) - 9 : (cad.substr(5,
                            1)) * 1);
                        valor = valor + (cad.substr(6, 1) * 2 > 9 ? ((cad.substr(6, 1)) * 2) - 9 : (cad.substr(6,
                            1)) * 2);
                        valor = valor + (cad.substr(7, 1) * 1 > 9 ? ((cad.substr(7, 1)) * 1) - 9 : (cad.substr(7,
                            1)) * 1);
                        valor = valor + (cad.substr(8, 1) * 2 > 9 ? ((cad.substr(8, 1)) * 2) - 9 : (cad.substr(8,
                            1)) * 2);
                        valor = 10 - ((valor % 10) == 0 ? 10 : (valor % 10))
                        if (valor == (cad.substr(9, 1)) && (cad.substr(10, 3)) == "001") {
                            controlador = 2;
                            recuperacionValidacion(cad);
                        } else {
                            event.preventDefault();
                            AIZ.plugins.notify('warning', 'El RUC no es válido');
                            $('#identificacion').focus();
                        }
                    }
                }

            } else {
                event.preventDefault();
                AIZ.plugins.notify('warning', 'Ingrese un RUC o Cédula válido');
                $('#identificacion').focus();
            }

        });

        function recuperacionValidacion(cad) {

            $.post('{{ route('recuperarInformacionPost') }}', {
                _token: '{{ csrf_token() }}',
                cedula: cad
            }, function(data) {
                if (data.identificacion) {
                    $("#cedula").val(data.identificacion);
                    $("#nombre").val(data.razon_social)
                    datosred.submit();
                } else {
                    $("#cedula").val(cad);
                    datosred.submit();

                }
            });

        }
    </script>
@endsection
