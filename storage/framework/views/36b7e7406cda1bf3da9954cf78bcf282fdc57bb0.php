<div class="aiz-card-box border border-light rounded hov-shadow-md mt-1 mb-2 has-transition bg-white">
    <div class="position-relative">
        <a href="<?php echo e(route('product',$product->productosid)); ?>" class="d-block">
            <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                src="data:image/jpg;base64,<?php if($product->imagen): ?> <?php echo e(base64_encode($product->imagen)); ?> <?php else: ?> <?php echo e(get_setting('imagen_defecto')); ?> <?php endif; ?>"
                alt="<?php echo e($product->descripcion); ?>">
        </a>
        <div class="absolute-top-right aiz-p-hov-icon">
            <a href="javascript:void(0)" onclick="addToWishList(<?php echo e($product->productosid); ?>)" data-toggle="tooltip"
                data-title="" data-placement="left">
                <i class="la la-heart-o"></i>
            </a>
            <?php if(get_setting('tipo_tienda')=='publico'): ?>
            <a href="javascript:void(0)" onclick="showAddToCartModal(<?php echo e($product->productosid); ?>)" data-toggle="tooltip"
                data-title="" data-placement="left">
                <i class="las la-shopping-cart"></i>
            </a>
            <?php else: ?>
            <?php if(auth()->guard()->check()): ?>
            <a href="javascript:void(0)" onclick="showAddToCartModal(<?php echo e($product->productosid); ?>)" data-toggle="tooltip"
                data-title="" data-placement="left">
                <i class="las la-shopping-cart"></i>
            </a>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="p-md-3 p-2 text-left">
        <div class="fs-15">
            <?php if(Auth::check()): ?>
            <?php if($product->precio<$product->precio2): ?>
                <del class="fw-600 opacity-50 mr-1">$<?php echo e(number_format(round($product->precio2,2),2)); ?></del>
                <?php endif; ?>
                <span class="fw-700 text-primary">$<?php echo e(number_format(round($product->precio,2),2)); ?></span>
                <?php else: ?>
                <?php if(get_setting('tipo_tienda')=='publico'): ?>
                <span class="fw-700 text-primary">$<?php echo e(number_format(round($product->precio,2),2)); ?></span>
                <?php endif; ?>
                <?php endif; ?>
        </div>
        <div class="rating rating-sm mt-1">
            <?php echo e(renderStarRating(json_decode($product->parametros_json)->rating)); ?>

        </div>
        <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
            <a href="<?php echo e(route('product',$product->productosid)); ?>"
                class="d-block text-reset"><?php if(get_setting('ver_codigo')==1): ?> <?php echo e($product->productocodigo); ?>-<?php endif; ?>
                <?php echo e($product->descripcion); ?></a>
        </h3>


    </div>
</div><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/partials/product_box_1.blade.php ENDPATH**/ ?>