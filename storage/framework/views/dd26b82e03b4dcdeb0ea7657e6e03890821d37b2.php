<?php $__env->startSection('content'); ?>

    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="row aiz-steps arrow-divider">
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-shopping-cart"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block ">1. Mi Carrito</h3>
                            </div>
                        </div>
                        <div class="col active">
                            <div class="text-center text-primary">
                                <i class="la-3x mb-2 las la-map"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block ">2. Información de la Compra</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">3. Pago</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">4. Confirmación</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4 gry-bg">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-xxl-8 col-xl-10 mx-auto">
                    <form class="form-default" data-toggle="validator" action="<?php echo e(route('checkout.store_shipping_infostore')); ?>" role="form"
                        method="POST">
                        <?php echo csrf_field(); ?>
                        <?php if(Auth::check()): ?>
                            <div class="shadow-sm bg-white p-4 rounded mb-4">
                                <div class="row gutters-5">

                                    <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6 mb-3">
                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                <input type="radio" name="clientes_sucursalesid" value="<?php echo e($address->clientes_sucursalesid); ?>"
                                                    <?php if($key == 0): ?> checked <?php endif; ?> required>
                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                    <span class="flex-grow-1 pl-3 text-left">
                                                        <?php if($address->descripcion != ''): ?>
                                                            <div>
                                                                <span class="opacity-60">Descripción:</span>
                                                                <span class="fw-600 ml-2"><?php echo e($address->descripcion); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div>
                                                            <span class="opacity-60">Direccion:</span>
                                                            <span class="fw-600 ml-2"><?php echo e($address->direccion); ?></span>
                                                        </div>
                                                        <div>
                                                            <?php
                                                                $direccion = \App\Models\ClientesSucursales::findOrFail(
                                                                    $address->clientes_sucursalesid,
                                                                );
                                                                $ciudad = \App\Models\Ciudades::findOrFail($direccion->ciudadesid);
                                                            ?>
                                                            <span class="opacity-60">Ciudad:</span>
                                                            <span class="fw-600 ml-2"><?php echo e($ciudad->ciudad); ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="opacity-60">Telefono:</span>
                                                            <span class="fw-600 ml-2"><?php echo e($address->telefono1); ?></span>
                                                        </div>
                                                    </span>
                                                </span>
                                            </label>
                                            <?php if(get_setting('maneja_sucursales') != 'on'): ?>
                                                <div class="dropdown position-absolute right-0 top-0">
                                                    <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                                        <i class="la la-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" onclick="edit_address('<?php echo e($address->clientes_sucursalesid); ?>')">
                                                            Editar
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="checkout_type" value="logged">
                                    <?php if(get_setting('maneja_sucursales') != 'on'): ?>
                                        <div class="col-md-6 mx-auto mb-3">
                                            <div class="border  rounded mb-3 c-pointer text-center bg-white h-100 d-flex flex-column justify-content-center"
                                                onclick="add_new_address()">
                                                <i class="las la-plus la-2x mb-3"></i>
                                                <div class="alpha-7">Agregar Nueva Direccion</div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                                <a href="<?php echo e(route('home')); ?>" class="btn btn-link">
                                    <i class="las la-arrow-left"></i>
                                    Regresar a la tienda
                                </a>
                            </div>
                            <div class="col-md-6 text-center text-md-right">
                                <button type="submit" class="btn btn-primary fw-600">Continuar con la informacion de
                                    envio</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <div class="modal fade" id="new-address-modal">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel">Nueva Direccion</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" role="form" action="<?php echo e(route('addresses.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>Descripción</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="Casa, Trabajo, etc." name="descripcion" value=""
                                        autocomplete="off" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>Provincia</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="provinciasid" id="provinciasid"
                                        required>
                                        <option value="">Seleccione Provincia</option>
                                        <?php $__currentLoopData = $provincias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provincia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($provincia->provinciasid); ?>"><?php echo e($provincia->provincia); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>Ciudad</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="ciudadesid" id="ciudadesid"
                                        required disabled>
                                        <option value="">Seleccione Ciudad</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>Parroquias</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="parroquiasid" id="parroquiasid"
                                        required disabled>
                                        <option value="">Seleccione Parroquia</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>Direccion</label>
                                </div>
                                <div class="col-md-10">
                                    <textarea class="form-control textarea-autogrow mb-3" placeholder="Su Direccion" rows="1" name="direccion" onkeydown="controlar(event)"
                                        required></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>Telefono</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="999999999" name="telefono" value="" required
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-address-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Editar Direccion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="edit_modal_body">

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        // Función simplificada para formatear IDs según su tipo
        function formatearId(id, tipo) {
            if (!id) return "";

            // Determinar la longitud requerida según el tipo
            let longitudRequerida = 2; // Por defecto 2 dígitos (provincia)

            if (tipo === 'ciudad') {
                longitudRequerida = 4;
            } else if (tipo === 'parroquia') {
                longitudRequerida = 6;
            }

            // Convertir a string y aplicar padding
            return id.toString().padStart(longitudRequerida, '0');
        }

        // Función para controlar el Enter en textareas
        function controlar(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                return false;
            }
        }

        // Función para inicializar el formulario de edición de direcciones
        function initEditFormSelectors() {
            // Obtener los segmentos de la URL
            var pathSegments = window.location.pathname.split('/').filter(segment => segment !== "");
            var tieneTienda = pathSegments.includes("tienda");
            var empresaSegment = pathSegments.find(segment => !isNaN(segment));
            var baseURL = tieneTienda ? '/tienda/' + empresaSegment : '/' + empresaSegment;


            // Valores iniciales para edición (desde los campos ocultos)
            var provinciaInicial = $('#edit_provincia_inicial').val();
            var ciudadInicial = $('#edit_ciudad_inicial').val();
            var parroquiaInicial = $('#edit_parroquia_inicial').val();

            // Formatear los valores iniciales
            provinciaInicial = formatearId(provinciaInicial, 'provincia');
            ciudadInicial = formatearId(ciudadInicial, 'ciudad');
            parroquiaInicial = formatearId(parroquiaInicial, 'parroquia');

            // Asegurarse de que la provincia correcta esté seleccionada
            $("#edit_provinciasid option").each(function() {
                var optionValue = formatearId($(this).val());
                if (optionValue === provinciaInicial) {
                    $(this).prop('selected', true);
                }
            });

            // Actualizar el selector visual
            $('.aiz-selectpicker').selectpicker('refresh');

            // Función para cargar ciudades
            function cargarCiudadesEdicion(provinciaId) {
                return new Promise(function(resolve, reject) {
                    // Formatear provinciaId a 2 dígitos
                    provinciaId = formatearId(provinciaId, 'provincia');

                    if (provinciaId) {

                        $.ajax({
                            url: baseURL + '/obtener-ciudades/' + provinciaId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#edit_ciudadesid').empty();
                                $('#edit_ciudadesid').append('<option value="">Seleccione Ciudad</option>');


                                $.each(data, function(key, value) {
                                    var formattedId = formatearId(value.ciudadesid, 'ciudad');
                                    var selected = (ciudadInicial && ciudadInicial == formattedId) ? 'selected' : '';

                                    $('#edit_ciudadesid').append('<option value="' + formattedId + '" ' + selected + '>' +
                                        value.ciudad + '</option>');
                                });

                                $('#edit_ciudadesid').prop('disabled', false);
                                $('.aiz-selectpicker').selectpicker('refresh');

                                resolve(true);
                            },
                            error: function(error) {
                                console.error("Error al cargar ciudades:", error);
                                reject(error);
                            }
                        });
                    } else {
                        resolve(false);
                    }
                });
            }

            // Función para cargar parroquias
            function cargarParroquiasEdicion(ciudadId) {
                return new Promise(function(resolve, reject) {
                    // Formatear ciudadId a 2 dígitos
                    ciudadId = formatearId(ciudadId, 'ciudad');

                    if (ciudadId) {

                        $.ajax({
                            url: baseURL + '/obtener-parroquias/' + ciudadId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#edit_parroquiasid').empty();
                                $('#edit_parroquiasid').append('<option value="">Seleccione Parroquia</option>');


                                $.each(data, function(key, value) {
                                    var formattedId = formatearId(value.parroquiasid, 'parroquia');
                                    var selected = (parroquiaInicial && parroquiaInicial == formattedId) ? 'selected' : '';

                                    $('#edit_parroquiasid').append('<option value="' + formattedId + '" ' + selected +
                                        '>' + value.parroquia + '</option>');
                                });

                                $('#edit_parroquiasid').prop('disabled', false);
                                $('.aiz-selectpicker').selectpicker('refresh');

                                resolve(true);
                            },
                            error: function(error) {
                                console.error("Error al cargar parroquias:", error);
                                reject(error);
                            }
                        });
                    } else {
                        resolve(false);
                    }
                });
            }

            // Cargar las ciudades y parroquias iniciales
            if (provinciaInicial) {
                cargarCiudadesEdicion(provinciaInicial).then(function(result) {
                    if (result && ciudadInicial) {
                        return cargarParroquiasEdicion(ciudadInicial);
                    }
                }).catch(function(error) {
                    console.error("Error en la carga inicial de edición:", error);
                });
            }

            // Event listeners para cambios en el formulario de edición
            $('#edit_provinciasid').change(function() {
                var provinciaId = $(this).val();

                // Limpiar y deshabilitar los selectores dependientes
                $('#edit_ciudadesid').empty().prop('disabled', true).append('<option value="">Seleccione Ciudad</option>');
                $('#edit_parroquiasid').empty().prop('disabled', true).append('<option value="">Seleccione Parroquia</option>');
                $('.aiz-selectpicker').selectpicker('refresh');

                if (provinciaId) {
                    cargarCiudadesEdicion(provinciaId);
                }
            });

            $('#edit_ciudadesid').change(function() {
                var ciudadId = $(this).val();

                // Limpiar y deshabilitar el selector de parroquias
                $('#edit_parroquiasid').empty().prop('disabled', true).append('<option value="">Seleccione Parroquia</option>');
                $('.aiz-selectpicker').selectpicker('refresh');

                if (ciudadId) {
                    cargarParroquiasEdicion(ciudadId);
                }
            });
        }

        // Función para editar dirección
        function edit_address(address) {
            var url = '<?php echo e(route('addresses.edit', 'clientes_sucursalesid')); ?>';
            url = url.replace('clientes_sucursalesid', address);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#edit_modal_body').html(response);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');
                    // La inicialización se ejecutará automáticamente por el script incluido en el formulario
                }
            });
        }

        // Función para añadir nueva dirección
        function add_new_address() {
            $('#new-address-modal').modal('show');
        }

        // Cuando el documento está listo
        $(document).ready(function() {
            // Obtener los segmentos de la URL para el formulario principal
            var pathSegments = window.location.pathname.split('/').filter(segment => segment !== "");
            var tieneTienda = pathSegments.includes("tienda");
            var empresaSegment = pathSegments.find(segment => !isNaN(segment));
            var baseURL = tieneTienda ? '/tienda/' + empresaSegment : '/' + empresaSegment;

            // Cuando cambia la provincia en el formulario de nueva dirección
            $('#provinciasid').change(function() {
                var provinciaId = $(this).val();
                provinciaId = formatearId(provinciaId, 'provincia');

                $('#ciudadesid').prop('disabled', true);
                $('#parroquiasid').prop('disabled', true);

                if (provinciaId) {
                    $.ajax({
                        url: baseURL + '/obtener-ciudades/' + provinciaId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#ciudadesid').empty();
                            $('#ciudadesid').append('<option value="">Seleccione Ciudad</option>');

                            $.each(data, function(key, value) {
                                var formattedId = formatearId(value.ciudadesid, 'ciudad');
                                $('#ciudadesid').append('<option value="' + formattedId + '">' + value.ciudad +
                                    '</option>');
                            });

                            $('#ciudadesid').prop('disabled', false);
                            $('.aiz-selectpicker').selectpicker('refresh');
                        }
                    });
                }
            });

            // Cuando cambia la ciudad en el formulario de nueva dirección
            $('#ciudadesid').change(function() {
                var ciudadId = $(this).val();
                ciudadId = formatearId(ciudadId, 'ciudad');

                $('#parroquiasid').prop('disabled', true);

                if (ciudadId) {
                    $.ajax({
                        url: baseURL + '/obtener-parroquias/' + ciudadId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#parroquiasid').empty();
                            $('#parroquiasid').append('<option value="">Seleccione Parroquia</option>');

                            $.each(data, function(key, value) {
                                var formattedId = formatearId(value.parroquiasid, 'parroquia');
                                $('#parroquiasid').append('<option value="' + formattedId + '">' + value.parroquia +
                                    '</option>');
                            });

                            $('#parroquiasid').prop('disabled', false);
                            $('.aiz-selectpicker').selectpicker('refresh');
                        }
                    });
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/shipping_info.blade.php ENDPATH**/ ?>