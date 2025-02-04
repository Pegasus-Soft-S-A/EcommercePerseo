<?php

namespace App\Http\Controllers;

use App\Models\ParametrosEmpresa;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function index()
    {
        $grupo = get_setting('grupo_productos');
        $grupoid = getGrupoID();
        $parametros = ParametrosEmpresa::first();
        if ($parametros->tipopresentacionprecios == 1) {
            $wishlists = Wishlist::select('ecommerce_lista_deseos.ecommerce_lista_deseosid', 'ecommerce_lista_deseos.clientesid', 'ecommerce_lista_deseos.productosid', 'productos_imagenes.imagen', 'productos.productocodigo', 'productos.descripcion', 'productos.parametros_json', 'productos_tarifas.precioiva as precio2', DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
        } else {
            $wishlists = Wishlist::select('ecommerce_lista_deseos.ecommerce_lista_deseosid', 'ecommerce_lista_deseos.clientesid', 'ecommerce_lista_deseos.productosid', 'productos_imagenes.imagen', 'productos.productocodigo', 'productos.descripcion', 'productos.parametros_json', 'productos_tarifas.precio as precio2', DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
        }

        $wishlists = $wishlists->join('productos', 'productos.productosid', 'ecommerce_lista_deseos.productosid')
            ->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'ecommerce_lista_deseos.productosid')
            ->leftJoin('productos_imagenes', function ($products) {
                $products->on('productos_imagenes.productosid', '=', 'ecommerce_lista_deseos.productosid')
                    ->where('productos_imagenes.principal', '=', "1");
            })
            ->where('productos_tarifas.tarifasid', '=', get_setting('tarifa_productos'))
            ->where('productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna'))
            ->where('ecommerce_lista_deseos.clientesid', Auth::user()->clientesid);

        // Agregar condición adicional si maneja_sucursales está activado
        if (get_setting('maneja_sucursales') == "on") {
            $wishlists->where('ecommerce_lista_deseos.clientes_sucursalesid', session('sucursalid'));
        }

        // Finalizar con la paginación
        $wishlists = $wishlists->paginate(9);


        return view('frontend.cliente.view_wishlist', compact('wishlists'));
    }
    public function store(Request $request)
    {
        if (get_setting('maneja_sucursales') == "on") {
            $wishlist = Wishlist::where('clientes_sucursalesid', session('sucursalid'))->where('productosid', $request->id)->first();
        } else {
            $wishlist = Wishlist::where('clientesid', Auth::user()->clientesid)->where('productosid', $request->id)->first();
        }
        if ($wishlist == null) {
            $wishlist = new Wishlist;
            $wishlist->clientesid = Auth::user()->clientesid;
            $wishlist->productosid = $request->id;
            if (get_setting('maneja_sucursales') == "on") {
                $wishlist->clientes_sucursalesid = session('sucursalid');
            }
            $wishlist->save();
            return 1;
        } else {
            return 2;
        }
    }

    public function remove(Request $request)
    {
        $wishlist = Wishlist::findOrFail($request->id);
        if ($wishlist != null) {
            if (Wishlist::destroy($request->id)) {
                return view('frontend.partials.wishlist');
            }
        }
    }
}
