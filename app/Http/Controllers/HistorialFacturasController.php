<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facturas;
use App\Models\FacturasDetalles;
use App\Models\Clientes;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class HistorialFacturasController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->fecha;
        $orders = Facturas::select('facturas.establecimiento', 'facturas.puntoemision', 'facturas.secuencial', 'facturas.facturasid', 'facturas.estado', 'facturas.emision', 'facturas.total', 'pedidos.pedidos_codigo', 'pedidos.pedidosid', 'facturas.archivo_xml')
            ->where('facturas.clientesid', Auth::user()->clientesid)
            ->where('pedidos.usuariocreacion', 'Ecommerce')
            ->join('pedidos', 'pedidos.documentosid', '=', 'facturas.facturasid');

        // Agregar join si maneja sucursales
        if (get_setting('maneja_sucursales') == "on") {
            $orders = $orders->where('facturas.clientes_sucursalesid', session('sucursalid'));
        }

        if ($fecha != null) {
            $orders = $orders->whereDate('facturas.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('facturas.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        $orders = $orders->orderBy('facturas.emision', 'desc')
            ->paginate(9);

        return view('frontend.cliente.facturas_history', compact('orders', 'fecha'));
    }

    public function factura_history_details(Request $request)
    {
        $factura = Facturas::where('facturasid', $request->factura_id)->first();
        $cliente = Clientes::select('email', 'razonsocial')->where('clientesid', $factura->clientesid)->first();
        $detallesFactura = FacturasDetalles::where('facturasid', $request->factura_id)->get();

        return view('frontend.cliente.facturas_details_customer', compact('factura', 'cliente', 'detallesFactura'));
    }

    public function exportarPdf(Request $request)
    {

        $fecha = $request->fecha;
        $orders = Facturas::select('facturas.establecimiento', 'facturas.puntoemision', 'facturas.secuencial', 'facturas.facturasid', 'facturas.estado', 'facturas.emision', 'facturas.total', 'pedidos.pedidos_codigo', 'pedidos.pedidosid')
            ->where('facturas.clientesid', Auth::user()->clientesid)
            ->where('pedidos.usuariocreacion', 'Ecommerce')
            ->join('pedidos', 'pedidos.documentosid', '=', 'facturas.facturasid');

        if ($fecha != null) {
            $orders = $orders->whereDate('facturas.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('facturas.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        $orders = $orders->orderBy('facturas.emision', 'desc');

        // Generar la vista para el PDF
        $pdf = \PDF::loadView('facturas', [
            'orders' => $orders->get(),
        ]);

        // Descargar el archivo PDF con un nombre adecuado
        return $pdf->download('Facturas.pdf');
    }

    public function downloadXml($id)
    {
        // Buscar el pedido en la base de datos por su ID
        $order = Facturas::findOrFail($id);

        // Recuperar el XML almacenado en la columna de la base de datos (asumiendo que la columna es 'archivo_xml')
        $xmlContent = $order->archivo_xml;

        if (!$xmlContent) {
            return redirect()->back()->with('error', 'No se encontró el archivo XML para este pedido.');
        }

        // Crear una respuesta para la descarga del archivo XML
        return Response::make($xmlContent, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="factura_' . $order->secuencial . '.xml"',
        ]);
    }
}
