@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Facturas</h5>
            </div>
            <div class="col-auto">
                <!-- Botón para mostrar/ocultar los filtros -->
                <button type="button" class="btn btn-secondary" id="toggleFilterButton">Filtrar</button>
            </div>
        </div>

        <!-- Sección de filtros, inicialmente oculta -->
        <div class="card-header row gutters-5" id="filterSection" style="display: none;">
            <div class="col-lg-3">
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
            <table class="table aiz-table mb-0 ">
                <thead>
                    <tr>
                        <th width="25%">Secuencia</th>
                        @if (get_setting('maneja_sucursales') == "on")
                        <th data-breakpoints="sm">Sucursal</th>
                        @else
                        <th data-breakpoints="sm">Cliente</th>
                        @endif
                        <th data-breakpoints="md">Total</th>
                        <th data-breakpoints="md">Estado</th>
                        <th class="text-center" width="15%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($facturas as $key => $factura)
                    <?php
                        $secuencial = $factura->establecimiento . ' - ' . $factura->puntoemision . ' - ' . $factura->secuencial;
                    ?>
                    <tr>
                        <td>
                            {{ $secuencial }}
                        </td>
                        @if (get_setting('maneja_sucursales') == "on")
                        <td>{{ $factura->descripcion }}</td>
                        @else
                        <td>{{ $factura->razonsocial }}</td>
                        @endif
                        <td>
                            $ {{ number_format(round(($factura->total),2),2) }}
                        </td>
                        <td>

                            @if($factura->estado == 0)
                            <span class="badge badge-inline badge-danger">Facturado</span>
                            @elseif($factura->estado == 1)
                            <span class="badge badge-inline" style="background: #377dff; color:white">En la
                                Entrega</span>
                            @elseif($factura->estado == 2)
                            <span class="badge badge-inline badge-success">Entregado</span>
                            @endif


                        </td>
                        <td class="text-center">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{route('facturas.show', $factura->facturasid)}}" title="Ver">
                                <i class="las la-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination text-center">
                {{ $facturas->appends(request()->input())->links('pagination::bootstrap-4') }}
            </div>


        </div>
    </form>
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

            if (estado) params.push('estado=' + encodeURIComponent(estado));
            if (fecha) params.push('fecha=' + encodeURIComponent(fecha));
            if (busqueda) params.push('busqueda=' + encodeURIComponent(busqueda));

            return params;
        }
    });

</script>
@endsection
