<?php $__env->startSection('content'); ?>
<section class="pt-4 mb-3">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-6 text-center text-lg-left">
                <h1 class="fw-600 h4">Todas las <?php echo e(ucfirst(get_setting('grupo_productos'))); ?></h1>
            </div>
            <div class="col-lg-6">
                <ul class="breadcrumb bg-transparent p-0 justify-content-center justify-content-lg-end">
                    <li class="breadcrumb-item opacity-50">
                        <a class="text-reset" href="<?php echo e(route('home')); ?>">Inicio</a>
                    </li>
                    <li class="text-dark fw-600 breadcrumb-item">
                        <a class="text-reset" href="<?php echo e(route('categories.all')); ?>">Todas las
                            <?php echo e(ucfirst(get_setting('grupo_productos'))); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container">
        <div class="row row-cols-xxl-5 row-cols-xl-4 row-cols-lg-3 row-cols-md-3 row-cols-2">
            <?php $__currentLoopData = grupoProductos(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $grupos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('products.category', $grupos->id)); ?>"
                class="bg-white border text-reset rounded-lg hov-shadow-lg d-block overflow-hidden mt-1">
                <div class="p-2">
                    <div class="row align-items-center no-gutters">
                        <div class="col-4 text-center">
                            <img src="data:image/jpg;base64,<?php echo e(base64_encode($grupos->imagen)); ?>"
                                onerror="this.onerror=null;this.src='data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>';"
                                class="img-fluid img lazyload h-60px">
                        </div>
                        <div class="col-8">
                            <div class="text-truncat-2 pl-3 fs-12 fw-600 text-center">
                                <?php echo e($grupos->descripcion); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/all_category.blade.php ENDPATH**/ ?>