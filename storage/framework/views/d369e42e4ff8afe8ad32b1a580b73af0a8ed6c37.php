<?php $__env->startSection('content'); ?>

<section class="mb-4 pt-3">
    <div class="container sm-px-0">
        <form class="" id="search-form" action="" method="GET">
            <div class="row">
                <div class="col-xl-3">
                    <div class="aiz-filter-sidebar collapse-sidebar-wrap sidebar-xl sidebar-right z-1035">
                        <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle"
                            data-target=".aiz-filter-sidebar" data-same=".filter-sidebar-thumb"></div>
                        <div class="collapse-sidebar c-scrollbar-light text-left">
                            <div class="d-flex d-xl-none justify-content-between align-items-center pl-3 border-bottom">
                                <h3 class="h6 mb-0 fw-600">Filtros</h3>
                                <button type="button" class="btn btn-sm p-2 filter-sidebar-thumb"
                                    data-toggle="class-toggle" data-target=".aiz-filter-sidebar">
                                    <i class="las la-times la-2x"></i>
                                </button>
                            </div>
                            <div class="bg-white shadow-sm rounded mb-3">
                                <div class="fs-15 fw-600 p-3 border-bottom">
                                    <?php echo e(ucfirst(get_setting('grupo_productos'))); ?>

                                </div>
                                <div class="p-3">
                                    <ul class="list-unstyled">
                                        <?php if(!isset($id)): ?>
                                        <?php $__currentLoopData = grupoProductos(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="mb-2 ml-2">
                                            <a class="text-reset fs-14"
                                                href="<?php echo e(route('products.category', $category->id)); ?>"><?php echo e($category->descripcion); ?></a>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                        <li class="mb-2">
                                            <a class="text-reset fs-14 fw-600"
                                                href="<?php if(get_setting('vista_categorias')==1): ?> <?php echo e(route('categories.all')); ?> <?php else: ?> <?php echo e(route('search')); ?> <?php endif; ?>">
                                                <i class="las la-angle-left"></i>
                                                Todas las <?php echo e(ucfirst(get_setting('grupo_productos'))); ?>

                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <label class="text-reset fs-14 fw-600" href="#">
                                                <i class="las la-angle-down"></i>
                                                <?php echo e($descripcion); ?>

                                            </label>
                                        </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="bg-white shadow-sm rounded mb-3">
                                <div class="fs-15 fw-600 p-3 border-bottom">
                                    Rango de Precios
                                </div>

                                <div class="p-3">
                                    <div class="aiz-range-slider">
                                        <div id="input-slider-range" data-range-value-min="<?php echo e($preciominimo); ?>"
                                            data-range-value-max="<?php echo e($preciomaximo+0.99); ?>"></div>

                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <span class="range-slider-value value-low fs-14 fw-600 opacity-70   "
                                                    <?php if(isset($min_price)): ?> data-range-value-low="<?php echo e($min_price); ?>"
                                                    <?php elseif( "<?php echo e($preciominimo  > 0); ?>" ): ?>
                                                    data-range-value-low="<?php echo e($preciominimo); ?>" <?php else: ?>
                                                    data-range-value-low="0" <?php endif; ?>
                                                    id="input-slider-range-value-low"></span>
                                            </div>
                                            <div class="col-6 text-right">
                                                <span class="range-slider-value value-high fs-14 fw-600 opacity-70  "
                                                    <?php if(isset($max_price)): ?> data-range-value-high="<?php echo e($max_price); ?>"
                                                    <?php elseif("<?php echo e($preciomaximo> 0); ?>"): ?>
                                                    data-range-value-high="<?php echo e($preciomaximo + 0.99); ?>" <?php else: ?>
                                                    data-range-value-high="0" <?php endif; ?>
                                                    id="input-slider-range-value-high"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">

                    <ul class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href="<?php echo e(route('home')); ?>">Inicio</a>
                        </li>
                        <?php if(!isset($id)): ?>
                        <li class="breadcrumb-item fw-600  text-dark">
                            <a class="text-reset" href=<?php if(get_setting('vista_categorias')==1): ?> <?php echo e(route('categories.all')); ?> <?php else: ?> <?php echo e(route('search')); ?> <?php endif; ?>">Todas las
                                <?php echo e(ucfirst(get_setting('grupo_productos'))); ?></a>
                        </li>
                        <?php else: ?>
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href=<?php if(get_setting('vista_categorias')==1): ?> <?php echo e(route('categories.all')); ?> <?php else: ?> <?php echo e(route('search')); ?> <?php endif; ?>">Todas las
                                <?php echo e(ucfirst(get_setting('grupo_productos'))); ?></a>
                        </li>
                        <?php endif; ?>
                        <?php if(isset($id)): ?>
                        <li class="text-dark fw-600 breadcrumb-item">
                            <label class="text-reset" href="">
                                <?php echo e($descripcion); ?></label>
                        </li>
                        <?php endif; ?>

                    </ul>

                    <div class="text-left">
                        <div class="d-flex align-items-center">
                            <div>
                                <h1 class="h6 fw-600 text-body">

                                    <?php if(isset($id)): ?>
                                    <?php echo e($descripcion); ?>

                                    <?php elseif(isset($query)): ?>
                                    Buscar Resultados por "<?php echo e($query); ?>"
                                    
                                    <?php endif; ?>


                                </h1>
                            </div>
                            <div class="d-xl-none ml-auto ml-xl-3 mr-0 form-group align-self-end">
                                <button type="button" class="btn btn-icon p-0" data-toggle="class-toggle"
                                    data-target=".aiz-filter-sidebar">
                                    <i class="la la-filter la-2x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="min_price" value="">
                    <input type="hidden" name="max_price" value="">

                    <div class="row gutters-5 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-2">
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col">
                            <?php echo $__env->make('frontend.partials.product_box_1',['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="aiz-pagination aiz-pagination-center mt-4">
                        <?php echo e($products->appends(request()->input())->links('pagination::bootstrap-4')); ?>

                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    function filter(){
            $('#search-form').submit();
        }
        function rangefilter(arg){
            $('input[name=min_price]').val(arg[0]);
            $('input[name=max_price]').val(arg[1]);
            filter();
        }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/product_listing.blade.php ENDPATH**/ ?>