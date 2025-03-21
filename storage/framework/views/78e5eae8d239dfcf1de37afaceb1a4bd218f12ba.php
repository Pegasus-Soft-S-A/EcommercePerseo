<form class="form-default" role="form" action="<?php echo e(route('addresses.update', $address_data->clientes_sucursalesid)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class="p-3">
        <div class="row">
            <div class="col-md-2">
                <label>Descripción</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="Casa, Trabajo, etc." value="<?php echo e($address_data->descripcion); ?>"
                    name="descripcion" autocomplete="off" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <label>Provincia</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="provinciasid" id="edit_provinciasid" required>
                    <option value="">Seleccione Provincia</option>
                    <?php $__currentLoopData = $provincias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provincia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($provincia->provinciasid); ?>" <?php if($provincia->provinciasid == $address_data->provinciasid): ?> selected <?php endif; ?>><?php echo e($provincia->provincia); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Ciudad</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="ciudadesid" id="edit_ciudadesid" required>
                    <option value="">Seleccione Ciudad</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Parroquias</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="parroquiasid" id="edit_parroquiasid" required>
                    <option value="">Seleccione Parroquia</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Direccion</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3" placeholder="Su Direccion" rows="2" name="direccion" onkeydown="controlar(event)" required><?php echo e($address_data->direccion); ?></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Telefono</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="9999999999" value="<?php echo e($address_data->telefono1); ?>" name="telefono"
                    required>
            </div>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
    </div>

    <input type="hidden" id="edit_provincia_inicial" value="<?php echo e($address_data->provinciasid); ?>">
    <input type="hidden" id="edit_ciudad_inicial" value="<?php echo e($address_data->ciudadesid); ?>">
    <input type="hidden" id="edit_parroquia_inicial" value="<?php echo e($address_data->parroquiasid); ?>">
</form>

<script>
    // Este script se ejecutará cuando el formulario se cargue en el modal
    $(document).ready(function() {
        // Inicializar los selectores en el formulario de edición
        initEditFormSelectors();
    });
</script>
<?php /**PATH C:\laragon\www\tienda\resources\views/frontend/edit_address_modal.blade.php ENDPATH**/ ?>