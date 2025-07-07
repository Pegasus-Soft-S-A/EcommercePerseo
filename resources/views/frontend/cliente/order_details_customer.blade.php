<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Pedido: {{ $pedido->pedidos_codigo }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>


<div class="modal-body gry-bg px-3 pt-3">
    <div class="py-4">
        <div class="row gutters-5 text-center aiz-steps">
            <div class="col done">
                <div class="icon">
                    <i class="las la-file-invoice"></i>
                </div>
                <div class="title fs-12">Pedido Realizado</div>
            </div>
            <div
                class="col @if($pedido->estado == 2  || $pedido->estado == 3 || $pedido->estado == 4 || $pedido->estado == 5 ) active else   @endif">
                <div class="icon">
                    <i class="las la-file"></i>
                </div>
                <div class="title fs-12">Confirmado</div>
            </div>
            <div
                class="col @if($pedido->estado == 3 || $pedido->estado == 4 || $pedido->estado == 5 ) active @else  @endif">
                <div class="icon">
                    <i class="las la-newspaper"></i>
                </div>
                <div class="title fs-12">Facturado</div>
            </div>
            <div class="col @if($pedido->estado == 4 || $pedido->estado == 5) active @else @endif">
                <div class="icon">
                    <i class="las la-truck"></i>
                </div>
                <div class="title fs-12">En la Entrega</div>
            </div>
            <div class="col @if($pedido->estado == 5) active @else @endif">
                <div class="icon">
                    <i class="las la-clipboard-check"></i>
                </div>
                <div class="title fs-12">Entregado</div>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header">
            <b class="fs-15">Resumen del Pedido</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">Codigo del Pedido:</td>
                            <td>{{$pedido->pedidos_codigo}}</td>
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
                            <td class="w-50 fw-600">Fecha del Pedido:</td>
                            <td>{{ $pedido->emision}}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Estado del Pedido:</td>
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
                            <td class="w-50 fw-600">Total del Pedido:</td>
                            <td>$ {{ number_format(round($pedido->total,2),2) }}</td>
                        </tr>
                        @if(get_setting('maneja_sucursales') == "on")
                        <tr>
                            <td class="w-50 fw-600">Centro Costo:</td>
                            <td>{{$centrocosto->descripcion}}</td>
                        </tr>
                            <tr>
                                <td class="w-50 fw-600">Sucursal:</td>
                                @php
                                    $sucursal=\App\Models\ClientesSucursales::findOrFail($pedido->clientes_sucursalesid);
                                @endphp
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
                                <th width="20%">Medida</th>
                                <th width="15%">Cantidad</th>
                                @if($modificado==true)
                                <th width="15%">Cantidad Anterior</th>
                                @endif
                                <th width="15%">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $subtotal =0;
                            @endphp
                            @foreach ($detalles as $key => $pedidoDetail)
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
                                <span class="strong-600">$ {{number_format(round(($pedido->subtotalneto),2),2)}}</span>
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
