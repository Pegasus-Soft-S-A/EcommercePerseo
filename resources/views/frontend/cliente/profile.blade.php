@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">Administrar Perfil</h1>
        </div>
    </div>
</div>

<!-- Basic Info-->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">Informacion Basica</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label class="col-md-2 col-form-label">Identificacion</label>
                <div class="col-md-10">
                    <input type="text" class="form-control" placeholder="Identificacion" name="identificacion"
                        value="{{ Auth::user()->identificacion }}" @if (Auth::user()->identificacion != '') readonly
                    @endif autocomplete="off">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">Nombre</label>
                <div class="col-md-10">
                    <input type="text" class="form-control" placeholder="Nombre" name="razonsocial"
                        value="{{ Auth::user()->razonsocial }}" @if (Auth::user()->identificacion != '')
                    @endif autocomplete="off">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">Email</label>
                <div class="col-md-10">
                    <input type="text" class="form-control" placeholder="Email" name="email"
                        value="{{ Auth::user()->email_login }}" autocomplete="off" required>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">Telefono</label>
                <div class="col-md-10">
                    <input type="text" class="form-control" placeholder="Telefono" name="telefono1"
                        value="{{ Auth::user()->telefono1 }}" autocomplete="off">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">Convencional</label>
                <div class="col-md-10">
                    <input type="text" class="form-control" placeholder="Convencional" name="telefono2"
                        value="{{ Auth::user()->telefono2 }}" autocomplete="off">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">Whatsapp</label>
                <div class="col-md-10">
                    <input type="text" class="form-control" placeholder="Whatsapp" name="telefono3"
                        value="{{ Auth::user()->telefono3 }}" autocomplete="off">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">Contraseña:</label>
                <div class="col-md-10">
                    <input type="password" class="form-control" placeholder="Nueva Contraseña" name="new_password">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-md-2 col-form-label">Confirmar Contraseña</label>
                <div class="col-md-10">
                    <input type="password" class="form-control" placeholder="Confirmar Contraseña"
                        name="confirm_password">
                </div>
            </div>

            <div class="form-group mb-0 text-right">
                <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
            </div>
        </form>
    </div>
</div>

<!-- Address -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">Direccion</h5>
    </div>
    <div class="card-body">
        <div class="row gutters-10">
            @php
            $sucursales = \App\Models\ClientesSucursales::select('clientes_sucursales.clientes_sucursalesid',
            'clientes_sucursales.direccion', 'clientes_sucursales.telefono1', 'ciudades.ciudad')
            ->join('ciudades', 'ciudades.ciudadesid', 'clientes_sucursales.ciudadesid')
            ->where('clientes_sucursales.clientesid', Auth::user()->clientesid)
            ->get();
            @endphp
            @foreach ($sucursales as $key => $address)
            <div class="col-lg-6">
                <div class="border p-3 pr-5 rounded mb-3 position-relative">
                    <div>
                        <span class="w-50 fw-600">Direccion:</span>
                        <span class="ml-2">{{ $address->direccion }}</span>
                    </div>
                    <div>
                        <span class="w-50 fw-600">Ciudad:</span>
                        <span class="ml-2">{{ $address->ciudad }}</span>
                    </div>
                    <div>
                        <span class="w-50 fw-600">Telefono:</span>
                        <span class="ml-2">{{ $address->telefono1 }}</span>
                    </div>
                    <div class="dropdown position-absolute right-0 top-0">
                        <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                            <i class="la la-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" onclick="edit_address('{{ $address->clientes_sucursalesid }}')">
                                Editar
                            </a>
                            <a class="dropdown-item"
                                href="{{ route('addresses.destroy', $address->clientes_sucursalesid) }}">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="col-lg-6 mx-auto" onclick="add_new_address()">
                <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                    <i class="la la-plus la-2x"></i>
                    <div class="alpha-7">Agregar Nueva Direccion</div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('modal')
<div class="modal fade" id="new-address-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-zoom" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Nueva Direccion</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3">

                        <div class="row">
                            <div class="col-md-2">
                                <label>Ciudad</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="ciudad"
                                    required>
                                    <option value="">Seleccione Ciudad</option>
                                    @foreach (\App\Models\Ciudades::get() as $key => $ciudad)
                                    <option value="{{ $ciudad->ciudadesid }}">{{ $ciudad->ciudad }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>Direccion</label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control textarea-autogrow mb-3" placeholder="Su Direccion"
                                    rows="1" name="direccion" onkeydown="controlar(event)" required></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>Telefono</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="999999999" name="telefono"
                                    value="" required autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Direccion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="edit_modal_body">

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    function controlar(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                return false;
            }
        }

        function edit_address(address) {
            var url = '{{ route('addresses.edit', 'clientes_sucursalesid') }}';
            url = url.replace('clientes_sucursalesid', address);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#edit_modal_body').html(response);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });
        }

        function add_new_address() {
            $('#new-address-modal').modal('show');
        }
</script>
@endsection