@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">Historial de Facturas</h5>
    </div>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>Secuencial</th>
                    <th data-breakpoints="sm">Fecha</th>
                    <th>Total</th>
                    <th data-breakpoints="sm">Estado</th>
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
                        <a href="#"
                            onclick="show_facturas_history_details({{ $order->facturasid }})">{{ $secuencial}}</a>
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
                    <td class="text-center">
                        <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm"
                            onclick="show_facturas_history_details({{ $order->facturasid }})"
                            title="Detalles del Pedido">
                            <i class="las la-eye"></i>
                        </a>

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