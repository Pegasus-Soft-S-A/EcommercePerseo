@extends('frontend.layouts.app')


@section('meta')
<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="{{ $detallesProducto->descripcion }}">
<meta itemprop="description" content="{{ $detallesProducto->fichatecnica }}">
<meta itemprop="image" content="">

<!-- Twitter Card data -->
<meta name="twitter:card" content="product">
<meta name="twitter:site" content="@publisher_handle">
<meta name="twitter:title" content="{{ $detallesProducto->descripcion }}">
<meta name="twitter:description" content="{{ $detallesProducto->fichatecnica }}">
<meta name="twitter:creator" content="@author_handle">
<meta name="twitter:image" content="">
<meta name="twitter:data1" content="{{ number_format(round($precioProducto->precio, 2), 2) }}">
<meta name="twitter:label1" content="Precio">

<!-- Open Graph data -->
<meta property="og:title" content="{{ $detallesProducto->descripcion }}" />
<meta property="og:type" content="og:product" />
<meta property="og:url" content="{{ route('product', $detallesProducto->productosid) }}" />
<meta property="og:image" content="" />
<meta property="og:description" content="{{ $detallesProducto->fichatecnica }}" />
<meta property="og:site_name" content="{{ get_setting('nombre_sitio') }}" />
<meta property="og:price:amount" content="{{ number_format(round($precioProducto->precio, 2), 2) }}" />
<meta property="product:price:currency" content="USD" />

<meta property="fb:app_id" content="{{ get_setting('FACEBOOK_PIXEL_ID') }}">
@endsection

@section('content')
<section class="mb-4 pt-3">
    <div class="container">
        <div class="bg-white shadow-sm rounded p-3">
            <div class="row">
                <div class="col-xl-5 col-lg-6 mb-4">
                    <div class="sticky-top z-3 row gutters-10">
                        <div class="col order-1 order-md-2">
                            <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb'
                                data-fade='true' data-auto-height='true'>
                                @if (count($imagenProducto) > 0)
                                @foreach ($imagenProducto as $imagenProduct)
                                <div class="carousel-box img-zoom rounded">
                                    <img class="img-fluid lazyload"
                                        src="data:image/jpg;base64,{{ base64_encode($imagenProduct->imagen) }}">
                                </div>
                                @endforeach
                                @else
                                <div class="carousel-box img-zoom rounded">
                                    <img class="img-fluid lazyload"
                                        src="data:image/jpg;base64,{{ get_setting('imagen_defecto') }}">
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-md-auto w-md-80px order-2 order-md-1 mt-3 mt-md-0">
                            <div class="aiz-carousel product-gallery-thumb" data-items='5'
                                data-nav-for='.product-gallery' data-vertical='true' data-vertical-sm='false'
                                data-focus-select='true' data-arrows='true'>
                                @if (count($imagenProducto) > 0)
                                @foreach ($imagenProducto as $imagenProduct)
                                <div class="carousel-box c-pointer border p-1 rounded"
                                    data-medidasid="{{ $imagenProduct->medidasid }}">
                                    <img class="lazyload mw-100 size-50px mx-auto"
                                        src="data:image/jpg;base64,{{ base64_encode($imagenProduct->imagen) }}">
                                </div>
                                @endforeach
                                @else
                                <div class="carousel-box c-pointer border p-1 rounded" data-medidasid="">
                                    <img class="lazyload mw-100 size-50px mx-auto"
                                        src="data:image/jpg;base64,{{ get_setting('imagen_defecto') }}">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7 col-lg-6">
                    <div class="text-left">
                        <h1 class="mb-2 fs-20 fw-600">
                            {{ $detallesProducto->descripcion }}
                        </h1>

                        <div class="row align-items-center">
                            <div class="col-6">
                                @php
                                $total = 0;
                                $total += $numerocomentarios;
                                @endphp
                                <span class="rating">
                                    {{ renderStarRating(json_decode($detallesProducto->parametros_json)->rating) }}
                                </span>
                                <span class="ml-1 opacity-50">{{ $total }} Reseñas</span>
                            </div>
                        </div>

                        <hr>


                        <div class="row no-gutters mt-3">
                            <div class="col-sm-2">
                                <div class="opacity-50 my-2">Código:</div>
                            </div>
                            <div class="col-sm-10">
                                <div class="fs-20 opacity-60">
                                    {{ $detallesProducto->productocodigo }}
                                </div>
                            </div>
                        </div>
                        <hr>
                        @if (get_setting('controla_stock') == 2 && Auth::check())
                        <div class="row no-gutters mt-3">
                            <div class="col-sm-2">
                                <div class="opacity-50 my-2">Sucursal Actual:</div>
                            </div>
                            @php
                            $nombreAlmacen = App\Models\Almacenes::select('descripcion')
                            ->where('almacenesid', session('almacenesid'))
                            ->first();
                            @endphp
                            <div class="col-sm-10">
                                <div class="fs-20 opacity-60">
                                    <span id="nombreAlmacen">
                                        {{ $nombreAlmacen->descripcion }}

                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        @endif
                        @if (get_setting('productos_existencias') == 'todos' ||
                        get_setting('controla_stock') == 1 ||
                        (get_setting('controla_stock') == 2 && Auth::check()))
                        <div class="row no-gutters mt-3">

                            <div class="col-sm-2">
                                <div class="opacity-50 my-2">Existencias:</div>
                            </div>
                            <div class="d-flex no-gutters">
                                <div class="">
                                    @if ((get_setting('controla_stock') == 0 && !Auth::check()) ||
                                    (get_setting('controla_stock') == 2 && !Auth::check()))
                                    @if ($detallesProducto->existenciastotales > 0)
                                    <div class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                        <span>{{ get_setting('productos_disponibles') }}</span>
                                    </div>
                                    @else
                                    <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                        <span>{{ get_setting('productos_no_disponibles') }}</span>
                                    </div>
                                    @endif
                                    @elseif(get_setting('controla_stock') == 0 && Auth::check())
                                    @if ($detallesProducto->existenciastotales > 0)
                                    <div class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                        <span>{{ get_setting('productos_disponibles') }}</span>
                                    </div>
                                    @else
                                    <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                        <span>{{ get_setting('productos_no_disponibles') }}</span>
                                    </div>
                                    @endif
                                    @else
                                    <strong class="h2 fw-600 text-primary">
                                        <span id="cantidad"></span>

                                    </strong>
                                    @endif
                                </div>
                                @if (get_setting('controla_stock') == 2 && Auth::check())
                                <div class="mx-4">
                                    <button type="button" id="cambiarAlmacen" class="my-2 btn btn-soft-primary btn-xs">
                                        Ver Sucursales
                                        <i class="las la-eye"></i>

                                    </button>
                                </div>
                                @endif
                            </div>



                        </div>

                        <hr>
                        @endif





                        @if (Auth::check())
                        @if ($precioProducto->precio <= $precioProducto2->precio)
                            <div class="row no-gutters mt-3">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">Precio:</div>
                                </div>
                                <div class="col-sm-10 ">
                                    <div class="">
                                        <strong class="h2 fw-600 text-primary" id="precio">

                                        </strong>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @else
                            <div class="row no-gutters mt-3">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">Precio:</div>
                                </div>
                                <div class="col-sm-10 ">
                                    <div class="fs-20 opacity-60">
                                        <del id="precionormal">

                                        </del>
                                    </div>
                                </div>
                            </div>

                            <div class="row no-gutters my-3">
                                <div class="col-sm-2">
                                    <div class="opacity-50">Precio cliente:</div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="">
                                        <strong class="h2 fw-600 text-primary" id="precio">

                                        </strong>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @else
                            @if (get_setting('tipo_tienda') == 'publico')
                            <div class="row no-gutters mt-3">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">Precio:</div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="">
                                        <strong class="h2 fw-600 text-primary" id="precio">

                                        </strong>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endif
                            @endif

                            <form id="option-choice-form">
                                @csrf
                                {{-- Valores para agregar al carrito --}}
                                <input type="hidden" id="variableinicio" name="variableinicio"
                                    value="{{ session('almacenesid') }}">
                                <input type="hidden" id="existencias" name="existencias" value="">
                                <input type="hidden" id="id" name="id" value="{{ $detallesProducto->productosid }}">
                                <input type="hidden" name="preciocompleto" value="{{ $precioProducto->precio }}"
                                    id="preciocompleto">
                                <input type="hidden" name="precioIVA" value="{{ $precioProducto->precioiva }}"
                                    id="precioiva">
                                <input type="hidden" id="factor" name="factor" value="{{ $precioProducto->factor }}">
                                {{-- <input type="hidden" name="IVA" value="{{$precioProducto->iva}}"> --}}

                                <div class="row no-gutters">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">
                                            Medida:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="aiz-radio-inline">
                                            @foreach ($medidas as $key => $medida)
                                            <label class="aiz-megabox pl-0 mr-2">
                                                <input type="radio" name="medidasid" value="{{ $medida->medidasid }}"
                                                    @if ($key==0) checked @endif>
                                                <span
                                                    class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2">
                                                    {{ $medida->descripcion }}
                                                </span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                @if (get_setting('tipo_tienda') == 'publico')
                                <!-- Quantity + Add to cart -->
                                <div class="row no-gutters">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">Cantidad:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="product-quantity d-flex align-items-center">
                                            <div class="row no-gutters align-items-center aiz-plus-minus mr-3"
                                                style="width: 130px;">
                                                <button class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                    type="button" data-type="minus" data-field="quantity" disabled="">
                                                    <i class="las la-minus"></i>
                                                </button>
                                                <input type="number" name="quantity" id="botonMas" placeholder="1"
                                                    value="1" min="1"
                                                    class="col border-0 text-center flex-grow-1 fs-16      input-number"
                                                    @if (get_setting('controla_stock')==0)
                                                    max="{{ get_setting('cantidad_maxima') }}" @else max="" @endif
                                                    autocomplete="off">
                                                <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light"
                                                    type="button" data-type="plus" data-field="quantity" id="plus">
                                                    <i class="las la-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                @else
                                @auth
                                <div class="row no-gutters">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">Cantidad:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="product-quantity d-flex align-items-center">
                                            <div class="row no-gutters align-items-center aiz-plus-minus mr-3"
                                                style="width: 130px;">
                                                <button class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                    type="button" data-type="minus" data-field="quantity" disabled="">
                                                    <i class="las la-minus"></i>
                                                </button>
                                                <input type="number" name="quantity" id="botonMas" placeholder="1"
                                                    value="1" min="1"
                                                    class="col border-0 text-center flex-grow-1 fs-16          input-number"
                                                    @if (get_setting('controla_stock')==0)
                                                    max="{{ get_setting('cantidad_maxima') }}" @else max="" @endif
                                                    autocomplete="off">
                                                <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light"
                                                    type="button" data-type="plus" data-field="quantity">
                                                    <i class="las la-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                @endauth
                                @endif

                                <div class="row no-gutters pb-3 d-none " id="chosen_price_div">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">Precio Total:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="product-price">
                                            <strong id="chosen_price" class="h4 fw-600 text-primary">

                                            </strong>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            @if (get_setting('tipo_tienda') == 'publico')
                            <div class="mt-3">

                                @if (!Auth::check() && get_setting('controla_stock') == 2)
                                <a href="{{ route('user.login') }}">
                                    <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600">
                                        <i class="las la-shopping-bag"></i>
                                        <span class="d-none d-md-inline-block"> Añadir al carrito</span>
                                    </button>
                                    <button type="button" class="btn btn-primary buy-now fw-600">
                                        <i class="la la-shopping-cart"></i> Comprar Ahora
                                    </button>
                                </a>
                                @else
                                <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600"
                                    onclick="addToCart()">
                                    <i class="las la-shopping-bag"></i>
                                    <span class="d-none d-md-inline-block"> Añadir al carrito</span>
                                </button>
                                <button type="button" class="btn btn-primary buy-now fw-600" onclick="buyNow()">
                                    <i class="la la-shopping-cart"></i> Comprar Ahora
                                </button>
                                @endif
                            </div>
                            @else
                            @auth

                            @if (!Auth::check() && get_setting('controla_stock') == 2)
                            <a href="{{ route('user.login') }}">
                                <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600">
                                    <i class="las la-shopping-bag"></i>
                                    <span class="d-none d-md-inline-block"> Añadir al carrito</span>
                                </button>
                                <button type="button" class="btn btn-primary buy-now fw-600">
                                    <i class="la la-shopping-cart"></i> Comprar Ahora
                                </button>
                            </a>
                            @else
                            <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600"
                                onclick="addToCart()">
                                <i class="las la-shopping-bag"></i>
                                <span class="d-none d-md-inline-block"> Añadir al carrito</span>
                            </button>
                            <button type="button" class="btn btn-primary buy-now fw-600" onclick="buyNow()">
                                <i class="la la-shopping-cart"></i> Comprar Ahora
                            </button>
                            @endif
                            @endauth
                            @endif
                            <div class="d-table width-100 mt-3">
                                <div class="d-table-cell">
                                    <!-- Add to wishlist button -->
                                    <button type="button" class="btn pl-0 btn-link fw-600"
                                        onclick="addToWishList({{ $detallesProducto->productosid }})">
                                        Añadir a lista de deseos
                                    </button>
                                </div>
                            </div>
                            <div class="row no-gutters mt-4">
                                <div class="col-sm-2">
                                    <div class="opacity-50 my-2">Compartir:</div>
                                </div>
                                <div class="col-sm-10">
                                    <div class="aiz-share"></div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="mb-4">
    <div class="container">
        <div class="row gutters-10">
            <div class="col-xl-3 order-1 order-xl-0">
                <div class="bg-white shadow-sm mb-3">
                    <div class="position-relative p-3 text-left">

                        <div class="text-center border rounded p-2 mt-3">
                            <div class="rating">
                                {{ renderStarRating(json_decode($detallesProducto->parametros_json)->rating) }}
                            </div>
                            <div class="opacity-60 fs-12">{{ $total }} Opiniones de Usuarios</div>
                        </div>
                    </div>

                </div>
                <div class="bg-white rounded shadow-sm mb-3">
                    <div class="p-3 border-bottom fs-16 fw-600">
                        Top Productos más vendidos
                    </div>
                    <div class="p-3">
                        <ul class="list-group list-group-flush">
                            @foreach ($top as $item)
                            <li class="py-3 px-0 list-group-item border-light">
                                <div class="row gutters-10 align-items-center">
                                    <div class="col-5">
                                        <a href="{{ route('product', $item->productosid) }}" class="d-block text-reset">
                                            <img class="img-fit lazyload h-xxl-110px h-xl-80px h-120px"
                                                src="data:image/jpg;base64,@if ($item->imagen) {{ base64_encode($item->imagen) }} @else {{ get_setting('imagen_defecto') }} @endif"
                                                alt="{{ $item->descripcion }}">
                                        </a>
                                    </div>
                                    <div class="col-7 text-left">
                                        <h4 class="fs-13 text-truncate-2">
                                            <a href="{{ route('product', $item->productosid) }}"
                                                class="d-block text-reset">{{ $item->descripcion }}</a>
                                        </h4>
                                        <div class="rating rating-sm mt-1">
                                            {{ renderStarRating(json_decode($item->parametros_json)->rating) }}
                                        </div>
                                        <div class="mt-2">
                                            @if (Auth::check())
                                            @if ($item->precio < $item->precio2)
                                                <del class="fs-17 fw-600 opacity-50 mr-1">${{
                                                    number_format(round($item->precio2, 2), 2) }}</del>
                                                @endif
                                                <span class="fs-17 fw-600 text-primary">
                                                    ${{ number_format(round($item->precio, 2), 2) }}</span>
                                                @else
                                                @if (get_setting('tipo_tienda') == 'publico')
                                                <span class="fs-17 fw-600 text-primary">
                                                    ${{ number_format(round($item->precio, 2), 2) }}</span>
                                                @endif
                                                @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 order-0 order-xl-1">
                <div class="bg-white mb-3 shadow-sm rounded">
                    <div class="nav border-bottom aiz-nav-tabs">
                        @if ($detallesProducto->fichatecnica)
                        <a href="#tab_default_1" data-toggle="tab" class="p-3 fs-16 fw-600 text-reset active show">Ficha
                            Técnica</a>
                        @endif
                        @if ($detallesProducto->observaciones)
                        <a href="#tab_default_2" data-toggle="tab"
                            class="p-3 fs-16 fw-600 text-reset @if (!$detallesProducto->fichatecnica) active show @endif">Observaciones</a>
                        @endif
                        <a href="#tab_default_3" data-toggle="tab"
                            class="p-3 fs-16 fw-600 text-reset @if (!$detallesProducto->fichatecnica && !$detallesProducto->observaciones) active show @endif">Reseñas</a>
                    </div>

                    <div class="tab-content pt-0">
                        @if ($detallesProducto->fichatecnica)
                        <div class="tab-pane fade active show" id="tab_default_1">
                            <div class="p-4">
                                <div class="mw-100 overflow-hidden text-left aiz-editor-data">
                                    @php echo nl2br($detallesProducto->fichatecnica) @endphp
                                </div>
                            </div>
                        </div>
                        @endif
                        @if ($detallesProducto->observaciones)
                        <div class="tab-pane fade @if (!$detallesProducto->fichatecnica) active show @endif"
                            id="tab_default_2">
                            <div class="p-4">
                                <div class="mw-100 overflow-hidden text-left aiz-editor-data">
                                    @php echo nl2br($detallesProducto->observaciones) @endphp
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="tab-pane fade @if (!$detallesProducto->fichatecnica && !$detallesProducto->observaciones) active show @endif"
                            id="tab_default_3">
                            <div class="p-4">
                                <ul class="list-group list-group-flush">
                                    @foreach ($comentarios as $key => $review)
                                    <li class="media list-group-item d-flex">

                                        <div class="media-body text-left">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="fs-15 fw-600 mb-0">{{ $review->razonsocial }}</h3>
                                                <span class="rating rating-sm">
                                                    @for ($i = 0; $i < $review->valoracion; $i++)
                                                        <i class="las la-star active"></i>
                                                        @endfor
                                                        @for ($i = 0; $i < 5 - $review->valoracion; $i++)
                                                            <i class="las la-star"></i>
                                                            @endfor
                                                </span>
                                            </div>
                                            <div class="opacity-60 mb-2">
                                                {{ date('d-m-Y', strtotime($review->fechacreacion)) }}</div>
                                            <p class="comment-text">
                                                {{ $review->comentario }}
                                            </p>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>

                                @if ($total <= 0) <div class="text-center fs-18 opacity-70">
                                    Todavia no hay reseñas para este producto.
                            </div>
                            @endif

                            @if (Auth::check())
                            @php
                            $commentable = false;
                            $facturas = \App\Models\Facturas::where('clientesid', Auth::user()->clientesid)->get();
                            @endphp
                            @foreach ($facturas as $key => $factura)
                            @foreach (\App\Models\FacturasDetalles::where('facturasid', $factura->facturasid)->get() as
                            $key => $detalle)
                            @if (\App\Models\Comentarios::where('clientesid',
                            Auth::user()->clientesid)->where('productosid', $detallesProducto->productosid)->first() ==
                            null && $detallesProducto->productosid == $detalle->productosid)
                            @php
                            $commentable = true;
                            @endphp
                            @endif
                            @endforeach
                            @endforeach
                            @if ($commentable)
                            <div class="pt-4">
                                <div class="border-bottom mb-4">
                                    <h3 class="fs-17 fw-600">
                                        Escribe un comentario
                                    </h3>
                                </div>
                                <form class="form-default" role="form" action="{{ route('reviews.store') }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="productosid"
                                        value="{{ $detallesProducto->productosid }}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="text-uppercase c-gray-light">Su
                                                    Nombre</label>
                                                <input type="text" name="name" value="{{ Auth::user()->razonsocial }}"
                                                    class="form-control" disabled required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="text-uppercase c-gray-light">Email</label>
                                                <input type="text" name="email" value="{{ Auth::user()->email }}"
                                                    class="form-control" required disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="opacity-60">Rating</label>
                                        <div class="rating rating-input">
                                            <label>
                                                <input type="radio" name="rating" value="1" required>
                                                <i class="las la-star"></i>
                                            </label>
                                            <label>
                                                <input type="radio" name="rating" value="2">
                                                <i class="las la-star"></i>
                                            </label>
                                            <label>
                                                <input type="radio" name="rating" value="3">
                                                <i class="las la-star"></i>
                                            </label>
                                            <label>
                                                <input type="radio" name="rating" value="4">
                                                <i class="las la-star"></i>
                                            </label>
                                            <label>
                                                <input type="radio" name="rating" value="5">
                                                <i class="las la-star"></i>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="opacity-60">Comentario</label>
                                        <textarea class="form-control" rows="4" name="comentario"
                                            placeholder="Su comentario" required></textarea>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary mt-3">
                                            Enviar Comentario
                                        </button>
                                    </div>
                                </form>
                            </div>
                            @endif
                            @endif

                        </div>
                    </div>
                </div>
                <div class="bg-white rounded shadow-sm">
                    <div class="border-bottom p-3">
                        <h3 class="fs-16 fw-600 mb-0">
                            <span class="mr-4">Productos Relacionados</span>
                        </h3>
                    </div>
                    <div class="p-3">
                        <div class="aiz-carousel gutters-5 half-outside-arrow" data-items="5" data-xl-items="3"
                            data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true'
                            data-infinite='true'>
                            @foreach ($relacionados as $item)
                            <div class="carousel-box">
                                <div class="aiz-card-box border border-light rounded hov-shadow-md my-2 has-transition">
                                    <div class="">
                                        <a href="{{ route('product', $item->productosid) }}" class="d-block">
                                            <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                                                src="data:image/jpg;base64,@if ($item->imagen) {{ base64_encode($item->imagen) }} @else {{ get_setting('imagen_defecto') }} @endif"
                                                alt="{{ $item->descripcion }}">
                                        </a>
                                    </div>
                                    <div class="p-md-3 p-2 text-left">
                                        <div class="fs-15">
                                            @if (Auth::check())
                                            @if ($item->precio < $item->precio2)
                                                <del class="fw-700 opacity-50 mr-1">${{
                                                    number_format(round($item->precio2, 2), 2) }}</del>
                                                @endif
                                                <span class="fw-700 text-primary">
                                                    ${{ number_format(round($item->precio, 2), 2) }}
                                                </span>
                                                @else
                                                @if (get_setting('tipo_tienda') == 'publico')
                                                <span class="fw-700 text-primary">
                                                    ${{ number_format(round($item->precio, 2), 2) }}
                                                </span>
                                                @endif
                                                @endif

                                        </div>
                                        <div class="rating rating-sm mt-1">
                                            {{ renderStarRating(json_decode($item->parametros_json)->rating) }}
                                        </div>
                                        <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                                            <a href="{{ route('product', $item->productosid) }}"
                                                class="d-block text-reset">{{ $item->descripcion }}</a>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@if (get_setting('controla_stock') != 2 && Auth::check())
@section('modal')
<div class="modal fade" id="modal-almacen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">

                <h4 class="modal-title" id="myModalLabel">Cambiar Sucursal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-head-custom table-hover w-100 text-center" id="kt_datatable">
                    <thead>
                        <tr>

                            <th>idd</th>
                            <th data-priority="1">Almacen</th>
                            <th data-priority="2">Existencias</th>
                            <th data-priority="3">Accion</th>

                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
@endsection
@endif


@section('script')
<script>
    $(document).ready(function() {
            getVariantPrice();
            $('#option-choice-form input').on('change', function() {
                getVariantPrice();
            });
            if ('{{ get_setting('controla_stock') == 2 }}') {
                var table = $('#kt_datatable').DataTable({
                    paging: false,
                    searching: false,
                    bInfo: false,
                    responsive: true,
                    processing: true,
                    //Combo cantidad de registros a mostrar por pantalla
                    lengthMenu: [
                        [15, 25, 50, -1],
                        [15, 25, 50, 'Todos']
                    ],
                    //Registros por pagina
                    pageLength: 15,
                    //Orden inicial
                    order: [
                        [0, 'asc']
                    ],

                    //Trabajar del lado del server
                    serverSide: true,
                    //Peticion ajax que devuelve los registros
                    ajax: {
                        url: "{{ route('almacenes.index') }}",
                        type: 'GET',
                        data: function(data) {

                            data.factorValor = $('#factor').val();
                            data.producto = $('#id').val();
                        }
                    },
                    columns: [{
                            data: 'almacenesid',
                            name: 'almacenesid',
                            searchable: false,
                            visible: false

                        },
                        {
                            data: 'descripcion',
                            name: 'descripcion',
                            searchable: false,

                        },
                        {
                            data: 'existencias',
                            name: 'existencias',
                            searchable: false,

                        },

                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]

                });
            }

            $("#cambiarAlmacen").click(function() {
                $("#modal-almacen").modal();
                var almacen = $('#variableinicio').val();
                $('#' + almacen).attr("checked", "checked");
            });

            $("input[name='medidasid']").click(function() {

                window.setTimeout(existencias, 700);

                function existencias() {
                    table.draw();
                }

            });

        });

        function cambiarAlmacen(e) {
            $('#variableinicio').val(e.target.id);
            $('#cantidad').empty();
            $('#cantidad').append(e.target.value);
            $('#existencias').val(e.target.value);
            if ($('#botonMas').val() > e.target.value) {
                $('#botonMas').val(1);
                $('#plus').prop("disabled", false);

            } else {
                $('#plus').prop("disabled", false);
            }
            var id = document.getElementById(e.target.id);

            $('#nombreAlmacen').empty();
            $('#nombreAlmacen').append(id.getAttribute('nombrealmacen'));

        }

        $("#modal-almacen").on('hidden.bs.modal', function(event) {
            getVariantPrice();
        })
</script>
@endsection