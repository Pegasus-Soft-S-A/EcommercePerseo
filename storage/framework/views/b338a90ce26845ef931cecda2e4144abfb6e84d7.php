<?php $__env->startSection('content'); ?>

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Pedidos</h5>
            </div>
            <div class="col-lg-3 ml-auto">
                <select class="form-control aiz-selectpicker" name="estado" id="estado">
                    <option value="">Todos</option>
                    <option value="1" <?php if(isset($estado)): ?><?php if($estado==1 ): ?> selected <?php endif; ?> <?php endif; ?>>Realizado</option>
                    <option value="2" <?php if(isset($estado)): ?><?php if($estado==2 ): ?> selected <?php endif; ?> <?php endif; ?>>Confirmado</option>
                    <option value="3" <?php if(isset($estado)): ?><?php if($estado==3 ): ?> selected <?php endif; ?> <?php endif; ?>>Facturado</option>
                    <option value="4" <?php if(isset($estado)): ?><?php if($estado==4 ): ?> selected <?php endif; ?> <?php endif; ?>>En Entrega</option>
                    <option value="5" <?php if(isset($estado)): ?><?php if($estado==5 ): ?> selected <?php endif; ?> <?php endif; ?>>Entregado</option>
                    <option value="6" <?php if(isset($estado)): ?><?php if($estado==0 ): ?> selected <?php endif; ?> <?php endif; ?>>No Aplica</option>
                </select>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="aiz-date-range form-control" value="<?php echo e($fecha); ?>" name="fecha"
                        placeholder="Filtrar por Fecha" data-format="DD-MM-Y" data-separator=" a "
                        data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="busqueda" name="busqueda" <?php if(isset($busqueda)): ?>
                        value="<?php echo e($busqueda); ?>" <?php endif; ?> placeholder="Buscar por codigo o cliente" autocomplete="off">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th data-breakpoints="sm">Cliente</th>
                        <th>Total</th>
                        <th data-breakpoints="sm">Estado</th>
                        <th data-breakpoints="sm" class="text-center" width="15%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $pedidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <?php echo e($pedido->pedidos_codigo); ?>

                        </td>
                        <td>
                            <?php echo e($pedido->razonsocial); ?>

                        </td>
                        <td>
                            $ <?php echo e(number_format($pedido->total,2)); ?>

                        </td>
                        <td>
                            <?php if($pedido->estado == 1): ?>
                            <span class="badge badge-inline badge-danger">Realizado</span>
                            <?php elseif($pedido->estado == 2): ?>
                            <span class="badge badge-inline badge-primary">Confirmado</span>
                            <?php elseif($pedido->estado == 3): ?>
                            <span class="badge badge-inline badge-primary">Facturado</span>
                            <?php elseif($pedido->estado == 4): ?>
                            <span class="badge badge-inline badge-primary">En Entrega</span>
                            <?php elseif($pedido->estado == 5): ?>
                            <span class="badge badge-inline badge-success">Entregado</span>
                            <?php elseif($pedido->estado == 0): ?>
                            <span class="badge badge-inline badge-danger">No Aplica</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="<?php echo e(route('pedidos.show', $pedido->pedidosid)); ?>" title="Ver">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                href="<?php echo e(route('invoice.download', $pedido->pedidosid)); ?>" title="Descargar">
                                <i class="las la-download"></i>
                            </a>
                            <?php if($pedido->documentosid==0): ?>
                            <a href="javascript:void(0)"
                                class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                data-href="<?php echo e(route('orders.destroy', $pedido->pedidosid)); ?>" title="Eliminar">
                                <i class="las la-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div class="aiz-pagination">
                <?php echo e($pedidos->appends(request()->input())->links('pagination::bootstrap-4')); ?>

            </div>


        </div>
    </form>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
<?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div id="order-details-modal-body">

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div id="payment_modal_body">

            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/backend/pedidos.blade.php ENDPATH**/ ?>