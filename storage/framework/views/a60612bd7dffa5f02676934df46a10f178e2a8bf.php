<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Configuraciones SMTP</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="<?php echo e(route('smtp_settings.update')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-from-label">Host</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="smtp_servidor"
                                value="<?php echo e($parametros->smtp_servidor); ?>" autocomplete="off" placeholder="Host">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-from-label">Puerto</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="smtp_puerto"
                                value="<?php echo e($parametros->smtp_puerto); ?>" autocomplete="off" placeholder="Puerto">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-from-label">Correo Remitente</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="smtp_from"
                                value="<?php echo e($parametros->smtp_from); ?>" autocomplete="off" placeholder="Correo Remitente">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-from-label">Usuario</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="smtp_usuario"
                                value="<?php echo e($parametros->smtp_usuario); ?>" autocomplete="off" placeholder="Usuario">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label class="col-from-label">Contraseña</label>
                        </div>
                        <div class="col-md-9">
                            <input type="password" class="form-control" name="smtp_clave"
                                value="<?php echo e($parametros->smtp_clave); ?>" autocomplete="off" placeholder="Contraseña">
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">Guardar Configuracion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Probar la configuración de SMTP </h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('test.smtp')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <input type="email" class="form-control" name="email" value="" autocomplete="off"
                                placeholder="Ingrese Correo">
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">Enviar Email de Prueba</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/backend/smtp_settings.blade.php ENDPATH**/ ?>