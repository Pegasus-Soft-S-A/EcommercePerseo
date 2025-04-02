@php
    if (auth()->user() != null) {
        $clientesid = Auth::user()->clientesid;
        if (get_setting('maneja_sucursales') == 'on') {
            $cart = \App\Models\Carrito::where('clientes_sucursalesid', session('sucursalid'))->get();
        } else {
            $cart = \App\Models\Carrito::where('clientesid', $clientesid)->get();
        }
    } else {
        $usuario_temporalid = Session()->get('usuario_temporalid');
        if ($usuario_temporalid) {
            $cart = \App\Models\Carrito::where('usuario_temporalid', $usuario_temporalid)->get();
        }
    }
@endphp

<a href="javascript:void(0)" class="d-flex align-items-center text-reset h-100" data-toggle="dropdown" data-display="static">
    <i class="la la-shopping-cart la-2x opacity-80"></i>
    <span class="flex-grow-1 ml-1">
        @if (isset($cart) && count($cart) > 0)
            <span class="badge badge-primary badge-inline badge-pill">
                {{ count($cart) }}
            </span>
        @else
            <span class="badge badge-primary badge-inline badge-pill">0</span>
        @endif
        <span class="nav-box-text d-none d-xl-block opacity-70">Carrito</span>
    </span>
</a>
<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg p-0 stop-propagation">

    @if (isset($cart) && count($cart) > 0)
        <div class="p-3 fs-15 fw-600 border-bottom">
            Items del Carrito
        </div>
        <ul class="h-250px overflow-auto c-scrollbar-light list-group list-group-flush">
            @php
                $total = 0;
            @endphp
            @foreach ($cart as $key => $cartItem)
                @php
                    $product = \App\Models\Producto::where('productosid', $cartItem['productosid'])->first();
                    $imagenProducto = \App\Models\ProductoImagen::select('productos_imagenes.imagen')
                        ->where('productos_imagenes.productosid', '=', $cartItem['productosid'])
                        ->where('productos_imagenes.medidasid', '=', $cartItem['medidasid'])
                        ->where('productos_imagenes.ecommerce_visible', '=', '1')
                        ->first();
                    $total = $total + $cartItem['precio'] * $cartItem['cantidad'];
                    $preciovisible =
                        \App\Models\ParametrosEmpresa::first()->tipopresentacionprecios == 1 ? $cartItem['precioiva'] : $cartItem['precio'];
                @endphp
                @if ($product != null)
                    <li class="list-group-item">
                        <span class="d-flex align-items-center">
                            <a href="{{ route('product', $product->productosid) }}" class="text-reset d-flex align-items-center flex-grow-1">
                                @if ($imagenProducto)
                                    <img src="data:image/jpg;base64,{{ base64_encode($imagenProducto->imagen) }}" data-src=""
                                        class="img-fit lazyload size-60px rounded" alt="">
                                @else
                                    <img src="data:image/jpg;base64,{{ get_setting('imagen_defecto') }}" data-src=""
                                        class="img-fit lazyload size-60px rounded" alt="">
                                @endif
                                <span class="minw-0 pl-2 flex-grow-1">
                                    <span class="fw-600 mb-1 text-truncate-2">
                                        {{ $product->descripcion }}
                                    </span>
                                    <span class="">{{ round($cartItem['cantidad'], 2) }}x</span>
                                    <span class="">${{ number_format(round($preciovisible, 2), 2) }}</span>
                                </span>
                            </a>
                            <span class="">
                                <button onclick="removeFromCart({{ $cartItem['ecommerce_carritosid'] }})"
                                    class="btn btn-sm btn-icon stop-propagation">
                                    <i class="la la-close"></i>
                                </button>
                            </span>
                        </span>
                    </li>
                @endif
            @endforeach
        </ul>
        <div class="px-3 py-2 fs-15 border-top d-flex justify-content-between">
            <span class="opacity-60">Subtotal</span>
            <span class="fw-600">${{ number_format(round($total, 2), 2) }}</span>
        </div>
        <div class="px-3 py-2 text-center border-top">
            <ul class="list-inline mb-0">
                <li class="list-inline-item">
                    <a href="{{ route('cart') }}" class="btn btn-soft-primary btn-sm">
                        Ver el Carrito
                    </a>
                </li>
                @if (Auth::check())
                    @if (get_setting('maneja_sucursales') != 'on')
                        <li class="list-inline-item">
                            <a href="{{ route('checkout.shipping_info') }}" class="btn btn-primary btn-sm">
                                Comprar
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    @else
        <div class="text-center p-3">
            <i class="las la-frown la-3x opacity-60 mb-3"></i>
            <h3 class="h6 fw-700">Tu carrito está vacío</h3>
        </div>
    @endif

</div>
