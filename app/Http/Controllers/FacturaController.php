<?php

namespace App\Http\Controllers;

use App\Models\CentroCostos;
use App\Models\Clientes;
use App\Models\Facturas;
use App\Models\FacturasDetalles;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function verTodos(Request $request)
    {
        $fecha = $request->fecha;
        $busqueda = null;
        $centrocostos = CentroCostos::where('estado', 1)->orderBy('centro_costocodigo')->get();
        $centrocostoid = $request->centrocostoid;

        if (get_setting('maneja_sucursales') == "on") {
            $facturas = Facturas::select('facturas.facturasid', 'facturas.establecimiento', 'facturas.puntoemision', 'facturas.secuencial', 'clientes_sucursales.descripcion', 'facturas.total', 'facturas.estado')
                ->join('clientes_sucursales', 'clientes_sucursales.clientes_sucursalesid', '=', 'facturas.clientes_sucursalesid')
                ->where('facturas.usuariocreacion', 'Ecommerce');
        } else {
            $facturas = Facturas::select('facturas.facturasid', 'facturas.establecimiento', 'facturas.puntoemision', 'facturas.secuencial', 'clientes.razonsocial', 'facturas.total', 'facturas.estado')
                ->join('clientes', 'clientes.clientesid', '=', 'facturas.clientesid')
                ->where('facturas.usuariocreacion', 'Ecommerce');
        }

        if ($fecha != null) {
            $facturas = $facturas->whereDate('facturas.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('facturas.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        // Filtrar por estado si está presente en la solicitud
        if (!is_null($centrocostoid)) {
            $facturas = $facturas->where('facturas.centros_costosid', $centrocostoid);
        }

        if ($request->busqueda != null) {
            $busqueda = $request->busqueda;
            $facturas = $facturas->where('clientes.razonsocial', 'like', '%' . $busqueda . '%');
        }

        $facturas = $facturas->orderBy('facturas.emision', 'desc')
            ->paginate(15);

        return view('backend.facturas', compact('facturas', 'fecha', 'busqueda', 'centrocostos', 'centrocostoid'));
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
