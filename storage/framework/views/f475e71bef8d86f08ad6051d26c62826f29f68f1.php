<?php $__env->startSection('panel_content'); ?>
<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header">
            <div class="col">
                <h5 class="mb-md-0 h6">Historial de Pedidos</h5>
            </div>
            <div class="col-auto">
                <!-- Botón para mostrar/ocultar los filtros -->
                <button type="button" class="btn btn-secondary" id="toggleFilterButton">Filtrar</button>
            </div>
        </div>
        <div class="card-header row gutters-5" id="filterSection" style="display: none;">
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label for="estado">Estado</label>
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
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label for="fecha">Fecha</label>
                    <input type="text" class="aiz-date-range form-control" value="<?php echo e($fecha); ?>" name="fecha"
                        placeholder="Filtrar por Fecha" data-format="DD-MM-Y" data-separator=" a "
                        data-advanced-range="true" autocomplete="off">
                </div>
            </div>
            <?php if(get_setting('maneja_sucursales') == "on"): ?>
            <div class="col-lg-3 ">
                <div class="form-group mb-0">
                    <label for="centrocosto">Centro Costo</label>
                    <select class="form-control aiz-selectpicker" name="centrocostoid" id="centrocostoid">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $centrocostos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $centrocosto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($centrocosto->centros_costosid); ?>"
                            <?php if(isset($centrocostoid)): ?><?php if($centrocostoid==$centrocosto->centros_costosid ): ?> selected <?php endif; ?>
                            <?php endif; ?>><?php echo e($centrocosto->descripcion); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <label for="destinatario">Destinatario</label>
                    <input type="text" class="form-control" id="destinatario" name="destinatario"
                        value="<?php echo e($destinatario); ?>" placeholder="Buscar por destinatario" autocomplete="off">
                </div>
            </div>
            <?php endif; ?>
            <!-- Nueva fila con los botones "Aplicar Filtros" y "Exportar PDF" -->
        </div>
        <div class="card-header row " id="actionButtonsSection" style="display: none;">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                <a href="<?php echo e(route('orders.export.pdf')); ?>" class="btn btn-primary">Exportar PDF</a>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>Codigo</th>
                    <?php if(get_setting('maneja_sucursales') == "on"): ?>
                    <th data-breakpoints="sm">Destinatario</th>
                    <?php endif; ?>
                    <th data-breakpoints="sm">Fecha</th>
                    <th>Total</th>
                    <th data-breakpoints="sm">Estado</th>
                    <th data-breakpoints="sm" class="text-right">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <a href="#" onclick="show_purchase_history_details(<?php echo e($order->pedidosid); ?>)"><?php echo e($order->pedidos_codigo); ?></a>
                    </td>
                    <?php if(get_setting('maneja_sucursales') == "on"): ?>
                    <td><?php echo e($order->destinatario); ?></td>
                    <?php endif; ?>
                    <td><?php echo e($order->emision); ?></td>
                    <td>
                        $<?php echo e(number_format(round($order->total,2),2)); ?>

                    </td>
                    <td>
                        <?php if($order->estado == 1): ?>

                        <span class="badge badge-inline badge-danger">Pedido Realizado</span>

                        <?php elseif($order->estado == 2): ?>

                        <span class="badge badge-inline badge-danger">Pedido Confirmado</span>


                        <?php elseif($order->estado == 3): ?>

                        <span class="badge badge-inline badge-danger"> Pedido Facturado</span>

                        <?php elseif($order->estado == 4): ?>

                        <span class="badge badge-inline badge-danger"> En la Entrega</span>

                        <?php elseif($order->estado == 5): ?>

                        <span class="badge badge-inline badge-danger"> Entregado</span>

                        <?php elseif($order->estado == 0): ?>

                        <span class="badge badge-inline badge-danger">No Aplica</span>

                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <?php if($order->urbano <> ''): ?>
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0)"
                                title="Guia Urbano" onclick="showTracking('<?php echo e($order->urbano ?? ''); ?>')">
                                <i class="las la-thumbtack"></i>
                            </a>
                            <?php endif; ?>
                            <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                onclick="show_purchase_history_details(<?php echo e($order->pedidosid); ?>)"
                                title="Detalles del Pedido">
                                <i class="las la-eye"></i>
                            </a>
                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                href="<?php echo e(route('invoice.download', $order->pedidosid)); ?>" title="Descargar Pedido">
                                <i class="las la-download"></i>
                            </a>
                            <?php if($order->documentosid==0): ?>
                            <a href="javascript:void(0)"
                                class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                data-href="<?php echo e(route('orders.destroy', $order->pedidosid)); ?>" title="Eliminar">
                                <i class="las la-trash"></i>
                            </a>
                            <?php endif; ?>
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

<div class="modal fade" id="modalUrbano">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Urbano</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Estado</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="estadoUrbano" value="" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
    $(document).ready(function() {
        // Botón para mostrar/ocultar los filtros con animación
        $('#toggleFilterButton').on('click', function() {
            $('#filterSection, #actionButtonsSection').toggle(200); // Cambia la visibilidad con una animación de 300ms

            // Cambia el texto del botón entre "Filtrar" y "Ocultar Filtros"
            var buttonText = $(this).text() === 'Filtrar' ? 'Ocultar Filtros' : 'Filtrar';
            $(this).text(buttonText);
        });
    });

    function showTracking(codigo) {
        // Muestra el modal
        $('#modalUrbano').modal();

       // Coloca el mensaje "Cargando..." en el campo de estado mientras espera la respuesta
         $('#estadoUrbano').val('Cargando...');

        // Realiza la petición
        $.post('<?php echo e(route('trackingUrbano')); ?>', {
            _token: '<?php echo e(csrf_token()); ?>',
            codigo: codigo,
        }, function(data) {
            // Oculta el spinner
            $('#loading-spinner').hide();

            // Coloca el estado en el campo una vez recibida la respuesta
            $('#estadoUrbano').val(data.estado);
        }).fail(function() {
            // Oculta el spinner y muestra un mensaje de error si falla
            $('#loading-spinner').hide();
            $('#estado').val('Error al obtener el estado');
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.user_panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/cliente/purchase_history.blade.php ENDPATH**/ ?>