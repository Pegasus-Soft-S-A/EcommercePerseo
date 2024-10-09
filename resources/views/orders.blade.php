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
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->pedidos_codigo }}</td>
                <td>{{ $order->emision }}</td>
                <td>${{ number_format(round($order->total, 2), 2) }}</td>
                <td>
                    @switch($order->estado)
                    @case(1)
                    Pedido Realizado
                    @break
                    @case(2)
                    Pedido Confirmado
                    @break
                    @case(3)
                    Pedido Facturado
                    @break
                    @case(4)
                    En la Entrega
                    @break
                    @case(5)
                    Entregado
                    @break
                    @case(0)
                    No Aplica
                    @break
                    @default
                    -
                    @endswitch
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
