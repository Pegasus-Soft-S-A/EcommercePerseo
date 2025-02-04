<?php

namespace App\Http\Controllers;

use App\Models\CentroCostos;
use App\Models\Clientes;
use App\Models\ClientesSucursales;
use App\Models\Pedidos;
use App\Models\PedidosDetalles;
use App\Models\ParametrosEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HistorialPedidosController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->fecha;
        $estado = $request->estado;
        $centrocostoid = $request->centrocostoid;
        $destinatario = $request->destinatario;
        $centrocostos = CentroCostos::where('estado', 1)->get();
        // Iniciar la consulta base
        $ordersQuery = Pedidos::select('pedidos.pedidosid', 'pedidos.pedidos_codigo', 'pedidos.emision', 'pedidos.estado', 'pedidos.prioridad', 'pedidos.total', 'pedidos.observacion')
            ->where('pedidos.clientesid', Auth::user()->clientesid)
            ->where('pedidos.usuariocreacion', 'Ecommerce')
            ->where('pedidos.documentosid', 0);

        if (get_setting('maneja_sucursales') == "on") {
            $ordersQuery = $ordersQuery->where('pedidos.clientes_sucursalesid', session('sucursalid'));
        }

        // Filtrar por estado si está presente en la solicitud
        if (!is_null($estado)) {
            $ordersQuery = $ordersQuery->where('pedidos.estado', $estado);
        }

        // Filtrar por estado si está presente en la solicitud
        if (!is_null($centrocostoid)) {
            $ordersQuery = $ordersQuery->where('pedidos.centros_costosid', $centrocostoid);
        }

        // Filtrar por estado si está presente en la solicitud
        if (!is_null($destinatario)) {
            $ordersQuery = $ordersQuery->where('pedidos.observacion', 'like', '%' . $destinatario . '%');
        }
        // Filtrar por rango de fechas si está presente en la solicitud
        if (!is_null($fecha)) {
            $fechas = explode(" a ", $fecha);
            $ordersQuery = $ordersQuery->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime($fechas[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime($fechas[1])));
        }

        // Ordenar y paginar los resultados
        $orders = $ordersQuery->orderBy('pedidos.emision', 'desc')->paginate(9);
        // Recorrer los pedidos y extraer el "Urbano" y "Destinatario" de la observación
        foreach ($orders as $pedido) {
            $observacion = $pedido->observacion;
            $urbano = null;
            $destinatarioLista = null;

            // Primero, extraemos el "Destinatario"
            if (preg_match('/Destinatario:\s*([^;]+)/', $observacion, $matches)) {
                $destinatarioLista = $matches[1]; // El valor del destinatario
            }
            // Usamos la expresión regular para extraer el valor de "Urbano"
            if (preg_match('/Urbano:\s*([^;]+)/', $observacion, $matches)) {
                $urbano = $matches[1]; // El valor de "Urbano"
            }

            // Agregar el valor de "Urbano" al objeto pedido (puedes agregarlo como una propiedad temporal)
            $pedido->urbano = $urbano; // Ahora el objeto pedido tiene la propiedad 'urbano'
            $pedido->destinatario = $destinatarioLista; // Ahora el objeto pedido tiene la propiedad 'urbano'
        }
        return view('frontend.cliente.purchase_history', compact('orders', 'estado', 'fecha', 'centrocostos', 'centrocostoid', 'destinatario'));
    }


    public function purchase_history_details(Request $request)
    {

        $pedido = Pedidos::where('pedidosid', $request->pedido_id)->first();
        $cliente = Clientes::select('email', 'razonsocial')->where('clientesid', $pedido->clientesid)->first();
        $detalles = PedidosDetalles::where('pedidosid', $pedido->pedidosid)->get();

        // Variable que indica si hay algún detalle modificado
        $modificado = $detalles->contains(function ($detalle) {
            return $detalle->cantidadentregada != 0; // Condición para marcar como modificado
        });

        $centrocosto = CentroCostos::where('centros_costosid', $pedido->centros_costosid)->first();
        // Extraemos la parte que sigue a "Destinatario:" considerando saltos de línea
        $observacion = $pedido->observacion;
        $destinatario = null;

        // Primero, extraemos el "Destinatario"
        if (preg_match('/Destinatario:\s*([^;]+)/', $observacion, $matches)) {
            $destinatario = $matches[1]; // El valor del destinatario
        }

        return view('frontend.cliente.order_details_customer', compact('pedido', 'cliente', 'detalles', 'centrocosto', 'destinatario', 'modificado'));
    }

    public function descargar_pedido($id)
    {
        $order = Pedidos::where('pedidosid', $id)->first();
        $detalles = PedidosDetalles::where('pedidosid', $id)->get();
        $cliente = Clientes::select('email', 'razonsocial')->where('clientesid', $order->clientesid)->first();
        $sucursal = ClientesSucursales::where('clientes_sucursalesid', $order->clientes_sucursalesid)->first();
        $datosEmpresa = ParametrosEmpresa::select('nombrecomercial', 'email', 'telefono1')->first();
        $font_family = "'Roboto','sans-serif'";
        $direction = 'ltr';
        $text_align = 'left';
        $not_text_align = 'left';

        return \PDF::loadView('pedidos', [
            'order' => $order,
            'detalle' => $detalles,
            'cliente' => $cliente,
            'direccion' => $sucursal->direccion,
            'telefono' => $sucursal->telefono1,
            'datosEmpresa' => $datosEmpresa,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align
        ], [], 'utf-8')->download('Pedido-' . $order->pedidos_codigo . '.pdf');
    }

    public function exportPdf(Request $request)
    {
        $fecha = $request->fecha;

        $orders = Pedidos::where('clientesid', Auth::user()->clientesid)
            ->where('usuariocreacion', 'Ecommerce')
            ->orderBy('fechacreacion', 'desc');

        if ($request->estado != null) {
            $orders = $orders->where('pedidos.estado', $request->estado);
        }

        if ($fecha != null) {
            $orders = $orders->whereDate('pedidos.fechacreacion', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.fechacreacion', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        // Generar la vista para el PDF
        $pdf = \PDF::loadView('orders', [
            'orders' => $orders->get(),
        ]);

        // Procesar los registros en bloques de 500
        // $orders->chunk(500, function ($ordersChunk) use ($pdf) {
        //     $view = view('orders', ['orders' => $ordersChunk])->render();
        //     $pdf->appendPage($view);
        // });

        // Descargar el archivo PDF con un nombre adecuado
        return $pdf->download('Pedidos.pdf');
    }
}
