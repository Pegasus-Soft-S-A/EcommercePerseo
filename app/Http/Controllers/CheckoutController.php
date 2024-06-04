<?php

namespace App\Http\Controllers;

use App\Mail\Factura;
use App\Mail\Pedido;
use App\Models\Carrito;
use App\Models\Facturador;
use App\Models\Facturas;
use App\Models\FacturasDetalles;
use App\Models\MovimientosInventariosAlmacenes;
use App\Models\ParametrosEmpresa;
use App\Models\Pedidos;
use App\Models\PedidosDetalles;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\ProductoTarifa;
use App\Models\Secuenciales;
use App\Models\Secuencias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{

    public function __construct()
    {
        //
    }

    public function get_shipping_info(Request $request)
    {
        $carts = Carrito::where('clientesid', Auth::user()->clientesid)->get();
        if ($carts && count($carts) > 0) {
            // $categories = Categorias::all();
            return view('frontend.shipping_info', compact('carts'));
        }
        flash('El carrito esta vacio')->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        if ($request->clientes_sucursalesid == null) {
            flash('Agregue una direccion')->warning();
            return back();
        }
        $direccion = $request->clientes_sucursalesid;

        $carts = Carrito::where('clientesid', Auth::user()->clientesid)
            ->get();

        $parametros = ParametrosEmpresa::first();

        foreach ($carts as &$cartItem) {
            $product = Producto::where('productosid', $cartItem['productosid'])->first();
            $cartItem['producto_descripcion'] = $product->descripcion;
            $cartItem['precio_visible'] = $this->getPrecioVisible($cartItem, $parametros);
        }
        // Calcula los totales
        $totales = $this->calcularTotales($carts, $parametros);

        return view('frontend.payment_select', compact('carts', 'direccion', 'totales', 'parametros'));
    }

    protected function getPrecioVisible($cartItem, $parametros)
    {
        $preciovisible = $parametros->tipopresentacionprecios == 1 ?
            $cartItem['precioiva'] : $cartItem['precio'];

        return $preciovisible;
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

        return $totales;
    }

    public function checkout(Request $request)
    {

        $parametros = ParametrosEmpresa::first();


        if ($request->payment_option == 'pago_pedido') {

            DB::connection('empresa')->beginTransaction();
            try {

                $secuencial = Secuenciales::where('secuencial', 'PEDIDOS')->first();
                Secuenciales::where('secuenciales.secuencial', 'PEDIDOS')
                    ->update(
                        [
                            'valor' => $secuencial->valor + 1
                        ]
                    );

                $almacenes = DB::connection('empresa')->table('facturadores_almacenes')
                    ->where('facturadoresid', get_setting('facturador'))
                    ->where('principal', '1')
                    ->first();
                $pedido = new Pedidos;
                $pedido->emision = now()->format('Y-m-d');
                $pedido->pedidos_codigo = $secuencial->prefijo . str_pad($secuencial->valor, $secuencial->numeroceros, "0", STR_PAD_LEFT);
                $pedido->forma_pago_empresaid = 1;
                $pedido->clientesid = Auth::user()->clientesid;
                $pedido->clientes_sucursalesid = $request->clientes_sucursalesid;
                $pedido->almacenesid = get_setting('controla_stock') != 2 ? $almacenes->almacenesid : session('almacenesid');
                $pedido->centros_costosid = 1;
                $pedido->vendedoresid = get_setting('facturador');
                $pedido->facturadoresid =  get_setting('facturador');
                $pedido->tarifasid = Auth::user()->tarifasid;
                $pedido->concepto = "Pedido Ecommerce";
                $pedido->origen = "";
                $pedido->usuariocreacion = "Ecommerce";
                $pedido->fechacreacion = now();
                $pedido->estado = 1;
                $pedido->subtotalsiniva = $request->subtotalnetosiniva;
                $pedido->subtotalconiva = $request->subtotalnetoconiva;
                $pedido->total_descuento = $request->descuento;
                $pedido->subtotalneto = $request->subtotalneto;
                $pedido->total_iva = $request->totalIVA;
                $pedido->total = $request->total;
                if ($request->token) {
                    $pedido->observacion = "Tarjeta: " . $request->nombre_tarjeta . "\nNumero Voucher: " . $request->token;
                }
                $pedido->save();

                $carts = Carrito::where('clientesid', Auth::user()->clientesid)
                    ->get();

                foreach ($carts as $key => $cartItem) {
                    $detallepedido = new PedidosDetalles;
                    $detallepedido->pedidosid = $pedido->pedidosid;
                    $detallepedido->centros_costosid = 1;
                    $detallepedido->productosid = $cartItem->productosid;
                    $detallepedido->medidasid = $cartItem->medidasid;
                    $detallepedido->almacenesid = $cartItem->almacenesid;
                    $detallepedido->cantidaddigitada = $cartItem->cantidad;
                    $detallepedido->cantidadfactor = $cartItem->cantidadfactor;
                    $detallepedido->cantidad = $cartItem->cantidad * $cartItem->cantidadfactor;
                    $detallepedido->precio = $cartItem->precio / $cartItem->cantidadfactor;
                    $detallepedido->iva = $cartItem->iva;
                    $detallepedido->precioiva = $cartItem->precioiva;
                    $detallepedido->descuento = $cartItem->descuento;
                    $detallepedido->preciovisible = $parametros->tipopresentacionprecios == 1 ? $cartItem->precioiva : $cartItem->precio;
                    $detallepedido->save();
                }
                //Si todo se inserto correctamente hace un commit a la base
                DB::connection('empresa')->commit();
                $request->session()->put('forma_pago', 'pedido');

                flash('Su pedido ha sido realizado correctamente')->success();
            } catch (\Exception $e) {

                //Si ocurrio algun error mientras insertaba datos hace un rollback y no guarda ninguna ejecucion
                DB::connection('empresa')->rollback();
                $direccion = $request->clientes_sucursalesid;
                $carts = Carrito::where('clientesid', Auth::user()->clientesid)
                    ->get();

                flash('Ocurrio un error al realizar el pedido')->error();
                return view('frontend.payment_select', compact('carts', 'direccion'));
            }

            configurar_smtp();

            //Armar erray para enviar email
            $array['view'] = 'emails.pedido';
            $array['from'] = Config::get('mail.from.address');
            $array['subject'] = 'Pedido Ecommerce';
            $array['cliente'] = auth()->user()->razonsocial;
            $array['pedido'] = $pedido;
            $emails = explode(",", get_setting('email_pedidos'));
            array_push($emails, auth()->user()->email_login);
            $emails = array_diff($emails, array("", 0, null));

            try {
                Mail::mailer('smtp')->to($emails)->send(new Pedido($array));
            } catch (\Exception $e) {
            }

            return redirect()->route('order_confirmed', [$pedido->pedidosid, $pedido->clientesid]);
        }
    }

    public function order_confirmed($id, $cliente)
    {
        Carrito::where('clientesid', $cliente)
            ->delete();

        if (session('forma_pago') == 'pedido') {

            $pedido = Pedidos::findOrFail($id);
            $detalles = DB::connection('empresa')->table('pedidos_detalles')
                ->select('medidas.descripcion AS medida', 'productos.descripcion AS producto', 'pedidos_detalles.cantidaddigitada', 'pedidos_detalles.preciovisible', 'pedidos_detalles.precio', 'pedidos_detalles.cantidad')
                ->where('pedidosid', $id)
                ->join('medidas', 'medidas.medidasid', '=', 'pedidos_detalles.medidasid')
                ->join('productos', 'productos.productosid', '=', 'pedidos_detalles.productosid')
                ->get();

            return view('frontend.order_confirmed', compact('pedido', 'detalles'));
        } else {
            $factura = Facturas::findOrFail($id);
            $detalles = DB::connection('empresa')->table('facturas_detalles')
                ->select('medidas.descripcion AS medida', 'productos.descripcion AS producto', 'facturas_detalles.cantidaddigitada', 'facturas_detalles.preciovisible')
                ->where('facturasid', $id)
                ->join('medidas', 'medidas.medidasid', '=', 'facturas_detalles.medidasid')
                ->join('productos', 'productos.productosid', '=', 'facturas_detalles.productosid')
                ->get();

            return view('frontend.factura_confirmed', compact('factura', 'detalles'));
        }
    }

    public function crear_factura(Request $request)
    {

        $parametros = ParametrosEmpresa::first();
        $secuenciaid = DB::connection('empresa')->table('secuencias')
            ->select('facturadores_secuencias.secuenciasid')
            ->join('facturadores_secuencias', 'facturadores_secuencias.secuenciasid', 'secuencias.secuenciasid')
            ->where('secuencias.estado', '1')
            ->where('facturadores_secuencias.facturadoresid', get_setting('facturador'))
            ->where('secuencias.sri_documentoscodigo', '01')
            ->first();

        $secuencias = DB::connection('empresa')->table('secuencias')
            ->where('secuencias.secuenciasid', $secuenciaid->secuenciasid)
            ->first();

        $facturador = Facturador::where('facturadoresid', get_setting('facturador'))->first();

        $caja = DB::connection('empresa')->table('facturadores_cajas')
            ->where('facturadoresid', get_setting('facturador'))
            ->where('principal', '1')
            ->first();
        $almacenes = DB::connection('empresa')->table('facturadores_almacenes')
            ->where('facturadoresid', get_setting('facturador'))
            ->where('principal', '1')
            ->first();


        DB::connection('empresa')->beginTransaction();
        try {
            //Guardar cabecera
            $factura = new Facturas;
            $factura->emision = now()->format('Y-m-d');
            $factura->vence = now()->format('Y-m-d');
            $factura->clientesid = Auth::user()->clientesid;
            $factura->tarifasid = Auth::user()->tarifasid;
            $factura->centros_costosid = $facturador->centros_costosid;
            $factura->sri_documentoscodigo = '01';
            $factura->almacenesid = get_setting('controla_stock') != 2 ? $almacenes->almacenesid : session('almacenesid');
            $factura->facturadoresid = get_setting('facturador');
            $factura->secuenciasid = $secuencias->secuenciasid;
            $factura->forma_pago_empresaid = 4;
            $factura->forma_pago_sri_codigo = $request->tipo_tarjeta == 'credit' ? '19' : '16';
            $factura->cajasid = $caja->cajasid;
            $factura->concepto = "Venta Ecommerce";
            $factura->vendedoresid = get_setting('facturador');
            $factura->establecimiento = $secuencias->establecimiento;
            $factura->puntoemision = $secuencias->puntoemision;
            $factura->secuencial = str_pad($secuencias->numeroactual, '9', "0", STR_PAD_LEFT);
            $factura->clientes_sucursalesid = $request->clientes_sucursalesid;
            $factura->usuariocreacion = "Ecommerce";
            $factura->fechacreacion = now();
            $factura->subtotal = $request->subtotal;
            $factura->total_descuento = $request->descuento;
            $factura->subtotalconiva = $request->subtotalnetoconiva;
            $factura->subtotalsiniva = $request->subtotalnetosiniva;
            $factura->subtotalneto = $request->subtotalneto;
            $factura->total_iva = $request->totalIVA;
            $factura->total = $request->total;
            $factura->totalneto = $request->total;
            $factura->totalneto = $request->total;
            $factura->origen = 'Ecommerce';
            $factura->save();

            //Actualizar secuencia del vendedor
            Secuencias::where('secuencias.secuenciasid',  $secuencias->secuenciasid)
                ->update(
                    [
                        'numeroactual' => $secuencias->numeroactual + 1
                    ]
                );

            //Recorrer Carrito
            $carts = Carrito::where('clientesid', Auth::user()->clientesid)
                ->get();

            foreach ($carts as $key => $cartItem) {

                //Guardar detalles
                $producto = Producto::findOrFail($cartItem->productosid);
                $detallefactura = new FacturasDetalles;
                $detallefactura->facturasid = $factura->facturasid;
                $detallefactura->centros_costosid = $facturador->centros_costosid;
                $detallefactura->almacenesid = $cartItem->almacenesid;
                $detallefactura->productosid = $cartItem->productosid;
                $detallefactura->medidasid = $cartItem->medidasid;
                $detallefactura->cantidaddigitada = $cartItem->cantidad;
                $detallefactura->cantidadfactor = $cartItem->cantidadfactor;
                $detallefactura->cantidad = $cartItem->cantidad * $cartItem->cantidadfactor;
                $detallefactura->precio = $cartItem->precio / $cartItem->cantidadfactor;
                $detallefactura->iva = $cartItem->iva;
                $detallefactura->precioiva = $cartItem->precioiva;
                $detallefactura->descuento = $cartItem->descuento;
                $detallefactura->descuentovalor = $cartItem['precio']  * ($cartItem['descuento'] / 100);
                $detallefactura->preciovisible = $parametros->tipopresentacionprecios == 1 ? $cartItem->precioiva : $cartItem->precio;
                $detallefactura->costo = $parametros->costocalculoprecio == 2 ? $producto->costoactual : $producto->costoultimacompra;
                $detallefactura->save();

                //Actualizar movinventarios_almacenes
                $mov_inventario_almacen = DB::connection('empresa')->table('movinventarios_almacenes')->where('almacenesid', $cartItem->almacenesid)->where('movinventarios_almacenes.productosid', $cartItem->productosid)->first();
                if ($mov_inventario_almacen != null) {
                    DB::connection('empresa')->table('movinventarios_almacenes')->where('movinventarios_almacenes.almacenesid', $cartItem->almacenesid)->where('movinventarios_almacenes.productosid', $cartItem->productosid)
                        ->update(['movinventarios_almacenes.existencias' => $mov_inventario_almacen->existencias - ($cartItem->cantidad * $cartItem->cantidadfactor)]);
                } else {
                    DB::connection('empresa')->table('movinventarios_almacenes')
                        ->insert(['movinventarios_almacenes.almacenesid' => $cartItem->almacenesid, 'movinventarios_almacenes.productosid' => $cartItem->productosid, 'movinventarios_almacenes.existencias' => $cartItem->cantidad * $cartItem->cantidadfactor]);
                }

                //Actualizar Existencia Productos
                $producto = Producto::where('productos.productosid', $cartItem->productosid)->first();
                Producto::where('productos.productosid', $cartItem->productosid)
                    ->update(['productos.existenciastotales' => $producto->existenciastotales + ($cartItem->cantidad * $cartItem->cantidadfactor)]);

                //Actualizar movinventarios
                DB::connection('empresa')->table('movinventarios')
                    ->insert([
                        'documentosid' => $factura->facturasid,
                        'documento_detallesid' => $detallefactura->facturas_detallesid,
                        'productosid' => $cartItem->productosid,
                        'centros_costosid' => $detallefactura->centros_costosid,
                        'cantidaddigitada' => $cartItem->cantidad,
                        'cantidadfactor' => $cartItem->cantidadfactor,
                        'cantidad' => $cartItem->cantidad * $cartItem->cantidadfactor,
                        'beneficiarioid' => Auth::user()->clientesid,
                        'medidasid' => $cartItem->medidasid,
                        'almacenesid' => $cartItem->almacenesid,
                        'precio' => $detallefactura->precio,
                        'precioiva' => $detallefactura->precioiva,
                        'preciovisible' => $detallefactura->preciovisible,
                        'movinventarios_tiposid' => 3,
                        'descuento' => $cartItem->descuento,
                        'descuentovalor' => $cartItem['precio']  * ($cartItem['descuento'] / 100),
                        'costo' => $detallefactura->costo,
                        'iva' => $cartItem->iva,
                        'servicio' => 0,
                        'referencia' => substr($factura->establecimiento . '-' . $factura->puntoemision . '-' . $factura->secuencial, 0, 17),
                        'fechamovimiento' => $factura->emision
                    ]);
            }

            //Guardar CxC
            DB::connection('empresa')->table('cuentasporcobrar')
                ->insert([
                    'documentosid' => $factura->facturasid,
                    'clientesid' => Auth::user()->clientesid,
                    'cajasid' => $factura->cajasid,
                    'bancosid' => 0,
                    'forma_pago_empresaid' => $factura->forma_pago_empresaid,
                    'forma_pago_sri_codigo' => $factura->forma_pago_sri_codigo,
                    'emision' => $factura->emision,
                    'recepcion' => $factura->emision,
                    'vence' => $factura->vence,
                    'centros_costosid' => $factura->centros_costosid,
                    'secuencial' => $factura->secuencial,
                    'vendedoresid' => $factura->vendedoresid,
                    'cobradoresid' => $factura->facturadoresid,
                    'origen' => 'Facturacion',
                    'tipo' => 'FC',
                    'importe' => round($factura->total, 2),
                    'concepto' => $factura->concepto,
                    'numerochequedeposito' => "",
                    'relaciondocumentoid' => 0,
                    'asientocontable' => 0,
                    'usuariocreacion' => 'Ecommerce',
                    'fechacreacion' => $factura->fechacreacion

                ]);

            //Abono
            DB::connection('empresa')->table('cuentasporcobrar')
                ->insert([
                    'documentosid' => $factura->facturasid,
                    'clientesid' => Auth::user()->clientesid,
                    'cajasid' => $factura->cajasid,
                    'bancosid' => 0,
                    'forma_pago_empresaid' => $factura->forma_pago_empresaid,
                    'forma_pago_sri_codigo' => $factura->forma_pago_sri_codigo,
                    'emision' => $factura->emision,
                    'recepcion' => $factura->emision,
                    'vence' => $factura->vence,
                    'centros_costosid' => $factura->centros_costosid,
                    'secuencial' => $factura->secuencial,
                    'vendedoresid' => $factura->vendedoresid,
                    'cobradoresid' => $factura->facturadoresid,
                    'origen' => 'Facturacion',
                    'tipo' => 'AB',
                    'importe' => -round($factura->totalneto, 2),
                    'concepto' => $factura->concepto,
                    'numerochequedeposito' => $request->token,
                    'relaciondocumentoid' => 0,
                    'asientocontable' => 0,
                    'usuariocreacion' => 'Ecommerce',
                    'fechacreacion' => $factura->fechacreacion

                ]);


            //Secuencial de CAJAINGRESOS
            $secuencial = Secuenciales::where('secuencial', 'CAJAINGRESOS')->first();
            Secuenciales::where('secuenciales.secuencial', 'CAJAINGRESOS')
                ->update(['valor' => $secuencial->valor + 1]);

            //Guardar en movcaja
            $tarjeta = DB::connection('empresa')->table('tarjetas')->where('descripcion', 'like', '%' . $request->nombre_tarjeta . '%')->first();
            DB::connection('empresa')->table('movcaja')
                ->insert([
                    'forma_pago_empresaid' => $factura->forma_pago_empresaid,
                    'documentosid' => $factura->facturasid,
                    'cajasid' => $factura->cajasid,
                    'origen' => 'Facturacion',
                    'importe' => round($factura->totalneto, 2),
                    'tipo' => 'AB',
                    'bancotarjetaid' => $tarjeta->tarjetasid,
                    'numerochequevoucher' => $request->token,
                    'fechamovimiento' => $factura->emision,
                    'fechavence' => $factura->emision,
                    'concepto' => $factura->concepto,
                    'beneficiario' => Auth::user()->razonsocial,
                    'comprobante' => $secuencial->prefijo . str_pad($secuencial->valor, $secuencial->numeroceros, "0", STR_PAD_LEFT),
                    'asientocontable' => 0,
                    'documentoorigen' => $factura->secuencial,
                    'usuariocreacion' => 'Ecommerce',
                    'fechacreacion' => $factura->fechacreacion
                ]);

            //Si todo se inserto correctamente hace un commit a la base
            DB::connection('empresa')->commit();
            $request->session()->put('forma_pago', 'factura');
        } catch (\Exception $e) {
            //Si ocurrio algun error mientras insertaba datos hace un rollback y no guarda ninguna ejecucion

            DB::connection('empresa')->rollback();
            return 0;
        }

        configurar_smtp();

        //Enviar email
        $array['view'] = 'emails.factura';
        $array['from'] = Config::get('mail.from.address');
        $array['subject'] = 'Factura Ecommerce';
        $array['cliente'] = auth()->user()->razonsocial;
        $array['factura'] = $factura;
        $emails = explode(",", get_setting('email_pedidos'));
        array_push($emails, auth()->user()->email_login);
        $emails = array_diff($emails, array("", 0, null));

        try {
            Mail::mailer('smtp')->to($emails)->send(new Factura($array));
        } catch (\Exception $e) {
        }

        return $factura->facturasid;
    }

    public function verificar_existencias(Request $request, $cliente)
    {

        $carrito = Carrito::select(DB::raw('SUM(cantidad * cantidadfactor) as cantidad_total'), 'productosid', 'medidasid', 'almacenesid')->where('clientesid', $cliente)->groupBy('productosid')->get();
        $productos = [];
        foreach ($carrito as $key => $carro) {
            $nombre = Producto::select('descripcion', 'existenciastotales')->where('productosid', $carro->productosid)->first();

            if (get_setting('controla_stock') == 1) {
                $cantidadFinal = $nombre->existenciastotales;
            } else if (get_setting('controla_stock') == 2) {
                if (Auth::check()) {

                    if (get_setting('controla_stock') == 2) {


                        $cantidad = MovimientosInventariosAlmacenes::where('productosid', $carro->productosid)
                            ->where('almacenesid', $carro->almacenesid)
                            ->first();
                        if ($cantidad) {
                            $cantidadFinal = $cantidad->existencias;
                        } else {
                            $cantidadFinal = 0;
                        }
                    }
                }
            } elseif (get_setting('controla_stock') == 0) {
                $cantidadFinal = get_setting('cantidad_maxima');
            }
            if (floatval($carro->cantidad_total) > floatval($cantidadFinal)) {
                $productos[] = $nombre->descripcion;
            }
        }

        if (count($productos) > 0) {

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

            return view('frontend.view_cart', compact('productos', 'carts', 'totales'));
        } else {
            return redirect()->route('checkout.shipping_info');
        }
    }

    protected function getCarts(Request $request)
    {
        if (auth()->user() != null) {
            $clientesid = Auth::user()->clientesid;
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
}
