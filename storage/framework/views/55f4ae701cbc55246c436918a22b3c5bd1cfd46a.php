<?php $__env->startSection('meta'); ?>
<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="<?php echo e($detallesProducto->descripcion); ?>">
<meta itemprop="description" content="<?php echo e($detallesProducto->fichatecnica); ?>">
<meta itemprop="image" content="">

<!-- Twitter Card data -->
<meta name="twitter:card" content="product">
<meta name="twitter:site" content="@publisher_handle">
<meta name="twitter:title" content="<?php echo e($detallesProducto->descripcion); ?>">
<meta name="twitter:description" content="<?php echo e($detallesProducto->fichatecnica); ?>">
<meta name="twitter:creator" content="@author_handle">
<meta name="twitter:image" content="">
<meta name="twitter:data1" content="<?php echo e(number_format(round($precioProducto->precio, 2), 2)); ?>">
<meta name="twitter:label1" content="Precio">

<!-- Open Graph data -->
<meta property="og:title" content="<?php echo e($detallesProducto->descripcion); ?>" />
<meta property="og:type" content="og:product" />
<meta property="og:url" content="<?php echo e(route('product', $detallesProducto->productosid)); ?>" />
<meta property="og:image" content="" />
<meta property="og:description" content="<?php echo e($detallesProducto->fichatecnica); ?>" />
<meta property="og:site_name" content="<?php echo e(get_setting('nombre_sitio')); ?>" />
<meta property="og:price:amount" content="<?php echo e(number_format(round($precioProducto->precio, 2), 2)); ?>" />
<meta property="product:price:currency" content="USD" />

<meta property="fb:app_id" content="<?php echo e(get_setting('FACEBOOK_PIXEL_ID')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<section class="mb-4 pt-3">
    <div class="container">
        <div class="bg-white shadow-sm rounded p-3">
            <div class="row">
                <div class="col-xl-5 col-lg-6 mb-4">
                    <div class="sticky-top z-3 row gutters-10">
                        <div class="col order-1 order-md-2">
                            <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb'
                                data-fade='true' data-auto-height='true'>
                                <?php if(count($imagenProducto) > 0): ?>
                                <?php $__currentLoopData = $imagenProducto; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imagenProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="carousel-box img-zoom rounded">
                                    <img class="img-fluid lazyload"
                                        src="data:image/jpg;base64,<?php echo e(base64_encode($imagenProduct->imagen)); ?>">
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <div class="carousel-box img-zoom rounded">
                                    <img class="img-fluid lazyload"
                                        src="data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto w-md-80px order-2 order-md-1 mt-3 mt-md-0">
                            <div class="aiz-carousel product-gallery-thumb" data-items='5'
                                data-nav-for='.product-gallery' data-vertical='true' data-vertical-sm='false'
                                data-focus-select='true' data-arrows='true'>
                                <?php if(count($imagenProducto) > 0): ?>
                                <?php $__currentLoopData = $imagenProducto; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imagenProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="carousel-box c-pointer border p-1 rounded"
                                    data-medidasid="<?php echo e($imagenProduct->medidasid); ?>">
                                    <img class="lazyload mw-100 size-50px mx-auto"
                                        src="data:image/jpg;base64,<?php echo e(base64_encode($imagenProduct->imagen)); ?>">
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <div class="carousel-box c-pointer border p-1 rounded" data-medidasid="">
                                    <img class="lazyload mw-100 size-50px mx-auto"
                                        src="data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7 col-lg-6">
                    <div class="text-left">
                        <h1 class="mb-2 fs-20 fw-600">
                            <?php echo e($detallesProducto->descripcion); ?>

                        </h1>

                        <div class="row align-items-center">
                            <div class="col-6">
                                <?php
                                $total = 0;
                                $total += $numerocomentarios;
                                ?>
                                <span class="rating">
                                    <?php echo e(renderStarRating(json_decode($detallesProducto->parametros_json)->rating)); ?>

                                </span>
                                <span class="ml-1 opacity-50"><?php echo e($total); ?> Reseñas</span>
                            </div>
                        </div>

                        <hr>


                        <div class="row no-gutters mt-3">
                            <div class="col-sm-2">
                                <div class="opacity-50 my-2">Código:</div>
                            </div>
                            <div class="col-sm-10">
                                <div class="fs-20 opacity-60">
                                    <?php echo e($detallesProducto->productocodigo); ?>

                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php if(get_setting('controla_stock') == 2 && Auth::check()): ?>
                        <div class="row no-gutters mt-3">
                            <div class="col-sm-2">
                                <div class="opacity-50 my-2">Sucursal Actual:</div>
                            </div>
                            <?php
                            $nombreAlmacen = App\Models\Almacenes::select('descripcion')
                            ->where('almacenesid', session('almacenesid'))
                            ->first();
                            ?>
                            <div class="col-sm-10">
                                <div class="fs-20 opacity-60">
                                    <span id="nombreAlmacen">
                                        <?php echo e($nombreAlmacen->descripcion); ?>


                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php endif; ?>
                        <?php if(get_setting('productos_existencias') == 'todos' ||
                        get_setting('controla_stock') == 1 ||
                        (get_setting('controla_stock') == 2 && Auth::check())): ?>
                        <div class="row no-gutters mt-3">

                            <div class="col-sm-2">
                                <div class="opacity-50 my-2">Existencias:</div>
                            </div>
                            <div class="d-flex no-gutters">
                                <div class="">
                                    <?php if((get_setting('controla_stock') == 0 && !Auth::check()) ||
                                    (get_setting('controla_stock') == 2 && !Auth::check())): ?>
                                    <?php if($detallesProducto->existenciastotales > 0): ?>
                                    <div class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                        <span><?php echo e(get_setting('productos_disponibles')); ?></span>
                                    </div>
                                    <?php else: ?>
                                    <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                        <span><?php echo e(get_setting('productos_no_disponibles')); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php elseif(get_setting('controla_stock') == 0 && Auth::check()): ?>
                                    <?php if($detallesProducto->existenciastotales > 0): ?>
                                    <div class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                        <span><?php echo e(get_setting('productos_disponibles')); ?></span>
                                    </div>
                                    <?php else: ?>
                                    <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                        <span><?php echo e(get_setting('productos_no_disponibles')); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <strong class="h2 fw-600 text-primary">
                                        <span id="cantidad"></span>

                                    </strong>
                                    <?php endif; ?>
                                </div>
                                <?php if(get_setting('controla_stock') == 2 && Auth::check()): ?>
                                <div class="mx-4">
                                    <button type="button" id="cambiarAlmacen" class="my-2 btn btn-soft-primary btn-xs">
                                        Ver Sucursales
                                        <i class="las la-eye"></i>

                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>



                        </div>

                        <hr>
                        <?php endif; ?>





                        <?php if(Auth::check()): ?>
                        <?php if($precioProducto->precio <= $precioProducto2->precio): ?>
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
                            <?php else: ?>
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
                            <?php endif; ?>
                            <?php else: ?>
                            <?php if(get_setting('tipo_tienda') == 'publico'): ?>
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
                            <?php endif; ?>
                            <?php endif; ?>

                            <form id="option-choice-form">
                                <?php echo csrf_field(); ?>
                                
                                <input type="hidden" id="variableinicio" name="variableinicio"
                                    value="<?php echo e(session('almacenesid')); ?>">
                                <input type="hidden" id="existencias" name="existencias" value="">
                                <input type="hidden" id="id" name="id" value="<?php echo e($detallesProducto->productosid); ?>">
                                <input type="hidden" name="preciocompleto" value="<?php echo e($precioProducto->precio); ?>"
                                    id="preciocompleto">
                                <input type="hidden" name="precioIVA" value="<?php echo e($precioProducto->precioiva); ?>"
                                    id="precioiva">
                                <input type="hidden" id="factor" name="factor" value="<?php echo e($precioProducto->factor); ?>">
                                

                                <div class="row no-gutters">
                                    <div class="col-sm-2">
                                        <div class="opacity-50 my-2">
                                            Medida:</div>
                                    </div>
                                    <div class="col-sm-10">
                                        <div class="aiz-radio-inline">
                                            <?php $__currentLoopData = $medidas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $medida): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="aiz-megabox pl-0 mr-2">
                                                <input type="radio" name="medidasid" value="<?php echo e($medida->medidasid); ?>"
                                                    <?php if($key==0): ?> checked <?php endif; ?>>
                                                <span
                                                    class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2">
                                                    <?php echo e($medida->descripcion); ?>

                                                </span>
                                            </label>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <?php if(get_setting('tipo_tienda') == 'publico'): ?>
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
                                                    <?php if(get_setting('controla_stock')==0): ?>
                                                    max="<?php echo e(get_setting('cantidad_maxima')); ?>" <?php else: ?> max="" <?php endif; ?>
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
                                <?php else: ?>
                                <?php if(auth()->guard()->check()): ?>
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
                                                    <?php if(get_setting('controla_stock')==0): ?>
                                                    max="<?php echo e(get_setting('cantidad_maxima')); ?>" <?php else: ?> max="" <?php endif; ?>
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
                                <?php endif; ?>
                                <?php endif; ?>

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

                            <?php if(get_setting('tipo_tienda') == 'publico'): ?>
                            <div class="mt-3">

                                <?php if(!Auth::check() && get_setting('controla_stock') == 2): ?>
                                <a href="<?php echo e(route('user.login')); ?>">
                                    <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600">
                                        <i class="las la-shopping-bag"></i>
                                        <span class="d-none d-md-inline-block"> Añadir al carrito</span>
                                    </button>
                                    <button type="button" class="btn btn-primary buy-now fw-600">
                                        <i class="la la-shopping-cart"></i> Comprar Ahora
                                    </button>
                                </a>
                                <?php else: ?>
                                <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600"
                                    onclick="addToCart()">
                                    <i class="las la-shopping-bag"></i>
                                    <span class="d-none d-md-inline-block"> Añadir al carrito</span>
                                </button>
                                <button type="button" class="btn btn-primary buy-now fw-600" onclick="buyNow()">
                                    <i class="la la-shopping-cart"></i> Comprar Ahora
                                </button>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <?php if(auth()->guard()->check()): ?>

                            <?php if(!Auth::check() && get_setting('controla_stock') == 2): ?>
                            <a href="<?php echo e(route('user.login')); ?>">
                                <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600">
                                    <i class="las la-shopping-bag"></i>
                                    <span class="d-none d-md-inline-block"> Añadir al carrito</span>
                                </button>
                                <button type="button" class="btn btn-primary buy-now fw-600">
                                    <i class="la la-shopping-cart"></i> Comprar Ahora
                                </button>
                            </a>
                            <?php else: ?>
                            <button type="button" class="btn btn-soft-primary mr-2 add-to-cart fw-600"
                                onclick="addToCart()">
                                <i class="las la-shopping-bag"></i>
                                <span class="d-none d-md-inline-block"> Añadir al carrito</span>
                            </button>
                            <button type="button" class="btn btn-primary buy-now fw-600" onclick="buyNow()">
                                <i class="la la-shopping-cart"></i> Comprar Ahora
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>
                            <div class="d-table width-100 mt-3">
                                <div class="d-table-cell">
                                    <!-- Add to wishlist button -->
                                    <button type="button" class="btn pl-0 btn-link fw-600"
                                        onclick="addToWishList(<?php echo e($detallesProducto->productosid); ?>)">
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
                                <?php echo e(renderStarRating(json_decode($detallesProducto->parametros_json)->rating)); ?>

                            </div>
                            <div class="opacity-60 fs-12"><?php echo e($total); ?> Opiniones de Usuarios</div>
                        </div>
                    </div>

                </div>
                <div class="bg-white rounded shadow-sm mb-3">
                    <div class="p-3 border-bottom fs-16 fw-600">
                        Top Productos más vendidos
                    </div>
                    <div class="p-3">
                        <ul class="list-group list-group-flush">
                            <?php $__currentLoopData = $top; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="py-3 px-0 list-group-item border-light">
                                <div class="row gutters-10 align-items-center">
                                    <div class="col-5">
                                        <a href="<?php echo e(route('product', $item->productosid)); ?>" class="d-block text-reset">
                                            <img class="img-fit lazyload h-xxl-110px h-xl-80px h-120px"
                                                src="data:image/jpg;base64,<?php if($item->imagen): ?> <?php echo e(base64_encode($item->imagen)); ?> <?php else: ?> <?php echo e(get_setting('imagen_defecto')); ?> <?php endif; ?>"
                                                alt="<?php echo e($item->descripcion); ?>">
                                        </a>
                                    </div>
                                    <div class="col-7 text-left">
                                        <h4 class="fs-13 text-truncate-2">
                                            <a href="<?php echo e(route('product', $item->productosid)); ?>"
                                                class="d-block text-reset"><?php echo e($item->descripcion); ?></a>
                                        </h4>
                                        <div class="rating rating-sm mt-1">
                                            <?php echo e(renderStarRating(json_decode($item->parametros_json)->rating)); ?>

                                        </div>
                                        <div class="mt-2">
                                            <?php if(Auth::check()): ?>
                                            <?php if($item->precio < $item->precio2): ?>
                                                <del class="fs-17 fw-600 opacity-50 mr-1">$<?php echo e(number_format(round($item->precio2, 2), 2)); ?></del>
                                                <?php endif; ?>
                                                <span class="fs-17 fw-600 text-primary">
                                                    $<?php echo e(number_format(round($item->precio, 2), 2)); ?></span>
                                                <?php else: ?>
                                                <?php if(get_setting('tipo_tienda') == 'publico'): ?>
                                                <span class="fs-17 fw-600 text-primary">
                                                    $<?php echo e(number_format(round($item->precio, 2), 2)); ?></span>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 order-0 order-xl-1">
                <div class="bg-white mb-3 shadow-sm rounded">
                    <div class="nav border-bottom aiz-nav-tabs">
                        <?php if($detallesProducto->fichatecnica): ?>
                        <a href="#tab_default_1" data-toggle="tab" class="p-3 fs-16 fw-600 text-reset active show">Ficha
                            Técnica</a>
                        <?php endif; ?>
                        <?php if($detallesProducto->observaciones): ?>
                        <a href="#tab_default_2" data-toggle="tab"
                            class="p-3 fs-16 fw-600 text-reset <?php if(!$detallesProducto->fichatecnica): ?> active show <?php endif; ?>">Observaciones</a>
                        <?php endif; ?>
                        <a href="#tab_default_3" data-toggle="tab"
                            class="p-3 fs-16 fw-600 text-reset <?php if(!$detallesProducto->fichatecnica && !$detallesProducto->observaciones): ?> active show <?php endif; ?>">Reseñas</a>
                    </div>

                    <div class="tab-content pt-0">
                        <?php if($detallesProducto->fichatecnica): ?>
                        <div class="tab-pane fade active show" id="tab_default_1">
                            <div class="p-4">
                                <div class="mw-100 overflow-hidden text-left aiz-editor-data">
                                    <?php echo nl2br($detallesProducto->fichatecnica) ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($detallesProducto->observaciones): ?>
                        <div class="tab-pane fade <?php if(!$detallesProducto->fichatecnica): ?> active show <?php endif; ?>"
                            id="tab_default_2">
                            <div class="p-4">
                                <div class="mw-100 overflow-hidden text-left aiz-editor-data">
                                    <?php echo nl2br($detallesProducto->observaciones) ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="tab-pane fade <?php if(!$detallesProducto->fichatecnica && !$detallesProducto->observaciones): ?> active show <?php endif; ?>"
                            id="tab_default_3">
                            <div class="p-4">
                                <ul class="list-group list-group-flush">
                                    <?php $__currentLoopData = $comentarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="media list-group-item d-flex">

                                        <div class="media-body text-left">
                                            <div class="d-flex justify-content-between">
                                                <h3 class="fs-15 fw-600 mb-0"><?php echo e($review->razonsocial); ?></h3>
                                                <span class="rating rating-sm">
                                                    <?php for($i = 0; $i < $review->valoracion; $i++): ?>
                                                        <i class="las la-star active"></i>
                                                        <?php endfor; ?>
                                                        <?php for($i = 0; $i < 5 - $review->valoracion; $i++): ?>
                                                            <i class="las la-star"></i>
                                                            <?php endfor; ?>
                                                </span>
                                            </div>
                                            <div class="opacity-60 mb-2">
                                                <?php echo e(date('d-m-Y', strtotime($review->fechacreacion))); ?></div>
                                            <p class="comment-text">
                                                <?php echo e($review->comentario); ?>

                                            </p>
                                        </div>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>

                                <?php if($total <= 0): ?> <div class="text-center fs-18 opacity-70">
                                    Todavia no hay reseñas para este producto.
                            </div>
                            <?php endif; ?>

                            <?php if(Auth::check()): ?>
                            <?php
                            $commentable = false;
                            $facturas = \App\Models\Facturas::where('clientesid', Auth::user()->clientesid)->get();
                            ?>
                            <?php $__currentLoopData = $facturas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $factura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = \App\Models\FacturasDetalles::where('facturasid', $factura->facturasid)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\App\Models\Comentarios::where('clientesid',
                            Auth::user()->clientesid)->where('productosid', $detallesProducto->productosid)->first() ==
                            null && $detallesProducto->productosid == $detalle->productosid): ?>
                            <?php
                            $commentable = true;
                            ?>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($commentable): ?>
                            <div class="pt-4">
                                <div class="border-bottom mb-4">
                                    <h3 class="fs-17 fw-600">
                                        Escribe un comentario
                                    </h3>
                                </div>
                                <form class="form-default" role="form" action="<?php echo e(route('reviews.store')); ?>"
                                    method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="productosid"
                                        value="<?php echo e($detallesProducto->productosid); ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="text-uppercase c-gray-light">Su
                                                    Nombre</label>
                                                <input type="text" name="name" value="<?php echo e(Auth::user()->razonsocial); ?>"
                                                    class="form-control" disabled required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="text-uppercase c-gray-light">Email</label>
                                                <input type="text" name="email" value="<?php echo e(Auth::user()->email); ?>"
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
                            <?php endif; ?>
                            <?php endif; ?>

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
                            <?php $__currentLoopData = $relacionados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="carousel-box">
                                <div class="aiz-card-box border border-light rounded hov-shadow-md my-2 has-transition">
                                    <div class="">
                                        <a href="<?php echo e(route('product', $item->productosid)); ?>" class="d-block">
                                            <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                                                src="data:image/jpg;base64,<?php if($item->imagen): ?> <?php echo e(base64_encode($item->imagen)); ?> <?php else: ?> <?php echo e(get_setting('imagen_defecto')); ?> <?php endif; ?>"
                                                alt="<?php echo e($item->descripcion); ?>">
                                        </a>
                                    </div>
                                    <div class="p-md-3 p-2 text-left">
                                        <div class="fs-15">
                                            <?php if(Auth::check()): ?>
                                            <?php if($item->precio < $item->precio2): ?>
                                                <del class="fw-700 opacity-50 mr-1">$<?php echo e(number_format(round($item->precio2, 2), 2)); ?></del>
                                                <?php endif; ?>
                                                <span class="fw-700 text-primary">
                                                    $<?php echo e(number_format(round($item->precio, 2), 2)); ?>

                                                </span>
                                                <?php else: ?>
                                                <?php if(get_setting('tipo_tienda') == 'publico'): ?>
                                                <span class="fw-700 text-primary">
                                                    $<?php echo e(number_format(round($item->precio, 2), 2)); ?>

                                                </span>
                                                <?php endif; ?>
                                                <?php endif; ?>

                                        </div>
                                        <div class="rating rating-sm mt-1">
                                            <?php echo e(renderStarRating(json_decode($item->parametros_json)->rating)); ?>

                                        </div>
                                        <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                                            <a href="<?php echo e(route('product', $item->productosid)); ?>"
                                                class="d-block text-reset"><?php echo e($item->descripcion); ?></a>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>
<?php if(get_setting('controla_stock') != 2 && Auth::check()): ?>
<?php $__env->startSection('modal'); ?>
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
<?php $__env->stopSection(); ?>
<?php endif; ?>


<?php $__env->startSection('script'); ?>
<script>
    $(document).ready(function() {
            getVariantPrice();
            $('#option-choice-form input').on('change', function() {
                getVariantPrice();
            });
            if ('<?php echo e(get_setting('controla_stock') == 2); ?>') {
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
                        url: "<?php echo e(route('almacenes.index')); ?>",
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/product_details.blade.php ENDPATH**/ ?>