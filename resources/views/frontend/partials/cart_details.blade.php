<div class="container-fluid">

    <div class="row">
        <div class="col-xxl-8 col-xl-10 mx-auto">
            <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                @if (isset($productos))
                    <div id="mensajes" class="alert alert-danger d-flex align-items-center">

                        @foreach ($productos as $prod)
                            @foreach ($productos as $prod)
                                * El producto "{{ $prod }}" sobrepasa las existencias. <br>
                            @endforeach
                        @endforeach




                    </div>
                @endif
                <div class="mb-4">
                    <div class="row gutters-5 d-none d-md-flex border-bottom mb-3 pb-3">
                        <div class="col-md-5 fw-600">Producto</div>
                        <div class="col fw-600">Precio</div>
                        <div class="col fw-600 ml-5">Cantidad</div>
                        <div class="col fw-600 ml-4">Total</div>
                            <div   @if (get_setting('productos_existencias') == 'todos' || get_setting('controla_stock') == 1 || (get_setting('controla_stock') == 2 && Auth::check())) class="col fw-600 " @else class="col fw-600 invisible  " @endif>Existencias</div>

                        <div class="col-auto fw-600">Eliminar</div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @php
                            
                            $parametros = \App\Models\ParametrosEmpresa::first();
                            $subtotal = 0;
                            $descuento = 0;
                            $subtotalneto = 0;
                            $subtotalnetoconiva = 0;
                            $totalIVA = 0;
                            $total = 0;
                        @endphp
                        @foreach ($carts as $key => $cartItem)
                            @php
                                $product = \App\Models\Producto::where('productosid', $cartItem['productosid'])->first();
                                $subtotal = $subtotal + $cartItem['precio'] * $cartItem['cantidad'];
                                $descuento = $descuento + $cartItem['precio'] * $cartItem['cantidad'] * ($cartItem['descuento'] / 100);
                                $subtotalneto = $subtotalneto + $cartItem['precio'] * $cartItem['cantidad'] - $cartItem['precio'] * $cartItem['cantidad'] * ($cartItem['descuento'] / 100);
                                
                                if ($cartItem['iva'] > 0) {
                                    $subtotalnetoconiva = $subtotalnetoconiva + $cartItem['precio'] * $cartItem['cantidad'] - $cartItem['precio'] * $cartItem['cantidad'] * ($cartItem['descuento'] / 100);
                                    $totalIVA = round($subtotalnetoconiva * ($cartItem['iva'] / 100), $parametros->fdv_iva);
                                }
                                $total = round($subtotalneto + $totalIVA, $parametros->fdc_totales);
                                $imagenProducto = \App\Models\ProductoImagen::select('productos_imagenes.imagen')
                                    ->where('productos_imagenes.productosid', '=', $cartItem['productosid'])
                                    ->where('productos_imagenes.medidasid', '=', $cartItem['medidasid'])
                                    ->where('productos_imagenes.ecommerce_visible', '=', '1')
                                    ->first();
                                $preciovisible = \App\Models\ParametrosEmpresa::first()->tipopresentacionprecios == 1 ? $cartItem['precioiva'] : $cartItem['precio'];
                                
                                if (get_setting('controla_stock') == 1 || get_setting('controla_stock') == 0) {
                                    $cantidad = App\Models\Producto::select('existenciastotales')
                                        ->where('productosid', $cartItem->productosid)
                                        ->first();
                                    $cantidadProductos = $cantidad->existenciastotales;
                                } elseif (get_setting('controla_stock') == 2) {
                                    $cantidad = App\Models\MovimientosInventariosAlmacenes::where('productosid', $cartItem->productosid)
                                        ->where('almacenesid', $cartItem->almacenesid)
                                        ->first();
                                    $cantidadProductos = $cantidad->existencias;
                                } else {
                                    $cantidadProductos = 0;
                                }
                                
                                if ($cartItem->cantidadfactor != 0) {
                                    $cantidadFinal = $cantidadProductos / $cartItem->cantidadfactor;
                                } else {
                                    $cantidadFinal = $cantidadProductos;
                                }
                            @endphp
                            <li class="list-group-item px-0 px-lg-3">
                                <div class="row gutters-5">
                                    <div class="col-lg-5 d-flex">
                                        <span class="mr-2 ml-0">
                                            @if ($imagenProducto)
                                                <img src="data:image/jpg;base64,{{ base64_encode($imagenProducto->imagen) }}"
                                                    data-src="" class="img-fit size-60px rounded" alt="">
                                            @else
                                                <img src="data:image/jpg;base64,{{ get_setting('imagen_defecto') }}"
                                                    data-src="" class="img-fit size-60px rounded" alt="">
                                            @endif
                                        </span>
                                        <span class="fs-14 opacity-60 mt-3 ml-4">
                                            @if ($cartItem['iva'] > 0)
                                                <span class="text-danger">*</span>
                                            @endif {{ $product->descripcion }}
                                        </span>
                                    </div>

                                    <div class="col-lg col-4 order-1 order-lg-0 my-3 my-lg-0">
                                        <span class="opacity-60 fs-12 d-block d-lg-none">Precio</span>
                                        <span
                                            class="fw-600 fs-16">${{ number_format(round($preciovisible, 2), 2) }}</span>
                                    </div>

                                    <div class="col-lg col-6 order-4 order-lg-0">

                                        <div class="row no-gutters align-items-center aiz-plus-minus mr-2 ml-0">
                                            <button class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                type="button" data-type="minus"
                                                data-field="quantity[{{ $cartItem['ecommerce_carritosid'] }}]">
                                                <i class="las la-minus"></i>
                                            </button>

                                            <input type="number"
                                                name="quantity[{{ $cartItem['ecommerce_carritosid'] }}]"
                                                class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                placeholder="1" value="{{ round($cartItem['cantidad'], 2) }}"
                                                min="1"
                                                @if (get_setting('controla_stock') == 0) max="{{ get_setting('cantidad_maxima') }}" @else max="{{ round($cantidadFinal, 2) }}" @endif
                                                autocomplete="off"
                                                onchange="updateQuantity({{ $cartItem['ecommerce_carritosid'] }}, {{ round($cantidadFinal, 2) }}, this)">
                                            <button class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                type="button" data-type="plus"
                                                data-field="quantity[{{ $cartItem['ecommerce_carritosid'] }}]">
                                                <i class="las la-plus"></i>
                                            </button>


                                        </div>

                                    </div>

                                    <div class="col-lg col-4 order-3 order-lg-0 my-3 my-lg-0 text-center">
                                        <span class="opacity-60 fs-12 d-block d-lg-none">Total</span>
                                        <span
                                            class="fw-600 fs-16 text-primary ml-1">${{ number_format(round($preciovisible * $cartItem['cantidad'], 2), 2) }}</span>
                                    </div>

                                    <div @if (get_setting('productos_existencias') == 'todos' || get_setting('controla_stock') == 1 || (get_setting('controla_stock') == 2 && Auth::check())) class="col-lg col-4 order-3 order-lg-0 my-3 my-lg-0 text-center" @else class="col-lg col-4 order-3 order-lg-0 my-3 my-lg-0 text-center invisible" @endif>
                                            <span class="opacity-60 fs-12 d-block d-lg-none ">Existencias</span>
                                            @if ((get_setting('controla_stock') == 0 && !Auth::check()) || (get_setting('controla_stock') == 2 && !Auth::check()))
                                                <div class="col-sm-10 ">
                                                    @if ($cantidadFinal > 0)
                                                        <div
                                                            class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                                            <span>{{ get_setting('productos_disponibles') }}</span>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                                            <span>{{ get_setting('productos_no_disponibles') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif(get_setting('controla_stock') == 0 && Auth::check())
                                                <div class="col-sm-10 ">
                                                    @if ($cantidadFinal > 0)
                                                        <div
                                                            class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                                            <span>{{ get_setting('productos_disponibles') }}</span>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                                            <span>{{ get_setting('productos_no_disponibles') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="">

                                                    <span class="fw-600 fs-16 text-primary"
                                                        id="cantidad">{{ round($cantidadFinal, 2) }}</span>

                                                </div>
                                            @endif
                                        </div>

                                        <hr>

                                    <div class="col-lg-auto col-6 order-5 order-lg-0 text-right">
                                        <a href="javascript:void(0)"
                                            onclick="removeFromCartView(event, {{ $cartItem['ecommerce_carritosid'] }})"
                                            class="btn btn-icon btn-sm btn-soft-primary btn-circle">
                                            <i class="las la-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">Subtotal</span>
                    <span
                        class="fw-600 fs-17">${{ number_format(round($subtotal, $parametros->fdv_subtotales), $parametros->fdv_subtotales) }}</span>
                </div>
                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">Descuento</span>
                    <span class="fw-600 fs-17">${{ number_format(round($descuento, 2), 2) }}</span>
                </div>
                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">Subtotal Neto</span>
                    <span
                        class="fw-600 fs-17">${{ number_format(round($subtotalneto, $parametros->fdv_subtotales), $parametros->fdv_subtotales) }}</span>
                </div>
                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">IVA</span>
                    <span
                        class="fw-600 fs-17">${{ number_format(round($totalIVA, $parametros->fdv_iva), $parametros->fdv_iva) }}</span>
                </div>
                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">Total</span>
                    <span class="fw-600 fs-17">${{ number_format(round($total, 2), 2) }}</span>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                        <a href="{{ route('home') }}" class="btn btn-link">
                            <i class="las la-arrow-left"></i>
                            Regresar a la tienda
                        </a>
                    </div>
                    <div class="col-md-6 text-center text-md-right">
                        @if (Auth::check())
                            {{-- <a href="{{ route('checkout.shipping_info') }}" class="btn btn-primary fw-600">
                                            Continuar con la Compra
                                        </a> --}}
                            <a href="{{ route('verificarexistencias.shipping_info', Auth::user()->clientesid) }}"
                                class="btn btn-primary fw-600">
                                Continuar con la Compra
                            </a>
                        @else
                            <button class="btn btn-primary fw-600" onclick="showCheckoutModal()">Continuar
                                con la Compra</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    AIZ.extra.plusMinus();
</script>
