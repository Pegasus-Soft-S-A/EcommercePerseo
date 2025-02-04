@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="card">
    <form class="" action="" id="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Historial de Facturas</h5>
            </div>
            <div class="col-auto">
                <!-- Botón para mostrar/ocultar los filtros -->
                <button type="button" class="btn btn-secondary" id="toggleFilterButton">Filtrar</button>
            </div>
        </div>
        <div class="card-header row gutters-5" id="filterSection" style="display: none;">
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{$fecha}}" name="fecha"
                        placeholder="Filtrar por Fecha" data-format="DD-MM-Y" data-separator=" a "
                        data-advanced-range="true" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="card-header row " id="actionButtonsSection" style="display: none;">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                <a href="{{ route('factura.export.pdf') }}" class="btn btn-primary">Exportar PDF</a>
            </div>
        </div>
    </form>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>Secuencial</th>
                    <th data-breakpoints="sm">Fecha</th>
                    <th>Total</th>
                    <th data-breakpoints="sm">Estado</th>
                    <th data-breakpoints="sm">Pedido</th>
                    <th data-breakpoints="sm" class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $key => $order)
                <tr>
                    <td>
                        <?php
                            $secuencial= $order->establecimiento .' - '.$order->puntoemision .' - '.$order->secuencial;

                        ?>
                        {{ $secuencial}}
                    </td>
                    <td>{{ $order->emision }}</td>
                    <td>
                        ${{ number_format(round($order->total,2),2) }}
                    </td>
                    <td>

                        @if($order->estado == 0)

                        <span class="badge badge-inline badge-danger">Facturado</span>

                        @elseif($order->estado == 1)

                        <span class="badge badge-inline" style="background: #377dff; color:white">En la Entrega</span>

                        @elseif($order->estado == 2)

                        <span class="badge badge-inline badge-success">Entregado</span>


                        @endif

                    </td>
                    <td>
                        {{$order->pedidos_codigo}}
                    </td>
                    <td class="text-center">
                        <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm"
                            onclick="show_purchase_history_details({{ $order->pedidosid }})"
                            title="Detalles del Pedido">
                            <i class="las la-eye"></i>
                        </a>
                        @if($order->archivo_xml <> "")
                            <a href="{{ route('orders.downloadXml', $order->facturasid) }}"
                                class="btn btn-soft-warning btn-icon btn-circle btn-sm" title="Descargar XML">
                                <i class="las la-download"></i>
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

<div class="modal fade" id="factura_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div id="factura-details-modal-body">

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
    });
</script>
@endsection