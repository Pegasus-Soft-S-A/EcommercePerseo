<form class="form-default" role="form" action="<?php echo e(route('addresses.update', $address_data->clientes_sucursalesid)); ?>"
    method="POST">
    <?php echo csrf_field(); ?>
    <div class="p-3">
        <div class="row">
            <div class="col-md-2">
                <label>Descripción</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="Casa, Trabajo, etc."
                    value="<?php echo e($address_data->descripcion); ?>" name="descripcion" value="" autocomplete="off" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <label>Provincia</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="provinciasid"
                    id="provinciasid" required>
                    <option value="">Seleccione Provincia</option>
                    <?php $__currentLoopData = $provincias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provincia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($provincia->provinciasid); ?>" <?php if($provincia->
                        provinciasid==$address_data->provinciasid): ?> selected <?php endif; ?>><?php echo e($provincia->provincia); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Ciudad</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="ciudadesid"
                    id="ciudadesid" required>
                    <option value="">Seleccione Ciudad</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Parroquias</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="parroquiasid"
                    id="parroquiasid" required>
                    <option value="">Seleccione Parroquia</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Direccion</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3" placeholder="Su Direccion" rows="2" name="direccion"
                    onkeydown="controlar(event)" required><?php echo e($address_data->direccion); ?> </textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Telefono</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="9999999999"
                    value="<?php echo e($address_data->telefono1); ?>" name="telefono" value="" required>
            </div>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
    </div>
</form>
<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    function controlar(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            return false;
        }
    }

    $(document).ready(function() {
         // Obtener los segmentos de la URL
         var pathSegments = window.location.pathname.split('/').filter(segment => segment !== "");

        // Verificar si "tienda" está en la URL
        var tieneTienda = pathSegments.includes("tienda");

        // Buscar el primer número en la URL (ID de la empresa)
        var empresaSegment = pathSegments.find(segment => !isNaN(segment));

        // Construir la URL base dinámicamente
        var baseURL = tieneTienda ? '/tienda/' + empresaSegment : '/' + empresaSegment;

        // Valores iniciales para edición
        console.log('<?php echo e($address_data); ?>');
        var provinciaInicial = '<?php echo e($address_data->provinciasid); ?>';
        var ciudadInicial = '<?php echo e($address_data->ciudadesid); ?>';
        var parroquiaInicial = '<?php echo e($address_data->parroquiasid); ?>';

        // Función para cargar ciudades
        function cargarCiudades(provinciaId, ciudadSeleccionada = null) {
            if(provinciaId) {
                $.ajax({
                    url: baseUrl + '/obtener-ciudades/' + provinciaId,
                    type: 'GET',
                    success: function(data) {
                        $('#ciudadesid').empty();
                        $('#ciudadesid').append('<option value="">Seleccione Ciudad</option>');

                        $.each(data, function(key, value) {
                            var selected = (ciudadSeleccionada && ciudadSeleccionada == value.ciudadesid) ? 'selected' : '';
                            $('#ciudadesid').append('<option value="' + value.ciudadesid + '" ' + selected + '>' + value.ciudad + '</option>');
                        });

                        $('#ciudadesid').prop('disabled', false);
                        $('.aiz-selectpicker').selectpicker('refresh');

                        // Si hay una ciudad seleccionada, cargar sus parroquias
                        if(ciudadSeleccionada) {
                            cargarParroquias(ciudadSeleccionada, parroquiaInicial);
                        }
                    }
                });
            }
        }

        // Función para cargar parroquias
        function cargarParroquias(ciudadId, parroquiaSeleccionada = null) {
            if(ciudadId) {
                $.ajax({
                    url: baseUrl + '/obtener-parroquias/' + ciudadId,
                    type: 'GET',
                    success: function(data) {
                        $('#parroquiasid').empty();
                        $('#parroquiasid').append('<option value="">Seleccione Parroquia</option>');

                        $.each(data, function(key, value) {
                            var selected = (parroquiaSeleccionada && parroquiaSeleccionada == value.parroquiasid) ? 'selected' : '';
                            $('#parroquiasid').append('<option value="' + value.parroquiasid + '" ' + selected + '>' + value.parroquia + '</option>');
                        });

                        $('#parroquiasid').prop('disabled', false);
                        $('.aiz-selectpicker').selectpicker('refresh');
                    }
                });
            }
        }

        // Cargar datos iniciales si estamos en modo edición
        if(provinciaInicial) {
            cargarCiudades(provinciaInicial, ciudadInicial);
        }

        // Event listeners para cambios
        $('#provinciasid').change(function() {
            var provinciaId = $(this).val();
            $('#ciudadesid').prop('disabled', true);
            $('#parroquiasid').prop('disabled', true);

            if(provinciaId) {
                cargarCiudades(provinciaId);
            }
        });

        $('#ciudadesid').change(function() {
            var ciudadId = $(this).val();
            $('#parroquiasid').prop('disabled', true);

            if(ciudadId) {
                cargarParroquias(ciudadId);
            }
        });
    });
</script>
<?php $__env->stopSection(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/edit_address_modal.blade.php ENDPATH**/ ?>