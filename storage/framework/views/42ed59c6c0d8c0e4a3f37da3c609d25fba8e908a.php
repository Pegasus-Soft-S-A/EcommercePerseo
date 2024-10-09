<?php $__env->startSection('content'); ?>

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col">
            <h5 class="mb-md-0 h5">Pedido</h5>
        </div>
        <div class="col-lg-2 ml-auto">
            <h5 class="mb-md-0 h6">Actualizar Estado</h5>
        </div>
        <div class="col-lg-3 ml-auto">
            <select class="form-control aiz-selectpicker" name="estado" id="estado">
                <option value="1" <?php if(isset($pedido->estado)): ?><?php if($pedido->estado==1 ): ?> selected <?php endif; ?> <?php endif; ?>>Realizado
                </option>
                <option value="2" <?php if(isset($pedido->estado)): ?><?php if($pedido->estado==2 ): ?> selected <?php endif; ?> <?php endif; ?>>Confirmado
                </option>
                <option value="3" <?php if(isset($pedido->estado)): ?><?php if($pedido->estado==3 ): ?> selected <?php endif; ?> <?php endif; ?>>Facturado
                </option>
                <option value="4" <?php if(isset($pedido->estado)): ?><?php if($pedido->estado==4 ): ?> selected <?php endif; ?> <?php endif; ?>>En Entrega
                </option>
                <option value="5" <?php if(isset($pedido->estado)): ?><?php if($pedido->estado==5 ): ?> selected <?php endif; ?> <?php endif; ?>>Entregado
                </option>
                <option value="6" <?php if(isset($pedido->estado)): ?><?php if($pedido->estado==6 ): ?> selected <?php endif; ?> <?php endif; ?>>No Aplica
                </option>
            </select>
        </div>
    </div>

    <div class="card-body">

        <div class="card mt-4">
            <div class="card-header">
                <b class="fs-15">Resumen del Pedido</b>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="w-50 fw-600">Codigo:</td>
                                <td><?php echo e($pedido->pedidos_codigo); ?></td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Cliente:</td>
                                <td><?php echo e($cliente->razonsocial); ?></td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Email:</td>
                                <td><?php echo e($cliente->email_login); ?></td>
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
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="w-50 fw-600">Fecha:</td>
                                <td><?php echo e($pedido->emision); ?></td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Estado:</td>
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
                                <td class="w-50 fw-600">Total:</td>
                                <td>$ <?php echo e(number_format(round($pedido->total,2),2)); ?></td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Telefono:</td>
                                <td><?php echo e($cliente->telefono1); ?></td>
                            </tr>

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
                                    <th width="15%">Precio Unitario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subtotal =0;
                                ?>
                                <?php $__currentLoopData = $detalle; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pedidoDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                    <span class="strong-600">$
                                        <?php echo e(number_format(round(($pedido->subtotalneto),2),2)); ?></span>
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
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    $('#estado').on('change', function() {
            var order_id = <?php echo e($pedido->pedidosid); ?>;
            var status = $('#estado').val();
            $.post('<?php echo e(route('pedido.actualizarestado')); ?>', {
                _token: '<?php echo e(@csrf_token()); ?>',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', 'Estado Actualizado Correctamente');
                setTimeout(esperar, 2500);
            });
        });
        function esperar(){
            location.reload();
        }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/backend/pedidos_show.blade.php ENDPATH**/ ?>