<?php $__env->startSection('content'); ?>

<div class="card">
    <!-- Formulario de filtros -->
    <form class="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Pedidos</h5>
            </div>
            <div class="col-auto">
                <!-- Botón para mostrar/ocultar los filtros -->
                <button type="button" class="btn btn-secondary" id="toggleFilterButton">Filtrar</button>
            </div>
            <div class="col-auto">
                <a href="<?php echo e(route('pedidos.crear')); ?>" class="btn btn-primary">Nuevo</a>
            </div>
            
            <div class="col-auto">
                <a onclick="showExcel()" class="btn btn-success text-white">Importar</a>
            </div>
        </div>

        <!-- Sección de filtros, inicialmente oculta -->
        <div class="card-header row " id="filterSection" style="display: none;">
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="estado">Estado</label>
                    <select class="form-control aiz-selectpicker" name="estado" id="estado">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = [
                        1 => 'Realizado',
                        2 => 'Confirmado',
                        3 => 'Facturado',
                        4 => 'En Entrega',
                        5 => 'Entregado',
                        0 => 'No Aplica']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $estado_texto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(isset($estado) && $estado==$key): ?> selected <?php endif; ?>>
                            <?php echo e($estado_texto); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="fecha">Fecha</label>
                    <input type="text" class="aiz-date-range form-control" value="<?php echo e($fecha); ?>" name="fecha"
                        placeholder="Filtrar por Fecha" data-format="DD-MM-Y" data-separator=" a "
                        data-advanced-range="true" autocomplete="off" id="fecha">
                </div>
            </div>
            <?php if(get_setting('maneja_sucursales') == "on"): ?>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="busqueda">Sucursal</label>
                    <?php
                    $sucursales = \App\Models\ClientesSucursales::where('clientesid',
                    get_setting('cliente_pedidos'))->get();
                    ?>
                    <select class="form-control aiz-selectpicker" name="busqueda">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sucursal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sucursal->clientes_sucursalesid); ?>"><?php echo e($sucursal->descripcion); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <?php else: ?>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="busqueda">Búsqueda</label>
                    <input type="text" class="form-control" id="busqueda" name="busqueda" <?php if(isset($busqueda)): ?>
                        value="<?php echo e($busqueda); ?>" <?php endif; ?> placeholder="Buscar por código o cliente" autocomplete="off">
                </div>
            </div>
            <?php endif; ?>
            <?php if(get_setting('maneja_sucursales') == "on"): ?>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="busqueda">Centro Costos</label>
                    <select class="form-control aiz-selectpicker" name="centrocostoid" id="centrocostoid"
                        data-live-search="true">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $centrocostos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $centrocosto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($centrocosto->centros_costosid); ?>"
                            <?php if(isset($centrocostoid)): ?><?php if($centrocostoid==$centrocosto->centros_costosid ): ?> selected <?php endif; ?>
                            <?php endif; ?>><?php echo e($centrocosto->centro_costocodigo); ?>-<?php echo e($centrocosto->descripcion); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="destinatario">Destinatario</label>
                    <input type="text" class="form-control" id="destinatario" name="destinatario" <?php if(isset($destinatario)): ?>
                        value="<?php echo e($destinatario); ?>" <?php endif; ?> placeholder="Buscar por destinatario" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group mb-3">
                    <label for="prioridad">Prioridad</label>
                    <select class="form-control aiz-selectpicker" name="prioridad" id="prioridad"
                        data-live-search="true">
                        <option value="">Todos</option>
                        <option value="1" <?php if(isset($prioridad)): ?><?php if($prioridad==1): ?> selected <?php endif; ?> <?php endif; ?>>Alta Prioridad
                        </option>
                        <option value="0" <?php if(isset($prioridad)): ?><?php if($prioridad==0): ?> selected <?php endif; ?> <?php endif; ?>>Prioridad
                            Normal</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Nueva fila con los botones "Aplicar Filtros" y "Exportar PDF" -->
        <div class="card-header row gutters-5" id="actionButtonsSection" style="display: none;">
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                <a id="exportPdfButton" class="btn btn-primary text-white">Exportar PDF</a>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <?php if(get_setting('maneja_sucursales') == "on"): ?>
                        <th data-breakpoints="sm">Prioridad</th>
                        <th data-breakpoints="sm">Destinatario</th>
                        <?php endif; ?>
                        <th>Codigo</th>
                        <?php if(get_setting('maneja_sucursales') == "on"): ?>
                        <th data-breakpoints="sm">Sucursal</th>
                        <?php else: ?>
                        <th data-breakpoints="sm">Cliente</th>
                        <?php endif; ?>
                        <th data-breakpoints="sm">Emision</th>
                        <th>Total</th>
                        <th data-breakpoints="sm">Estado</th>
                        <th data-breakpoints="sm" class="text-center" width="15%">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $pedidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <?php if(get_setting('maneja_sucursales') == "on"): ?>
                        <td>
                            <?php if($pedido->prioridad == 1): ?>
                            <i class="las la-check-circle la-2x text-danger" title="Alta Prioridad"></i>
                            <?php else: ?>
                            <i class="las la-check-circle la-2x text-muted" title="Prioridad Normal"></i>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($pedido->destinatario); ?></td>
                        <?php endif; ?>

                        <td><?php echo e($pedido->pedidos_codigo); ?></td>
                        <?php if(get_setting('maneja_sucursales') == "on"): ?>
                        <td><?php echo e($pedido->descripcion); ?></td>
                        <?php else: ?>
                        <td><?php echo e($pedido->razonsocial); ?></td>
                        <?php endif; ?>
                        <td><?php echo e($pedido->emision); ?></td>
                        <td>$ <?php echo e(number_format($pedido->total, 2)); ?></td>
                        <td>
                            <?php
                            $estadoLabels = [
                            1 => ['text' => 'Realizado', 'class' => 'danger'],
                            2 => ['text' => 'Confirmado', 'class' => 'primary'],
                            3 => ['text' => 'Facturado', 'class' => 'primary'],
                            4 => ['text' => 'En Entrega', 'class' => 'primary'],
                            5 => ['text' => 'Entregado', 'class' => 'success'],
                            0 => ['text' => 'No Aplica', 'class' => 'danger']
                            ];
                            $estadoLabel = $estadoLabels[$pedido->estado];
                            ?>
                            <span class="badge badge-inline badge-<?php echo e($estadoLabel['class']); ?>"><?php echo e($estadoLabel['text']); ?></span>
                        </td>


                        <td class="text-right">
                            <?php if($pedido->urbano <> ''): ?>
                                <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0)"
                                    title="Guia Urbano" onclick="showTracking('<?php echo e($pedido->urbano ?? ''); ?>')">
                                    <i class="las la-thumbtack"></i>
                                </a>
                                <?php endif; ?>
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="<?php echo e(route('pedidos.show', $pedido->pedidosid)); ?>" title="Ver">
                                    <i class="las la-eye"></i>
                                </a>
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="<?php echo e(route('invoice.download', $pedido->pedidosid)); ?>" title="Descargar">
                                    <i class="las la-download"></i>
                                </a>
                                <?php if($pedido->documentosid == 0): ?>
                                <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                    href="<?php echo e(route('pedidos.editar', $pedido->pedidosid)); ?>" title="Editar">
                                    <i class="las la-pen"></i>
                                </a>
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

<!-- Modal de Carga -->
<div class="modal fade" id="carga">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cargando...</h5>
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
    </div>
</div>


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

<div class="modal fade" id="modalExcel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Importar Excel</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <form id="formExcel" action="<?php echo e(route('pedidos.importarExcel')); ?>" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">Seleccione Excel</label>
                            <div class="col-md-8">
                                <input type="file" name="file" required>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block" type="submit">Importar</button>
                    </form>
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

        $('#exportPdfButton').on('click', function() {
            var params = gatherFilterParams();
            var url = '<?php echo e(route("pedido.export.pdf")); ?>' + (params.length > 0 ? '?' + params.join('&') : '');
            window.location.href = url;
        });

        function gatherFilterParams() {
            var params = [];
            var estado = $('#estado').val();
            var fecha = $('#fecha').val();
            var busqueda = $('#busqueda').val();
            var destinatario = $('#destinatario').val();
            var prioridad = $('#prioridad').val();

            if (estado) params.push('estado=' + encodeURIComponent(estado));
            if (fecha) params.push('fecha=' + encodeURIComponent(fecha));
            if (busqueda) params.push('busqueda=' + encodeURIComponent(busqueda));
            if (destinatario) params.push('destinatario=' + encodeURIComponent(destinatario));
            if (prioridad) params.push('prioridad=' + encodeURIComponent(prioridad));

            return params;
        }

        // Manejo del formulario de Excel
        $('#formExcel').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $('#carga').modal({backdrop: 'static', keyboard: false});
            $('.c-preloader').show();

            $.ajax({
                type: "POST",
                url: "<?php echo e(route('pedidos.importarExcel')); ?>",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#carga').modal('hide'); // Ocultar el modal de carga
                    //$('#modalExcel').modal('hide'); // Ocultar el modal de carga
                    if (response.success) {
                        window.location.reload();
                    } else {
                        AIZ.plugins.notify('error', response.message);
                    }
                },
                error: function() {
                    $('#carga').modal('hide'); // Asegurarse de ocultar el modal si hay un error
                    AIZ.plugins.notify('error', 'Error en la solicitud');
                }
            });
        });

    });

    function showTracking(codigo) {
        $('#modalUrbano').modal();
        $('#estadoUrbano').val('Cargando...');
        $.post('<?php echo e(route('trackingUrbano')); ?>', {
            _token: '<?php echo e(csrf_token()); ?>',
            codigo: codigo,
        }, function(data) {
            $('#estadoUrbano').val(data.estado);
        }).fail(function() {
            $('#estadoUrbano').val('Error al obtener el estado');
        });
    }
    function showExcel() {
        $('#modalExcel').modal();
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/backend/pedidos.blade.php ENDPATH**/ ?>