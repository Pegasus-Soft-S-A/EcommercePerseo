<div class="container-fluid">
    <div class="row">
        <div class="col-xxl-8 col-xl-10 mx-auto">
            <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                @if (isset($productos))
                <div id="mensajes" class="alert alert-danger d-flex align-items-center">
                    @foreach ($productos as $prod)
                    * El producto "{{ $prod }}" sobrepasa las existencias. <br>
                    @endforeach
                </div>
                @endif
                <div class="mb-4">
                    <div class="row gutters-7 d-none d-lg-flex border-bottom mb-1 pb-3">
                        <div class="col-md-4 fw-600 text-center">Producto</div>
                        <div class="col-md-1 fw-600 text-center">Precio</div>
                        <div class="col-md-2 fw-600 text-center">Cantidad</div>
                        <div class="col-md-1 fw-600 text-center">Total</div>
                        <div class="col-md-1 fw-600 text-center">Observacion</div>
                        @if (get_setting('productos_existencias')=='todos' || get_setting('controla_stock')==1 ||
                        (get_setting('controla_stock')==2 && Auth::check()))
                        <div class="col-md-2 fw-600 text-center">Existencias</div>
                        @endif
                        <div class="col-md-1 fw-600 text-center"></div>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($carts as $key => $cartItem)
                        <li class="list-group-item px-0 px-lg-3">
                            <div class="row gutters-7 align-items-center">
                                <div class="col-md-4 d-flex">
                                    <span class="mr-2 ml-0">
                                        @if ($cartItem['imagen_producto'])
                                        <img src="data:image/jpg;base64,{{ base64_encode($cartItem['imagen_producto']) }}"
                                            class="img-fit lazyload size-60px rounded" alt="">
                                        @else
                                        <img src="data:image/jpg;base64,{{ get_setting('imagen_defecto') }}"
                                            class="img-fit lazyload size-60px rounded" alt="">
                                        @endif
                                    </span>
                                    <span class="fs-14 opacity-60 mt-3 ml-4">
                                        @if ($cartItem['iva'] > 0)
                                        <span class="text-danger">*</span>
                                        @endif
                                        {{ $cartItem['producto_descripcion'] }}
                                    </span>
                                </div>

                                <div class="col-md-1 text-center">
                                    <span class="opacity-60 fs-12 d-block d-lg-none">Precio</span>
                                    <span class="fw-600 fs-16">${{ number_format(round($cartItem['precio_visible'],
                                        2), 2) }}</span>
                                </div>

                                <div class="col-md-2 text-center">
                                    <div class="row no-gutters align-items-center aiz-plus-minus mr-2 ml-0">
                                        <button class="btn col-auto btn-icon btn-sm btn-circle btn-light" type="button"
                                            data-type="minus"
                                            data-field="quantity[{{ $cartItem['ecommerce_carritosid'] }}]">
                                            <i class="las la-minus"></i>
                                        </button>
                                        <input type="number" name="quantity[{{ $cartItem['ecommerce_carritosid'] }}]"
                                            class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                            placeholder="1" value="{{ round($cartItem['cantidad'], 2) }}" min="1"
                                            max="{{ round($cartItem['cantidad_final'], 2) }}" autocomplete="off"
                                            onchange="updateQuantity({{ $cartItem['ecommerce_carritosid'] }}, {{ round($cartItem['cantidad_final'], 2) }}, this)">
                                        <button class="btn col-auto btn-icon btn-sm btn-circle btn-light" type="button"
                                            data-type="plus"
                                            data-field="quantity[{{ $cartItem['ecommerce_carritosid'] }}]">
                                            <i class="las la-plus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-1 text-center">
                                    <span class="opacity-60 fs-12 d-block d-lg-none">Total</span>
                                    <span class="fw-600 fs-16 text-primary">${{
                                        number_format(round($cartItem['precio_visible'] * $cartItem['cantidad'], 2),
                                        2) }}</span>
                                </div>

                                <div class="col-md-1 text-center">
                                    <a href="javascript:void(0)"
                                        onclick="removeFromCartView(event, {{ $cartItem['ecommerce_carritosid'] }})"
                                        class="btn btn-icon btn-sm btn-soft-success btn-circle">
                                        <i class="las la-pen"></i>
                                    </a>
                                </div>

                                <div @if (get_setting('productos_existencias')=='todos' ||
                                    get_setting('controla_stock')==1 || (get_setting('controla_stock')==2 &&
                                    Auth::check())) class="col-md-2 text-center" @else
                                    class="col-md-2 text-center invisible" @endif>
                                    <span class="opacity-60 fs-12 d-block d-lg-none ">Existencias</span>
                                    @if ((get_setting('controla_stock') == 0 && !Auth::check()) ||
                                    (get_setting('controla_stock') == 2 && !Auth::check()))

                                    @if ($cartItem['cantidad_final'] > 0)
                                    <div class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                        <span>{{ get_setting('productos_disponibles') }}</span>
                                    </div>
                                    @else
                                    <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                        <span>{{ get_setting('productos_no_disponibles') }}</span>
                                    </div>
                                    @endif

                                    @elseif(get_setting('controla_stock') == 0 && Auth::check())

                                    @if ($cartItem['cantidad_final'] > 0)
                                    <div class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                        <span>{{ get_setting('productos_disponibles') }}</span>
                                    </div>
                                    @else
                                    <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                        <span>{{ get_setting('productos_no_disponibles') }}</span>
                                    </div>
                                    @endif

                                    @else
                                    <div class="">
                                        <span class="fw-600 fs-16 text-primary" id="cantidad">{{
                                            round($cartItem['cantidad_final'], 2) }}</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="col-md-1 text-center">
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
                    <span class="fw-600 fs-17">${{ $totales['subtotal'] }}</span>
                </div>
                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">Descuento</span>
                    <span class="fw-600 fs-17">${{ $totales['descuento'] }}</span>
                </div>
                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">Subtotal Neto</span>
                    <span class="fw-600 fs-17">${{ $totales['subtotalNeto'] }}</span>
                </div>
                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">IVA</span>
                    <span class="fw-600 fs-17">${{ $totales['totalIVA'] }}</span>
                </div>
                <div class="px-3 py-2  border-top d-flex justify-content-between">
                    <span class="opacity-60 fs-15">Total</span>
                    <span class="fw-600 fs-17">${{ $totales['total'] }}</span>
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