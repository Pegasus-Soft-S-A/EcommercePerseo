<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Facturas</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 2px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h3>Historial de Facturas</h3>
    <table>
        <thead>
            <tr>
                <th>Secuencial</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Codigo Pedido</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->establecimiento}}-{{$order->puntoemision }}-{{$order->secuencial}}</td>
                <td>{{ $order->emision }}</td>
                <td>${{ number_format(round($order->total, 2), 2) }}</td>
                <td>
                    {{$order->pedidos_codigo}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>