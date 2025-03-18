<?php

namespace App\Http\Controllers;

use App\Mail\Test;
use App\Models\Categorias;
use App\Models\Clientes;
use App\Models\ClientesSucursales;
use App\Models\Facturador;
use App\Models\GrupoClientes;
use Illuminate\Http\Request;
use App\Models\Integraciones;
use App\Models\Lineas;
use App\Models\Medidas;
use App\Models\ParametrosEmpresa;
use App\Models\Producto;
use App\Models\Subcategorias;
use App\Models\Subgrupos;
use App\Models\Tarifas;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ConfiguracionesController extends Controller
{
    private function updateSettings($tipo, $updates)
    {
        $base = request()->segment(1);

        $businessSettings = Integraciones::where('tipo', $tipo)->firstOrFail();
        $settings = json_decode($businessSettings->parametros);

        foreach ($updates as $key => $value) {
            $settings->$key = $value;
        }

        Integraciones::where('tipo', $tipo)->update(['parametros' => json_encode($settings)]);
        // Convertir $settings a un arreglo asociativo y guardarlo en caché
        Cache::forever('settings' . $base, (array) $settings);
        flash('Configuración actualizada correctamente')->success();
    }

    public function general()
    {
        $productos_existencias = get_setting('productos_existencias');
        $facturadores = Facturador::where('facturador', '=', 1)
            ->where('estado', '=', 1)
            ->get();
        $tarifas = Tarifas::all();
        $grupos = GrupoClientes::all();
        $almacenes = DB::connection('empresa')->table('almacenes')->where('estado', '1')->get();
        $clientes = Clientes::where('clientesid', get_setting('cliente_pedidos'))->first();
        return view('backend.configuraciones-generales', compact('facturadores', 'productos_existencias', 'tarifas', 'grupos', 'almacenes', 'clientes'));
    }

    public function update_general(Request $request)
    {
        $tags = [];

        if ($request->get('email_pedidos')[0] != null) {
            foreach (json_decode($request->get('email_pedidos')[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }

        $updates = [
            'grupo_productos' => $request->grupo_productos,
            'facturador' => $request->facturador,
            'productos_existencias' => $request->productos_existencias,
            'tarifa_productos' => $request->tarifa_productos,
            'registra_clientes' => $request->registra_clientes,
            'grupo_clientes' => $request->grupo_clientes,
            'tarifa_clientes' => $request->tarifa_clientes,
            'cantidad_maxima' => $request->cantidad_maxima,
            'pago_pedido' => $request->pago_pedido,
            'pago_plux' => $request->pago_plux,
            'email_pago_plux' => $request->email_pago_plux,
            'pedido_pago_plux' => $request->pedido_pago_plux,
            'productos_disponibles' => $request->productos_disponibles,
            'productos_no_disponibles' => $request->productos_no_disponibles,
            'ver_codigo' => $request->ver_codigo,
            'tipo_tienda' => $request->tipo_tienda,
            'controla_stock' => $request->controla_stock,
            'vista_categorias' => $request->vista_categorias,
            'email_pedidos' => implode(',', $tags),
            'cupo_sucursal' => $request->cupo_sucursal,
            'cliente_pedidos' => $request->cliente_pedidos,
            'maneja_sucursales' => $request->maneja_sucursales,
        ];

        if ($request->file('imagen_defecto')) {
            $imagen = base64_encode(file_get_contents($request->file('imagen_defecto')));
            $updates['imagen_defecto'] = $imagen;
        }

        $this->updateSettings(5, $updates);

        return back();
    }

    public function header()
    {
        return view('backend.configuraciones-header');
    }

    public function update_header(Request $request)
    {
        $updates = [
            'header_stikcy' => $request->header_stikcy,
        ];

        if ($request->file('header_logo')) {
            $imagen = $request->header_logo;
            $base = $request->segment(1);
            $updates['header_logo'] = base64_encode(file_get_contents($request->file('header_logo')));

            move_uploaded_file($imagen->getRealPath(), public_path("assets/img/") . 'logo-' . $base . '.png');
        }

        $this->updateSettings(5, $updates);

        return back();
    }

    public function footer()
    {
        return view('backend.configuraciones-footer');
    }

    public function update_footer(Request $request)
    {
        $updates = [
            'acerca_nosotros' => $request->acerca_nosotros,
            'direccion_contacto' => $request->direccion_contacto,
            'telefono_contacto' => $request->telefono_contacto,
            'email_contacto' => $request->email_contacto,
            'show_social_links' => $request->show_social_links,
            'facebook_link' => $request->facebook_link,
            'twitter_link' => $request->twitter_link,
            'instagram_link' => $request->instagram_link,
        ];

        if ($request->file('footer_logo')) {
            $updates['footer_logo'] = base64_encode(file_get_contents($request->file('footer_logo')));
        }

        $this->updateSettings(5, $updates);

        return back();
    }

    public function apariencia()
    {
        return view('backend.configuraciones-apariencia');
    }

    public function update_apariencia(Request $request)
    {
        $updates = [
            'nombre_sitio' => $request->nombre_sitio,
            'lema_sitio' => $request->lema_sitio,
            'color_sitio' => $request->color_sitio,
            'color_hover_sitio' => $request->color_hover_sitio,
            'header_script' => $request->header_script,
            'footer_script' => $request->footer_script,
        ];

        if ($request->file('icono_sitio')) {
            $updates['icono_sitio'] = base64_encode(file_get_contents($request->file('icono_sitio')));
        }

        $this->updateSettings(5, $updates);

        return back();
    }

    public function paginas()
    {
        return view('backend.paginas.index');
    }

    public function edit_paginas($pagina)
    {

        $contenido =  get_setting($pagina);

        if ($pagina == 'inicio') {

            $grupo = get_setting('grupo_productos');
            $grupoid = getGrupoID();
            $grupos = "";

            switch ($grupo) {
                case 'lineas':
                    $grupos = Lineas::get();
                    break;
                case 'categorias':
                    $grupos = Categorias::get();
                    break;
                case 'subcategorias':
                    $grupos = Subcategorias::get();
                    break;
                case 'subgrupos':
                    $grupos = Subgrupos::get();
                    break;
                default:
                    break;
            }

            return view('backend.paginas.inicio_edit', compact('pagina', 'grupos', 'grupoid'));
        } else {
            return view('backend.paginas.edit', compact('pagina', 'contenido'));
        }
    }

    public function update_paginas(Request $request)
    {
        $pagina = $request->get('pagina');
        $contenido = $request->get('contenido');

        $updates = [$pagina => $contenido];

        $this->updateSettings(5, $updates);

        return back();
    }

    public function update_inicio(Request $request)
    {

        $sliders = [];

        if ($request->inicio) {
            foreach ($request->inicio as $key => $type) {

                if (isset($request->imagenes[$key])) {
                    $imagen = base64_encode(file_get_contents($request->imagenes[$key]));
                } else {
                    $imagen = $request->imagen[$key];
                }

                $slider = [
                    "imagen" => $imagen,
                    "link" => $request->links[$key],
                    "inicio" => $request->inicio[$key],
                    "fin" => $request->fin[$key]
                ];

                array_push($sliders, $slider);
            }
        }

        $updates = [
            'home_slider' => json_encode($sliders),
            'top10_categories' => $request->get('top10_categories', '')  // Usa valor predeterminado si no se proporciona
        ];

        $this->updateSettings(5, $updates);

        return back();
    }

    public function analytics()
    {
        return view('backend.configuraciones-analytics');
    }

    public function update_analytics(Request $request)
    {
        $updates = [
            'facebook_pixel' => $request->facebook_pixel,
            'FACEBOOK_PIXEL_ID' => $request->FACEBOOK_PIXEL_ID,
            'google_analytics' => $request->google_analytics,
            'TRACKING_ID' => $request->TRACKING_ID,
        ];

        $this->updateSettings(5, $updates);

        return back();
    }

    public function social_login(Request $request)
    {
        return view('backend.social_login');
    }

    public function update_social_login(Request $request)
    {
        foreach ($request->types as $key => $type) {
            $this->overWriteEnvFile($type, $request[$type]);
        }

        $updates = [
            'login_google' => $request->login_google,
            'login_facebook' => $request->login_facebook,
            'login_apple' => $request->login_apple,
        ];

        $this->updateSettings(5, $updates);

        return back();
    }

    public function overWriteEnvFile($type, $val)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $val = '"' . trim($val) . '"';
            if (is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0) {
                file_put_contents($path, str_replace(
                    $type . '="' . env($type) . '"',
                    $type . '=' . $val,
                    file_get_contents($path)
                ));
            } else {
                file_put_contents($path, file_get_contents($path) . "\r\n" . $type . '=' . $val);
            }
        }
    }

    public function smtp_settings(Request $request)
    {
        $parametros = ParametrosEmpresa::first();
        return view('backend.smtp_settings', compact('parametros'));
    }

    public function update_smtp(Request $request)
    {
        $parametros = ParametrosEmpresa::first();
        $parametros->smtp_servidor = $request->smtp_servidor;
        $parametros->smtp_usuario = $request->smtp_usuario;
        $parametros->smtp_clave = $request->smtp_clave;
        $parametros->smtp_puerto = $request->smtp_puerto;
        $parametros->smtp_from = $request->smtp_from;
        $parametros->save();

        flash('Guardado Correctamente')->success();
        return back();
    }

    public function testEmail(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'email' => 'required|email',
        ]);

        // Obtener parámetros de la empresa
        $parametros = ParametrosEmpresa::first();

        // Preparar datos del correo
        $array = [
            'view' => 'emails.test',
            'subject' => "SMTP Test",
            'from' => Config::get('mail.from.address'),
            'content' => "Esto es un email de prueba.",
        ];

        // Determinar el método de envío según el tipo SMTP
        if ($parametros->smtp_tipo == 1) {
            // Enviar por SMTP tradicional
            configurar_smtp();

            try {
                Mail::mailer('smtp')->to($request->email)->send(new Test($array));
                flash('Email enviado correctamente')->success();
            } catch (\Exception $e) {
                flash('Error enviando email, revise los parametros: ' . $e->getMessage())->error();
            }
        } elseif ($parametros->smtp_tipo == 2) {
            // Enviar por API de Gmail usando el helper
            [$success, $message] = enviar_por_gmail_api($request->email, $array, $parametros);

            if ($success) {
                flash($message)->success();
            } else {
                flash($message)->error();
            }
        } else {
            flash('Tipo de configuración de correo no válido')->error();
        }

        return back();
    }

    public function asignar_claves()
    {
        $clientes = Clientes::all();
        foreach ($clientes as $key => $cliente) {
            $clave = json_decode($cliente->clave);
            if ($clave) {
                $clave->ecommerce = encrypt_openssl(substr($cliente->identificacion, 0, 4), "Perseo1232*");
            } else {
                $clave = [
                    'ecommerce' => encrypt_openssl(substr($cliente->identificacion, 0, 4), "Perseo1232*"),
                    'documentos' => null,
                ];
            }

            Clientes::where('clientesid', $cliente->clientesid)
                ->update(
                    [
                        'clave' => json_encode($clave),
                    ]
                );
        }
        return 1;
    }

    public function asignar_direcciones()
    {
        $clientes = Clientes::all();
        foreach ($clientes as $key => $cliente) {
            $sucursal = ClientesSucursales::where('clientesid', $cliente->clientesid)->first();

            if (!$sucursal) {
                $address = new ClientesSucursales();
                $address->clientesid = $cliente->clientesid;
                $address->ciudadesid = $cliente->ciudadesid;
                $address->direccion = substr($cliente->direccion, 0, 100);
                $address->telefono1 = $cliente->telefono1;
                $address->fechacreacion = now();
                $address->usuariocreacion = 'Ecommerce';

                $address->save();
            }
        }
        return 1;
    }

    public function trackingUrbano(Request $request)
    {
        $integracion = Integraciones::where('tipo', 8)->first();
        $integracion = json_decode($integracion->parametros);

        // Inicializar el cliente de Guzzle
        $client = new Client();

        // URL base de la API
        $url = $integracion->SMTP_Servidor . 'tracking/';

        // Parámetros de consulta en formato JSON
        $queryParams = [
            'json' => json_encode([
                'vp_linea' => 3,
                'guia' => $request->codigo,  // Aquí se usa el código que se pasa como parámetro
                'docref' => ''
            ])
        ];

        // Encabezados de la petición
        $headers = [
            'user' => $integracion->SMTP_Usuario,
            'pass' => $integracion->SMTP_Pass,
        ];

        try {
            // Realizar la petición GET
            $response = $client->request('GET', $url, [
                'query' => $queryParams,
                'headers' => $headers
            ]);
            // Obtener el cuerpo de la respuesta
            $data = json_decode($response->getBody(), true);

            // Extraer el estado de la primera entrada
            $estado = $data[0]['estado'] ?? 'Estado no disponible';

            // Retornar solo el estado en la respuesta
            return response()->json(['estado' => $estado]);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'error' => 'Error al consultar la API',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function busquedaProducto(Request $request)
    {
        // Obtenemos el término de búsqueda enviado desde el input
        $termino = $request->producto;

        // Buscamos en la base de datos utilizando el modelo Producto
        // donde coincida el término con alguno de los campos indicados
        $productos = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.barras', 'productos.descripcion', 'productos_tarifas.tarifasid', 'productos_tarifas.medidasid', 'medidas.descripcioncorta as medida', 'productos_tarifas.precio', 'productos_tarifas.precioiva', 'productos_tarifas.factor', 'sri_tipos_ivas.valor as valoriva')
            ->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
            ->join('medidas', 'productos_tarifas.medidasid', '=', 'medidas.medidasid')
            ->join('sri_tipos_ivas', 'productos.sri_tipos_ivas_codigo', '=', 'sri_tipos_ivas.sri_tipos_ivas_codigo')
            ->where('productos.estado', 1)
            ->where('productos_tarifas.tarifasid', get_setting('tarifa_productos'))
            ->where('productos.venta', '1')
            ->where('productos.servicio', '0')
            ->where('productos.bien', '0')
            ->where('productos.ecommerce_estado', '1')
            ->where(function ($query) use ($termino) {
                $query->where('productos.productocodigo', 'like', '%' . $termino . '%')
                    ->orWhere('productos.barras', 'like', '%' . $termino . '%')
                    ->orWhere('productos.descripcion', 'like', '%' . $termino . '%');
            })
            ->get();

        // Devolvemos los resultados como JSON
        return response()->json($productos);
    }

    public function busquedaCliente(Request $request)
    {
        $identificacion = $request->cliente;

        $clientes = Clientes::select('clientesid', 'identificacion', 'razonsocial')
            ->where('identificacion', $identificacion)
            ->first();

        return response()->json($clientes);
    }

    public function obtenerMedidas(Request $request)
    {
        $productoId = $request->input('id');

        $medidas = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.barras', 'productos.descripcion', 'productos_tarifas.tarifasid', 'productos_tarifas.medidasid', 'productos_tarifas.factor', 'medidas.descripcioncorta as medida', 'productos_tarifas.precio', 'productos_tarifas.precioiva')
            ->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
            ->join('medidas', 'productos_tarifas.medidasid', '=', 'medidas.medidasid')
            ->where('productos.productosid', $productoId)
            ->where('productos_tarifas.tarifasid', get_setting('tarifa_productos'))
            ->get();

        return response()->json(['medidas' => $medidas]);
    }
}
