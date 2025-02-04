@extends('backend.layouts.app')

@section('content')

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col">
            <h5 class="mb-md-0 h5">Pedido</h5>
        </div>

    </div>

    <div class="card-body">
        <a href="{{ route('pedidos.index') }}" id="btnVolver"
            class="btn btn-sm btn-secondary mr-2 text-white">Volver</a>
        <div class="card mt-4">
            <div class="card-header">
                <b class="fs-15">Resumen del Pedido</b>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="w-50 fw-600">Codigo:</td>
                                <td>{{$pedido->pedidos_codigo}}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Cliente:</td>
                                <td>{{$cliente->razonsocial}}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Email:</td>
                                <td>{{$cliente->email_login}}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Direccion:</td>
                                @php
                                $direccion=\App\Models\ClientesSucursales::where('clientes_sucursalesid',$pedido->clientes_sucursalesid)->first();
                                @endphp
                                @if ($direccion!=null)
                                @php
                                $ciudad=\App\Models\Ciudades::findOrFail($direccion->ciudadesid);
                                @endphp
                                <td> {{$direccion->direccion}},
                                    {{ $ciudad->ciudad }}</td>
                                @endif
                            </tr>
                            @if (get_setting('maneja_sucursales') == "on")
                            <tr>
                                <td class="w-50 fw-600">Destinatario:</td>
                                <td>{{$destinatario}}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="w-50 fw-600">Fecha:</td>
                                <td>{{ $pedido->emision}}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Estado:</td>
                                <td>
                                    @if($pedido->estado == 1)
                                    Pedido realizado
                                    @elseif($pedido->estado == 2)
                                    Pedido Confirmado
                                    @elseif($pedido->estado == 3)
                                    Pedido Facturado
                                    @elseif($pedido->estado == 4)
                                    En la Entrega
                                    @elseif($pedido->estado == 5)
                                    Entregado
                                    @elseif($pedido->estado == 0)
                                    No aplica
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Telefono:</td>
                                <td>{{$cliente->telefono1}}</td>
                            </tr>
                            @if (get_setting('maneja_sucursales') == "on")
                            <tr>
                                <td class="w-50 fw-600">Centro Costo:</td>
                                <td>{{$centrocosto->centro_costocodigo}}-{{$centrocosto->descripcion}}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Sucursal:</td>
                                <td>{{$sucursal->descripcion}}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="card mt-4">
                    <div class="card-header">
                        <b class="fs-15">Detalles del Pedido</b>
                    </div>
                    <div class="card-body pb-0">
                        <table class="table table-borderless table-responsive text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th width="50%">Producto</th>
                                    <th width="10%">Medida</th>
                                    <th width="15%">Cantidad</th>
                                    @if($modificado==true)
                                    <th width="15%">Cantidad Anterior</th>
                                    @endif
                                    <th width="15%">Precio Unitario</th>
                                    <th width="10%">Observacion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $subtotal =0;
                                @endphp
                                @foreach ($detalle as $key => $pedidoDetail)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    @php
                                    $producto=App\Models\Producto::select('descripcion','productocodigo')->where('productosid',$pedidoDetail->productosid)->first();
                                    $medida=App\Models\Medidas::select('descripcion')->where('medidasid',$pedidoDetail->medidasid)->first();
                                    $subtotal = $subtotal + ($pedidoDetail->precio *
                                    $pedidoDetail->cantidad);
                                    @endphp

                                    <td>
                                        <a href="{{ route('product', $pedidoDetail->productosid) }}"
                                            target="_blank">{{$producto->descripcion}}</a>
                                    </td>

                                    <td>{{$medida->descripcion}}</td>

                                    <td>
                                        {{ number_format(round($pedidoDetail->cantidaddigitada,2),2) }}
                                    </td>
                                    @if($modificado==true)
                                    <td>
                                        @if($pedidoDetail->cantidadentregada<>0)
                                            {{ number_format(round($pedidoDetail->cantidadentregada,2),2) }}
                                            @endif
                                    </td>
                                    @endif
                                    <td>
                                        $ {{ number_format(round($pedidoDetail->preciovisible,2),2) }}
                                    </td>
                                    <td>
                                        @if($pedidoDetail->informacion)
                                        <a href="javascript:void(0)"
                                            onclick="showObservationModal('{{ $pedidoDetail->informacion }}')"
                                            class="btn btn-icon btn-sm btn-soft-success btn-circle">
                                            <i class="las la-eye"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 float-right">

            <div class="card mt-4 ">
                <div class="card-header">
                    <b class="fs-15">Total del Pedido</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="w-50 fw-600">Subtotal</td>
                                <td class="text-right">
                                    <span class="strong-600">$ {{number_format(round($subtotal,2),2)}} </span>


                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Descuento</td>
                                <td class="text-right">
                                    <span class="strong-600">$
                                        {{number_format(round(($pedido->total_descuento),2),2)}}</span>
                                </td>
                            </tr>
                            <tr>

                            <tr>
                                <td class="w-50 fw-600">Subtotal Neto</td>
                                <td class="text-right">
                                    <span class="strong-600">$
                                        {{number_format(round(($pedido->subtotalneto),2),2)}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="w-50 fw-600">IVA</td>
                                <td class="text-right">
                                    <span class="strong-600">$ {{number_format(round(($pedido->total_iva),2),2)}}</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="w-50 fw-600">VALOR TOTAL</td>
                                <td class="text-right">
                                    <strong><span>$ {{ number_format(round(($pedido->total),2),2) }}</span></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('modal')
<div class="modal fade" id="modalObservacion">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Observación</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <div class="form-group">
                        <input type="hidden" value="" name="ecommerce_carritosid" id="ecommerce_carritosid">
                        <textarea class="form-control h-auto form-control-lg" placeholder="Observación"
                            name="observacion" id="observacion" autocomplete="off" required rows="4"
                            disabled></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    function showObservationModal(observacion) {
    // Set the value of the observation textarea in the modal
    document.getElementById('observacion').value = observacion;

    // Show the modal
    $('#modalObservacion').modal('show');
}
</script>
@endsection