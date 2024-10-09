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
                <form class="form-default" data-toggle="validator"
                    action="<?php echo e(route('checkout.store_shipping_infostore')); ?>" role="form" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php if(Auth::check()): ?>
                    <div class="shadow-sm bg-white p-4 rounded mb-4">
                        <div class="row gutters-5">
                            <?php
                            $sucursales = \App\Models\ClientesSucursales::where('clientesid',
                            Auth::user()->clientesid)->get();
                            ?>
                            <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 mb-3">
                                <label class="aiz-megabox d-block bg-white mb-0">
                                    <input type="radio" name="clientes_sucursalesid"
                                        value="<?php echo e($address->clientes_sucursalesid); ?>" <?php if($key==0): ?> checked <?php endif; ?>
                                        required>
                                    <span class="d-flex p-3 aiz-megabox-elem">
                                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                        <span class="flex-grow-1 pl-3 text-left">
                                            <div>
                                                <span class="opacity-60">Direccion:</span>
                                                <span class="fw-600 ml-2"><?php echo e($address->direccion); ?></span>
                                            </div>
                                            <div>
                                                <?php
                                                $direccion =
                                                \App\Models\ClientesSucursales::findOrFail($address->clientes_sucursalesid);
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
                                <div class="dropdown position-absolute right-0 top-0">
                                    <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                        <i class="la la-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item"
                                            onclick="edit_address('<?php echo e($address->clientes_sucursalesid); ?>')">
                                            Editar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <input type="hidden" name="checkout_type" value="logged">
                            <div class="col-md-6 mx-auto mb-3">
                                <div class="border  rounded mb-3 c-pointer text-center bg-white h-100 d-flex flex-column justify-content-center"
                                    onclick="add_new_address()">
                                    <i class="las la-plus la-2x mb-3"></i>
                                    <div class="alpha-7">Agregar Nueva Direccion</div>
                                </div>
                            </div>
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
                                <label>Ciudad</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="ciudad"
                                    required>
                                    <option value="">Seleccione Ciudad</option>
                                    <?php $__currentLoopData = \App\Models\Ciudades::get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $ciudad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ciudad->ciudadesid); ?>"><?php echo e($ciudad->ciudad); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>Direccion</label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control textarea-autogrow mb-3" placeholder="Su Direccion"
                                    rows="1" name="direccion" onkeydown="controlar(event)" required
                                    maxlength="100"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>Telefono</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="999999999" name="telefono"
                                    value="" required autocomplete="off">
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

<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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
                }
            });
        }

        function add_new_address() {
            $('#new-address-modal').modal('show');
        }

        function controlar(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                return false;
            }
        }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/frontend/shipping_info.blade.php ENDPATH**/ ?>