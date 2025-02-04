@extends('frontend.layouts.app')

@section('content')
<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">Mi Carrito</h3>
                        </div>
                    </div>
                    @if(get_setting('maneja_sucursales') != "on")
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">Información de la Compra</h3>
                        </div>
                    </div>
                    @endif
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">Pago</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">Confirmación</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-4">
    <div class="container text-left">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body">
                        <div class="text-center py-4 mb-4">
                            <i class="la la-check-circle la-3x text-success mb-3"></i>
                            <h1 class="h3 mb-3 fw-600">Gracias por su pedido</h1>
                            <h2 class="h5">Secuencial: <span class="fw-700 text-primary">{{
                                    $factura->establecimiento.'-'.$factura->puntoemision.'-'.$factura->secuencial
                                    }}</span>
                            </h2>
                            <p class="opacity-70 font-italic">
                                Se ha enviado una copia o el resumen de su pedido a
                                {{ Auth::user()->email_login }}</p>
                        </div>
                        <div class="mb-4">
                            <h5 class="fw-600 mb-3 fs-17 pb-2">Resumen del pedido</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <td class="w-50 fw-600">Secuencial:</td>
                                            <td>{{
                                                $factura->establecimiento.'-'.$factura->puntoemision.'-'.$factura->secuencial
                                                }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Nombre:</td>
                                            <td>{{ Auth::user()->razonsocial }}</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Email:</td>
                                            <td>{{ Auth::user()->email_login }}</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Direccion de Envio:</td>
                                            @php
                                            $direccion=\App\Models\ClientesSucursales::findOrFail($factura->clientes_sucursalesid);
                                            $ciudad=\App\Models\Ciudades::findOrFail($direccion->ciudadesid);
                                            @endphp
                                            <td> {{$direccion->direccion}},
                                                {{ $ciudad->ciudad }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <td class="w-50 fw-600">Fecha de Orden:</td>
                                            <td>{{ $factura->emision }}</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Estado de Orden:</td>
                                            <td> Pedido Facturado</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Total Pedido:</td>
                                            <td>{{ number_format(round($factura->total,2),2) }}
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h5 class="fw-600 mb-3 fs-17 pb-2">Detalles del Pedido</h5>
                            <div>
                                <table class="table table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th width="30%">Producto</th>
                                            <th>Medida</th>
                                            <th>Cantidad</th>
                                            <th class="text-right">Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ( $detalles as $key => $orderDetail)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>
                                                {{ $orderDetail->producto }}
                                            </td>
                                            <td>
                                                {{ $orderDetail->medida }}
                                            </td>
                                            <td>
                                                {{ round($orderDetail->cantidaddigitada,2) }}
                                            </td>
                                            <td class="text-right">
                                                {{ number_format(round($orderDetail->preciovisible,2),2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xl-5 col-md-6 ml-auto mr-0">
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <th>Subtotal</th>
                                                <td class="text-right">
                                                    <span class="fw-600">{{
                                                        number_format(round($factura->subtotalconiva,2),2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Descuento</th>
                                                <td class="text-right">
                                                    <span class="fw-600">{{
                                                        number_format(round($factura->total_descuento,2),2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Subtotal Neto</th>
                                                <td class="text-right">
                                                    <span class="fw-600">{{
                                                        number_format(round($factura->subtotalneto,2),2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Total IVA</th>
                                                <td class="text-right">
                                                    <span class="fw-600">{{
                                                        number_format(round($factura->total_iva,2),2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="fw-600">Total</span></th>
                                                <td class="text-right">
                                                    <strong><span>{{ number_format(round($factura->total,2),2)
                                                            }}</span></strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
