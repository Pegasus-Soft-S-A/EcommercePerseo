<div class="modal-body p-4 added-to-cart">
    <div class="text-center text-success mb-4">
        <i class="las la-check-circle la-3x"></i>
        <h3>Item añadido al carrito</h3>
    </div>
    <div class="media mb-4">
        <?php if($imagenProducto): ?>
        <img src="data:image/jpg;base64,<?php echo e(base64_encode($imagenProducto->imagen)); ?>" data-src=""
            class="mr-3 lazyload size-100px img-fit rounded" alt="">
        <?php else: ?>
        <img src="data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>" data-src=""
            class="mr-3 lazyload size-100px img-fit rounded" alt="">
        <?php endif; ?>

        <div class="media-body pt-3 text-left">
            <h6 class="fw-600">
                <?php echo e($product->descripcion); ?>

            </h6>
            <div class="row mt-3">
                <div class="col-sm-2 opacity-60">
                    <div>Precio:</div>
                </div>
                <div class="col-sm-10">
                    <div class="h6 text-primary">
                        <?php if(\App\Models\ParametrosEmpresa::first()->tipopresentacionprecios == 1): ?>
                        <strong>
                            $ <?php echo e(number_format(round(($data['precioiva'] * $data['cantidad']),2),2)); ?>

                        </strong>
                        <?php else: ?>
                        <strong>
                            $ <?php echo e(number_format(round(($data['precio'] * $data['cantidad']),2),2)); ?>

                        </strong>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button class="btn btn-outline-primary mb-3 mb-sm-0" data-dismiss="modal">Seguir Comprando</button>
        <a href="<?php echo e(route('cart')); ?>" class="btn btn-primary mb-3 mb-sm-0">Ir al Carrito</a>
    </div>
</div><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/partials/addedToCart.blade.php ENDPATH**/ ?>