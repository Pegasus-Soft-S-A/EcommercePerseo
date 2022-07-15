<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\Facturas;
use App\Models\FacturasDetalles;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function verTodos(Request $request)
    {
        $fecha = $request->fecha;
        $estado = null;
        $busqueda = null;

        $facturas = Facturas::select('facturas.facturasid', 'facturas.establecimiento', 'facturas.puntoemision', 'facturas.secuencial', 'clientes.razonsocial', 'facturas.total', 'facturas.estado')
            ->join('clientes', 'clientes.clientesid', '=', 'facturas.clientesid')
            ->where('facturas.usuariocreacion', 'Ecommerce');

        if ($request->estado != null) {
            $facturas = $facturas->where('facturas.estado', $request->estado);
            $estado = $request->estado;
        }

        if ($fecha != null) {
            $facturas = $facturas->whereDate('facturas.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('facturas.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        if ($request->busqueda != null) {
            $busqueda = $request->busqueda;
            $facturas = $facturas->where('clientes.razonsocial', 'like', '%' . $busqueda . '%');
        }

        $facturas = $facturas->orderBy('facturas.emision', 'desc')
            ->paginate(15);

        return view('backend.facturas', compact('facturas', 'fecha', 'busqueda', 'estado'));
    }

    public function facturas_show($id)
    {
        $factura = Facturas::findOrFail($id);
        $detalle = FacturasDetalles::where('facturasid', $id)->get();
        $cliente = Clientes::where('clientesid', $factura->clientesid)->first();
        return view('backend.facturas_show', compact('factura', 'detalle', 'cliente'));
    }

    public function actualizar_estado(Request $request)
    {
        $factura = Facturas::findOrFail($request->order_id);
        $factura->estado = $request->status;
        $factura->save();
        return 1;
    }
}
