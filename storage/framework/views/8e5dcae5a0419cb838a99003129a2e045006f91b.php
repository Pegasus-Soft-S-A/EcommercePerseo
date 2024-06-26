<div class="card border-0 shadow-sm rounded">
    <div class="card-header">
        <h3 class="fs-16 fw-600 mb-0">Resumen</h3>
        <div class="text-right">
            <span class="badge badge-inline badge-primary">
                <?php echo e(count($carts)); ?>

                Items
            </span>
        </div>
    </div>

    <div class="card-body">

        <div id="existencias">

        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class="product-name">Producto</th>
                    <th class="product-total text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cartItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="cart_item">
                    <td class="product-name">
                        <?php echo e($cartItem['producto_descripcion']); ?>

                        <strong class="product-quantity">
                            × <?php echo e(round($cartItem['cantidad'],2)); ?>

                        </strong>
                    </td>
                    <td class="product-total text-right">
                        <span class="pl-4 pr-0"><?php echo e(number_format(round( $cartItem['precio_visible'] *
                            $cartItem['cantidad'],2),2)); ?></span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <table class="table">

            <tfoot>
                <tr class="cart-subtotal">
                    <th>Subtotal</th>
                    <td class="text-right">
                        <span class="fw-600"><?php echo e(number_format(round($totales['subtotal'],$parametros->fdv_subtotales),$parametros->fdv_subtotales)); ?></span>
                        <input type="hidden" value="<?php echo e(round($totales['subtotal'],$parametros->fdv_subtotales)); ?>"
                            name="subtotal" id="subtotal">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Descuento</th>
                    <td class="text-right">
                        <span class="fw-600"><?php echo e(number_format(round($totales['descuento'],2),2)); ?></span>
                        <input type="hidden" value="<?php echo e($totales['descuento']); ?>" name="descuento" id="descuento">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Subtotal Neto</th>
                    <td class="text-right">
                        <span class="fw-600"><?php echo e(number_format(round($totales['subtotalNeto'],$parametros->fdv_subtotales),$parametros->fdv_subtotales)); ?></span>
                        <input type="hidden" value="<?php echo e(round($totales['subtotalNeto'],$parametros->fdv_subtotales)); ?>"
                            name="subtotalneto" id="subtotalneto">
                        <input type="hidden"
                            value="<?php echo e(round($totales['subtotalNetoConIva'],$parametros->fdv_subtotales)); ?>"
                            name="subtotalnetoconiva" id="subtotalnetoconiva">
                        <input type="hidden"
                            value="<?php echo e(round($totales['subtotalNetoSinIva'],$parametros->fdv_subtotales)); ?>"
                            name="subtotalnetosiniva" id="subtotalnetosiniva">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>IVA</th>
                    <td class="text-right">
                        <span class="fw-600"><?php echo e(number_format(round($totales['totalIVA'],$parametros->fdv_iva),$parametros->fdv_iva)); ?></span>
                        <input type="hidden" value="<?php echo e(round($totales['totalIVA'],$parametros->fdv_iva)); ?>"
                            name="totalIVA" id="totalIVA">
                        <input type="hidden" value="0" name="inputCero" id="inputCero">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Total</th>
                    <td class="text-right">
                        <span class="fw-600"><?php echo e(number_format(round($totales['total'],2),2)); ?></span>
                        <input type="hidden" value="<?php echo e(round($totales['total'],2)); ?>" name="total" id="total">
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/partials/cart_summary.blade.php ENDPATH**/ ?>