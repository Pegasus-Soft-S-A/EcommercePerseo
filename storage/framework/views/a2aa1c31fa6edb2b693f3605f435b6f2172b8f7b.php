<?php $__env->startSection('content'); ?>
<?php
$user = Session::get('user');
session(['ruta' => 'carrito']);
$almacenes = App\Models\Almacenes::where('disponibleventa', 1)->get();
?>
<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">Mi Carrito</h3>
                        </div>
                    </div>
                    <?php if(get_setting('maneja_sucursales') != "on"): ?>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">Información de la Compra</h3>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">Pago</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">Confirmación</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="mb-4" id="cart-summary">
    <div class="container-fluid">
        <?php if($carts && count($carts) > 0): ?>
        <div class="row">
            <div class="col-xxl-8 col-xl-10 mx-auto">
                <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                    <?php if(isset($productos)): ?>
                    <div id="mensajes" class="alert alert-danger d-flex align-items-center">
                        <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        * El producto "<?php echo e($prod); ?>" sobrepasa las existencias. <br>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                    <div class="mb-4">
                        <div class="row gutters-7 d-none d-lg-flex border-bottom mb-1 pb-3">
                            <div class="col-md-4 fw-600 text-center">Producto</div>
                            <div class="col-md-1 fw-600 text-center">Precio</div>
                            <div class="col-md-2 fw-600 text-center">Cantidad</div>
                            <div class="col-md-1 fw-600 text-center">Total</div>
                            <div class="col-md-1 fw-600 text-center">Observacion</div>
                            <?php if(get_setting('productos_existencias')=='todos' || get_setting('controla_stock')==1 ||
                            (get_setting('controla_stock')==2 && Auth::check())): ?>
                            <div class="col-md-2 fw-600 text-center">Existencias</div>
                            <?php endif; ?>
                            <div class="col-md-1 fw-600 text-center"></div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php $__currentLoopData = $carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cartItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item px-0 px-lg-3">
                                <div class="row gutters-7 align-items-center">
                                    <div class="col-md-4 d-flex">
                                        <span class="mr-2 ml-0">
                                            <?php if($cartItem['imagen_producto']): ?>
                                            <img src="data:image/jpg;base64,<?php echo e(base64_encode($cartItem['imagen_producto'])); ?>"
                                                class="img-fit lazyload size-60px rounded" alt="">
                                            <?php else: ?>
                                            <img src="data:image/jpg;base64,<?php echo e(get_setting('imagen_defecto')); ?>"
                                                class="img-fit lazyload size-60px rounded" alt="">
                                            <?php endif; ?>
                                        </span>
                                        <span class="fs-14 opacity-60 mt-3 ml-4">
                                            <?php if($cartItem['iva'] > 0): ?>
                                            <span class="text-danger">*</span>
                                            <?php endif; ?>
                                            <?php echo e($cartItem['producto_descripcion']); ?>

                                        </span>
                                    </div>

                                    <div class="col-md-1 text-center">
                                        <span class="opacity-60 fs-12 d-block d-lg-none">Precio</span>
                                        <span class="fw-600 fs-16">$<?php echo e(number_format(round($cartItem['precio_visible'],
                                            2), 2)); ?></span>
                                    </div>

                                    <div class="col-md-2 text-center">
                                        <div class="row no-gutters align-items-center aiz-plus-minus mr-2 ml-0">
                                            <button class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                type="button" data-type="minus"
                                                data-field="quantity[<?php echo e($cartItem['ecommerce_carritosid']); ?>]">
                                                <i class="las la-minus"></i>
                                            </button>
                                            <input type="number"
                                                name="quantity[<?php echo e($cartItem['ecommerce_carritosid']); ?>]"
                                                class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                placeholder="1" value="<?php echo e(round($cartItem['cantidad'], 2)); ?>" min="1"
                                                max="<?php echo e(round($cartItem['cantidad_final'], 2)); ?>" autocomplete="off"
                                                onchange="updateQuantity(<?php echo e($cartItem['ecommerce_carritosid']); ?>, <?php echo e(round($cartItem['cantidad_final'], 2)); ?>, this)">
                                            <button class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                type="button" data-type="plus"
                                                data-field="quantity[<?php echo e($cartItem['ecommerce_carritosid']); ?>]">
                                                <i class="las la-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-1 text-center">
                                        <span class="opacity-60 fs-12 d-block d-lg-none">Total</span>
                                        <span class="fw-600 fs-16 text-primary">$<?php echo e(number_format(round($cartItem['precio_visible'] * $cartItem['cantidad'], 2),
                                            2)); ?></span>
                                    </div>

                                    <div class="col-md-1 text-center">
                                        <a href="javascript:void(0)"
                                            onclick="showObservacion(<?php echo e($cartItem['ecommerce_carritosid']); ?>)"
                                            class="btn btn-icon btn-sm btn-soft-success btn-circle">
                                            <i class="las la-pen"></i>
                                        </a>
                                    </div>

                                    <div <?php if(get_setting('productos_existencias')=='todos' ||
                                        get_setting('controla_stock')==1 || (get_setting('controla_stock')==2 &&
                                        Auth::check())): ?> class="col-md-2 text-center" <?php else: ?>
                                        class="col-md-2 text-center invisible" <?php endif; ?>>
                                        <span class="opacity-60 fs-12 d-block d-lg-none ">Existencias</span>
                                        <?php if((get_setting('controla_stock') == 0 && !Auth::check()) ||
                                        (get_setting('controla_stock') == 2 && !Auth::check())): ?>

                                        <?php if($cartItem['cantidad_final'] > 0): ?>
                                        <div
                                            class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                            <span><?php echo e(get_setting('productos_disponibles')); ?></span>
                                        </div>
                                        <?php else: ?>
                                        <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                            <span><?php echo e(get_setting('productos_no_disponibles')); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php elseif(get_setting('controla_stock') == 0 && Auth::check()): ?>

                                        <?php if($cartItem['cantidad_final'] > 0): ?>
                                        <div
                                            class="d-inline-block rounded px-2 border-success mt-1 text-success border">
                                            <span><?php echo e(get_setting('productos_disponibles')); ?></span>
                                        </div>
                                        <?php else: ?>
                                        <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border">
                                            <span><?php echo e(get_setting('productos_no_disponibles')); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <?php else: ?>
                                        <div class="">
                                            <span class="fw-600 fs-16 text-primary" id="cantidad"><?php echo e(round($cartItem['cantidad_final'], 2)); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-1 text-center">
                                        <a href="javascript:void(0)"
                                            onclick="removeFromCartView(event, <?php echo e($cartItem['ecommerce_carritosid']); ?>)"
                                            class="btn btn-icon btn-sm btn-soft-primary btn-circle">
                                            <i class="las la-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>

                    <?php if(get_setting('maneja_sucursales') == "on"): ?>
                    <div class="px-3 py-2 border-top d-flex justify-content-between">
                        <span class="opacity-60 fs-15">Centro de Costos</span>
                        <select class="form-control aiz-selectpicker" name="centros_costosid" data-live-search="true">
                            <?php $__currentLoopData = $centro_costos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $centro_costo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($centro_costo->centros_costosid); ?>"><?php echo e($centro_costo->centro_costocodigo); ?>-<?php echo e($centro_costo->descripcion); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="px-3 py-2 border-top d-flex justify-content-between">
                        <span class="opacity-60 fs-15">Sucursal</span>
                        <select class="form-control aiz-selectpicker ml-5" name="sucursalesid" data-live-search="true">
                            <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sucursal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sucursal->clientes_sucursalesid); ?>" <?php if($sucursal->clientes_sucursalesid
                                ==session('sucursalid')): ?> selected <?php endif; ?>>
                                <?php echo e($sucursal->descripcion); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="px-3 py-2 border-top d-flex justify-content-between">
                        <span class="opacity-60 fs-15">Destinatario</span>
                        <input type="text" class="form-control ml-4" name="destinatario" id="destinatario"
                            autocomplete="off">
                    </div>
                    <?php endif; ?>

                    <div class="px-3 py-2  border-top d-flex justify-content-between">
                        <span class="opacity-60 fs-15">Subtotal</span>
                        <span class="fw-600 fs-17">$<?php echo e($totales['subtotal']); ?></span>
                    </div>
                    <div class="px-3 py-2  border-top d-flex justify-content-between">
                        <span class="opacity-60 fs-15">Descuento</span>
                        <span class="fw-600 fs-17">$<?php echo e($totales['descuento']); ?></span>
                    </div>
                    <div class="px-3 py-2  border-top d-flex justify-content-between">
                        <span class="opacity-60 fs-15">Subtotal Neto</span>
                        <span class="fw-600 fs-17">$<?php echo e($totales['subtotalNeto']); ?></span>
                    </div>
                    <div class="px-3 py-2  border-top d-flex justify-content-between">
                        <span class="opacity-60 fs-15">IVA</span>
                        <span class="fw-600 fs-17">$<?php echo e($totales['totalIVA']); ?></span>
                    </div>
                    <div class="px-3 py-2  border-top d-flex justify-content-between">
                        <span class="opacity-60 fs-15">Total</span>
                        <span class="fw-600 fs-17">$<?php echo e($totales['total']); ?></span>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                            <a href="<?php echo e(route('home')); ?>" class="btn btn-link">
                                <i class="las la-arrow-left"></i>
                                Regresar a la tienda
                            </a>
                        </div>

                        <div class="col-md-6 text-center text-md-right">
                            <?php if(Auth::check()): ?>
                            <a href="<?php echo e(route('verificarexistencias.shipping_info', Auth::user()->clientesid)); ?>"
                                class="btn btn-primary fw-600">
                                Continuar con la Compra
                            </a>
                            <?php else: ?>
                            <button class="btn btn-primary fw-600" onclick="showCheckoutModal()">Continuar
                                con la Compra</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="shadow-sm bg-white p-4 rounded">
                    <div class="text-center p-3">
                        <i class="las la-frown la-3x opacity-60 mb-3"></i>
                        <h3 class="h4 fw-700">Tu carrito está vacío</h3>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
<div class="modal fade" id="GuestCheckout">
    <div class="modal-dialog modal-dialog-zoom">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Iniciar Sesion</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <form class="form-default" role="form" action="<?php echo e(route('login.cliente')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <input type="text"
                                class="form-control h-auto form-control-lg <?php echo e($errors->has('identificacion') ? ' is-invalid' : ''); ?>"
                                value="<?php echo e(old('identificacion')); ?>" placeholder="Identificacion" name="identificacion"
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <input type="password" name="clave" class="form-control h-auto form-control-lg"
                                placeholder="Contraseña">
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                    <span class=opacity-60>Recuerdame</span>
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                            <div class="col-6 text-right">
                                <a href="" class="text-reset opacity-60 fs-14">Olvidó su contraseña</a>
                            </div>
                        </div>

                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary btn-block fw-600">Ingresar</button>
                            <?php if(get_setting('registra_clientes') == 'on'): ?>
                            <a href="<?php echo e(route('user.registration')); ?>"
                                class="btn btn-primary btn-block fw-600">Registrarse ahora</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <?php if(get_setting('registra_clientes') == 'on'): ?>

                    <?php if(get_setting('login_google') == 'on' || get_setting('login_facebook') == 'on'): ?>
                    <div class="separator mb-3">
                        <span class="bg-white px-3 opacity-60">O inicia sesion</span>
                    </div>
                    <ul class="list-inline social colored text-center mb-3">
                        <?php if(get_setting('login_facebook') == 'on'): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo e(route('social.login', ['provider' => 'facebook'])); ?>" class="facebook">
                                <i class="lab la-facebook-f"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(get_setting('login_google') == 'on'): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo e(route('social.login', ['provider' => 'google'])); ?>" class="google">
                                <i class="lab la-google"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if(get_setting('login_apple') == 'on'): ?>
                        <li class="list-inline-item">
                            <a href="<?php echo e(route('social.login', ['provider' => 'apple'])); ?>" id="apple">
                                <svg style="color: rgb(0, 0, 0);" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="currentColor" class="bi bi-apple" viewBox="0 0 16 16">
                                    <path
                                        d="M11.182.008C11.148-.03 9.923.023 8.857 1.18c-1.066 1.156-.902 2.482-.878 2.516.024.034 1.52.087 2.475-1.258.955-1.345.762-2.391.728-2.43zm3.314 11.733c-.048-.096-2.325-1.234-2.113-3.422.212-2.189 1.675-2.789 1.698-2.854.023-.065-.597-.79-1.254-1.157a3.692 3.692 0 0 0-1.563-.434c-.108-.003-.483-.095-1.254.116-.508.139-1.653.589-1.968.607-.316.018-1.256-.522-2.267-.665-.647-.125-1.333.131-1.824.328-.49.196-1.422.754-2.074 2.237-.652 1.482-.311 3.83-.067 4.56.244.729.625 1.924 1.273 2.796.576.984 1.34 1.667 1.659 1.899.319.232 1.219.386 1.843.067.502-.308 1.408-.485 1.766-.472.357.013 1.061.154 1.782.539.571.197 1.111.115 1.652-.105.541-.221 1.324-1.059 2.238-2.758.347-.79.505-1.217.473-1.282z"
                                        fill="#000000"></path>
                                    <path
                                        d="M11.182.008C11.148-.03 9.923.023 8.857 1.18c-1.066 1.156-.902 2.482-.878 2.516.024.034 1.52.087 2.475-1.258.955-1.345.762-2.391.728-2.43zm3.314 11.733c-.048-.096-2.325-1.234-2.113-3.422.212-2.189 1.675-2.789 1.698-2.854.023-.065-.597-.79-1.254-1.157a3.692 3.692 0 0 0-1.563-.434c-.108-.003-.483-.095-1.254.116-.508.139-1.653.589-1.968.607-.316.018-1.256-.522-2.267-.665-.647-.125-1.333.131-1.824.328-.49.196-1.422.754-2.074 2.237-.652 1.482-.311 3.83-.067 4.56.244.729.625 1.924 1.273 2.796.576.984 1.34 1.667 1.659 1.899.319.232 1.219.386 1.843.067.502-.308 1.408-.485 1.766-.472.357.013 1.061.154 1.782.539.571.197 1.111.115 1.652-.105.541-.221 1.324-1.059 2.238-2.758.347-.79.505-1.217.473-1.282z"
                                        fill="#000000"></path>
                                </svg>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalIdentificacion">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Verificar Usuario</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <form class="form-default" role="form" action="<?php echo e(route('verificar.identificacion')); ?>"
                        method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="form-group">
                            <input type="hidden" value=" <?php if(isset($user)): ?> <?php echo e($user->email); ?> <?php endif; ?>" name="email">
                            <input type="hidden" value=" <?php if(isset($user)): ?> <?php echo e($user->name); ?> <?php endif; ?>" name="nombre">
                            <input type="text" class="form-control h-auto form-control-lg" placeholder="Identificacion"
                                name="identificacion" minlength="10" maxlength="13" pattern="[0-9]+"
                                onkeypress="return validarNumero(event)" id="identificacion" autocomplete="off"
                                required>
                        </div>

                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary btn-block fw-600"
                                id="buttonVerificar">Verificar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
                    <form class="form-default" role="form" action="<?php echo e(route('cart.updateObservacion')); ?>" method="POST">
                        <?php echo csrf_field(); ?>

                        <div class="form-group">
                            <input type="hidden" value="" name="ecommerce_carritosid" id="ecommerce_carritosid">
                            <textarea class="form-control h-auto form-control-lg" placeholder="Observación"
                                name="observacion" id="observacion" autocomplete="off" required rows="4"></textarea>
                        </div>

                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary btn-block fw-600">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    // Detectar cambios en los select e input y enviar los datos al controlador
    $(document).on('change', 'select[name="centros_costosid"], select[name="sucursalesid"]', function () {
        let fieldName = $(this).attr('name');
        let fieldValue = $(this).val();

        // Realiza una solicitud AJAX para almacenar el valor en la sesión
        $.post('<?php echo e(route('update.session')); ?>', {
            _token: '<?php echo e(csrf_token()); ?>',
            field: fieldName,
            value: fieldValue
        }, function (response) {
            if (response.success) {
                console.log('Sesión actualizada con éxito');
            } else {
                console.error('Error al actualizar la sesión');
            }
        });
    });

    $(document).on('blur', 'input[name="destinatario"]', function () {
        let fieldName = $(this).attr('name');
        let fieldValue = $(this).val();

        // Realiza una solicitud AJAX para almacenar el valor en la sesión
        $.post('<?php echo e(route('update.session')); ?>', {
            _token: '<?php echo e(csrf_token()); ?>',
            field: fieldName,
            value: fieldValue
        }, function (response) {
            if (response.success) {
                console.log('Sesión actualizada con éxito');
            } else {
                console.error('Error al actualizar la sesión');
            }
        });
    });


    $(document).ready(function() {
            <?php if(isset($user)): ?>
                $('#modalIdentificacion').modal()
            <?php endif; ?>
        });

        function validarNumero(e) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla == 8) return true;
            patron = /[0-9]/;
            te = String.fromCharCode(tecla);
            return patron.test(te);
        }

        $("#buttonVerificar").click(function() {
            var cad = document.getElementById("identificacion").value.trim();
            var total = 0;
            var longitud = cad.length;
            var longcheck = longitud - 1;
            var digitos = cad.split('').map(Number);
            var codigo_provincia = digitos[0] * 10 + digitos[1];
            if (cad !== "" && longitud === 10) {

                if (cad != '2222222222' && codigo_provincia >= 1 && (codigo_provincia <= 24 || codigo_provincia ==
                        30)) {
                    for (i = 0; i < longcheck; i++) {
                        if (i % 2 === 0) {
                            var aux = cad.charAt(i) * 2;
                            if (aux > 9) aux -= 9;
                            total += aux;
                        } else {
                            total += parseInt(cad.charAt(i)); // parseInt o concatenará en lugar de sumar
                        }
                    }
                    total = total % 10 ? 10 - total % 10 : 0;

                    if (cad.charAt(longitud - 1) == total) {
                        // recuperacionValidacion(cad);
                    } else {
                        event.preventDefault();
                        AIZ.plugins.notify('warning', 'La Cédula no es válida');
                        $('#identificacion').focus();
                    }
                } else {
                    event.preventDefault();
                    AIZ.plugins.notify('warning', 'La Cédula no es válida');
                    $('#identificacion').focus();
                }
            } else
            if (longitud == 13) {
                var controlador = 1;
                var valor = 0;

                valor = valor + ((cad.substr(0, 1)) * 4);
                valor = valor + ((cad.substr(1, 1)) * 3);
                valor = valor + ((cad.substr(2, 1)) * 2);
                valor = valor + ((cad.substr(3, 1)) * 7);
                valor = valor + ((cad.substr(4, 1)) * 6);
                valor = valor + ((cad.substr(5, 1)) * 5);
                valor = valor + ((cad.substr(6, 1)) * 4);
                valor = valor + ((cad.substr(7, 1)) * 3);
                valor = valor + ((cad.substr(8, 1)) * 2);

                valor = 11 - ((valor % 11) == 0 ? 11 : (valor % 11));

                if (valor == (cad.substr(9, 1)) && (cad.substr(10, 3)) == "001") {
                    controlador = 2;
                    // recuperacionValidacion(cad);
                } else {
                    valor = 0;
                    valor = valor + cad.substr(0, 1) * 3;
                    valor = valor + cad.substr(1, 1) * 2;
                    valor = valor + cad.substr(2, 1) * 7;
                    valor = valor + cad.substr(3, 1) * 6;
                    valor = valor + cad.substr(4, 1) * 5;
                    valor = valor + cad.substr(5, 1) * 4;
                    valor = valor + cad.substr(6, 1) * 3;
                    valor = valor + cad.substr(7, 1) * 2;
                    valor = 11 - ((valor % 11) == 0 ? 11 : (valor % 11));

                    if (valor == (cad.substr(8, 1)) && (cad.substr(9, 4)) == "0001") {
                        controlador = 2;
                        // recuperacionValidacion(cad);
                    } else {
                        valor = 0;
                        valor = valor + (cad.substr(0, 1) * 2 > 9 ? ((cad.substr(0, 1)) * 2) - 9 : (cad.substr(0,
                            1)) * 2);
                        valor = valor + (cad.substr(1, 1) * 1 > 9 ? ((cad.substr(1, 1)) * 1) - 9 : (cad.substr(1,
                            1)) * 1);
                        valor = valor + (cad.substr(2, 1) * 2 > 9 ? ((cad.substr(2, 1)) * 2) - 9 : (cad.substr(2,
                            1)) * 2);
                        valor = valor + (cad.substr(3, 1) * 1 > 9 ? ((cad.substr(3, 1)) * 1) - 9 : (cad.substr(3,
                            1)) * 1);
                        valor = valor + (cad.substr(4, 1) * 2 > 9 ? ((cad.substr(4, 1)) * 2) - 9 : (cad.substr(4,
                            1)) * 2);
                        valor = valor + (cad.substr(5, 1) * 1 > 9 ? ((cad.substr(5, 1)) * 1) - 9 : (cad.substr(5,
                            1)) * 1);
                        valor = valor + (cad.substr(6, 1) * 2 > 9 ? ((cad.substr(6, 1)) * 2) - 9 : (cad.substr(6,
                            1)) * 2);
                        valor = valor + (cad.substr(7, 1) * 1 > 9 ? ((cad.substr(7, 1)) * 1) - 9 : (cad.substr(7,
                            1)) * 1);
                        valor = valor + (cad.substr(8, 1) * 2 > 9 ? ((cad.substr(8, 1)) * 2) - 9 : (cad.substr(8,
                            1)) * 2);
                        valor = 10 - ((valor % 10) == 0 ? 10 : (valor % 10))
                        if (valor == (cad.substr(9, 1)) && (cad.substr(10, 3)) == "001") {
                            controlador = 2;
                            // recuperacionValidacion(cad);
                        } else {
                            event.preventDefault();
                            AIZ.plugins.notify('warning', 'El RUC no es válido');
                            $('#identificacion').focus();
                        }
                    }
                }

            } else {
                event.preventDefault();
                AIZ.plugins.notify('warning', 'Ingrese un RUC o Cédula válido');
                $('#identificacion').focus();
            }

        });

        function removeFromCartView(e, key) {
            e.preventDefault();
            removeFromCart(key);
        }

        function updateQuantity(key, cantidad, element) {
            $.post('<?php echo e(route('cart.updateQuantity')); ?>', {
                _token: '<?php echo e(csrf_token()); ?>',
                id: key,
                quantity: element.value,
                cantidad: cantidad

            }, function(data) {
                updateNavCart();
                $('#cart-summary').html(data);
            });
        }

        function updateObservacion(key, element) {
            $.post('<?php echo e(route('cart.updateObservacion')); ?>', {
                _token: '<?php echo e(csrf_token()); ?>',
                id: key,
                observacion: element.value,

            });
        }

        function showCheckoutModal() {
            $('#GuestCheckout').modal();
        }

        function showObservacion(key) {
            $.post('<?php echo e(route('cart.showObservacion')); ?>', {
                _token: '<?php echo e(csrf_token()); ?>',
                id: key,

            }, function(data) {
                $('#observacion').val(data.observacion)
                $('#ecommerce_carritosid').val(key)
            });

            $('#modalObservacion').modal();
        }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/view_cart.blade.php ENDPATH**/ ?>