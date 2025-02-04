@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header">
            <div class="col">
                <h5 class="mb-md-0 h6">Historial de Pedidos</h5>
            </div>
            <div class="col-auto">
                <!-- Botón para mostrar/ocultar los filtros -->
                <button type="button" class="btn btn-secondary" id="toggleFilterButton">Filtrar</button>
            </div>
        </div>
        <div class="card-header row gutters-5" id="filterSection" style="display: none;">
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label for="estado">Estado</label>
                    <select class="form-control aiz-selectpicker" name="estado" id="estado">
                        <option value="">Todos</option>
                        <option value="1" @isset($estado)@if($estado==1 ) selected @endif @endisset>Realizado</option>
                        <option value="2" @isset($estado)@if($estado==2 ) selected @endif @endisset>Confirmado</option>
                        <option value="3" @isset($estado)@if($estado==3 ) selected @endif @endisset>Facturado</option>
                        <option value="4" @isset($estado)@if($estado==4 ) selected @endif @endisset>En Entrega</option>
                        <option value="5" @isset($estado)@if($estado==5 ) selected @endif @endisset>Entregado</option>
                        <option value="6" @isset($estado)@if($estado==0 ) selected @endif @endisset>No Aplica</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label for="fecha">Fecha</label>
                    <input type="text" class="aiz-date-range form-control" value="{{$fecha}}" name="fecha"
                        placeholder="Filtrar por Fecha" data-format="DD-MM-Y" data-separator=" a "
                        data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            @if (get_setting('maneja_sucursales') == "on")
            <div class="col-lg-3 ">
                <div class="form-group mb-0">
                    <label for="centrocosto">Centro Costo</label>
                    <select class="form-control aiz-selectpicker" name="centrocostoid" id="centrocostoid">
                        <option value="">Todos</option>
                        @foreach ($centrocostos as $centrocosto)
                        <option value="{{ $centrocosto->centros_costosid }}"
                            @isset($centrocostoid)@if($centrocostoid==$centrocosto->centros_costosid ) selected @endif
                            @endisset>{{ $centrocosto->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label for="destinatario">Destinatario</label>
                    <input type="text" class="form-control" id="destinatario" name="destinatario"
                        value="{{ $destinatario }}" placeholder="Buscar por destinatario" autocomplete="off">
                </div>
            </div>
            @endif
            <!-- Nueva fila con los botones "Aplicar Filtros" y "Exportar PDF" -->
        </div>
        <div class="card-header row " id="actionButtonsSection" style="display: none;">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                <a href="{{ route('orders.export.pdf') }}" class="btn btn-primary">Exportar PDF</a>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>Codigo</th>
                    @if (get_setting('maneja_sucursales') == "on")
                    <th data-breakpoints="sm">Destinatario</th>
                    @endif
                    <th data-breakpoints="sm">Fecha</th>
                    <th>Total</th>
                    <th data-breakpoints="sm">Estado</th>
                    <th data-breakpoints="sm" class="text-right">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order)
                <tr>
                    <td>
                        <a href="#" onclick="show_purchase_history_details({{ $order->pedidosid }})">{{
                            $order->pedidos_codigo }}</a>
                    </td>
                    @if (get_setting('maneja_sucursales') == "on")
                    <td>{{ $order->destinatario }}</td>
                    @endif
                    <td>{{ $order->emision }}</td>
                    <td>
                        ${{ number_format(round($order->total,2),2) }}
                    </td>
                    <td>
                        @if($order->estado == 1)

                        <span class="badge badge-inline badge-danger">Pedido Realizado</span>

                        @elseif($order->estado == 2)

                        <span class="badge badge-inline badge-danger">Pedido Confirmado</span>


                        @elseif($order->estado == 3)

                        <span class="badge badge-inline badge-danger"> Pedido Facturado</span>

                        @elseif($order->estado == 4)

                        <span class="badge badge-inline badge-danger"> En la Entrega</span>

                        @elseif($order->estado == 5)

                        <span class="badge badge-inline badge-danger"> Entregado</span>

                        @elseif($order->estado == 0)

                        <span class="badge badge-inline badge-danger">No Aplica</span>

                        @endif
                    </td>
                    <td class="text-right">
                        @if ($order->urbano <> '')
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0)"
                                title="Guia Urbano" onclick="showTracking('{{ $order->urbano ?? '' }}')">
                                <i class="las la-thumbtack"></i>
                            </a>
                            @endif
                            <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                onclick="show_purchase_history_details({{ $order->pedidosid }})"
                                title="Detalles del Pedido">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                href="{{ route('invoice.download', $order->pedidosid) }}" title="Descargar Pedido">
                                <i class="las la-download"></i>
                            </a>
                            @if ($order->documentosid==0)
                            <a href="javascript:void(0)"
                                class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                data-href="{{route('orders.destroy', $order->pedidosid)}}" title="Eliminar">
                                <i class="las la-trash"></i>
                            </a>
                            @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{$orders->appends(request()->input())->links('pagination::bootstrap-4')}}
        </div>
    </div>

</div>
@endsection

@section('modal')
@include('modals.delete_modal')

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
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Botón para mostrar/ocultar los filtros con animación
        $('#toggleFilterButton').on('click', function() {
            $('#filterSection, #actionButtonsSection').toggle(200); // Cambia la visibilidad con una animación de 300ms

            // Cambia el texto del botón entre "Filtrar" y "Ocultar Filtros"
            var buttonText = $(this).text() === 'Filtrar' ? 'Ocultar Filtros' : 'Filtrar';
            $(this).text(buttonText);
        });
    });

    function showTracking(codigo) {
        // Muestra el modal
        $('#modalUrbano').modal();

       // Coloca el mensaje "Cargando..." en el campo de estado mientras espera la respuesta
         $('#estadoUrbano').val('Cargando...');

        // Realiza la petición
        $.post('{{ route('trackingUrbano') }}', {
            _token: '{{ csrf_token() }}',
            codigo: codigo,
        }, function(data) {
            // Oculta el spinner
            $('#loading-spinner').hide();

            // Coloca el estado en el campo una vez recibida la respuesta
            $('#estadoUrbano').val(data.estado);
        }).fail(function() {
            // Oculta el spinner y muestra un mensaje de error si falla
            $('#loading-spinner').hide();
            $('#estado').val('Error al obtener el estado');
        });
    }
</script>
@endsection