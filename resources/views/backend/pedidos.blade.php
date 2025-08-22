@extends('backend.layouts.app')

@section('content')

<div class="card">
    <!-- Formulario de filtros -->
    <form class="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Pedidos</h5>
            </div>
            <div class="col-auto">
                <!-- Botón para mostrar/ocultar los filtros -->
                <button type="button" class="btn btn-secondary" id="toggleFilterButton">Filtrar</button>
            </div>
            @if (get_setting('maneja_sucursales') == "on")
            <div class="col-auto">
                <a href="{{route('pedidos.crear')}}" class="btn btn-primary">Nuevo</a>
            </div>

            <div class="col-auto">
                <a onclick="showExcel()" class="btn btn-success text-white">Importar</a>
            </div>
            @endif
        </div>

        <!-- Sección de filtros, inicialmente oculta -->
        <div class="card-header row " id="filterSection" style="display: none;">
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="estado">Estado</label>
                    <select class="form-control aiz-selectpicker" name="estado" id="estado">
                        <option value="">Todos</option>
                        @foreach ([
                        1 => 'Realizado',
                        2 => 'Confirmado',
                        3 => 'Facturado',
                        4 => 'En Entrega',
                        5 => 'Entregado',
                        0 => 'No Aplica'] as $key => $estado_texto)
                        <option value="{{ $key }}" @if(isset($estado) && $estado==$key) selected @endif>
                            {{ $estado_texto }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="fecha">Fecha</label>
                    <input type="text" class="aiz-date-range form-control" value="{{ $fecha }}" name="fecha"
                        placeholder="Filtrar por Fecha" data-format="DD-MM-Y" data-separator=" a "
                        data-advanced-range="true" autocomplete="off" id="fecha">
                </div>
            </div>
            @if (get_setting('maneja_sucursales') == "on")
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="busqueda">Sucursal</label>
                    @php
                    $sucursales = \App\Models\ClientesSucursales::where('clientesid',
                    get_setting('cliente_pedidos'))->get();
                    @endphp
                    <select class="form-control aiz-selectpicker" name="busqueda">
                        <option value="">Todos</option>
                        @foreach ($sucursales as $sucursal)
                        <option value="{{$sucursal->clientes_sucursalesid}}">{{$sucursal->descripcion}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @else
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="busqueda">Búsqueda</label>
                    <input type="text" class="form-control" id="busqueda" name="busqueda" @isset($busqueda)
                        value="{{ $busqueda }}" @endisset placeholder="Buscar por código o cliente" autocomplete="off">
                </div>
            </div>
            @endif
            @if (get_setting('maneja_sucursales') == "on")
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="busqueda">Centro Costos</label>
                    <select class="form-control aiz-selectpicker" name="centrocostoid" id="centrocostoid"
                        data-live-search="true">
                        <option value="">Todos</option>
                        @foreach ($centrocostos as $centrocosto)
                        <option value="{{ $centrocosto->centros_costosid }}"
                            @isset($centrocostoid)@if($centrocostoid==$centrocosto->centros_costosid ) selected @endif
                            @endisset>{{$centrocosto->centro_costocodigo}}-{{$centrocosto->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="destinatario">Destinatario</label>
                    <input type="text" class="form-control" id="destinatario" name="destinatario" @isset($destinatario)
                        value="{{ $destinatario }}" @endisset placeholder="Buscar por destinatario" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="prioridad">Prioridad</label>
                    <select class="form-control aiz-selectpicker" name="prioridad" id="prioridad"
                        data-live-search="true">
                        <option value="">Todos</option>
                        <option value="1" @isset($prioridad)@if($prioridad==1) selected @endif @endisset>Alta Prioridad
                        </option>
                        <option value="0" @isset($prioridad)@if($prioridad==0) selected @endif @endisset>Prioridad
                            Normal</option>
                    </select>
                </div>
            </div>
            @endif
        </div>

        <!-- Nueva fila con los botones "Aplicar Filtros" y "Exportar PDF" -->
        <div class="card-header row gutters-5" id="actionButtonsSection" style="display: none;">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                <a id="exportPdfButton" class="btn btn-primary text-white">Exportar PDF</a>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        @if (get_setting('maneja_sucursales') == "on")
                        <th data-breakpoints="sm">Prioridad</th>
                        <th data-breakpoints="sm">Destinatario</th>
                        @endif
                        <th>Codigo</th>
                        @if (get_setting('maneja_sucursales') == "on")
                        <th data-breakpoints="sm">Sucursal</th>
                        @else
                        <th data-breakpoints="sm">Cliente</th>
                        @endif
                        <th data-breakpoints="sm">Emision</th>
                        <th>Total</th>
                        <th data-breakpoints="sm">Estado</th>
                        <th data-breakpoints="sm" class="text-center" width="15%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $key => $pedido)
                    <tr>
                        @if (get_setting('maneja_sucursales') == "on")
                        <td>
                            @if ($pedido->prioridad == 1)
                            <i class="las la-check-circle la-2x text-danger" title="Alta Prioridad"></i>
                            @else
                            <i class="las la-check-circle la-2x text-muted" title="Prioridad Normal"></i>
                            @endif
                        </td>
                        <td>{{$pedido->destinatario}}</td>
                        @endif

                        <td>{{ $pedido->pedidos_codigo }}</td>
                        @if (get_setting('maneja_sucursales') == "on")
                        <td>{{ $pedido->descripcion }}</td>
                        @else
                        <td>{{ $pedido->razonsocial }}</td>
                        @endif
                        <td>{{ $pedido->emision }}</td>
                        <td>$ {{ number_format($pedido->total, 2) }}</td>
                        <td>
                            @php
                            $estadoLabels = [
                            1 => ['text' => 'Realizado', 'class' => 'danger'],
                            2 => ['text' => 'Confirmado', 'class' => 'primary'],
                            3 => ['text' => 'Facturado', 'class' => 'primary'],
                            4 => ['text' => 'En Entrega', 'class' => 'primary'],
                            5 => ['text' => 'Entregado', 'class' => 'success'],
                            0 => ['text' => 'No Aplica', 'class' => 'danger']
                            ];
                            $estadoLabel = $estadoLabels[$pedido->estado];
                            @endphp
                            <span class="badge badge-inline badge-{{ $estadoLabel['class'] }}">{{ $estadoLabel['text']
                                }}</span>
                        </td>


                        <td class="text-right">
                            @if ($pedido->urbano <> '')
                                <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0)"
                                    title="Guia Urbano" onclick="showTracking('{{ $pedido->urbano ?? '' }}')">
                                    <i class="las la-thumbtack"></i>
                                </a>
                                @endif
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="{{route('pedidos.show', $pedido->pedidosid)}}" title="Ver">
                                    <i class="las la-eye"></i>
                                </a>
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="{{ route('invoice.download', $pedido->pedidosid) }}" title="Descargar">
                                    <i class="las la-download"></i>
                                </a>
                                @if ($pedido->documentosid == 0)
                                <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                    href="{{route('pedidos.editar', $pedido->pedidosid)}}" title="Editar">
                                    <i class="las la-pen"></i>
                                </a>
                                <a href="javascript:void(0)"
                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                    data-href="{{route('orders.destroy', $pedido->pedidosid)}}" title="Eliminar">
                                    <i class="las la-trash"></i>
                                </a>
                                @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $pedidos->appends(request()->input())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
@include('modals.delete_modal')

<!-- Modal de Carga -->
<div class="modal fade" id="carga">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cargando...</h5>
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div id="order-details-modal-body">

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div id="payment_modal_body">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUrbano">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Urbano</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Estado</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="estadoUrbano" value="" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExcel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Importar Excel</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <form id="formExcel" action="{{ route('pedidos.importarExcel') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">Seleccione Excel</label>
                            <div class="col-md-8">
                                <input type="file" name="file" required>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit">Importar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Solo SweetAlert2 JS (incluye CSS) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Botón para mostrar/ocultar los filtros con animación
        $('#toggleFilterButton').on('click', function() {
            $('#filterSection, #actionButtonsSection').toggle(200); // Cambia la visibilidad con una animación de 300ms

            // Cambia el texto del botón entre "Filtrar" y "Ocultar Filtros"
            var buttonText = $(this).text() === 'Filtrar' ? 'Ocultar Filtros' : 'Filtrar';
            $(this).text(buttonText);
        });

        $('#exportPdfButton').on('click', function() {
            var params = gatherFilterParams();
            var url = '{{ route("pedido.export.pdf") }}' + (params.length > 0 ? '?' + params.join('&') : '');
            window.location.href = url;
        });

        function gatherFilterParams() {
            var params = [];
            var estado = $('#estado').val();
            var fecha = $('#fecha').val();
            var busqueda = $('#busqueda').val();
            var destinatario = $('#destinatario').val();
            var prioridad = $('#prioridad').val();

            if (estado) params.push('estado=' + encodeURIComponent(estado));
            if (fecha) params.push('fecha=' + encodeURIComponent(fecha));
            if (busqueda) params.push('busqueda=' + encodeURIComponent(busqueda));
            if (destinatario) params.push('destinatario=' + encodeURIComponent(destinatario));
            if (prioridad) params.push('prioridad=' + encodeURIComponent(prioridad));

            return params;
        }

        // Función para forzar el cierre del modal de carga
        function forceCloseLoadingModal() {
            $('.c-preloader').hide();
            $('.c-preloader').css('display', 'none');

            // Paso 2: Cerrar el modal #carga de manera agresiva
            $('#carga').modal('hide');
            $('#carga').removeClass('show in fade');
            $('#carga').css('display', 'none');
            $('#carga').attr('aria-hidden', 'true');

            // Paso 3: Limpiar body inmediatamente
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            $('body').css('overflow', '');

            // Paso 4: Remover backdrop inmediatamente
            $('.modal-backdrop').remove();

            // Paso 5: Forzar limpieza después de un breve delay
            setTimeout(function() {
                console.log('Limpieza adicional del modal...');

                // Forzar ocultación del modal #carga
                $('#carga').hide();
                $('#carga').removeClass('show in fade modal-open');
                $('#carga').css({
                    'display': 'none !important',
                    'z-index': '',
                    'opacity': '0'
                });

                // Limpiar completamente el body
                $('body').removeClass('modal-open');
                $('body').css({
                    'padding-right': '',
                    'overflow': '',
                    'position': '',
                    'top': ''
                });
                $('body').removeAttr('style');

                // Remover todos los backdrops posibles
                $('.modal-backdrop').remove();
                $('.fade').removeClass('in');

            }, 50);
        }

// Manejo del formulario de Excel con AIZ.plugins.notify
        $('#formExcel').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $('#carga').modal({backdrop: 'static', keyboard: false});
            $('.c-preloader').show();

            $.ajax({
                type: "POST",
                url: "{{ route('pedidos.importarExcel') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    forceCloseLoadingModal();

                    if (response.success) {
                        // Mensaje de éxito con SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: '¡Importación Exitosa!',
                            html: response.message.replace(/\n/g, '<br>'),
                            width: '600px',
                            confirmButtonText: 'Recargar Página',
                            confirmButtonColor: '#28a745',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalExcel').modal('hide');
                                window.location.reload();
                            }
                        });
                    } else {
                        // Mensaje de error con SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en Importación',
                            html: response.message.replace(/\n/g, '<br>'),
                            width: '700px',
                            confirmButtonText: 'Cerrar',
                            confirmButtonColor: '#dc3545',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#modalExcel').modal('hide');
                            }
                        });
                    }
                },
                error: function(xhr) {
                    forceCloseLoadingModal();

                    let errorMessage = 'Error en la solicitud';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error del Servidor',
                        text: errorMessage,
                        confirmButtonText: 'Cerrar',
                        confirmButtonColor: '#dc3545'
                    });
                },
                complete: function() {
                    // Doble seguridad para cerrar el modal
                    forceCloseLoadingModal();
                }
            });
        });

    });

    function showTracking(codigo) {
        $('#modalUrbano').modal();
        $('#estadoUrbano').val('Cargando...');
        $.post('{{ route('trackingUrbano') }}', {
            _token: '{{ csrf_token() }}',
            codigo: codigo,
        }, function(data) {
            $('#estadoUrbano').val(data.estado);
        }).fail(function() {
            $('#estadoUrbano').val('Error al obtener el estado');
        });
    }
    function showExcel() {
        $('#modalExcel').modal();
    }
</script>
@endsection
