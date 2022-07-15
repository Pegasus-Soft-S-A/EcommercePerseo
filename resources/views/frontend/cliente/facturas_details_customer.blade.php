<?php
    $secuencial = $factura->establecimiento . ' - ' . $factura->puntoemision . ' - ' . $factura->secuencial;
?>
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Factura: {{ $secuencial}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body gry-bg px-3 pt-3">
    <div class="py-4">
        <div class="row gutters-5 text-center aiz-steps">
            <div class="col active">
                <div class="icon">
                    <i class="las la-file-invoice"></i>
                </div>
                <div class="title fs-12">Pedido Facturado</div>
            </div>
            <div class="col @if($factura->estado == 1  || $factura->estado == 2 ) active else   @endif">
                <div class="icon">
                    <i class="las la-file"></i>
                </div>
                <div class="title fs-12">En la Entrega</div>
            </div>
            <div class="col  @if($factura->estado == 2 ) active else   @endif">
                <div class="icon">
                    <i class="las la-file"></i>
                </div>
                <div class="title fs-12">Entregado</div>
            </div>
        </div>

    </div>
    <div class="card mt-4">
        <div class="card-header">
            <b class="fs-15">Resumen de la Factura</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">Secuencia de Factura:</td>
                            <td>{{ $secuencial}}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Cliente:</td>
                            <td>{{$cliente->razonsocial}}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Email:</td>
                            <td>{{$cliente->email}}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Dirección:</td>
                            @php
                            $direccion=\App\Models\ClientesSucursales::findOrFail($factura->clientes_sucursalesid);
                            $ciudad=\App\Models\Ciudades::findOrFail($direccion->ciudadesid);
                            @endphp
                            <td> {{$direccion->direccion}},
                                {{ $ciudad->ciudad }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">Fecha de la Factura:</td>
                            <td>{{ $factura->emision}}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Estado de la Factura:</td>
                            <td>
                                @if($factura->estado == 0)
                                Facturado
                                @elseif($factura->estado == 1)
                                En la Entrega
                                @elseif($factura->estado == 2)
                                Entregado

                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Total de la Factura:</td>
                            <td>$ {{ number_format(round($factura->total,2),2) }}</td>
                        </tr>

                    </table>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 text-center">
            <div class="card mt-4">
                <div class="card-header">
                    <b class="fs-15">Detalles de la Factura</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-borderless table-responsive text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th width="50%">Producto</th>
                                <th width="20%">Medida</th>
                                <th width="15%">Cantidad</th>
                                <th width="15%">Precio Unitario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $subtotal =0;

                            @endphp
                            @foreach ($detallesFactura as $key => $facturaDetail)
                            <tr>
                                <td>{{$key+1}}</td>
                                @php
                                $producto=App\Models\Producto::select('descripcion','productocodigo')->where('productosid',$facturaDetail->productosid)->first();
                                $medida=App\Models\Medidas::select('descripcion')->where('medidasid',$facturaDetail->medidasid)->first();
                                $subtotal = $subtotal + ($facturaDetail->precio * $facturaDetail->cantidad);
                                @endphp

                                <td>
                                    <a href="{{ route('product', $facturaDetail->productosid) }}"
                                        target="_blank">{{$producto->descripcion}}</a>
                                </td>

                                <td>{{$medida->descripcion}}</td>

                                <td>
                                    {{ number_format(round($facturaDetail->cantidaddigitada,2),2) }}
                                </td>

                                <td>
                                    $ {{ number_format(round($facturaDetail->preciovisible,2),2) }}
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
                                    {{number_format(round(($factura->total_descuento),2),2)}}
                                </span>
                            </td>
                        </tr>
                        <tr>

                        <tr>
                            <td class="w-50 fw-600">Subtotal Neto</td>
                            <td class="text-right">
                                <span class="strong-600">$ {{number_format(round(($factura->subtotalneto),2),2)}}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td class="w-50 fw-600">IVA 12%</td>
                            <td class="text-right">
                                <span class="strong-600">$ {{number_format(round(($factura->total_iva),2),2)}}</span>
                            </td>
                        </tr>

                        <tr>
                            <td class="w-50 fw-600">VALOR TOTAL</td>
                            <td class="text-right">
                                <strong><span>$ {{number_format(round(($factura->total),2),2)}} </span></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>