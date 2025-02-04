<?php

namespace App\Http\Controllers;

use App\Mail\Pedido;
use App\Models\CentroCostos;
use App\Models\Clientes;
use App\Models\ClientesSucursales;
use App\Models\LogSistema;
use App\Models\Medidas;
use App\Models\ParametrosEmpresa;
use Illuminate\Http\Request;
use App\Models\Pedidos;
use App\Models\PedidosDetalles;
use App\Models\Producto;
use App\Models\Secuenciales;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PedidoController extends Controller
{
    public function verTodos(Request $request)
    {
        $fecha = $request->fecha;
        $estado = null;
        $busqueda = null;
        $prioridad = $request->prioridad;
        $destinatario = $request->destinatario;
        $centrocostos = CentroCostos::where('estado', 1)->get();
        $centrocostoid = $request->centrocostoid;

        if (get_setting('maneja_sucursales') == "on") {
            $pedidos = Pedidos::select('pedidos.pedidosid', 'pedidos.pedidos_codigo', 'pedidos.total', 'pedidos.estado', 'clientes_sucursales.descripcion', 'pedidos.emision', 'pedidos.documentosid', 'pedidos.observacion', 'pedidos.prioridad')
                ->join('clientes_sucursales', 'clientes_sucursales.clientes_sucursalesid', '=', 'pedidos.clientes_sucursalesid')
                ->where('pedidos.usuariocreacion', 'Ecommerce');
        } else {
            $pedidos = Pedidos::select('pedidos.pedidosid', 'pedidos.pedidos_codigo', 'pedidos.total', 'pedidos.estado', 'clientes.razonsocial', 'pedidos.emision', 'pedidos.documentosid', 'pedidos.observacion', 'pedidos.prioridad')
                ->join('clientes', 'clientes.clientesid', '=', 'pedidos.clientesid')
                ->where('pedidos.usuariocreacion', 'Ecommerce');
        }

        if ($request->estado != null) {
            $pedidos = $pedidos->where('pedidos.estado', $request->estado);
            $estado = $request->estado;
        }

        // Filtrar por estado si está presente en la solicitud
        if (!is_null($centrocostoid)) {
            $pedidos = $pedidos->where('pedidos.centros_costosid', $centrocostoid);
        }

        if (!is_null($prioridad)) {
            $pedidos = $pedidos->where('pedidos.prioridad', $prioridad);
        }

        if ($fecha != null) {
            $pedidos = $pedidos->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        if ($request->busqueda != null) {
            if (get_setting('maneja_sucursales') == "on") {
                $pedidos = $pedidos->where('clientes_sucursales.clientes_sucursalesid', $request->busqueda);
            } else {
                $busqueda = $request->busqueda;
                $pedidos = $pedidos->where('clientes.razonsocial', 'like', '%' . $busqueda . '%')
                    ->orwhere('pedidos.pedidos_codigo', 'like', '%' . $busqueda . '%');
            }
        }

        if ($destinatario != null) {
            $pedidos = $pedidos->where('pedidos.observacion', 'like', '%' . $destinatario . '%');
        }

        // Obtener los pedidos con paginación
        $pedidos = $pedidos->orderBy('pedidos.emision', 'desc')
            ->orderBy('pedidos.pedidos_codigo', 'desc')
            ->paginate(15);

        // Recorrer los pedidos y extraer el "Urbano" y "Destinatario" de la observación
        foreach ($pedidos as $pedido) {
            $observacion = $pedido->observacion;
            $urbano = null;
            $destinatarioLista = null;

            // Primero, extraemos el "Destinatario"
            if (preg_match('/Destinatario:\s*([^;]+)/', $observacion, $matches)) {
                $destinatarioLista = $matches[1]; // El valor del destinatario
            }
            // Usamos la expresión regular para extraer el valor de "Urbano"
            if (preg_match('/Urbano:\s*([^;]+)/', $observacion, $matches)) {
                $urbano = $matches[1]; // El valor de "Urbano"
            }

            // Agregar el valor de "Urbano" al objeto pedido (puedes agregarlo como una propiedad temporal)
            $pedido->urbano = $urbano; // Ahora el objeto pedido tiene la propiedad 'urbano'
            $pedido->destinatario = $destinatarioLista; // Ahora el objeto pedido tiene la propiedad 'urbano'
        }

        return view('backend.pedidos', compact('pedidos', 'estado', 'busqueda', 'fecha', 'centrocostos', 'centrocostoid', 'destinatario'));
    }

    public function crear()
    {
        $secuencial = Secuenciales::where('secuencial', 'PEDIDOS')->first();
        $cliente = Clientes::where('clientesid', get_setting('cliente_pedidos'))->first();
        $sucursales = ClientesSucursales::where('clientesid', get_setting('cliente_pedidos'))->orderBy('descripcion')->get();
        $centros_costos = CentroCostos::where('estado', 1)
            // Primero ordenamos por el prefijo alfabético (antes del primer punto)
            ->orderByRaw("SUBSTRING_INDEX(centro_costocodigo, '.', 1) ASC")
            // Luego ordenamos por los segmentos numéricos posteriores, de forma que se interpreten como enteros
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 2), '.', -1), UNSIGNED) ASC")  // Segmento 1 (después del primer punto)
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 3), '.', -1), UNSIGNED) ASC")  // Segmento 2 (después del segundo punto)
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 4), '.', -1), UNSIGNED) ASC")  // Segmento 3 (después del tercer punto)
            // Agrega más líneas si hay más segmentos que ordenar
            ->get();
        $pedido = new Pedidos();
        $pedido->pedidos_codigo = $secuencial->prefijo . str_pad($secuencial->valor, $secuencial->numeroceros, "0", STR_PAD_LEFT);
        $pedido->emision = date('Y-m-d');
        $pedido->clientesid = get_setting('cliente_pedidos');
        return view('backend.pedidos.crear', ['pedido' => $pedido, 'cliente' => $cliente, 'centros_costos' => $centros_costos, 'sucursales' => $sucursales]);
    }

    public function guardar(Request $request)
    {
        // Obtener los datos de la cabecera.
        $cabecera = $request->input('cabecera');
        // Obtener los productos.
        $productos = $request->input('productos');
        // Obtener los totales.
        $totales = $request->input('totales');

        $parametros = ParametrosEmpresa::first();

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
            $pedido->emision = $cabecera['fecha'];
            $pedido->pedidos_codigo = $cabecera['codigo'];
            $pedido->forma_pago_empresaid = 1;
            $pedido->clientesid = get_setting('cliente_pedidos');
            $pedido->clientes_sucursalesid = $cabecera['sucursal'];
            $pedido->almacenesid = $almacenes->almacenesid;
            $pedido->centros_costosid = $cabecera['centroCosto'];
            $pedido->vendedoresid = get_setting('facturador');
            $pedido->facturadoresid =  get_setting('facturador');
            $pedido->tarifasid = get_setting('tarifa_productos');
            $pedido->concepto = "Pedido Ecommerce Admin";
            $pedido->observacion = 'Destinatario: ' . $cabecera['destinatario'];
            $pedido->origen = "";
            $pedido->usuariocreacion = "Admin Ecommerce";
            $pedido->fechacreacion = now();
            $pedido->estado = 1;
            $pedido->subtotalsiniva = $totales['subtotalNetoSinIva'];
            $pedido->subtotalconiva = $totales['subtotalNetoConIva'];
            $pedido->subtotalconiva2 = $totales['subtotalNetoIva5'];
            $pedido->total_descuento = $totales['descuentoTotal'];
            $pedido->subtotalneto = $totales['subtotalNeto'];
            $pedido->subtotal = $totales['subtotal'];
            $pedido->total_iva = $totales['totalIVA'];
            $pedido->total_iva2 = $totales['totalIVA5'];
            $pedido->total = $totales['total'];
            $pedido->save();

            foreach ($productos as $producto) {
                $detallepedido = new PedidosDetalles;
                $detallepedido->pedidosid = $pedido['pedidosid'];
                $detallepedido->centros_costosid = $cabecera['centroCosto'];
                $detallepedido->productosid = $producto['id'];
                $detallepedido->medidasid = $producto['medidasid'];;
                $detallepedido->almacenesid = $almacenes->almacenesid;
                $detallepedido->cantidaddigitada = $producto['cantidad'];
                $detallepedido->cantidadfactor = $producto['factor']; //$producto->cantidadfactor;
                $detallepedido->cantidad = $producto['cantidad'] * $producto['factor']; //$producto->cantidad * $producto->cantidadfactor;
                $detallepedido->precio = $producto['precio'] / $producto['factor']; // $producto->precio / $producto->cantidadfactor;
                $detallepedido->iva = $producto['valoriva'];
                $detallepedido->precioiva = $producto['precioiva'];
                $detallepedido->descuento = $producto['descuento'];
                $detallepedido->preciovisible = $parametros->tipopresentacionprecios == 1 ? $producto['precioiva'] : $producto['precio'];
                // Solo asigna la información si existe y no está vacía
                if (array_key_exists('informacion', $producto) && !empty($producto['informacion'])) {
                    $detallepedido->informacion = $producto['informacion'];
                }

                $detallepedido->save();
            }

            // Crear el objeto de log
            $log = new LogSistema();

            // Llenar los campos del log
            $log->sis_subcategoriasid = 113;
            $log->equipo = "Ecommerce";
            $log->sis_empresasid = $request->segment(1);
            $log->sis_usuariosid = Auth::guard('admin')->user()->sis_usuariosid; // ID del usuario autenticado
            $log->tipooperacion = 'N';
            $log->fecha = now();

            // Construir el detalle a partir de los datos del pedido y detalles
            $detalle = [
                "pedidos" => [
                    "pedidosid" => $pedido->pedidosid,
                    "emision" => now()->format('Ymd'),
                    "pedidos_codigo" => $pedido->pedidos_codigo,
                    "forma_pago_empresaid" => $pedido->forma_pago_empresaid,
                    "facturadoresid" => $pedido->facturadoresid,
                    "clientesid" => $pedido->clientesid,
                    "clientes_sucursalesid" => $pedido->clientes_sucursalesid,
                    "almacenesid" => $pedido->almacenesid,
                    "centros_costosid" => $pedido->centros_costosid,
                    "vendedoresid" => $pedido->vendedoresid,
                    "tarifasid" => $pedido->tarifasid,
                    "concepto" => $pedido->concepto,
                    "origen" => $pedido->origen,
                    "documentosid" => 0,
                    "promocionesid" => 1,
                    "observacion" => $pedido->observacion,
                    "subtotal" => $pedido->subtotal,
                    "subtotalsiniva" => $pedido->subtotalsiniva,
                    "subtotalconiva" => $pedido->subtotalconiva,
                    "subtotalconiva2" => $pedido->subtotalconiva2,
                    "total_descuento" => $pedido->total_descuento,
                    "subtotalneto" => $pedido->subtotalneto,
                    "total_iva" => $pedido->total_iva,
                    "total_iva2" => $pedido->total_iva2,
                    "total" => $pedido->total,
                    "ecommerceid" => 0,
                    "restaurante_mesasid" => 0,
                    "estado" => $pedido->estado,
                    "uuid" => "",
                    "estado_sync" => 0,
                    "prioridad" => $pedido->prioridad,
                    "fechacreacion" => $pedido->fechacreacion,
                    "usuariocreacion" => $pedido->usuariocreacion,
                    "fechamodificacion" => null,
                    "usuariomodificacion" => ""
                ],
                "Detalles" => PedidosDetalles::where('pedidosid', $pedido->pedidosid)->get()->map(function ($detalle) {
                    $producto = Producto::findOrFail($detalle->productosid);

                    return [
                        "ProductoID" => $detalle->productosid,
                        "Codigo" => $producto->productocodigo ?? '',
                        "Descripcion" => $producto->descripcion ?? '',
                        "Cantidad" => $detalle->cantidad,
                        "Costo" => $detalle->precio,
                        "Descuento" => $detalle->descuento,
                        "Total" => $detalle->cantidad * $detalle->precio
                    ];
                })->toArray(),
            ];

            // Asignar el detalle serializado al campo correspondiente
            $log->detalle = json_encode($detalle);
            $log->save();
            // Si todo se inserta correctamente, realiza un commit a la base de datos
            DB::connection('empresa')->commit();
            // Retornar una respuesta JSON para indicar éxito
            return response()->json([
                'status' => 'success',
                'message' => 'Su pedido ha sido realizado correctamente',
                'redirect_url' => route('pedidos.editar', $pedido['pedidosid'])  // Puedes devolver la URL a la que deseas redirigir
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            //Si ocurrio algun error mientras insertaba datos hace un rollback y no guarda ninguna ejecucion
            DB::connection('empresa')->rollback();
            // Retornar una respuesta JSON para indicar el error
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al realizar el pedido. Por favor, intente nuevamente.',
                'error' => $e->getMessage()  // Esto puede ser útil para depuración, pero deberías quitarlo en producción
            ], 500);
        }
    }

    public function editar(Request $request)
    {
        $pedido = Pedidos::findOrFail($request->pedidosid);
        $detalles = PedidosDetalles::select('pedidos_detalles.precio', 'pedidos_detalles.precioiva', 'pedidos_detalles.iva as valoriva', 'pedidos_detalles.cantidad', 'pedidos_detalles.descuento', 'productos.descripcion', 'productos.productocodigo', 'productos.productosid as id', 'pedidos_detalles.informacion', 'pedidos_detalles.medidasid', 'pedidos_detalles.cantidadentregada as cantidadanterior', 'pedidos_detalles.cantidadfactor as factor', 'medidas.descripcioncorta as medida')
            ->join('productos', 'productos.productosid', '=', 'pedidos_detalles.productosid')
            ->join('medidas', 'medidas.medidasid', '=', 'pedidos_detalles.medidasid')
            ->where('pedidosid', $request->pedidosid)->get();
        $cliente = Clientes::where('clientesid', $pedido->clientesid)->first();
        $sucursales = ClientesSucursales::where('clientesid', $cliente->clientesid)->get();
        $centros_costos = CentroCostos::where('estado', 1)
            // Primero ordenamos por el prefijo alfabético (antes del primer punto)
            ->orderByRaw("SUBSTRING_INDEX(centro_costocodigo, '.', 1) ASC")
            // Luego ordenamos por los segmentos numéricos posteriores, de forma que se interpreten como enteros
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 2), '.', -1), UNSIGNED) ASC")  // Segmento 1 (después del primer punto)
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 3), '.', -1), UNSIGNED) ASC")  // Segmento 2 (después del segundo punto)
            ->orderByRaw("CONVERT(SUBSTRING_INDEX(SUBSTRING_INDEX(centro_costocodigo, '.', 4), '.', -1), UNSIGNED) ASC")  // Segmento 3 (después del tercer punto)
            // Agrega más líneas si hay más segmentos que ordenar
            ->get();
        // Extraemos la parte que sigue a "Destinatario:" considerando saltos de línea
        $observacion = $pedido->observacion;
        $destinatario = null;
        $urbano = null;

        // Primero, extraemos el "Destinatario"
        if (preg_match('/Destinatario:\s*([^;]+)/', $observacion, $matches)) {
            $destinatario = $matches[1]; // El valor del destinatario
        }

        // Luego, extraemos el "Urbano" (si está presente después del destinatario)
        if (preg_match('/Urbano:\s*([^;]+)/', $observacion, $matches)) {
            $urbano = $matches[1]; // El valor del urbano
        }

        return view('backend.pedidos.editar', ['pedido' => $pedido, 'cliente' => $cliente, 'centros_costos' => $centros_costos, 'sucursales' => $sucursales, 'detalles' => $detalles, 'destinatario' => $destinatario, 'urbano' => $urbano]);
    }

    public function actualizar(Request $request)
    {
        // Obtener los datos de la cabecera.
        $cabecera = $request->input('cabecera');
        // Obtener los productos.
        $productos = $request->input('productos');
        // Obtener los totales.
        $totales = $request->input('totales');

        $parametros = ParametrosEmpresa::first();

        DB::connection('empresa')->beginTransaction();
        try {
            $almacenes = DB::connection('empresa')->table('facturadores_almacenes')
                ->where('facturadoresid', get_setting('facturador'))
                ->where('principal', '1')
                ->first();

            $pedido =  Pedidos::findOrFail($cabecera['pedidosid']);
            $pedido->emision = $cabecera['fecha'];
            $pedido->clientes_sucursalesid = $cabecera['sucursal'];
            $pedido->centros_costosid = $cabecera['centroCosto'];
            $pedido->estado = $cabecera['estado'];
            $pedido->prioridad = $cabecera['prioridad'];
            $observacion = '';
            if (!empty($cabecera['destinatario'])) {
                $observacion .= 'Destinatario: ' . $cabecera['destinatario'];
            }

            if (!empty($cabecera['urbano'])) {
                // Si ya hay un destinatario, agregar un salto de línea antes de "Urbano"
                if (!empty($observacion)) {
                    $observacion .= ";"; // salto de línea
                }
                $observacion .= 'Urbano: ' . $cabecera['urbano'];
            }

            $pedido->observacion = $observacion;
            $pedido->usuariomodificacion = "Admin Ecommerce";
            $pedido->fechamodificacion = now();
            $pedido->subtotalsiniva = $totales['subtotalNetoSinIva'];
            $pedido->subtotalconiva = $totales['subtotalNetoConIva'];
            $pedido->subtotalconiva2 = $totales['subtotalNetoIva5'];
            $pedido->total_descuento = $totales['descuentoTotal'];
            $pedido->subtotalneto = $totales['subtotalNeto'];
            $pedido->subtotal = $totales['subtotal'];
            $pedido->total_iva = $totales['totalIVA'];
            $pedido->total_iva2 = $totales['totalIVA5'];
            $pedido->total = $totales['total'];

            $pedido->save();
            // Eliminar los detalles del pedido
            PedidosDetalles::where('pedidosid', $cabecera['pedidosid'])->delete();

            foreach ($productos as $producto) {
                $detallepedido = new PedidosDetalles;
                $detallepedido->pedidosid = $pedido['pedidosid'];
                $detallepedido->centros_costosid = $cabecera['centroCosto'];
                $detallepedido->productosid = $producto['id'];
                $detallepedido->medidasid = $producto['medidasid'];;
                $detallepedido->almacenesid = $almacenes->almacenesid;
                $detallepedido->cantidaddigitada = $producto['cantidad'];
                $detallepedido->cantidadentregada = $producto['cantidadanterior'];
                $detallepedido->cantidadfactor = $producto['factor']; //$producto->cantidadfactor;
                $detallepedido->cantidad = $producto['cantidad'] * $producto['factor']; //$producto->cantidad * $producto->cantidadfactor;
                $detallepedido->precio = $producto['precio'] / $producto['factor']; // $producto->precio / $producto->cantidadfactor;
                $detallepedido->iva = $producto['valoriva'];
                $detallepedido->precioiva = $producto['precioiva'];
                $detallepedido->descuento = $producto['descuento'];
                $detallepedido->preciovisible = $parametros->tipopresentacionprecios == 1 ? $producto['precioiva'] : $producto['precio'];
                // Solo asigna la información si existe y no está vacía
                if (array_key_exists('informacion', $producto) && !empty($producto['informacion'])) {
                    $detallepedido->informacion = $producto['informacion'];
                }
                $detallepedido->save();
            }
            // Crear el objeto de log
            $log = new LogSistema();

            // Llenar los campos del log
            $log->sis_subcategoriasid = 113;
            $log->equipo = "Ecommerce";
            $log->sis_empresasid = $request->segment(1);
            $log->sis_usuariosid = Auth::guard('admin')->user()->sis_usuariosid; // ID del usuario autenticado
            $log->tipooperacion = 'M';
            $log->fecha = now();

            // Construir el detalle a partir de los datos del pedido y detalles
            $detalle = [
                "pedidos" => [
                    "pedidosid" => $pedido->pedidosid,
                    "emision" => now()->format('Ymd'),
                    "pedidos_codigo" => $pedido->pedidos_codigo,
                    "forma_pago_empresaid" => $pedido->forma_pago_empresaid,
                    "facturadoresid" => $pedido->facturadoresid,
                    "clientesid" => $pedido->clientesid,
                    "clientes_sucursalesid" => $pedido->clientes_sucursalesid,
                    "almacenesid" => $pedido->almacenesid,
                    "centros_costosid" => $pedido->centros_costosid,
                    "vendedoresid" => $pedido->vendedoresid,
                    "tarifasid" => $pedido->tarifasid,
                    "concepto" => $pedido->concepto,
                    "origen" => $pedido->origen,
                    "documentosid" => 0,
                    "promocionesid" => 1,
                    "observacion" => $pedido->observacion,
                    "subtotal" => $pedido->subtotal,
                    "subtotalsiniva" => $pedido->subtotalsiniva,
                    "subtotalconiva" => $pedido->subtotalconiva,
                    "subtotalconiva2" => $pedido->subtotalconiva2,
                    "total_descuento" => $pedido->total_descuento,
                    "subtotalneto" => $pedido->subtotalneto,
                    "total_iva" => $pedido->total_iva,
                    "total_iva2" => $pedido->total_iva2,
                    "total" => $pedido->total,
                    "ecommerceid" => 0,
                    "restaurante_mesasid" => 0,
                    "estado" => $pedido->estado,
                    "uuid" => "",
                    "estado_sync" => 0,
                    "prioridad" => $pedido->prioridad,
                    "fechacreacion" => $pedido->fechacreacion,
                    "usuariocreacion" => $pedido->usuariocreacion,
                    "fechamodificacion" => $pedido->fechamodificacion,
                    "usuariomodificacion" => $pedido->usuariomodificacion
                ],
                "Detalles" => PedidosDetalles::where('pedidosid', $pedido->pedidosid)->get()->map(function ($detalle) {
                    $producto = Producto::findOrFail($detalle->productosid);

                    return [
                        "ProductoID" => $detalle->productosid,
                        "Codigo" => $producto->productocodigo ?? '',
                        "Descripcion" => $producto->descripcion ?? '',
                        "Cantidad" => $detalle->cantidad,
                        "Costo" => $detalle->precio,
                        "Descuento" => $detalle->descuento,
                        "Total" => $detalle->cantidad * $detalle->precio
                    ];
                })->toArray(),
            ];

            // Asignar el detalle serializado al campo correspondiente
            $log->detalle = json_encode($detalle);
            $log->save();
            // Si todo se inserta correctamente, realiza un commit a la base de datos
            DB::connection('empresa')->commit();
            // Retornar una respuesta JSON para indicar éxito
            return response()->json([
                'status' => 'success',
                'message' => 'Su pedido ha sido actualizado correctamente',
                'redirect_url' => route('pedidos.index')  // Puedes devolver la URL a la que deseas redirigir
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            //Si ocurrio algun error mientras insertaba datos hace un rollback y no guarda ninguna ejecucion
            DB::connection('empresa')->rollback();
            // Retornar una respuesta JSON para indicar el error
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al realizar el pedido. Por favor, intente nuevamente.',
                'error' => $e->getMessage()  // Esto puede ser útil para depuración, pero deberías quitarlo en producción
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $pedido = Pedidos::findOrFail($id);
        if ($pedido->documentosid == 0) {
            // Crear el objeto de log
            $log = new LogSistema();

            // Llenar los campos del log
            $log->sis_subcategoriasid = 113;
            $log->equipo = "Ecommerce";
            $log->sis_empresasid = $request->segment(1);
            $log->sis_usuariosid = Auth::guard('admin')->user()->sis_usuariosid; // ID del usuario autenticado
            $log->tipooperacion = 'E';
            $log->fecha = now();

            // Construir el detalle a partir de los datos del pedido y detalles
            $detalle = [
                "pedidos" => [
                    "pedidosid" => $pedido->pedidosid,
                    "emision" => now()->format('Ymd'),
                    "pedidos_codigo" => $pedido->pedidos_codigo,
                    "forma_pago_empresaid" => $pedido->forma_pago_empresaid,
                    "facturadoresid" => $pedido->facturadoresid,
                    "clientesid" => $pedido->clientesid,
                    "clientes_sucursalesid" => $pedido->clientes_sucursalesid,
                    "almacenesid" => $pedido->almacenesid,
                    "centros_costosid" => $pedido->centros_costosid,
                    "vendedoresid" => $pedido->vendedoresid,
                    "tarifasid" => $pedido->tarifasid,
                    "concepto" => $pedido->concepto,
                    "origen" => $pedido->origen,
                    "documentosid" => 0,
                    "promocionesid" => 1,
                    "observacion" => $pedido->observacion,
                    "subtotal" => $pedido->subtotal,
                    "subtotalsiniva" => $pedido->subtotalsiniva,
                    "subtotalconiva" => $pedido->subtotalconiva,
                    "subtotalconiva2" => $pedido->subtotalconiva2,
                    "total_descuento" => $pedido->total_descuento,
                    "subtotalneto" => $pedido->subtotalneto,
                    "total_iva" => $pedido->total_iva,
                    "total_iva2" => $pedido->total_iva2,
                    "total" => $pedido->total,
                    "ecommerceid" => 0,
                    "restaurante_mesasid" => 0,
                    "estado" => $pedido->estado,
                    "uuid" => "",
                    "estado_sync" => 0,
                    "prioridad" => $pedido->prioridad,
                    "fechacreacion" => $pedido->fechacreacion,
                    "usuariocreacion" => $pedido->usuariocreacion,
                    "fechamodificacion" => $pedido->fechamodificacion,
                    "usuariomodificacion" => $pedido->usuariomodificacion
                ],
                "Detalles" => PedidosDetalles::where('pedidosid', $pedido->pedidosid)->get()->map(function ($detalle) {
                    $producto = Producto::findOrFail($detalle->productosid);

                    return [
                        "ProductoID" => $detalle->productosid,
                        "Codigo" => $producto->productocodigo ?? '',
                        "Descripcion" => $producto->descripcion ?? '',
                        "Cantidad" => $detalle->cantidad,
                        "Costo" => $detalle->precio,
                        "Descuento" => $detalle->descuento,
                        "Total" => $detalle->cantidad * $detalle->precio
                    ];
                })->toArray(),
            ];

            // Asignar el detalle serializado al campo correspondiente
            $log->detalle = json_encode($detalle);
            $log->save();

            PedidosDetalles::where('pedidosid', $id)->delete();
            $pedido->delete();

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
        // Variable que indica si hay algún detalle modificado
        $modificado = $detalle->contains(function ($detalle) {
            return $detalle->cantidadentregada != 0; // Condición para marcar como modificado
        });
        $cliente = Clientes::where('clientesid', $pedido->clientesid)->first();
        if (get_setting('maneja_sucursales') == "on") {
            $centrocosto = CentroCostos::where('centros_costosid', $pedido->centros_costosid)->first();
        } else {
            $centrocosto = 1;
        }
        // Extraemos la parte que sigue a "Destinatario:" considerando saltos de línea
        $observacion = $pedido->observacion;
        $destinatario = null;

        // Primero, extraemos el "Destinatario"
        if (preg_match('/Destinatario:\s*([^;]+)/', $observacion, $matches)) {
            $destinatario = $matches[1]; // El valor del destinatario
        }
        if (get_setting('maneja_sucursales') == "on") {
            $sucursal = ClientesSucursales::where('clientes_sucursalesid', $pedido->clientes_sucursalesid)->first();
        } else {
            $sucursal = 0;
        }
        return view('backend.pedidos_show', compact('pedido', 'detalle', 'cliente', 'centrocosto', 'destinatario', 'modificado', 'sucursal'));
    }

    public function actualizar_estado(Request $request)
    {
        $order = Pedidos::findOrFail($request->order_id);
        $order->estado = $request->status;
        $order->save();
        return 1;
    }

    public function exportPdf(Request $request)
    {
        $fecha = $request->fecha;
        $busqueda = null;

        $pedidos = Pedidos::select('pedidos.pedidosid', 'pedidos.pedidos_codigo', 'pedidos.total', 'pedidos.estado', 'clientes.razonsocial', 'pedidos.emision', 'pedidos.documentosid')
            ->join('clientes', 'clientes.clientesid', '=', 'pedidos.clientesid')
            ->where('pedidos.usuariocreacion', 'Ecommerce');

        if ($request->estado != null) {
            $pedidos = $pedidos->where('pedidos.estado', $request->estado);
        }

        if ($fecha != null) {
            $pedidos = $pedidos->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])));
        }

        if ($request->busqueda != null) {
            $busqueda = $request->busqueda;
            $pedidos = $pedidos->where('clientes.razonsocial', 'like', '%' . $busqueda . '%')
                ->orwhere('pedidos.pedidos_codigo', 'like', '%' . $busqueda . '%');
        }

        $pedidos = $pedidos->orderBy('pedidos.emision', 'desc');

        // Generar la vista para el PDF
        $pdf = \PDF::loadView('pedidos_listado', [
            'pedidos' => $pedidos->get(),
        ]);

        // Descargar el archivo PDF con un nombre adecuado
        return $pdf->download('Pedidos.pdf');
    }

    public function importarExcel(Request $request)
    {
        // Validar si se subió un archivo
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Obtener el archivo del request
        $file = $request->file('file');

        // Cargar la hoja de Excel con PhpSpreadsheet
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getSheet(0); // Seleccionar la primera hoja

        // Leer la primera fila (encabezados) para mapear las columnas
        $headerRow = $sheet->getRowIterator(1)->current();
        $headers = [];

        foreach ($headerRow->getCellIterator() as $cell) {
            $headers[$cell->getValue()] = $cell->getColumn();
        }

        // Verificar que los encabezados necesarios existen
        $requiredHeaders = ['Destinatario', 'Cod Agencia', 'Cod Centro', 'Cod Producto', 'Unidad', 'Cantidad Proveeduría'];
        foreach ($requiredHeaders as $requiredHeader) {
            if (!isset($headers[$requiredHeader])) {
                return response()->json(['success' => false, 'message' => "Falta la columna requerida: $requiredHeader"]);
            }
        }

        $lastKey = null; // Variable para rastrear cambios en las columnas A, B y D
        $hojaData = [];
        $pedidoItems = []; // Lista de ítems temporal para el pedido actual

        foreach ($sheet->getRowIterator(2) as $row) {
            $destinatario = $sheet->getCell($headers['Destinatario'] . $row->getRowIndex())->getValue();
            $codAgenc = $sheet->getCell($headers['Cod Agencia'] . $row->getRowIndex())->getValue();
            $codCent = $sheet->getCell($headers['Cod Centro'] . $row->getRowIndex())->getValue();
            $codigoProducto = $sheet->getCell($headers['Cod Producto'] . $row->getRowIndex())->getValue();
            $codMedida = $sheet->getCell($headers['Unidad'] . $row->getRowIndex())->getValue();
            $cantidad = $sheet->getCell($headers['Cantidad Proveeduría'] . $row->getRowIndex())->getValue();

            // Saltar filas vacías
            if (empty($destinatario) && empty($codAgenc) && empty($codCent) && empty($codigoProducto) && empty($codMedida) && empty($cantidad)) {
                continue; // Salta a la siguiente fila
            }

            // Generar una clave única para el "pedido actual"
            $currentKey = $destinatario . '|' . $codAgenc . '|' . $codCent;

            // Si hay un cambio en la clave, procesamos el pedido anterior y reiniciamos
            if ($lastKey !== null && $currentKey !== $lastKey) {
                $hojaData[] = $pedidoItems; // Guardar los ítems del pedido anterior
                $pedidoItems = []; // Reiniciar la lista de ítems
            }

            // Validar si la medida existe
            $medida = Medidas::where('descripcioncorta', $codMedida)->first();
            if (!$medida) {
                continue; // Saltar si no existe la medida
            }

            // Buscar producto en la base de datos
            $producto = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.barras', 'productos.descripcion', 'productos_tarifas.tarifasid', 'productos_tarifas.medidasid', 'productos_tarifas.precio', 'productos_tarifas.factor', 'productos_tarifas.precioiva', 'sri_tipos_ivas.valor as valoriva')
                ->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
                ->join('sri_tipos_ivas', 'productos.sri_tipos_ivas_codigo', '=', 'sri_tipos_ivas.sri_tipos_ivas_codigo')
                ->where('productos.estado', 1)
                ->where('productos_tarifas.tarifasid', get_setting('tarifa_productos'))
                ->where('productos_tarifas.medidasid', $medida->medidasid)
                ->where('productos.productocodigo', $codigoProducto)
                ->first();

            if ($producto) {
                $pedidoItems[] = [
                    'destinatario' => $destinatario,
                    'codAgenc' => $codAgenc,
                    'codCent' => $codCent,
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'descuento' => 0,
                ];
            }

            $lastKey = $currentKey;
        }

        // Procesar el último grupo si existe
        if (!empty($pedidoItems)) {
            $hojaData[] = $pedidoItems;
        }

        // Comenzar a crear pedidos por cada grupo de datos
        DB::connection('empresa')->beginTransaction();
        try {
            foreach ($hojaData as $pedidoItems) {
                $this->crearPedido($pedidoItems);
            }

            DB::connection('empresa')->commit();
            return response()->json(['success' => true, 'message' => 'Pedidos importados correctamente']);
        } catch (\Exception $e) {
            DB::connection('empresa')->rollback();
            return response()->json(['success' => false, 'message' => 'Ocurrió un error al importar los pedidos: ' . $e->getMessage()]);
        }
    }

    private function crearPedido($items)
    {
        $primerItem = $items[0];
        // Calcular los totales usando la función calcularTotales para la hoja actual
        $totales = $this->calcularTotales($items);

        $secuencial = Secuenciales::where('secuencial', 'PEDIDOS')->first();
        Secuenciales::where('secuenciales.secuencial', 'PEDIDOS')
            ->update(['valor' => $secuencial->valor + 1]);

        $almacenes = DB::connection('empresa')->table('facturadores_almacenes')
            ->where('facturadoresid', get_setting('facturador'))
            ->where('principal', '1')
            ->first();

        $pedido = new Pedidos;
        $pedido->emision = now()->format('Y-m-d');
        $pedido->pedidos_codigo = $secuencial->prefijo . str_pad($secuencial->valor, $secuencial->numeroceros, "0", STR_PAD_LEFT);
        $pedido->clientesid = get_setting('cliente_pedidos');
        $pedido->clientes_sucursalesid = $primerItem['codAgenc'];
        $pedido->forma_pago_empresaid = 1;
        $pedido->centros_costosid = CentroCostos::where('centro_costocodigo', $primerItem['codCent'])->first()->centros_costosid;
        $pedido->almacenesid = $almacenes->almacenesid;
        $pedido->concepto = "Pedido Ecommerce";
        $pedido->observacion = 'Destinatario: ' . $primerItem['destinatario'];
        $pedido->vendedoresid = get_setting('facturador');
        $pedido->facturadoresid = get_setting('facturador');
        $pedido->tarifasid = get_setting('tarifa_productos');
        $pedido->usuariocreacion = "Ecommerce";
        $pedido->fechacreacion = now();
        $pedido->estado = 1;
        $pedido->subtotalsiniva = $totales['subtotalNetoSinIva'];
        $pedido->subtotalconiva = $totales['subtotalNetoConIva'];
        $pedido->subtotalconiva2 = $totales['subtotalNetoIva5'];
        $pedido->total_descuento = $totales['descuentoTotal'];
        $pedido->subtotalneto = $totales['subtotalNeto'];
        $pedido->subtotal = $totales['subtotal'];
        $pedido->total_iva = $totales['totalIVA'];
        $pedido->total_iva2 = $totales['totalIva5'];
        $pedido->total = $totales['total'];
        $pedido->save();

        foreach ($items as $item) {

            $detallepedido = new PedidosDetalles;
            $detallepedido->pedidosid = $pedido->pedidosid;
            $detallepedido->centros_costosid = CentroCostos::where('centro_costocodigo', $primerItem['codCent'])->first()->centros_costosid;
            $detallepedido->productosid = $item['producto']->productosid;
            $detallepedido->medidasid =  $item['producto']->medidasid;
            $detallepedido->almacenesid = $almacenes->almacenesid;
            $detallepedido->cantidaddigitada = $item['cantidad'];
            $detallepedido->cantidadfactor = $item['producto']->factor;
            $detallepedido->cantidad = $item['cantidad'] * $item['producto']->factor;
            $detallepedido->precio = $item['producto']->precio /  $item['producto']->factor;
            $detallepedido->iva = $item['producto']->valoriva;
            $detallepedido->precioiva = $item['producto']->precioiva;
            $detallepedido->descuento = isset($item['producto']->descuento) ? $item['producto']->descuento : 0;
            $detallepedido->preciovisible = isset($parametros->tipopresentacionprecios) && $parametros->tipopresentacionprecios == 1 ? $item['producto']->precioiva : $item['producto']->precio;
            $detallepedido->save();
        }
    }

    public function calcularTotales($items)
    {

        $totales = [
            'subtotal' => 0,
            'descuentoTotal' => 0,
            'subtotalNetoConIva' => 0,
            'subtotalNetoIva5' => 0,
            'subtotalNetoSinIva' => 0,
            'totalIVA' => 0,
            'totalIva5' => 0,
            'total' => 0,
            'totalItems' => 0,
            'totalCantidad' => 0,
        ];

        foreach ($items as $item) {
            $cantidad = floatval($item['cantidad']);
            $precio = floatval($item['producto']->precio);
            $valoriva = floatval($item['producto']->valoriva) / 100;  // Convertir el porcentaje a decimal
            $descuentoPorcentaje = isset($item['descuento']) ? floatval($item['descuento']) : 0;

            // Subtotal del producto (sin IVA aplicado)
            $subtotalProducto = $cantidad * $precio;

            // Calcular el monto de descuento
            $descuentoMonto = $subtotalProducto * ($descuentoPorcentaje / 100);

            // Calcular subtotal después del descuento pero antes del IVA
            $subtotalConDescuento = $subtotalProducto - $descuentoMonto;

            // Calcular el monto del IVA correspondiente
            $ivaMonto = $subtotalConDescuento * $valoriva;

            // Total del producto después del descuento y con el IVA aplicado
            $totalProducto = $subtotalConDescuento + $ivaMonto;

            // Actualizar los campos de totales
            $totales['subtotal'] += $subtotalProducto;
            $totales['descuentoTotal'] += $descuentoMonto;

            // Diferenciar los valores por el tipo de IVA aplicado
            if ($valoriva > 0) {
                if ($valoriva === 0.05) {
                    // Si el IVA es del 5%, acumular en subtotal IVA 5%
                    $totales['subtotalNetoIva5'] += $subtotalConDescuento;
                    $totales['totalIva5'] += $ivaMonto;  // Acumular solo el IVA al 5%
                } else {
                    // Si el IVA no es del 5%, acumular en subtotal con IVA distinto del 5%
                    $totales['subtotalNetoConIva'] += $subtotalConDescuento;
                    $totales['totalIVA'] += $ivaMonto;  // Acumular solo el IVA distinto del 5%
                }
            } else {
                // Si no tiene IVA, acumular en subtotal sin IVA
                $totales['subtotalNetoSinIva'] += $subtotalConDescuento;
            }

            $totales['total'] += $totalProducto;
            $totales['totalItems'] += 1; // Contar la fila como un ítem
            $totales['totalCantidad'] += $cantidad; // Sumar las cantidades
        }

        $totales['subtotalNeto'] = $totales['subtotalNetoConIva'] + $totales['subtotalNetoIva5'] + $totales['subtotalNetoSinIva'];

        return $totales;
    }
}
