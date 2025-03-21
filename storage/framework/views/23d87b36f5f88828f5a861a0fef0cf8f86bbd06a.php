<?php $__env->startSection('panel_content'); ?>
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">Administrar Perfil</h1>
            </div>
        </div>
    </div>

    <!-- Basic Info-->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">Informacion Basica</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('profile.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Identificacion</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="Identificacion" name="identificacion"
                            value="<?php echo e($cliente->identificacion); ?>" <?php if(Auth::user()->identificacion != ''): ?> readonly <?php endif; ?> autocomplete="off">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Nombre</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="Nombre" name="razonsocial" value="<?php echo e($cliente->razonsocial); ?>"
                            <?php if(Auth::user()->identificacion != ''): ?>  <?php endif; ?> <?php if(get_setting('maneja_sucursales') == 'on'): ?> readonly <?php endif; ?> autocomplete="off">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Email</label>
                    <div class="col-md-10">
                        <input <?php if(get_setting('maneja_sucursales') == 'on'): ?> readonly <?php endif; ?> type="text" class="form-control" placeholder="Email" name="email"
                            value="<?php echo e($cliente->email_login); ?>" autocomplete="off" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Telefono</label>
                    <div class="col-md-10">
                        <input <?php if(get_setting('maneja_sucursales') == 'on'): ?> readonly <?php endif; ?> type="text" class="form-control" placeholder="Telefono"
                            name="telefono1" value="<?php echo e($cliente->telefono1); ?>" autocomplete="off">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Convencional</label>
                    <div class="col-md-10">
                        <input <?php if(get_setting('maneja_sucursales') == 'on'): ?> readonly <?php endif; ?> type="text" class="form-control" placeholder="Convencional"
                            name="telefono2" value="<?php echo e($cliente->telefono2); ?>" autocomplete="off">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Whatsapp</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="Whatsapp" name="telefono3" value="<?php echo e($cliente->telefono3); ?>"
                            autocomplete="off">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Contraseña:</label>
                    <div class="col-md-10">
                        <input type="password" class="form-control" placeholder="Nueva Contraseña" name="new_password">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Confirmar Contraseña</label>
                    <div class="col-md-10">
                        <input type="password" class="form-control" placeholder="Confirmar Contraseña" name="confirm_password">
                    </div>
                </div>

                <div class="card-footer">
                    <a href="javascript:void(0)" class="btn btn-danger text-white confirm-delete"
                        data-href="<?php echo e(route('profile_delete', auth()->user()->clientesid)); ?>">Eliminar Perfil</a>
                    <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Address -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">Direccion</h5>
        </div>
        <div class="card-body">
            <div class="row gutters-10">
                <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-6">
                        <div class="border p-3 pr-5 rounded mb-3 position-relative">
                            <?php if($address->descripcion != ''): ?>
                                <div>
                                    <span class="w-50 fw-600">Descripción:</span>
                                    <span class="ml-2"><?php echo e($address->descripcion); ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <span class="w-50 fw-600">Direccion:</span>
                                <span class="ml-2"><?php echo e($address->direccion); ?></span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">Ciudad:</span>
                                <span class="ml-2"><?php echo e($address->ciudad); ?></span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">Telefono:</span>
                                <span class="ml-2"><?php echo e($address->telefono1); ?></span>
                            </div>
                            <?php if(get_setting('maneja_sucursales') != 'on'): ?>
                                <div class="dropdown position-absolute right-0 top-0">
                                    <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                        <i class="la la-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" onclick="edit_address('<?php echo e($address->clientes_sucursalesid); ?>')">
                                            Editar
                                        </a>
                                        <a class="dropdown-item" href="<?php echo e(route('addresses.destroy', $address->clientes_sucursalesid)); ?>">Eliminar</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(get_setting('maneja_sucursales') != 'on'): ?>
                    <div class="col-lg-6 mx-auto" onclick="add_new_address()">
                        <div class="border p-4 rounded c-pointer text-center bg-light">
                            <i class="la la-plus la-2x"></i>
                            <div class="alpha-7">Agregar Nueva Direccion</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
    <div class="modal fade" id="new-address-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-zoom" role="document">
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
                                    <input type="text" class="form-control mb-3" placeholder="Casa, Trabajo, etc." name="descripcion"
                                        value="" autocomplete="off" required>
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

    <div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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


    <?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script type="text/javascript">
        // Función para manejar el Enter en textareas
        function controlar(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                return false;
            }
        }

        // Función para mostrar el modal de edición
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

        // Función para mostrar el modal de nueva dirección
        function add_new_address() {
            $('#new-address-modal').modal('show');
        }

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

        // Función para inicializar los selectores en el formulario de edición
        function initEditFormSelectors() {
            // Obtener los segmentos de la URL
            var pathSegments = window.location.pathname.split('/').filter(segment => segment !== "");
            var tieneTienda = pathSegments.includes("tienda");
            var empresaSegment = pathSegments.find(segment => !isNaN(segment));
            var baseURL = tieneTienda ? '/tienda/' + empresaSegment : '/' + empresaSegment;

            console.log("Inicializando selectores del formulario de edición");
            console.log("URL base:", baseURL);

            // Valores iniciales para edición (desde los campos ocultos)
            var provinciaInicial = $('#edit_provincia_inicial').val();
            var ciudadInicial = $('#edit_ciudad_inicial').val();
            var parroquiaInicial = $('#edit_parroquia_inicial').val();

            console.log("Valores iniciales de edición:", {
                provincia: provinciaInicial,
                ciudad: ciudadInicial,
                parroquia: parroquiaInicial
            });

            // Formatear los valores iniciales
            provinciaId = formatearId(provinciaId, 'provincia');

            provinciaInicial = formatearId(provinciaInicial, 'provincia');
            ciudadInicial = formatearId(ciudadInicial, 'ciudad');
            parroquiaInicial = formatearId(parroquiaInicial, 'parroquia');

            // Asegurarse de que la provincia correcta esté seleccionada
            $("#edit_provinciasid option").each(function() {
                var optionValue = formatearId($(this).val(), 'provincia');
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
                        console.log("Cargando ciudades para provincia:", provinciaId);

                        $.ajax({
                            url: baseURL + '/obtener-ciudades/' + provinciaId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#edit_ciudadesid').empty();
                                $('#edit_ciudadesid').append('<option value="">Seleccione Ciudad</option>');

                                console.log("Ciudades obtenidas:", data.length);

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
                        console.log("Cargando parroquias para ciudad:", ciudadId);

                        $.ajax({
                            url: baseURL + '/obtener-parroquias/' + ciudadId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) {
                                $('#edit_parroquiasid').empty();
                                $('#edit_parroquiasid').append('<option value="">Seleccione Parroquia</option>');

                                console.log("Parroquias obtenidas:", data.length);

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

                console.log("Provincia cambiada a:", provinciaId);
                if (provinciaId) {
                    cargarCiudadesEdicion(provinciaId);
                }
            });

            $('#edit_ciudadesid').change(function() {
                var ciudadId = $(this).val();

                // Limpiar y deshabilitar el selector de parroquias
                $('#edit_parroquiasid').empty().prop('disabled', true).append('<option value="">Seleccione Parroquia</option>');
                $('.aiz-selectpicker').selectpicker('refresh');

                console.log("Ciudad cambiada a:", ciudadId);
                if (ciudadId) {
                    cargarParroquiasEdicion(ciudadId);
                }
            });
        }

        $(document).ready(function() {
            // Obtener los segmentos de la URL para el formulario principal
            var pathSegments = window.location.pathname.split('/').filter(segment => segment !== "");
            var tieneTienda = pathSegments.includes("tienda");
            var empresaSegment = pathSegments.find(segment => !isNaN(segment));
            var baseURL = tieneTienda ? '/tienda/' + empresaSegment : '/' + empresaSegment;

            // Código para el formulario principal (nueva dirección)
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

<?php echo $__env->make('frontend.layouts.user_panel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/cliente/profile.blade.php ENDPATH**/ ?>