<?php

namespace App\Http\Controllers;

use App\Models\ClientesSucursales;
use App\Models\Pedidos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DireccionController extends Controller
{
    public function store(Request $request)
    {
        $address = new ClientesSucursales();
        $address->clientesid = Auth::user()->clientesid;
        $address->ciudadesid = str_pad($request->ciudad, '4', "0", STR_PAD_LEFT);
        $address->direccion = $request->direccion;
        $address->telefono1 = $request->telefono;
        $address->fechacreacion = now();
        $address->usuariocreacion = 'Ecommerce';
        $address->save();
        flash('Direccion Agregada Correctamente')->success();
        return back();
    }

    public function edit($id)
    {
        $data['address_data'] = ClientesSucursales::findOrFail($id);
        return view('frontend.edit_address_modal', $data);
    }

    public function update(Request $request, $id)
    {
        $address = ClientesSucursales::findOrFail($id);

        $address->clientesid = Auth::user()->clientesid;
        $address->ciudadesid = str_pad($request->ciudad, '4', "0", STR_PAD_LEFT);
        $address->direccion = $request->direccion;
        $address->telefono1 = $request->telefono;
        $address->fechamodificacion = now();
        $address->usuariomodificacion = 'Ecommerce';
        $address->save();

        flash('Direccion Actualizada Correctamente')->success();
        return back();
    }

    public function destroy($id)
    {
        $pedido = Pedidos::where('clientes_sucursalesid', $id)->get();
        if (count($pedido) > 0) {
            flash('Ya existen pedidos con esta direccion')->error();
        } else {
            $address = ClientesSucursales::findOrFail($id);
            $address->delete();
            flash('Eliminado Correctamente')->success();
        }
        return back();
    }
}
