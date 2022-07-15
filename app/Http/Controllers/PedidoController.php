<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use App\Models\Pedidos;
use App\Models\PedidosDetalles;

class PedidoController extends Controller
{
    public function verTodos(Request $request)
    {
        $fecha = $request->fecha;
        $estado = null;
        $busqueda = null;

        $pedidos = Pedidos::select('pedidos.pedidosid', 'pedidos.pedidos_codigo', 'pedidos.total', 'pedidos.estado', 'clientes.razonsocial', 'pedidos.documentosid')
            ->join('clientes', 'clientes.clientesid', '=', 'pedidos.clientesid')
            ->where('pedidos.usuariocreacion', 'Ecommerce');

        if ($request->estado != null) {
            $pedidos = $pedidos->where('pedidos.estado', $request->estado);
            $estado = $request->estado;
        }

        if ($fecha != null) {
            $pedidos = $pedidos->whereDate('pedidos.fechacreacion', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.fechacreacion', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        if ($request->busqueda != null) {
            $busqueda = $request->busqueda;
            $pedidos = $pedidos->where('clientes.razonsocial', 'like', '%' . $busqueda . '%')
                ->orwhere('pedidos.pedidos_codigo', 'like', '%' . $busqueda . '%');
        }

        $pedidos = $pedidos->orderBy('pedidos.emision', 'desc')
            ->paginate(15);

        return view('backend.pedidos', compact('pedidos', 'estado', 'busqueda', 'fecha'));
    }

    public function destroy($id)
    {
        $order = Pedidos::findOrFail($id);
        if ($order->documentosid == 0) {
            PedidosDetalles::where('pedidosid', $id)->delete();
            $order->delete();
            flash('Pedido eliminado correctamente')->success();
        } else {
            flash('El pedido ya se encuentra facturado')->error();
        }
        return back();
    }

    public function pedidos_show($id)
    {
        $pedido = Pedidos::findOrFail($id);
        $detalle = PedidosDetalles::where('pedidosid', $id)->get();
        $cliente = Clientes::where('clientesid', $pedido->clientesid)->first();
        return view('backend.pedidos_show', compact('pedido', 'detalle', 'cliente'));
    }

    public function actualizar_estado(Request $request)
    {
        $order = Pedidos::findOrFail($request->order_id);
        $order->estado = $request->status;
        $order->save();
        return 1;
    }
}
