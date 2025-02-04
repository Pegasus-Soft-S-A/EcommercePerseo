<?php $__env->startSection('content'); ?>

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col">
            <h5 class="mb-md-0 h5">Pedido</h5>
        </div>

    </div>

    <div class="card-body">
        <a href="<?php echo e(route('pedidos.index')); ?>" id="btnVolver"
            class="btn btn-sm btn-secondary mr-2 text-white">Volver</a>
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
                                <td class="w-50 fw-600">Telefono:</td>
                                <td><?php echo e($cliente->telefono1); ?></td>
                            </tr>
                            <?php if(get_setting('maneja_sucursales') == "on"): ?>
                            <tr>
                                <td class="w-50 fw-600">Centro Costo:</td>
                                <td><?php echo e($centrocosto->centro_costocodigo); ?>-<?php echo e($centrocosto->descripcion); ?></td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">Sucursal:</td>
                                <td><?php echo e($sucursal->descripcion); ?></td>
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
                                    <th width="10%">Medida</th>
                                    <th width="15%">Cantidad</th>
                                    <?php if($modificado==true): ?>
                                    <th width="15%">Cantidad Anterior</th>
                                    <?php endif; ?>
                                    <th width="15%">Precio Unitario</th>
                                    <th width="10%">Observacion</th>
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
                                    <td>
                                        <?php if($pedidoDetail->informacion): ?>
                                        <a href="javascript:void(0)"
                                            onclick="showObservationModal('<?php echo e($pedidoDetail->informacion); ?>')"
                                            class="btn btn-icon btn-sm btn-soft-success btn-circle">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <?php endif; ?>
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

<?php $__env->startSection('modal'); ?>
<div class="modal fade" id="modalObservacion">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Observación</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <div class="form-group">
                        <input type="hidden" value="" name="ecommerce_carritosid" id="ecommerce_carritosid">
                        <textarea class="form-control h-auto form-control-lg" placeholder="Observación"
                            name="observacion" id="observacion" autocomplete="off" required rows="4"
                            disabled></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    function showObservationModal(observacion) {
    // Set the value of the observation textarea in the modal
    document.getElementById('observacion').value = observacion;

    // Show the modal
    $('#modalObservacion').modal('show');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/backend/pedidos_show.blade.php ENDPATH**/ ?>