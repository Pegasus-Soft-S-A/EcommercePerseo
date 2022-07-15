@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Pedidos</h5>
            </div>
            <div class="col-lg-3 ml-auto">
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
                        value="{{ $busqueda }}" @endisset placeholder="Buscar por codigo o cliente" autocomplete="off">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th data-breakpoints="sm">Cliente</th>
                        <th>Total</th>
                        <th data-breakpoints="sm">Estado</th>
                        <th data-breakpoints="sm" class="text-center" width="15%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $key => $pedido)
                    <tr>
                        <td>
                            {{ $pedido->pedidos_codigo }}
                        </td>
                        <td>
                            {{ $pedido->razonsocial }}
                        </td>
                        <td>
                            $ {{ number_format($pedido->total,2) }}
                        </td>
                        <td>
                            @if($pedido->estado == 1)
                            <span class="badge badge-inline badge-danger">Realizado</span>
                            @elseif($pedido->estado == 2)
                            <span class="badge badge-inline badge-primary">Confirmado</span>
                            @elseif($pedido->estado == 3)
                            <span class="badge badge-inline badge-primary">Facturado</span>
                            @elseif($pedido->estado == 4)
                            <span class="badge badge-inline badge-primary">En Entrega</span>
                            @elseif($pedido->estado == 5)
                            <span class="badge badge-inline badge-success">Entregado</span>
                            @elseif($pedido->estado == 0)
                            <span class="badge badge-inline badge-danger">No Aplica</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{route('pedidos.show', $pedido->pedidosid)}}" title="Ver">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="{{ route('invoice.download', $pedido->pedidosid) }}" title="Descargar">
                                <i class="las la-download"></i>
                            </a>
                            @if ($pedido->documentosid==0)
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