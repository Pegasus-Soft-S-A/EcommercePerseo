<?php $__env->startSection('content'); ?>
<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block ">Mi Carrito</h3>
                        </div>
                    </div>
                    <?php if(get_setting('maneja_sucursales') != "on"): ?>
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block ">Información de la Compra</h3>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">Pago</h3>
                        </div>
                    </div>
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-check-circle"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">Confirmación</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-4">
    <div class="container text-left">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body">
                        <div class="text-center py-4 mb-4">
                            <i class="la la-check-circle la-3x text-success mb-3"></i>
                            <h1 class="h3 mb-3 fw-600">Gracias por su pedido</h1>
                            <h2 class="h5">Codigo de Pedido: <span class="fw-700 text-primary"><?php echo e($pedido->pedidos_codigo); ?></span>
                            </h2>
                            <p class="opacity-70 font-italic">
                                Se ha enviado una copia o el resumen de su pedido a
                                <?php echo e(Auth::user()->email_login); ?></p>
                        </div>
                        <div class="mb-4">
                            <h5 class="fw-600 mb-3 fs-17 pb-2">Resumen del pedido</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <td class="w-50 fw-600">Codigo de Orden:</td>
                                            <td><?php echo e($pedido->pedidos_codigo); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Nombre:</td>
                                            <td><?php echo e(Auth::user()->razonsocial); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Email:</td>
                                            <td><?php echo e(Auth::user()->email_login); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Direccion de Envio:</td>
                                            <?php
                                            $direccion=\App\Models\ClientesSucursales::findOrFail($pedido->clientes_sucursalesid);
                                            $ciudad=\App\Models\Ciudades::findOrFail($direccion->ciudadesid);
                                            ?>
                                            <td> <?php echo e($direccion->direccion); ?>,
                                                <?php echo e($ciudad->ciudad); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Destinatario:</td>
                                            <td><?php echo e($destinatario); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <td class="w-50 fw-600">Fecha de Orden:</td>
                                            <td><?php echo e($pedido->emision); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Estado de Orden:</td>
                                            <td>
                                                <?php if($pedido->estado == 1): ?>
                                                Pedido realizado
                                                <?php elseif($pedido->estado == 2): ?>
                                                Pedido Confirmado
                                                <?php elseif($pedido->estado == 3): ?>
                                                Pedido Facturado
                                                <?php elseif($pedido->estado == 4): ?>
                                                En la Entrega
                                                <?php elseif($pedido->estado == 5): ?>
                                                Entregado
                                                <?php elseif($pedido->estado == 0): ?>
                                                No aplica
                                                <?php endif; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">Total Pedido:</td>
                                            <td><?php echo e(number_format(round($pedido->total,2),2)); ?>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Centro Costos:</td>
                                            <td>
                                                <?php echo e($centro_costos->descripcion); ?>

                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h5 class="fw-600 mb-3 fs-17 pb-2">Detalles del Pedido</h5>
                            <div>
                                <table class="table table-responsive-md">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th width="30%">Producto</th>
                                            <th>Medida</th>
                                            <th>Cantidad</th>
                                            <th class="text-right">Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $subtotal =0;
                                        ?>
                                        <?php $__currentLoopData = $detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $orderDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $subtotal = $subtotal + ($orderDetail->precio *
                                        $orderDetail->cantidad);
                                        ?>
                                        <tr>
                                            <td><?php echo e($key+1); ?></td>
                                            <td>
                                                <?php echo e($orderDetail->producto); ?>

                                            </td>
                                            <td>
                                                <?php echo e($orderDetail->medida); ?>

                                            </td>
                                            <td>
                                                <?php echo e(round($orderDetail->cantidaddigitada,2)); ?>

                                            </td>
                                            <td class="text-right">
                                                <?php echo e(number_format(round($orderDetail->preciovisible,2),2)); ?>

                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xl-5 col-md-6 ml-auto mr-0">
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <th>Subtotal</th>
                                                <td class="text-right">
                                                    <span class="fw-600"><?php echo e(number_format(round($subtotal,2),2)); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Descuento</th>
                                                <td class="text-right">
                                                    <span class="fw-600"><?php echo e(number_format(round($pedido->total_descuento,2),2)); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Subtotal Neto</th>
                                                <td class="text-right">
                                                    <span class="fw-600"><?php echo e(number_format(round($pedido->subtotalneto,2),2)); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Total IVA</th>
                                                <td class="text-right">
                                                    <span class="fw-600"><?php echo e(number_format(round($pedido->total_iva,2),2)); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="fw-600">Total</span></th>
                                                <td class="text-right">
                                                    <strong><span><?php echo e(number_format(round($pedido->total,2),2)); ?></span></strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/order_confirmed.blade.php ENDPATH**/ ?>