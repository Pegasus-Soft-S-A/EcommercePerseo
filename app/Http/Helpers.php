<?php

use App\Models\Categorias;
use App\Models\Integraciones;
use App\Models\Lineas;
use App\Models\ParametrosEmpresa;
use App\Models\Producto;
use App\Models\Subcategorias;
use App\Models\Subgrupos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;

if (!function_exists('static_asset')) {
    function static_asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if (!function_exists('get_setting')) {
    //funcion global que devuelve el valor del parametro de la integracion que se le envia
    function get_setting($setting)
    {
        $base = Request::segment(1);

        $settings = Cache::rememberForever('settings' . $base, function () {
            $integraciones = Integraciones::select('parametros')->where('tipo', 5)->first();
            if ($integraciones && $integraciones->parametros) {
                return json_decode($integraciones->parametros, true);
            }

            // Aquí deberías retornar los valores por defecto de tus configuraciones
            // si no hay nada en la base de datos. Esto puede ser un array asociativo.
            return [
                'header_sticky' => null,
                'header_logo' => null,
                'footer_logo' => null,
                'acerca_nosotros' => null,
                'direccion_contacto' => null,
                'telefono_contacto' => null,
                'email_contacto' => null,
                'show_social_links' => null,
                'facebook_link' => null,
                'twitter_link' => null,
                'instagram_link' => null,
                'nombre_sitio' => null,
                'lema_sitio' => null,
                'icono_sitio' => null,
                'color_sitio' => '#377dff',
                'color_hover_sitio' => '#377dff',
                'header_script' => null,
                'footer_script' => null,
                'inicio' => null,
                'terminos_condiciones' => null,
                'politica_devoluciones' => null,
                'politica_soporte' => null,
                'politica_privacidad' => null,
                'home_slider' => null,
                'top10_categories' => [],
                'facebook_pixel' => null,
                'FACEBOOK_PIXEL_ID' => null,
                'google_analytics' => null,
                'TRACKING_ID' => null,
                'grupo_productos' => 'categorias',
                'facturador' => 1,
                'productos_existencias' => 'todos',
                'tarifa_productos' => 1,
                'cantidad_maxima' => '10',
                'registra_clientes' => null,
                'grupo_clientes' => null,
                'tarifa_clientes' => null,
                'email_pedidos' => null,
                'imagen_defecto' => base64_encode(file_get_contents(public_path('assets/img/placeholder.jpg'))), // Asegúrate de que esta ruta es accesible y existe.
                'login_google' => null,
                'login_facebook' => null,
                'login_apple' => null,
                "pago_pedido" => '1',
                "pago_plux" => '0',
                "email_pago_plux" => null,
                "pedido_pago_plux" => "pedido",
                "productos_disponibles" => 'En Stock',
                "productos_no_disponibles" => 'Bajo Pedido',
                "ver_codigo" => 0,
                "tipo_tienda" => 'publico',
                "controla_stock" => 0,
            ];
        });

        return $settings[$setting] ?? null; // Devuelve null o un valor por defecto si no se encuentra la configuración

    }
}

if (!function_exists('getGrupoID')) {
    function getGrupoID()
    {
        $grupo = get_setting('grupo_productos');
        switch ($grupo) {
            case 'lineas':
                $grupoid = "productos_lineasid";
                break;
            case 'categorias':
                $grupoid = "productos_categoriasid";
                break;
            case 'subcategorias':
                $grupoid = "productos_subcategoriasid";
                break;
            case 'subgrupos':
                $grupoid = "productos_subgruposid";
                break;
            default:
                break;
        }

        return  $grupoid;
    }
}

if (!function_exists('gruposDestacados')) {
    function gruposDestacados()
    {
        $grupo = get_setting('grupo_productos');
        switch ($grupo) {
            case 'lineas':
                $grupos = Lineas::select('productos_lineasid', 'descripcion', 'imagen')
                    ->where('parametros_json->destacado', 1)
                    ->get();
                break;
            case 'categorias':
                $grupos = Categorias::select('productos_categoriasid', 'descripcion', 'imagen')
                    ->where('parametros_json->destacado', 1)
                    ->get();
                break;
            case 'subcategorias':
                $grupos = Subcategorias::select('productos_subcategoriasid', 'descripcion', 'imagen')
                    ->where('parametros_json->destacado', 1)
                    ->get();
            case 'subgrupos':
                $grupos = Subgrupos::select('productos_subgruposid', 'descripcion', 'imagen')
                    ->where('parametros_json->destacado', 1)
                    ->get();
            default:
                break;
        }

        return  count($grupos) > 0 ? $grupos : null;
    }
}

if (!function_exists('productosOferta')) {
    function productosOferta()
    {
        $parametros = ParametrosEmpresa::first();
        if (Auth::check()) {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = Producto::select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precioiva as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            } else {
                $products = Producto::select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precio as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            }
            $products = $products->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
                ->leftJoin('productos_imagenes', function ($products) {
                    $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                        ->where('productos_imagenes.principal', '=', "1");
                })
                ->where('productos_tarifas.tarifasid', '=', get_setting('tarifa_productos'))
                ->where('productos.ecommerce_estado', 1)
                ->where('productos.venta', '=', '1')
                ->where('productos.servicio', '=', '0')
                ->where('productos.bien', '=', '0')
                ->where('productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna'))
                ->when(get_setting('productos_existencias') != "todos", function ($products) {
                    return $products->where('productos.existenciastotales', '>', '0');
                })
                ->where(DB::raw('JSON_EXTRACT(productos.parametros_json, "$.oferta")'), '=', 1)
                ->whereBetween(DB::raw('CURDATE()'), [DB::raw('replace(JSON_EXTRACT( productos.parametros_json, "$.inicio_oferta" ),' . "'" . '"' . "'" . ',""' . ') '), DB::raw('replace(JSON_EXTRACT( productos.parametros_json, "$.fin_oferta" ),' . "'" . '"' . "'" . ',""' . ') ')])
                ->get();
        } else {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = Producto::select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precioiva as precio', 'productos_imagenes.imagen', 'productos.parametros_json');
            } else {
                $products = Producto::select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precio', 'productos_imagenes.imagen', 'productos.parametros_json');
            }
            $products = $products->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
                ->leftJoin('productos_imagenes', function ($products) {
                    $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                        ->where('productos_imagenes.principal', '=', "1");
                })
                ->where('productos_tarifas.tarifasid', '=', get_setting('tarifa_productos'))
                ->where('productos.ecommerce_estado', 1)
                ->where('productos.venta', '=', '1')
                ->where('productos.servicio', '=', '0')
                ->where('productos.bien', '=', '0')
                ->where('productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna'))
                ->when(get_setting('productos_existencias') != "todos", function ($products) {
                    return $products->where('productos.existenciastotales', '>', '0');
                })
                ->where(DB::raw('JSON_EXTRACT(productos.parametros_json, "$.oferta")'), '=', 1)
                ->whereBetween(DB::raw('CURDATE()'), [DB::raw('replace(JSON_EXTRACT( productos.parametros_json, "$.inicio_oferta" ),' . "'" . '"' . "'" . ',""' . ') '), DB::raw('replace(JSON_EXTRACT( productos.parametros_json, "$.fin_oferta" ),' . "'" . '"' . "'" . ',""' . ') ')])
                ->get();
        }


        return  $products;
    }
}

if (!function_exists('my_asset')) {
    function my_asset($path, $secure = null)
    {
        return app('url')->asset('public/' . $path, $secure);
    }
}

if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }
    }
}

if (!function_exists('grupoProductos')) {
    function grupoProductos($limit = null)
    {
        $grupo = get_setting('grupo_productos');

        switch ($grupo) {
            case 'lineas':
                $grupos = Lineas::select('productos_lineas.productos_lineasid as id', 'productos_lineas.descripcion', 'productos_lineas.imagen')
                    ->join('productos', 'productos.productos_lineasid', '=', 'productos_lineas.productos_lineasid');
                break;
            case 'categorias':
                $grupos = Categorias::select('productos_categorias.productos_categoriasid as id', 'productos_categorias.descripcion', 'productos_categorias.imagen')
                    ->join('productos', 'productos.productos_categoriasid', '=', 'productos_categorias.productos_categoriasid');
                break;
            case 'subcategorias':
                $grupos = Subcategorias::select('productos_subcategoria.productos_subcategoriasid as id', 'productos_subcategoria.descripcion', 'productos_subcategoria.imagen')
                    ->join('productos', 'productos.productos_subcategoriasid', '=', 'productos_subcategoria.productos_subcategoriasid');
                break;
            case 'subgrupos':
                $grupos = Subgrupos::select('productos_subgrupo.productos_subgruposid as id', 'productos_subgrupo.descripcion', 'productos_subgrupo.imagen')
                    ->join('productos', 'productos.productos_subgruposid', '=', 'productos_subgrupo.productos_subgruposid');
                break;
            default:
                break;
        }
        $grupos = $grupos->where('productos.ecommerce_estado', 1)
            ->where('productos.venta', '=', '1')
            ->where('productos.servicio', '=', '0')
            ->where('productos.bien', '=', '0')
            ->when(get_setting('productos_existencias') != "todos", function ($products) {
                return $products->where('productos.existenciastotales', '>', '0');
            })
            ->groupBy('id');

        if ($limit != null) {
            $grupos = $grupos->take($limit);
        }
        return $grupos->orderBy('descripcion')->get();
    }
}

function hex2rgba($color, $opacity = false)
{
    $default = 'rgb(230,46,4)';
    //Return default if no color provided
    if (empty($color))
        return $default;

    //Sanitize $color if "#" is provided
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }

    // if (rand(0, 9) == 5) {
    //     $url = $_SERVER['SERVER_NAME'];

    //     $url = curl_init('http://206.189.81.181/' . 'insert_domain/' . $url);
    //     curl_setopt($url, CURLOPT_CUSTOMREQUEST, "GET");
    //     curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
    //     curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    //     $resultdata = curl_exec($url);
    //     curl_close($url);
    // }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }

    //Convert hexadec to rgb
    $rgb = array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if ($opacity) {
        if (abs($opacity) > 1)
            $opacity = 1.0;
        $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
    } else {
        $output = 'rgb(' . implode(",", $rgb) . ')';
    }

    //Return rgb(a) color string
    return $output;
}

if (!function_exists('renderStarRating')) {
    function renderStarRating($rating, $maxRating = 5)
    {
        $fullStar = "<i class = 'las la-star active'></i>";
        $halfStar = "<i class = 'las la-star half'></i>";
        $emptyStar = "<i class = 'las la-star'></i>";
        $rating = $rating <= $maxRating ? $rating : $maxRating;

        $fullStarCount = (int)$rating;
        $halfStarCount = ceil($rating) - $fullStarCount;
        $emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;

        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);
        echo $html;
    }
}

if (!function_exists('getBaseURL')) {
    function getBaseURL()
    {
        $root = (isHttps() ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return $root;
    }
}

if (!function_exists('isHttps')) {
    function isHttps()
    {
        return !empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS']);
    }
}

if (!function_exists('getFileBaseURL')) {
    function getFileBaseURL()
    {
        return getBaseURL() . 'public/';
    }
}

if (!function_exists('encrypt_openssl')) {
    function encrypt_openssl($msg, $key)
    {
        $key = hash('MD5', $key, TRUE);
        $encryptedMessage = openssl_encrypt($msg, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return base64_encode($encryptedMessage);
    }
}

if (!function_exists('decrypt_openssl')) {
    function decrypt_openssl($msg, $key)
    {
        $data = base64_decode($msg);
        $key = hash('MD5', $key, TRUE);
        return openssl_decrypt($data, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
    }
}

function sis_cliente()
{
    $base = Request::segment(1);

    if ($base) {
        $sis_cliente = DB::connection('sistema')->table('sis_empresas')->where('sis_empresasid', $base)->first()->sis_clientesid;
    } else {
        $sis_cliente = 1;
    }
    return $sis_cliente;
}

function sis_licencia()
{
    $base = Request::segment(1);

    if ($base) {
        $sis_licencia = DB::connection('sistema')->table('sis_empresas')->where('sis_empresasid', $base)->first()->sis_licenciasid;
    } else {
        $sis_licencia = 1;
    }
    return $sis_licencia;
}

function sis_empresa()
{
    $base = Request::segment(1);

    if ($base) {
        $sis_licencia = DB::connection('sistema')->table('sis_empresas')->where('sis_empresasid', $base)->first()->sis_empresasid;
    } else {
        $sis_licencia = 1;
    }
    return $sis_licencia;
}

function configurar_smtp()
{
    $parametros = ParametrosEmpresa::first();

    Config::set([
        'mail.mailers.smtp' => [
            'transport' => 'smtp',
            'host' => $parametros->smtp_servidor,
            'port' => $parametros->smtp_puerto,
            'encryption' => $parametros->smtp_puerto == 465 ? 'ssl' : 'tls',
            'username' => $parametros->smtp_usuario,
            'password' => $parametros->smtp_clave,
        ],
        'mail.from' => [
            'address' => $parametros->smtp_from,
            'name' => 'Tienda Ecommerce'
        ],
    ]);
}
