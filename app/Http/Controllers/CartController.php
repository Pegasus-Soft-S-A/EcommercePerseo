<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Categorias;
use App\Models\MovimientosInventariosAlmacenes;
use App\Models\ParametrosEmpresa;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\ProductoTarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user() != null) {
            $clientesid = Auth::user()->clientesid;
            $carts = Carrito::where('clientesid', $clientesid)->get();
        } else {
            $usuario_temporalid = $request->session()->get('usuario_temporalid');
            $carts = Carrito::where('usuario_temporalid', $usuario_temporalid)->get();
        }
       
       
        return view('frontend.view_cart', compact('carts'));
    }

    public function showCartModal(Request $request)
    {

        //presentar un producto
        $parametros = ParametrosEmpresa::first();
        $product = Producto::where('productosid', $request->productosid)->first();
        $precioProducto2 = "";
        if ($parametros->tipopresentacionprecios == 1) {
            $precioProducto = ProductoTarifa::select('productos_tarifas.precioiva as precio');
        } else {
            $precioProducto = ProductoTarifa::select('productos_tarifas.precio');
        }
        $precioProducto = $precioProducto->join('productos', 'productos.productosid', '=', 'productos_tarifas.productosid')
            ->where('productos_tarifas.productosid', $request->productosid)
            ->where('productos_tarifas.tarifasid', '=', get_setting('tarifa_productos'))
            ->where('productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna'))
            ->first();

        if (Auth::check()) {
            if ($parametros->tipopresentacionprecios == 1) {
                $precioProducto2 = ProductoTarifa::select('productos_tarifas.precioiva as precio',  'productos_tarifas.factor');
            } else {
                $precioProducto2 = ProductoTarifa::select('productos_tarifas.precio',  'productos_tarifas.factor');
            }
            $precioProducto2 = $precioProducto2->join('productos', 'productos.productosid', '=', 'productos_tarifas.productosid')
                ->where('productos_tarifas.productosid', $request->productosid)
                ->where('productos_tarifas.tarifasid', '=', auth()->user()->tarifasid)
                ->where('productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna'))
                ->first();
        }

        $imagenProducto =  ProductoImagen::select('productos_imagenes.imagen', 'productos_imagenes.medidasid')
            ->where('productos_imagenes.ecommerce_visible', '=', '1')
            ->where('productos_imagenes.productosid', '=', $request->productosid)
            ->orderBy('productos_imagenes.medidasid')
            ->get();

        $medidas = ProductoTarifa::select('medidas.descripcion', 'medidas.medidasid', 'productos_tarifas.factor')
            ->join('medidas', 'medidas.medidasid', '=', 'productos_tarifas.medidasid')
            ->where('productos_tarifas.productosid', '=', $request->productosid)
            ->groupBy('medidas.descripcion', 'medidas.medidasid', 'productos_tarifas.factor')
            ->orderBy('medidas.medidasid')
            ->get();

        $min_qty = 1;

        // $idProducto = $request->productosid;

        return view('frontend.partials.addToCart', compact('product', 'precioProducto', 'precioProducto2', 'imagenProducto', 'min_qty', 'medidas'));
    }

    public function addToCart(Request $request)
    {
        
        $product = Producto::where('productosid', $request->id)->first();
        $almacenes = DB::connection('empresa')->table('facturadores_almacenes')
        ->where('facturadoresid', get_setting('facturador'))
        ->where('principal', '1')
        ->first();
        $imagenProducto =  ProductoImagen::select('productos_imagenes.imagen')
            ->where('productos_imagenes.ecommerce_visible', '=', '1')
            ->where('productos_imagenes.productosid', '=', $request->id)
            ->where('productos_imagenes.medidasid', '=', $request->medidasid)
            ->first();

        $carts = array();
        $data = array();

        if (Auth::check()) {
            $clientesid = Auth::user()->clientesid;
            $data['clientesid'] = $clientesid;
            $data['descuento'] = Auth::user()->descuento;
            $carts = Carrito::where('clientesid', $clientesid)->get();
        } else {
            if ($request->session()->get('usuario_temporalid')) {
                $usuario_temporalid = $request->session()->get('usuario_temporalid');
            } else {
                $usuario_temporalid = bin2hex(random_bytes(10));
                $request->session()->put('usuario_temporalid', $usuario_temporalid);
            }
            $data['usuario_temporalid'] = $usuario_temporalid;
            $carts = Carrito::where('usuario_temporalid', $usuario_temporalid)->get();
        }

        $data['productosid'] = $product->productosid;
        $data['medidasid'] = $request['medidasid'];
        $data['cantidad'] = $request['quantity'];
        $data['precio'] = $request->preciocompleto;
        $data['precioiva'] = $request->precioIVA;
        $data['cantidadfactor'] = $request->factor;
        $data['almacenesid'] =  get_setting('controla_stock') != 2 ? $almacenes->almacenesid :  $request->variableinicio;

        switch ($product->sri_tipos_ivas_codigo) {
            case '0':
                $data['iva'] = 0;
                break;
            case '2':
                $data['iva'] = 12;
                break;
            default:
                $data['iva'] = 0;
                break;
        }

        if ($request['quantity'] == null ) {
            $data['cantidad'] = 1;
        }


        if ($carts && count($carts) > 0) {
            $encontroEnCarro = false;

            foreach ($carts as $key => $cartItem) {
                if ($cartItem['productosid'] == $request->id && $cartItem['medidasid'] == $request->medidasid && $cartItem['almacenesid'] == $request->variableinicio ) {
             
                    $encontroEnCarro = true;
                    $cartItem['cantidad'] += $request['quantity'];
                    $cartItem->save();
                }
            }
            if (!$encontroEnCarro) {
                Carrito::create($data);
            }
        } else {
            Carrito::create($data);
        }

        return array('status' => 1, 'view' => view('frontend.partials.addedToCart', compact('product', 'data', 'imagenProducto'))->render());
    }

    public function removeFromCart(Request $request)
    {
        Carrito::destroy($request->id);
        if (auth()->user() != null) {
            $clientesid = Auth::user()->clientesid;
            $carts = Carrito::where('clientesid', $clientesid)->get();
        } else {
            $usuario_temporalid = $request->session()->get('usuario_temporalid');
            $carts = Carrito::where('usuario_temporalid', $usuario_temporalid)->get();
        }

        return view('frontend.partials.cart_details', compact('carts'));
    }

    public function updateNavCart(Request $request)
    {
        return view('frontend.partials.cart');
    }

    public function updateQuantity(Request $request)
    {
        $carrito = Carrito::findOrFail($request->id);

        if ($carrito['ecommerce_carritosid'] == $request->id) {
            $product = Producto::where('productosid', $carrito['productosid'])->first();


            if ( get_setting('controla_stock') == 0) {
                $quantity = get_setting('cantidad_maxima');
            } else {
                $quantity = $request->cantidad;
            } 
            
            if ($quantity >= $request->quantity) {
                if ($request->quantity >= 1) {
                    $carrito['cantidad'] = $request->quantity;
                } else {
                    $carrito['cantidad'] = 1;
                }
            } else {
                $carrito['cantidad'] = 1;
            }

            $carrito->save();
        }


        if (auth()->user() != null) {
            $clientesid = Auth::user()->clientesid;
            $carts = Carrito::where('clientesid', $clientesid)->get();
        } else {
            $usuario_temporalid = $request->session()->get('usuario_temporalid');
            $carts = Carrito::where('usuario_temporalid',   $usuario_temporalid)->get();
        }

        return view('frontend.partials.cart_details', compact('carts'));
    }
}
