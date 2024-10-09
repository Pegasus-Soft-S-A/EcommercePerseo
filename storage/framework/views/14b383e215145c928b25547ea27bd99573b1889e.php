<div class="">
    <?php if(count($categories) > 0): ?>
    <div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">
        <?php echo e(get_setting('grupo_productos')); ?> Sugeridas</div>
    <ul class="list-group list-group-raw">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="list-group-item py-1">
            <a class="text-reset hov-text-primary"
                href="<?php echo e(route('products.category', $category->id)); ?>"><?php echo e($category->descripcion); ?></a>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <?php endif; ?>
</div>
<div class="">
    <?php if(count($products) > 0): ?>
    <div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">Productos</div>
    <ul class="list-group list-group-raw">
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="list-group-item">
            <a class="text-reset" href="<?php echo e(route('product', $product->productosid)); ?>">
                <div class="d-flex search-product align-items-center">
                    <div class="mr-3">
                        <img class="size-40px img-fit rounded"
                            src="data:image/jpg;base64,<?php if($product->imagen): ?> <?php echo e(base64_encode($product->imagen)); ?> <?php else: ?> <?php echo e(get_setting('imagen_defecto')); ?> <?php endif; ?>">
                    </div>
                    <div class="flex-grow-1 overflow--hidden minw-0">
                        <div class="product-name text-truncate fs-14 mb-5px">
                            <?php if(get_setting('ver_codigo')==1): ?>
                            <?php echo e($product->productocodigo); ?>-<?php endif; ?><?php echo e($product->descripcion); ?>

                        </div>
                        <div class="">
                            <?php if(Auth::check()): ?>
                            <?php if($product->precio<$product->precio2): ?>
                                <del
                                    class="fw-600  fs-16 opacity-50 mr-1">$<?php echo e(number_format(round($product->precio2,2),2)); ?></del>
                                <?php endif; ?>
                                <span class="fw-600 fs-16 text-primary">$
                                    <?php echo e(number_format(round($product->precio,2),2)); ?></span>
                                <?php else: ?>
                                <?php if(get_setting('tipo_tienda')=='publico'): ?>
                                <span class="fw-600 fs-16 text-primary">$
                                    <?php echo e(number_format(round($product->precio,2),2)); ?></span>
                                <?php endif; ?>
                                <?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <?php endif; ?>
</div><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/partials/search_content.blade.php ENDPATH**/ ?>