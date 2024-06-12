<?php $__env->startSection('content'); ?>
<?php
$parametros = \App\Models\ParametrosEmpresa::first();
?>
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
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block ">2. Información de la Compra</h3>
                        </div>
                    </div>
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">3. Pago</h3>
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
<section class="mb-4">
    <div class="container text-left">
        <form action="<?php echo e(route('payment.checkout')); ?>" class="form-default" role="form" method="POST"
            id="checkout-form">
            <div class="row">
                <div class="col-lg-8">
                    <?php echo csrf_field(); ?>
                    <div class="card shadow-sm border-0 rounded">
                        <div class="card-header p-3">
                            <h3 class="fs-16 fw-600 mb-0">
                                Seleccione una opcion de pago
                            </h3>
                        </div>
                        <?php
                        $payment_options = [
                        'pago_pedido' => get_setting('pago_pedido') == 'on',
                        'pago_plux' => get_setting('pago_plux') == 'on'
                        ];

                        $available_options = array_filter($payment_options, function($value) {
                        return $value == true;
                        });

                        $single_option_key = count($available_options) == 1 ? array_keys($available_options)[0] : null;
                        ?>

                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-xxl-8 col-xl-10 mx-auto">
                                    <div class="row gutters-10">
                                        <?php if($payment_options['pago_pedido']): ?>
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="pago_pedido" class="online_payment" type="radio"
                                                    name="payment_option" <?php echo e($single_option_key=='pago_pedido'
                                                    ? 'checked' : ''); ?>>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="<?php echo e(static_asset('assets/img/cards/cod.png')); ?>"
                                                        class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">Pedido</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <?php endif; ?>
                                        <?php if($payment_options['pago_plux']): ?>
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="pago_pedido" class="online_payment" type="radio"
                                                    name="payment_option" id="pagoplux" <?php echo e($single_option_key=='pago_plux' ? 'checked' : ''); ?>>
                                                <span class="d-block p-3 aiz-megabox-elem">
                                                    <img src="<?php echo e(static_asset('assets/img/cards/pagoplux.jpg')); ?>"
                                                        class="img-fluid mb-1">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">Pago Plux</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="pt-3">
                        <label class="aiz-checkbox">
                            <input type="checkbox" required id="agree_checkbox">
                            <span class="aiz-square-check"></span>
                            <span>Estoy de acuerdo con</span>
                        </label>
                        <a href="<?php echo e(route('terminos_condiciones')); ?>">terminos y condiciones</a>,
                        <a href="<?php echo e(route('politicas_devoluciones')); ?>">politica de devoluciones</a> &
                        <a href="<?php echo e(route('politicas_privacidad')); ?>">politicas de privacidad</a>
                        <div style="visibility:hidden;position: absolute !important;" id="ButtonPaybox"> </div>
                    </div>
                    <div class="row align-items-center pt-3">
                        <div class="col-6">
                            <a href="<?php echo e(route('home')); ?>" class="link link--style-3">
                                <i class="las la-arrow-left"></i>
                                Volver a la tienda
                            </a>
                        </div>
                        <div class="col-6 text-right">
                            <a type="button" onclick="submitOrder()" class="btn btn-primary text-white fw-600">Completar
                                Orden</a>
                        </div>
                    </div>
                </div>

                <?php
                $subtotalnetoconiva = 0;
                $subtotalnetosiniva = 0;
                ?>

                <div class="col-lg-4 mt-4 mt-lg-0" id="cart_summary">
                    <?php echo $__env->make('frontend.partials.cart_summary', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <input type="hidden" name="clientes_sucursalesid" value="<?php echo e($direccion); ?>">
                <?php
                $direccioncliente = \App\Models\ClientesSucursales::findOrFail($direccion);
                ?>
                <input type="hidden" value="<?php echo e($direccioncliente->direccion); ?>" id="direccion">
                <input type="hidden" name="tipo_tarjeta" id="tipo_tarjeta" value="">
                <input type="hidden" name="nombre_tarjeta" id="nombre_tarjeta" value="">
                <input type="hidden" name="token" id="token" value="">
            </div>
        </form>

        <form id="form-productos">
            <?php echo csrf_field(); ?>
            <?php $__currentLoopData = $carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cartItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="hidden" name="productosid[]" value="<?php echo e($cartItem['productosid']); ?>">
            <?php
            $cantidad = $cartItem['cantidad'] * ($cartItem['cantidadfactor'] == 0 ? 1 : $cartItem['cantidadfactor']);
            ?>
            <input type="hidden" name="cantidad[]" value="<?php echo e($cantidad); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('modal'); ?>
<div class="modal" id="carga">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
        role="document">
        <div class="modal-content position-relative">
            <div class="c-preloader text-center p-3">
                <i class="las la-spinner la-spin la-3x"></i>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    function submitOrder() {

            if (!$('.online_payment').is(":checked")) {
                AIZ.plugins.notify('danger', 'Debe seleccionar una forma de pago.');
                return;
            }

            if (!$('#agree_checkbox').is(":checked")) {
                AIZ.plugins.notify('danger', 'Debe estar de acuerdo con nuestras politicas.');
                return;
            }

            if ($("#pagoplux").is(':checked')) {
                let pedido = '<?php echo e(get_setting('pedido_pago_plux')); ?>';
                if (pedido == 'factura') {
                    if ('<?php echo e(get_setting("controla_stock") == 0); ?>' || '<?php echo e(get_setting("controla_stock") == 1); ?>') {
                        $.ajax({
                            type: "POST",
                            url: '<?php echo e(route('verificar_existencia')); ?>',
                            data: $('#form-productos').serializeArray(),
                            success: function(data) {
                                $('#existencias').empty();
                                if (data.length > 0) {
                                    $('#existencias').addClass('alert alert-danger d-flex align-items-center');
                                    $('#existencias').append('Productos sin existencias: </br>');
                                    jQuery.each(data, function(i, val) {
                                        $('#existencias').append('*' + val + '</br>');
                                    });
                                } else {
                                    $("#pay").click();
                                }
                            }
                        });
                    }else{
                        $("#pay").click();
                    }

                } else {
                    $("#pay").click();
                }
            } else {
                $('#checkout-form').submit();
            }
        }

        if ("<?php echo e(get_setting('pago_plux')); ?>" == "on") {

            var data = {

                /* Requerido. Email de la cuenta PagoPlux del Establecimiento o Id/Class del elemento html que posee el valor */

                PayboxRemail: "<?php echo e(get_setting('email_pago_plux')); ?>",

                /* Requerido. Email del usuario que realiza el pago o Id/Class del elemento html que posee el valor */

                PayboxSendmail: "<?php echo e(auth()->user()->email_login); ?>",

                /* Requerido. Nombre del establecimiento en PagoPlux o Id/Class del elemento html que posee el valor */

                PayboxRename: "<?php echo e($parametros->nombrecomercial); ?>",

                /* Requerido. Nombre del usuario que realiza el pago o Id/Class del elemento html que posee el valor */

                PayboxSendname: "<?php echo e(auth()->user()->razonsocial); ?>",

                /* Requerido. Ejemplo: 100.00, 10.00, 1.00 o Id/Class del elemento html que posee el valor de los productos sin impuestos */

                PayboxBase0: $('#inputCero').val(),

                /* Requerido. Ejemplo: 100.00, 10.00, 1.00 o Id/Class del elemento html que posee el valor de los productos con su impuesto incluido */

                PayboxBase12: $('#total').val(),

                /* Requerido. Descripción del pago o Id/Class del elemento html que posee el valor */

                PayboxDescription: "Pago Ecommerce",

                /* Requerido Tipo de Ejecución
                * Production: true (Modo Producción, Se procesarán cobros y se
                cargarán al sistema, afectará a la tdc)
                * Production: false (Modo Prueba, se realizarán cobros de prueba y no
                se guardará ni afectará al sistema)
                */
                PayboxProduction: true,


                PayboxEnvironment: "prod",
                /* Requerido. Lenguaje del Paybox
                 * Español: es | (string) (Paybox en español)
                 * Ingles:  us | (string) (Paybox en Ingles)
                 */
                PayboxLanguage: "es",

                /* Opcional Valores HTML que son requeridos por la web que implementa
                el botón de pago.
                * Se permiten utilizar los identificadores de # y . que describen los
                Id y Class de los Elementos HTML
                * Array de identificadores de elementos HTML |
                Ejemplo: PayboxRequired: ["#nombre", "#correo", "#monto"]
                */
                PayboxRequired: [],

                /*
                 * Requerido. dirección del tarjetahabiente o Id/Class del elemento
                 * html que posee el valor
                 */
                PayboxDirection: "<?php echo e($direccioncliente->direccion); ?>",

                /*
                 * Requerido. Teléfono del tarjetahabiente o Id/Class del elemento
                 * html que posee el valor
                 */
                PayBoxClientPhone: "<?php echo e(auth()->user()->telefono1); ?>",


            }

            var onAuthorize = function(response) {
                // Si el pago fue correcto realiza la factura
                if (response.status == 'succeeded') {
                    $('#tipo_tarjeta').val(response.detail.cardType);
                    $('#nombre_tarjeta').val(response.detail.cardIssuer);
                    $('#token').val(response.detail.token);
                    $('#carga').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('.c-preloader').show();
                    let pedido = '<?php echo e(get_setting('pedido_pago_plux')); ?>';
                    if (pedido == 'factura') {
                        $.ajax({
                            type: "POST",
                            url: '<?php echo e(route('factura.crear')); ?>',
                            data: $('#checkout-form').serializeArray(),
                            success: function(data) {
                                if (data > 0) {
                                    if ('<?php echo e(Request::segment(1)); ?>' == 'checkout') {
                                        location.href = "<?php echo e(env('APP_URL')); ?>/checkout/order-confirmed/" +
                                            data + "/<?php echo e(auth()->user()->clientesid); ?>";
                                    } else {
                                        location.href =
                                            "<?php echo e(env('APP_URL')); ?>/<?php echo e(Request::segment(1)); ?>/checkout/order-confirmed/" +
                                            data + "/<?php echo e(auth()->user()->clientesid); ?>";
                                    }
                                } else {
                                    $('#carga').modal('hide')
                                    AIZ.plugins.notify('danger', 'Ocurrio un error al guardar la factura');
                                }
                            }
                        });
                    } else {
                        $('#checkout-form').submit();
                    }
                }
            }
        }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/payment_select.blade.php ENDPATH**/ ?>