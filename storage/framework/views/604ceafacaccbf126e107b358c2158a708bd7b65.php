<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Pedidos</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3>Historial de Pedidos</h3>
    <table>
        <thead>
            <tr>
                <th>Codigo</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($order->pedidos_codigo); ?></td>
                <td><?php echo e($order->emision); ?></td>
                <td>$<?php echo e(number_format(round($order->total, 2), 2)); ?></td>
                <td>
                    <?php switch($order->estado):
                    case (1): ?>
                    Pedido Realizado
                    <?php break; ?>
                    <?php case (2): ?>
                    Pedido Confirmado
                    <?php break; ?>
                    <?php case (3): ?>
                    Pedido Facturado
                    <?php break; ?>
                    <?php case (4): ?>
                    En la Entrega
                    <?php break; ?>
                    <?php case (5): ?>
                    Entregado
                    <?php break; ?>
                    <?php case (0): ?>
                    No Aplica
                    <?php break; ?>
                    <?php default: ?>
                    -
                    <?php endswitch; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</body>

</html>
<?php /**PATH C:\laragon\www\tienda\resources\views/orders.blade.php ENDPATH**/ ?>