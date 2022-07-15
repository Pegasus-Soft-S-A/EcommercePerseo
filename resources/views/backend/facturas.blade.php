@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Facturas</h5>
            </div>
            <div class="col-lg-3 ml-auto">
                <select class="form-control aiz-selectpicker" name="estado" id="estado">
                    <option value="">Todos</option>
                    <option value="0" @isset($estado)@if($estado==0 ) selected @endif @endisset>Facturado</option>
                    <option value="1" @isset($estado)@if($estado==1 ) selected @endif @endisset>En la Entrega</option>
                    <option value="2" @isset($estado)@if($estado==2 ) selected @endif @endisset>Entregado</option>

                </select>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="{{ $fecha }}" name="fecha"
                        placeholder="Filtrar por Fecha" data-format="DD-MM-Y" data-separator=" a "
                        data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="busqueda" name="busqueda" @isset($busqueda)
                        value="{{ $busqueda }}" @endisset placeholder="Buscar por cliente" autocomplete="off">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0 ">
                <thead>
                    <tr>
                        <th width="25%">Secuencia</th>
                        <th data-breakpoints="md">Cliente</th>
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
                        <td>
                            {{ $factura->razonsocial }}
                        </td>
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