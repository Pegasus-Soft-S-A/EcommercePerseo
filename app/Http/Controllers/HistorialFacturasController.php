<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facturas;
use App\Models\FacturasDetalles;
use App\Models\Clientes;
use Illuminate\Support\Facades\Auth;

class HistorialFacturasController extends Controller
{
    public function index()
    {
        $orders = Facturas::where('clientesid', Auth::user()->clientesid)
            ->where('usuariocreacion', 'Ecommerce')
            ->orderBy('fechacreacion', 'desc')->paginate(9);
        return view('frontend.cliente.facturas_history', compact('orders'));
    }
    public function factura_history_details(Request $request)
    {
        $factura = Facturas::where('facturasid', $request->factura_id)->first();
        $cliente = Clientes::select('email', 'razonsocial')->where('clientesid', $factura->clientesid)->first();
        $detallesFactura = FacturasDetalles::where('facturasid', $request->factura_id)->get();
        return view('frontend.cliente.facturas_details_customer', compact('factura', 'cliente', 'detallesFactura'));
    }
}
