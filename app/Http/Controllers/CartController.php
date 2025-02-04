<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\CentroCostos;
use App\Models\ClientesSucursales;
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
        $carts = $this->getCarts($request);
        $parametros = ParametrosEmpresa::first();
        $sucursales = ClientesSucursales::where('clientesid', get_setting('cliente_pedidos'))->orderBy('descripcion')->get();

        foreach ($carts as &$cartItem) {
            $product = Producto::where('productosid', $cartItem['productosid'])->first();
            $cartItem['producto_descripcion'] = $product->descripcion; // Asumiendo que $product es un objeto
            $cartItem['imagen_producto'] = $this->getImagenProducto($cartItem);
            $cartItem['precio_visible'] = $this->getPrecioVisible($cartItem, $parametros);
            $cartItem['cantidad_final'] = $this->getCantidadFinal($cartItem);
            // Agrega cualquier otro campo que necesites calcular y mostrar
        }
        // Calcula los totales
        $totales = $this->calcularTotales($carts, $parametros);
        $centro_costos = CentroCostos::where('estado', 1)
            // Primero ordenamos por el prefijo alfabético (antes del primer punto)
            ->orderByRaw("SUBSTRING_INDEX(centro_costocodigo, '.', 1) ASC")
            // Luego ordenamos por los segmentos numéricos posteriores, de forma que se interpreten como enteros
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 2), '.', -1), UNSIGNED) ASC")  // Segmento 1 (después del primer punto)
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 3), '.', -1), UNSIGNED) ASC")  // Segmento 2 (después del segundo punto)
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 4), '.', -1), UNSIGNED) ASC")  // Segmento 3 (después del tercer punto)
            // Agrega más líneas si hay más segmentos que ordenar
            ->get();
        // Extrae el primer registro (si existe)
        $primerCentroCosto = $centro_costos->first();  // Obtienes el primer registro de la colección

        // Si hay al menos un centro de costo, guarda el 'centros_costosid' en la sesión
        if ($primerCentroCosto) {
            session(['centro_costo' => $primerCentroCosto->centros_costosid]);
        }
        session(['sucursal_carrito' => session('sucursalid')]);
        session(['destinatario' => '']);
        // Retorna la vista con los carritos y los totales ya calculados
        return view('frontend.view_cart', compact('carts', 'totales', 'centro_costos', 'sucursales'));
    }

    protected function getCarts(Request $request)
    {
        if (auth()->user() != null) {
            $clientesid = Auth::user()->clientesid;
            if (get_setting('maneja_sucursales') == "on") {
                return Carrito::where('clientesid', $clientesid)->where('clientes_sucursalesid', session('sucursalid'))->get();
            }
            return Carrito::where('clientesid', $clientesid)->get();
        } else {
            $usuario_temporalid = $request->session()->get('usuario_temporalid');
            return Carrito::where('usuario_temporalid', $usuario_temporalid)->get();
        }
    }

    protected function getImagenProducto($cartItem)
    {
        $imagenProducto = ProductoImagen::select('productos_imagenes.imagen')
            ->where('productos_imagenes.productosid', '=', $cartItem['productosid'])
            ->where('productos_imagenes.medidasid', '=', $cartItem['medidasid'])
            ->where('productos_imagenes.ecommerce_visible', '=', '1')
            ->first();

        return $imagenProducto->imagen ?? null;
    }

    protected function getPrecioVisible($cartItem, $parametros)
    {
        $preciovisible = $parametros->tipopresentacionprecios == 1 ?
            $cartItem['precioiva'] : $cartItem['precio'];

        return $preciovisible;
    }

    protected function getCantidadFinal($cartItem)
    {
        if (get_setting('controla_stock') == 1 || get_setting('controla_stock') == 0) {
            $cantidad = Producto::select('existenciastotales')
                ->where('productosid', $cartItem->productosid)
                ->first();
            $cantidadProductos = $cantidad->existenciastotales;
        } elseif (get_setting('controla_stock') == 2) {
            $cantidad = MovimientosInventariosAlmacenes::where(
                'productosid',
                $cartItem->productosid
            )
                ->where('almacenesid', $cartItem->almacenesid)
                ->first();
            $cantidadProductos = $cantidad->existencias;
        } else {
            $cantidadProductos = 0;
        }

        if ($cartItem->cantidadfactor != 0) {
            $cantidadFinal = $cantidadProductos / $cartItem->cantidadfactor;
        } else {
            $cantidadFinal = $cantidadProductos;
        }

        return $cantidadFinal;
    }

    protected function calcularTotales($carts, $parametros)
    {
        $totales = [
            'subtotal' => 0,
            'descuento' => 0,
            'subtotalNeto' => 0,
            'subtotalNetoConIva' => 0,
            'subtotalNetoSinIva' => 0,
            'totalIVA' => 0,
            'total' => 0,
        ];

        foreach ($carts as $cartItem) {
            $precioTotalItem = $cartItem['precio'] * $cartItem['cantidad'];
            $descuentoTotalItem = $precioTotalItem * ($cartItem['descuento'] / 100);
            $subtotalNetoItem = $precioTotalItem - $descuentoTotalItem;

            $totales['subtotal'] += $precioTotalItem;
            $totales['descuento'] += $descuentoTotalItem;
            $totales['subtotalNeto'] += $subtotalNetoItem;

            // Aquí calculamos el IVA para el artículo individual
            $IVAItem = $subtotalNetoItem * ($cartItem['iva'] / 100);

            // Acumulamos el IVA para cada producto en el carrito
            $totales['totalIVA'] += $IVAItem;

            // Separamos los subtotales netos con IVA y sin IVA para una posible distinción futura
            if ($cartItem['iva'] > 0) {
                $totales['subtotalNetoConIva'] += $subtotalNetoItem;
            } else {
                $totales['subtotalNetoSinIva'] += $subtotalNetoItem;
            }
        }

        // Ahora, fuera del bucle, calculamos el total sumando el subtotal neto y el IVA acumulado
        $totales['total'] = $totales['subtotalNeto'] + $totales['totalIVA'];

        // Aplicar redondeo y formateo al final
        $totales['subtotal'] = number_format(round($totales['subtotal'], $parametros->fdv_subtotales), $parametros->fdv_subtotales);
        $totales['descuento'] = number_format(round($totales['descuento'], 2), 2);
        $totales['subtotalNeto'] = number_format(round($totales['subtotalNeto'], $parametros->fdv_subtotales), $parametros->fdv_subtotales);
        $totales['totalIVA'] = number_format(round($totales['totalIVA'], $parametros->fdv_iva), $parametros->fdv_iva);
        $totales['total'] = number_format(round($totales['total'], 2), 2);


        return $totales;
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

        $iva = DB::connection('empresa')->table('sri_tipos_ivas')
            ->where('sri_tipos_ivas_codigo', $product->sri_tipos_ivas_codigo)
            ->first();

        $data['iva'] = $iva->valor;

        if ($request['quantity'] == null) {
            $data['cantidad'] = 1;
        }

        if (get_setting('maneja_sucursales') == "on") {
            $data['clientes_sucursalesid'] = session('sucursalid');
        }

        if ($carts && count($carts) > 0) {
            $encontroEnCarro = false;

            foreach ($carts as $key => $cartItem) {
                if ($cartItem['productosid'] == $request->id && $cartItem['medidasid'] == $request->medidasid && $cartItem['almacenesid'] == $request->variableinicio && $cartItem['sucursalid'] == session('sucursalid')) {

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
        $carts = $this->getCarts($request);
        $parametros = ParametrosEmpresa::first();

        foreach ($carts as &$cartItem) {
            $product = Producto::where('productosid', $cartItem['productosid'])->first();
            $cartItem['producto_descripcion'] = $product->descripcion; // Asumiendo que $product es un objeto
            $cartItem['imagen_producto'] = $this->getImagenProducto($cartItem);
            $cartItem['precio_visible'] = $this->getPrecioVisible($cartItem, $parametros);
            $cartItem['cantidad_final'] = $this->getCantidadFinal($cartItem);
            // Agrega cualquier otro campo que necesites calcular y mostrar
        }
        // Calcula los totales
        $totales = $this->calcularTotales($carts, $parametros);
        return view('frontend.partials.cart_details', compact('carts', 'totales'));
    }

    public function updateNavCart(Request $request)
    {
        return view('frontend.partials.cart');
    }

    public function updateQuantity(Request $request)
    {

        $carrito = Carrito::findOrFail($request->id);
        $sucursales = ClientesSucursales::where('clientesid', get_setting('cliente_pedidos'))->orderBy('descripcion')->get();
        $centro_costos = CentroCostos::where('estado', 1)
            // Primero ordenamos por el prefijo alfabético (antes del primer punto)
            ->orderByRaw("SUBSTRING_INDEX(centro_costocodigo, '.', 1) ASC")
            // Luego ordenamos por los segmentos numéricos posteriores, de forma que se interpreten como enteros
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 2), '.', -1), UNSIGNED) ASC")  // Segmento 1 (después del primer punto)
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 3), '.', -1), UNSIGNED) ASC")  // Segmento 2 (después del segundo punto)
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 4), '.', -1), UNSIGNED) ASC")  // Segmento 3 (después del tercer punto)
            // Agrega más líneas si hay más segmentos que ordenar
            ->get();

        if ($carrito['ecommerce_carritosid'] == $request->id) {
            $product = Producto::where('productosid', $carrito['productosid'])->first();


            if (get_setting('controla_stock') == 0) {
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

        $carts = $this->getCarts($request);

        $parametros = ParametrosEmpresa::first();

        foreach ($carts as &$cartItem) {
            $product = Producto::where('productosid', $cartItem['productosid'])->first();
            $cartItem['producto_descripcion'] = $product->descripcion; // Asumiendo que $product es un objeto
            $cartItem['imagen_producto'] = $this->getImagenProducto($cartItem);
            $cartItem['precio_visible'] = $this->getPrecioVisible($cartItem, $parametros);
            $cartItem['cantidad_final'] = $this->getCantidadFinal($cartItem);
            // Agrega cualquier otro campo que necesites calcular y mostrar
        }
        // Calcula los totales
        $totales = $this->calcularTotales($carts, $parametros);
        return view('frontend.partials.cart_details', compact('carts', 'totales', 'centro_costos', 'sucursales'));
    }

    public function showObservacion(Request $request)
    {
        $carrito = Carrito::findOrFail($request->id);

        return response()->json([
            'observacion' => $carrito->observacion ?? ''
        ]);
    }

    public function updateObservacion(Request $request)
    {
        $carrito = Carrito::findOrFail($request->ecommerce_carritosid);
        $carrito->observacion = $request->observacion;
        $carrito->save();

        return back();
    }
}
