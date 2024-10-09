<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
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
        $estado = null;

        $orders = Pedidos::where('clientesid', Auth::user()->clientesid)
            ->where('usuariocreacion', 'Ecommerce')
            ->orderBy('fechacreacion', 'desc');

        if ($request->estado != null) {
            $orders = $orders->where('pedidos.estado', $request->estado);
            $estado = $request->estado;
        }

        if ($fecha != null) {
            $orders = $orders->whereDate('pedidos.fechacreacion', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.fechacreacion', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        $orders = $orders->orderBy('pedidos.emision', 'desc')
            ->paginate(9);

        return view('frontend.cliente.purchase_history', compact('orders', 'estado', 'fecha'));
    }

    public function purchase_history_details(Request $request)
    {

        $pedido = Pedidos::where('pedidosid', $request->pedido_id)->first();
        $cliente = Clientes::select('email', 'razonsocial')->where('clientesid', $pedido->clientesid)->first();
        $detalles = PedidosDetalles::where('pedidosid', $pedido->pedidosid)->get();

        return view('frontend.cliente.order_details_customer', compact('pedido', 'cliente', 'detalles'));
    }

    public function descargar_pedido($id)
    {
        $order = Pedidos::where('pedidosid', $id)->first();
        $detalles = PedidosDetalles::where('pedidosid', $id)->get();
        $cliente = Clientes::select('email', 'razonsocial')->where('clientesid', $order->clientesid)->first();
        $direccion = \App\Models\ClientesSucursales::where('clientes_sucursalesid', $order->clientes_sucursalesid)->first();

        $datosEmpresa = ParametrosEmpresa::select('nombrecomercial', 'email', 'telefono1')->first();
        $font_family = "'Roboto','sans-serif'";
        $direction = 'ltr';
        $text_align = 'left';
        $not_text_align = 'left';

        return \PDF::loadView('pedidos', [
            'order' => $order,
            'detalle' => $detalles,
            'cliente' => $cliente,
            'direccion' => $direccion,
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
        $estado = null;

        $orders = Pedidos::where('clientesid', Auth::user()->clientesid)
            ->where('usuariocreacion', 'Ecommerce')
            ->orderBy('fechacreacion', 'desc');

        if ($request->estado != null) {
            $orders = $orders->where('pedidos.estado', $request->estado);
            $estado = $request->estado;
        }

        if ($fecha != null) {
            $orders = $orders->whereDate('pedidos.fechacreacion', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.fechacreacion', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        // Generar la vista para el PDF
        $pdf = \PDF::loadView('orders', [
            'orders' => $orders->get(),
        ]);

        // Descargar el archivo PDF con un nombre adecuado
        return $pdf->download('Pedidos.pdf');
    }
}
