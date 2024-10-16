@extends('backend.layouts.app')

@section('content')
<?php
    $secuencial = $factura->establecimiento . ' - ' . $factura->puntoemision . ' - ' . $factura->secuencial;
    ?>
<div class="card">
    <div class="card-header row gutters-5">
        <div class="col">
            <h5 class="mb-md-0 h5">Factura</h5>
        </div>
        <div class="col-lg-2 ml-auto">
            <h5 class="mb-md-0 h6">Actualizar Estado</h5>
        </div>
        <div class="col-lg-3 ml-auto">
            <select class="form-control aiz-selectpicker" name="estado" id="estado">
                <option value="0" @isset($factura->estado) @if ($factura->estado == 0)
                    selected @endif @endisset>Facturado
                </option>
                <option value="1" @isset($factura->estado) @if ($factura->estado == 1)
                    selected @endif @endisset>En Entrega
                </option>
                <option value="2" @isset($factura->estado) @if ($factura->estado == 2)
                    selected @endif @endisset>Entregado
                </option>
            </select>
        </div>
    </div>

    <div class="card-body">

        <div class="card mt-4">
            <div class="card-header">
                <b class="fs-15">Resumen de la Factura</b>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="w-50 fw-600">Secuencia:</td>
                                <td>{{ $secuencial }}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Cliente:</td>
                                <td>{{ $cliente->razonsocial }}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Email:</td>
                                <td>{{ $cliente->email_login }}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Direccion:</td>
                                @php
                                $direccion =
                                \App\Models\ClientesSucursales::findOrFail($factura->clientes_sucursalesid);
                                $ciudad = \App\Models\Ciudades::findOrFail($direccion->ciudadesid);
                                @endphp
                                <td> {{ $direccion->direccion }},
                                    {{ $ciudad->ciudad }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="w-50 fw-600">Fecha:</td>
                                <td>{{ $factura->emision }}</td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Estado:</td>
                                <td>

                                    @if ($factura->estado == 0)
                                    Pedido Facturado
                                    @elseif($factura->estado == 1)
                                    Pedido en la Entrega
                                    @elseif($factura->estado == 2)
                                    Pedido Entregado
                                    @endif

                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Total:</td>
                                <td>$ {{ number_format(round($factura->total, 2), 2) }}</td>
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
                                $subtotal = 0;
                                @endphp
                                @foreach ($detalle as $key => $pedidoDetail)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    @php
                                    $producto = App\Models\Producto::select('descripcion', 'productocodigo')
                                    ->where('productosid', $pedidoDetail->productosid)
                                    ->first();
                                    $medida = App\Models\Medidas::select('descripcion')
                                    ->where('medidasid', $pedidoDetail->medidasid)
                                    ->first();
                                    $subtotal = $subtotal + $pedidoDetail->precio * $pedidoDetail->cantidad;
                                    @endphp

                                    <td>
                                        <a href="{{ route('product', $pedidoDetail->productosid) }}" target="_blank">{{
                                            $producto->descripcion }}</a>
                                    </td>

                                    <td>{{ $medida->descripcion }}</td>

                                    <td>
                                        {{ number_format(round($pedidoDetail->cantidaddigitada, 2), 2) }}
                                    </td>

                                    <td>
                                        $ {{ number_format(round($pedidoDetail->preciovisible, 2), 2) }}
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
                    <b class="fs-15">Total de la Factura</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="w-50 fw-600">Subtotal</td>
                                <td class="text-right">
                                    <span class="strong-600">$ {{ number_format(round($subtotal, 2), 2) }} </span>


                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Descuento</td>
                                <td class="text-right">
                                    <span class="strong-600">$
                                        {{ number_format(round($factura->total_descuento, 2), 2) }}</span>
                                </td>
                            </tr>
                            <tr>

                            <tr>
                                <td class="w-50 fw-600">Subtotal Neto</td>
                                <td class="text-right">
                                    <span class="strong-600">$
                                        {{ number_format(round($factura->subtotalneto, 2), 2) }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="w-50 fw-600">IVA</td>
                                <td class="text-right">
                                    <span class="strong-600">$
                                        {{ number_format(round($factura->total_iva, 2), 2) }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td class="w-50 fw-600">VALOR TOTAL</td>
                                <td class="text-right">
                                    <strong><span>$ {{ number_format(round($factura->total, 2), 2) }}</span></strong>
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

@section('script')
<script type="text/javascript">
    $('#estado').on('change', function() {

            var order_id = {{ $factura->facturasid }};
            var status = $('#estado').val();
            $.post('{{ route('factura.actualizarestado') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {

                AIZ.plugins.notify('success', 'Estado Actualizado Correctamente');
                setTimeout(esperar, 2500);


            });
        });

        function esperar(){
            location.reload();
        }
</script>
@endsection