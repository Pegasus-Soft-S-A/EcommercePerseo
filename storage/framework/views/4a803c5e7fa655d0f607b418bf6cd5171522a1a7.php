<?php
$almacenes = App\Models\Almacenes::where('disponibleventa', 1)->get();
$user = Session::get('user');
?>

<div class="modal-body p-4 c-scrollbar-light">
    <div class="row">
        <div class="col-lg-6">
            <div class="row gutters-10 flex-row-reverse">

                <div class="col">
                    <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb' data-fade='true'
                        data-auto-height='true'>
                        <?php if(count($imagenProducto) > 0): ?>
                        <?php $__currentLoopData = $imagenProducto; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imagenProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="carousel-box img-zoom rounded">
                            <img class="img-fluid" loading="lazy"
                                src="data:image/jpg;base64,<?php echo e(base64_encode($imagenProduct->imagen)); ?>"
                                data-src="data:image/jpg;base64,<?php echo e(base64_encode($imagenProduct->imagen)); ?>" onerror="">
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        <div class="carousel-box img-zoom rounded">
                            <img class="img-fluid" loading="lazy"
                                src="data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>"
                                data-src="data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>" onerror="">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-auto w-90px">
                    <div class="aiz-carousel carousel-thumb product-gallery-thumb" data-items='5'
                        data-nav-for='.product-gallery' data-vertical='true' data-focus-select='true'>
                        <?php if(count($imagenProducto) > 0): ?>
                        <?php $__currentLoopData = $imagenProducto; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imagenProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="carousel-box c-pointer border p-1 rounded"
                            data-medidasid="<?php echo e($imagenProduct->medidasid); ?>">
                            <img class="lazyload mw-100 size-50px mx-auto"
                                src="data:image/jpg;base64,<?php echo e(base64_encode($imagenProduct->imagen)); ?>"
                                data-src="data:image/jpg;base64,<?php echo e(base64_encode($imagenProduct->imagen)); ?>" onerror="">
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                        <div class="carousel-box c-pointer border p-1 rounded" data-medidasid="">
                            <img class="lazyload mw-100 size-50px mx-auto"
                                src="data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>"
                                data-src="data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>" onerror="">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="text-left">
                <h2 class="mb-2 fs-20 fw-600">
                    <?php echo e($product->descripcion); ?>

                </h2>

                <hr>
                <?php if(get_setting('controla_stock') == 2 && Auth::check()): ?>
                <div class="row no-gutters mt-3 d-flex ">
                    <div>
                        <div class="opacity-50 my-2">Sucursal Actual:</div>
                    </div>
                    <?php
                    $nombreAlmacen = App\Models\Almacenes::select('descripcion')
                    ->where('almacenesid', session('almacenesid'))
                    ->first();
                    ?>
                    <div class="mx-2 mx-md-1">
                        <div class="fs-20 opacity-60">
                            <span id="nombreAlmacen" class="">
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
                    <div class="d-flex">
                        <?php if((get_setting('controla_stock') == 0 && !Auth::check()) ||
                        (get_setting('controla_stock') == 2 && !Auth::check())): ?>

                        <?php if($product->existenciastotales > 0): ?>
                        <div class="d-inline-block rounded px-2 border-success mt-1 text-success border ml-2">
                            <span><?php echo e(get_setting('productos_disponibles')); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border ml-2">
                            <span><?php echo e(get_setting('productos_no_disponibles')); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php elseif(get_setting('controla_stock') == 0 && Auth::check()): ?>
                        <?php if($product->existenciastotales > 0): ?>
                        <div class="d-inline-block rounded px-2 border-success mt-1 text-success border ml-2">
                            <span><?php echo e(get_setting('productos_disponibles')); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border ml-2">
                            <span><?php echo e(get_setting('productos_no_disponibles')); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php else: ?>
                        <div class="">

                            <span id="cantidad" class="h2 fw-600 text-primary ml-3"></span>


                        </div>

                        <?php endif; ?>
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
                        <div class="col-sm-10">
                            <div class="">
                                <strong class="h2 fw-600 text-primary" id="precio">

                                </strong>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row no-gutters mt-3">
                        <div class="col-sm-2">
                            <div class="opacity-50 my-2">Precio:</div>
                        </div>
                        <div class="col-sm-10">
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
                    <?php endif; ?>

                    <hr>
                    <?php
                    $almacenes = DB::connection('empresa')
                    ->table('facturadores_almacenes')
                    ->where('facturadoresid', get_setting('facturador'))
                    ->where('principal', '1')
                    ->first();

                    ?>
                    <form id="option-choice-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="variableinicio" name="variableinicio"
                            <?php if(get_setting('controla_stock')==2): ?> value="<?php echo e(session('almacenesid')); ?>" <?php else: ?>
                            value="<?php echo e($almacenes->almacenesid); ?>" <?php endif; ?>>
                        <input type="hidden" id="existencias" name="existencias" value="">
                        <input type="hidden" id="id" name="id" value="<?php echo e($product->productosid); ?>">
                        <input type="hidden" name="preciocompleto" value="<?php echo e($precioProducto->precio); ?>"
                            id="preciocompleto">
                        <input type="hidden" name="precioIVA" value="<?php echo e($precioProducto->precioiva); ?>" id="precioiva">
                        <input type="hidden" id="factor" name="factor" value="<?php echo e($precioProducto->factor); ?>">
                        

                        <div class="row no-gutters">
                            <div class="col-2">
                                <div class="opacity-50 mt-2 ">
                                    Medida:</div>
                            </div>
                            <div class="col-10">
                                <div class="aiz-radio-inline ">
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

                        <div class="row no-gutters">
                            <div class="col-2">
                                <div class="opacity-50 mt-2">Cantidad:</div>
                            </div>
                            <div class="col-10">
                                <div class="product-quantity d-flex align-items-center">
                                    <div class="row no-gutters align-items-center aiz-plus-minus mr-3"
                                        style="width: 130px;">
                                        <button class="btn col-auto btn-icon btn-sm btn-circle btn-light" type="button"
                                            data-type="minus" data-field="quantity" disabled="">
                                            <i class="las la-minus"></i>
                                        </button>
                                        <input type="text" name="quantity" id="botonMas"
                                            class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                            placeholder="1" value="<?php echo e($min_qty); ?>" min="<?php echo e($min_qty); ?>"
                                            <?php if(get_setting('controla_stock')==0): ?>
                                            max="<?php echo e(get_setting('cantidad_maxima')); ?>" <?php else: ?> max="" <?php endif; ?>
                                            autocomplete="off">
                                        <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light" id="plus"
                                            type="button" data-type="plus" data-field="quantity">
                                            <i class="las la-plus"></i>


                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
                            <div class="col-2">
                                <div class="opacity-50">Precio Total:</div>
                            </div>
                            <div class="col-10">
                                <div class="product-price">
                                    <strong id="chosen_price" class="h4 fw-600 text-primary">

                                    </strong>
                                </div>
                            </div>
                        </div>

                    </form>
                    <div class="mt-3">
                        <?php if(!Auth::check() && get_setting('controla_stock') == 2): ?>
                        <a href="<?php echo e(route('user.login')); ?>">
                            <button type="button" class="btn btn-primary buy-now fw-600 add-to-cart">

                                <i class="la la-shopping-cart"></i>
                                <span class="d-none d-md-inline-block"> Añadir al Carrito</span>
                            </button>
                        </a>
                        <?php else: ?>
                        <button type="button" class="btn btn-primary buy-now fw-600 add-to-cart" onclick="addToCart()">

                            <i class="la la-shopping-cart"></i>
                            <span class="d-none d-md-inline-block"> Añadir al Carrito</span>
                        </button>
                        <?php endif; ?>
                    </div>

            </div>
        </div>
    </div>
</div>





<script type="text/javascript">
    cartQuantityInitialize();
    $('#option-choice-form input').on('change', function() {
        getVariantPrice();
    });
    $(document).ready(function() {
        <?php if(isset($user)): ?>
            $('#modalIdentificacion').modal()
        <?php endif; ?>



        window.setTimeout(datatable, 700);


    });



    function datatable() {
        if ('<?php echo e(get_setting('controla_stock') == 2); ?>') {
            var table = $('#kt_datatable').DataTable({
                paging: false,
                searching: false,
                bInfo: false,
                responsive: true,
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
        $("input[name='medidasid']").click(function() {

            window.setTimeout(existencias, 700);

            function existencias() {
                table.draw();
                var almacen = $('#variableinicio').val();
                $('#' + almacen).attr("checked", "checked");
            }

        });
        $("#cambiarAlmacen").click(function() {
            $("#modal-almacen").modal();
            var almacen = $('#variableinicio').val();
            $('#' + almacen).attr("checked", "checked");
        });



    }

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
    $("#addToCart").on('hidden.bs.modal', function(event) {
        $('#kt_datatable').DataTable().destroy();
    })
</script><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/partials/addToCart.blade.php ENDPATH**/ ?>