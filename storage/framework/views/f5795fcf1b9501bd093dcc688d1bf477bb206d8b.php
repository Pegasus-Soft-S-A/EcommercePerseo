<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Pedido: <?php echo e($pedido->pedidos_codigo); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>


<div class="modal-body gry-bg px-3 pt-3">
    <div class="py-4">
        <div class="row gutters-5 text-center aiz-steps">
            <div class="col done">
                <div class="icon">
                    <i class="las la-file-invoice"></i>
                </div>
                <div class="title fs-12">Pedido Realizado</div>
            </div>
            <div
                class="col <?php if($pedido->estado == 2  || $pedido->estado == 3 || $pedido->estado == 4 || $pedido->estado == 5 ): ?> active else   <?php endif; ?>">
                <div class="icon">
                    <i class="las la-file"></i>
                </div>
                <div class="title fs-12">Confirmado</div>
            </div>
            <div
                class="col <?php if($pedido->estado == 3 || $pedido->estado == 4 || $pedido->estado == 5 ): ?> active <?php else: ?>  <?php endif; ?>">
                <div class="icon">
                    <i class="las la-newspaper"></i>
                </div>
                <div class="title fs-12">Facturado</div>
            </div>
            <div class="col <?php if($pedido->estado == 4 || $pedido->estado == 5): ?> active <?php else: ?> <?php endif; ?>">
                <div class="icon">
                    <i class="las la-truck"></i>
                </div>
                <div class="title fs-12">En la Entrega</div>
            </div>
            <div class="col <?php if($pedido->estado == 5): ?> active <?php else: ?> <?php endif; ?>">
                <div class="icon">
                    <i class="las la-clipboard-check"></i>
                </div>
                <div class="title fs-12">Entregado</div>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header">
            <b class="fs-15">Resumen del Pedido</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">Codigo del Pedido:</td>
                            <td><?php echo e($pedido->pedidos_codigo); ?></td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Cliente:</td>
                            <td><?php echo e($cliente->razonsocial); ?></td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Email:</td>
                            <td><?php echo e($cliente->email); ?></td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Direccion:</td>
                            <?php
                            $direccion=\App\Models\ClientesSucursales::where('clientes_sucursalesid',$pedido->clientes_sucursalesid)->first();
                            ?>
                            <?php if($direccion!=null): ?>
                            <?php
                            $ciudad=\App\Models\Ciudades::findOrFail($direccion->ciudadesid);
                            ?>
                            <td> <?php echo e($direccion->direccion); ?>,
                                <?php echo e($ciudad->ciudad); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php if(get_setting('maneja_sucursales') == "on"): ?>
                        <tr>
                            <td class="w-50 fw-600">Destinatario:</td>
                            <td><?php echo e($destinatario); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">Fecha del Pedido:</td>
                            <td><?php echo e($pedido->emision); ?></td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Estado del Pedido:</td>
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
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Total del Pedido:</td>
                            <td>$ <?php echo e(number_format(round($pedido->total,2),2)); ?></td>
                        </tr>
                        <?php if(get_setting('maneja_sucursales') == "on"): ?>
                        <tr>
                            <td class="w-50 fw-600">Centro Costo:</td>
                            <td><?php echo e($centrocosto->descripcion); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 text-center">
            <div class="card mt-4">
                <div class="card-header">
                    <b class="fs-15">Detalles del Pedido</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-borderless table-responsive text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th width="50%">Producto</th>
                                <th width="20%">Medida</th>
                                <th width="15%">Cantidad</th>
                                <?php if($modificado==true): ?>
                                <th width="15%">Cantidad Anterior</th>
                                <?php endif; ?>
                                <th width="15%">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subtotal =0;
                            ?>
                            <?php $__currentLoopData = $detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pedidoDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($key+1); ?></td>
                                <?php
                                $producto=App\Models\Producto::select('descripcion','productocodigo')->where('productosid',$pedidoDetail->productosid)->first();
                                $medida=App\Models\Medidas::select('descripcion')->where('medidasid',$pedidoDetail->medidasid)->first();
                                $subtotal = $subtotal + ($pedidoDetail->precio *
                                $pedidoDetail->cantidad);
                                ?>

                                <td>
                                    <a href="<?php echo e(route('product', $pedidoDetail->productosid)); ?>"
                                        target="_blank"><?php echo e($producto->descripcion); ?></a>
                                </td>

                                <td><?php echo e($medida->descripcion); ?></td>

                                <td>
                                    <?php echo e(number_format(round($pedidoDetail->cantidaddigitada,2),2)); ?>

                                </td>
                                <?php if($modificado==true): ?>
                                <td>
                                    <?php if($pedidoDetail->cantidadentregada<>0): ?>
                                        <?php echo e(number_format(round($pedidoDetail->cantidadentregada,2),2)); ?>

                                        <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td>
                                    $ <?php echo e(number_format(round($pedidoDetail->preciovisible,2),2)); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 float-right">

        <div class="card mt-4 ">
            <div class="card-header">
                <b class="fs-15">Total del Pedido</b>
            </div>
            <div class="card-body pb-0">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td class="w-50 fw-600">Subtotal</td>
                            <td class="text-right">
                                <span class="strong-600">$ <?php echo e(number_format(round($subtotal,2),2)); ?> </span>


                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">Descuento</td>
                            <td class="text-right">
                                <span class="strong-600">$
                                    <?php echo e(number_format(round(($pedido->total_descuento),2),2)); ?></span>
                            </td>
                        </tr>
                        <tr>

                        <tr>
                            <td class="w-50 fw-600">Subtotal Neto</td>
                            <td class="text-right">
                                <span class="strong-600">$ <?php echo e(number_format(round(($pedido->subtotalneto),2),2)); ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td class="w-50 fw-600">IVA</td>
                            <td class="text-right">
                                <span class="strong-600">$ <?php echo e(number_format(round(($pedido->total_iva),2),2)); ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td class="w-50 fw-600">VALOR TOTAL</td>
                            <td class="text-right">
                                <strong><span>$ <?php echo e(number_format(round(($pedido->total),2),2)); ?></span></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\tienda\resources\views/frontend/cliente/order_details_customer.blade.php ENDPATH**/ ?>