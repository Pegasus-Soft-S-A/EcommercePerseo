<?php $__env->startSection('panel_content'); ?>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">Historial de Facturas</h5>
    </div>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>Secuencial</th>
                    <th data-breakpoints="sm">Fecha</th>
                    <th>Total</th>
                    <th data-breakpoints="sm">Estado</th>
                    <th data-breakpoints="sm" class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <?php
                            $secuencial= $order->establecimiento .' - '.$order->puntoemision .' - '.$order->secuencial;
                            
                        ?>
                        <a href="#"
                            onclick="show_facturas_history_details(<?php echo e($order->facturasid); ?>)"><?php echo e($secuencial); ?></a>
                    </td>
                    <td><?php echo e($order->emision); ?></td>
                    <td>
                        $<?php echo e(number_format(round($order->total,2),2)); ?>

                    </td>
                    <td>

                        <?php if($order->estado == 0): ?>

                        <span class="badge badge-inline badge-danger">Facturado</span>

                        <?php elseif($order->estado == 1): ?>

                        <span class="badge badge-inline" style="background: #377dff; color:white">En la Entrega</span>

                        <?php elseif($order->estado == 2): ?>

                        <span class="badge badge-inline badge-success">Entregado</span>


                        <?php endif; ?>

                    </td>
                    <td class="text-center">
                        <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm"
                            onclick="show_facturas_history_details(<?php echo e($order->facturasid); ?>)"
                            title="Detalles del Pedido">
                            <i class="las la-eye"></i>
                        </a>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="aiz-pagination">
            <?php echo e($orders->appends(request()->input())->links('pagination::bootstrap-4')); ?>

        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
<?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="modal fade" id="factura_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div id="factura-details-modal-body">

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
<?php echo $__env->make('frontend.layouts.user_panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/cliente/facturas_history.blade.php ENDPATH**/ ?>