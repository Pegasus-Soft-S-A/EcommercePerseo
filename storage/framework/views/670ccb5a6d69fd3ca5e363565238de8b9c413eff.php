<?php $__env->startSection('content'); ?>
<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">Dashboard</h5>
            </div>

            <div class="col-lg-3">
                <div class="form-group mb-0">
                    <input id="buscarFecha" type="text" class="aiz-date-range form-control" value="<?php echo e($fecha); ?>"
                        name="fecha" placeholder="Filtrar por Fecha" data-format="DD-MM-Y" data-separator=" a "
                        data-advanced-range="true" autocomplete="off">

                </div>
            </div>

            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="row gutters-10">
    <div class="col-lg-12">
        <div class="row gutters-10">
            <div class="col-3">
                <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            Pedidos
                        </div>
                        <div class="h3 fw-700 mb-3">
                            <?php
                            use Illuminate\Support\Facades\DB;
                            if ($fecha == null) {
                                $totalPedidos = \App\Models\Pedidos::where(DB::raw('YEARWEEK(emision)'), DB::raw('YEARWEEK(CURDATE())'))
                                    ->where('pedidos.usuariocreacion', 'Ecommerce')->count();
                            }
                             ?>
                            <?php echo e($totalPedidos); ?>

                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                            d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            Facturas
                        </div>
                        <div class="h3 fw-700 mb-3">
                            <?php
                            if ($fecha == null) {
                                $totalFacturas = \App\Models\Facturas::where(DB::raw('YEARWEEK(emision)'), DB::raw('YEARWEEK(CURDATE())'))
                                    ->where('facturas.usuariocreacion', 'Ecommerce')->count();
                            }
                            ?>
                            <?php echo e($totalFacturas); ?>

                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                            d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            Pedidos Realizados
                        </div>
                        <div class="h3 fw-700 mb-3">
                            <?php
                            if ($fecha == null) {
                                $totalPedidosRealizados = \App\Models\Pedidos::where(DB::raw('YEARWEEK(emision)'), DB::raw('YEARWEEK(CURDATE())'))
                                    ->where('pedidos.usuariocreacion', 'Ecommerce')
                                    ->where('pedidos.estado', 1)->count();
                            }
                            ?>
                            <?php echo e($totalPedidosRealizados); ?>

                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                            d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-grad-4 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            Pedidos Confirmados
                        </div>
                        <div class="h3 fw-700 mb-3">
                            <?php
                            if ($fecha == null) {
                                $totalPedidosConfirmados = \App\Models\Pedidos::where(DB::raw('YEARWEEK(emision)'), DB::raw('YEARWEEK(CURDATE())'))
                                    ->where('pedidos.usuariocreacion', 'Ecommerce')
                                    ->where('pedidos.estado', 2)->count();
                            }
                            ?>
                            <?php echo e($totalPedidosConfirmados); ?>

                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                            d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row gutters-10">
    <div class="col-lg-12">
        <div class="row gutters-10">
            <div class="col-3">
                <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            Pedidos Facturados
                        </div>
                        <div class="h3 fw-700 mb-3">
                            <?php
                    if ($fecha == null) {
                        $totalPedidosFacturados = \App\Models\Pedidos::where(DB::raw('YEARWEEK(emision)'), DB::raw('YEARWEEK(CURDATE())'))
                            ->where('pedidos.usuariocreacion', 'Ecommerce')
                            ->where('pedidos.estado', 3)->count();
                    }
                    ?>
                            <?php echo e($totalPedidosFacturados); ?>

                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                            d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            Pedidos en la Entrega
                        </div>
                        <div class="h3 fw-700 mb-3">
                            <?php
                    if ($fecha == null) {
                        $totalPedidosEnLaEntrega = \App\Models\Pedidos::where(DB::raw('YEARWEEK(emision)'), DB::raw('YEARWEEK(CURDATE())'))
                            ->where('pedidos.usuariocreacion', 'Ecommerce')
                            ->where('pedidos.estado', 4)->count();
                    }
                    ?>
                            <?php echo e($totalPedidosEnLaEntrega); ?>

                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                            d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
                    <div class="px-3 pt-3">
                        <div class="opacity-50">
                            Pedidos Entregados
                        </div>
                        <div class="h3 fw-700 mb-3">
                            <?php
                    if ($fecha == null) {
                        $totalPedidosEntregados = \App\Models\Pedidos::where(DB::raw('YEARWEEK(emision)'), DB::raw('YEARWEEK(CURDATE())'))
                            ->where('pedidos.usuariocreacion', 'Ecommerce')
                            ->where('pedidos.estado', 5)->count();
                    }
                    ?>
                            <?php echo e($totalPedidosEntregados); ?>

                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                        <path fill="rgba(255,255,255,0.3)" fill-opacity="1"
                            d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script>
    var options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric'
    };
    var curr = new Date;
    var firstday = new Date(curr.setDate(curr.getDate() - curr.getDay()));
    var lastday = new Date(curr.setDate(curr.getDate() - curr.getDay() + 6));

    var dia1 = firstday.toLocaleDateString("es-ES", options);
    var dia7 = lastday.toLocaleDateString("es-ES", options);
    var mostrarFecha = dia1 + ' a ' + dia7;

    console.log(mostrarFecha);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/backend/dashboard.blade.php ENDPATH**/ ?>