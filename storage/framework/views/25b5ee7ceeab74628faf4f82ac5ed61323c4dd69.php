<?php $__env->startSection('content'); ?>
<div class="home-banner-area mb-4 pt-3">
    <div class="container">
        <div class="row gutters-10 position-relative">
            <div class="col-lg-3 position-static d-none d-lg-block">
                <?php echo $__env->make('frontend.partials.category_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <?php
            $featured_categories = gruposDestacados();
            $productos_oferta = productosOferta();
            $grupoid=getGrupoID();
            ?>
            <div class="<?php if(count($productos_oferta)>0): ?> col-lg-7 <?php else: ?> col-lg-9 <?php endif; ?>">
                <div class="aiz-carousel dots-inside-bottom mobile-img-auto-height" data-arrows="true" data-dots="true"
                    data-autoplay="true">
                    <?php if(get_setting('home_slider')!=null): ?>
                    <?php $__currentLoopData = json_decode(get_setting('home_slider')); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $slider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(strtotime(date('Y-m-d'))>=strtotime($slider->inicio) &&
                    strtotime(date('Y-m-d'))<=strtotime($slider->fin)): ?> <div class="carousel-box">
                            <a href="<?php echo e($slider->link); ?>" target="_blank">
                                <img class="d-block mw-100 img-fit-slider rounded shadow-sm"
                                    src="data:image/jpg;base64,<?php if($slider->imagen): ?> <?php echo e($slider->imagen); ?> <?php else: ?> <?php echo e(get_setting('imagen_defecto')); ?>  <?php endif; ?>"
                                    alt="promo" <?php if($featured_categories==null): ?> height="457" <?php else: ?> height="315" <?php endif; ?>>
                            </a>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                </div>
                <?php if($featured_categories != null): ?>
                <ul class=" list-unstyled mb-0 row gutters-5">
                    <?php $__currentLoopData = $featured_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="minw-0 col-4 col-md mt-3">
                        <a href="<?php echo e(route('products.category', $category->$grupoid)); ?>"
                            class="d-block rounded bg-white p-2 text-reset shadow-sm">
                            <img src="data:image/jpg;base64,<?php if($category->imagen): ?> <?php echo e(base64_encode($category->imagen)); ?> <?php else: ?> <?php echo e(get_setting('imagen_defecto')); ?> <?php endif; ?>"
                                alt="<?php echo e($category->descripcion); ?>" class="lazyload img-fit" height="78">

                            <div class="text-center text-truncate fs-12 fw-600 mt-2 opacity-70">
                                <?php echo e($category->descripcion); ?></div>
                        </a>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php endif; ?>
            </div>


            <?php if(count($productos_oferta)>0): ?>
            <div class="col-lg-2 order-3 mt-3 mt-lg-0">
                <div class="bg-white rounded shadow-sm">
                    <div class="bg-soft-primary rounded-top p-3 d-flex align-items-center justify-content-center">
                        <span class="fw-600 fs-16 text-center">
                            Ofertas Solo Hoy
                        </span>
                    </div>
                    <div class="c-scrollbar-light overflow-auto h-lg-400px p-2 bg-primary rounded-bottom">
                        <div class="gutters-5 lg-no-gutters row row-cols-2 row-cols-lg-1">
                            <?php $__currentLoopData = $productos_oferta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col mb-2">
                                <a href="<?php echo e(route('product',$producto->productosid)); ?>"
                                    class="d-block p-2 text-reset bg-white h-100 rounded">
                                    <div class="row gutters-5 align-items-center">
                                        <div class="col-xxl">
                                            <div class="img">
                                                <img class="lazyload img-fit h-140px h-lg-80px"
                                                    src="data:image/jpg;base64,<?php echo e(base64_encode($producto->imagen)); ?>">
                                            </div>
                                        </div>
                                        <div class="col-xxl">
                                            <div class="fs-16">
                                                <?php if(Auth::check()): ?>
                                                <?php if($producto->precio<$producto->precio2): ?>
                                                    <del
                                                        class="d-block text-center opacity-70">$<?php echo e(number_format(round($producto->precio2,2),2)); ?></del>
                                                    <?php endif; ?>
                                                    <span
                                                        class="d-block text-primary text-center fw-600">$<?php echo e(number_format(round($producto->precio,2),2)); ?></span>
                                                    <?php else: ?>
                                                    <span
                                                        class="d-block text-primary text-center fw-600">$<?php echo e(number_format(round($producto->precio,2),2)); ?></span>
                                                    <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>


<div id="section_featured">

</div>


<div id="section_best_selling">
</div>


<?php if(get_setting('top10_categories')): ?>
<section class="mb-4">
    <div class="container">
        <div class="row gutters-10 ">
            <div class="col-lg-12">
                <div class="d-flex mb-3 align-items-baseline border-bottom">
                    <h3 class="h5 fw-700 mb-0">
                        <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">Top 10
                            <?php echo e(ucfirst(get_setting('grupo_productos'))); ?></span>
                    </h3>
                    <a href="<?php if(get_setting('vista_categorias')==1): ?> <?php echo e(route('categories.all')); ?> <?php else: ?> <?php echo e(route('search')); ?> <?php endif; ?>"
                        class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">Ver
                        todas las
                        <?php echo e(ucfirst(get_setting('grupo_productos'))); ?></a>
                </div>
                <div class="row gutters-5">
                    <?php
                    $top10_categories = get_setting('top10_categories');
                    ?>
                    <?php $__currentLoopData = $top10_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $grupo = get_setting('grupo_productos');
                    $grupoid = getGrupoID();
                    $grupos = "";

                    switch ($grupo) {
                    case 'lineas':
                    $grupos = \App\Models\Lineas::where($grupoid,$value)->first();
                    break;
                    case 'categorias':
                    $grupos = \App\Models\Categorias::where($grupoid,$value)->first();
                    break;
                    case 'subcategorias':
                    $grupos = \App\Models\Subcategorias::where($grupoid,$value)->first();
                    break;
                    case 'subgrupos':
                    $grupos = \App\Models\Subgrupos::where($grupoid,$value)->first();
                    break;
                    default:
                    break;
                    }
                    ?>
                    <?php if($grupos != null): ?>
                    <div class="col-sm-6">
                        <a href="<?php echo e(route('products.category', $grupos->$grupoid)); ?>"
                            class="bg-white border d-block text-reset rounded p-2 hov-shadow-md mb-2">
                            <div class="row align-items-center no-gutters">
                                <div class="col-3 text-center">
                                    <img src="data:image/jpg;base64,<?php echo e(base64_encode($grupos->imagen)); ?>"
                                        onerror="this.onerror=null;this.src='data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>';"
                                        alt="Teconologia" class="img-fluid img lazyload h-60px">
                                </div>
                                <div class="col-7">
                                    <div class="text-truncat-2 pl-3 fs-14 fw-600 text-left">
                                        <?php echo e($grupos->descripcion); ?>

                                    </div>
                                </div>
                                <div class="col-2 text-center">
                                    <i class="la la-angle-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    $(document).ready(function(){
            $.post('<?php echo e(route('home.section.featured')); ?>', {_token:'<?php echo e(csrf_token()); ?>'}, function(data){
                $('#section_featured').html(data);
                AIZ.plugins.slickCarousel();
            });
            $.post('<?php echo e(route('home.section.best_selling')); ?>', {_token:'<?php echo e(csrf_token()); ?>'}, function(data){
                $('#section_best_selling').html(data);
                AIZ.plugins.slickCarousel();
            });

        });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/index.blade.php ENDPATH**/ ?>