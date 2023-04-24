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
use App\Models\ParametrosEmpresa;
use App\Models\Subcategorias;
use App\Models\Subgrupos;
use App\Models\Tarifas;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ConfiguracionesController extends Controller
{
    public function general()
    {
        $productos_existencias = get_setting('productos_existencias');
        $facturadores = Facturador::where('facturador', '=', 1)
            ->where('estado', '=', 1)
            ->get();
        $tarifas = Tarifas::all();
        $grupos = GrupoClientes::all();
        $almacenes = DB::connection('empresa')->table('almacenes')->where('estado', '1')->get();

        return view('backend.configuraciones-generales', compact('facturadores', 'productos_existencias', 'tarifas', 'grupos', 'almacenes'));
    }

    public function update_general(Request $request)
    {

        $business_settings = Integraciones::where('tipo', 5)->first();
        if ($business_settings == null) {
            return back();
        }
        $settings = json_decode($business_settings->parametros);
        $settings->grupo_productos = $request->get('grupo_productos');
        $settings->facturador = $request->get('facturador');
        $settings->productos_existencias = $request->get('productos_existencias');
        $settings->tarifa_productos = $request->get('tarifa_productos');
        $settings->registra_clientes = $request->get('registra_clientes');
        $settings->grupo_clientes = $request->get('grupo_clientes');
        $settings->tarifa_clientes = $request->get('tarifa_clientes');
        $settings->cantidad_maxima = $request->get('cantidad_maxima');
        $settings->pago_pedido = $request->get('pago_pedido');
        $settings->pago_plux = $request->get('pago_plux');
        $settings->email_pago_plux = $request->get('email_pago_plux');
        $settings->pedido_pago_plux = $request->get('pedido_pago_plux');
        $settings->productos_disponibles = $request->get('productos_disponibles');
        $settings->productos_no_disponibles = $request->get('productos_no_disponibles');
        $settings->ver_codigo = $request->get('ver_codigo');
        $settings->tipo_tienda = $request->get('tipo_tienda');
        $settings->controla_stock = $request->get('controla_stock');

        $tags = array();
        if ($request->get('email_pedidos')[0] != null) {
            foreach (json_decode($request->get('email_pedidos')[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }
        $settings->email_pedidos          = implode(',', $tags);

        if ($request->file('imagen_defecto')) {
            $settings->imagen_defecto = base64_encode(file_get_contents($request->file('imagen_defecto')));
        }

        Integraciones::where('tipo', 5)
            ->update(['parametros' => json_encode($settings)]);

        flash("Actualizado Correctamente")->success();
        return back();
    }

    public function header()
    {
        return view('backend.configuraciones-header');
    }

    public function update_header(Request $request)
    {

        $business_settings = Integraciones::where('tipo', 5)->first();
        $settings = json_decode($business_settings->parametros);
        $settings->header_stikcy = $request->get('header_stikcy');

        if ($request->file('header_logo')) {
            $settings->header_logo = base64_encode(file_get_contents($request->file('header_logo')));
            $imagen = $request->header_logo;
            move_uploaded_file($imagen->getRealPath(), public_path("assets/img/") . 'logo-' . sis_cliente() . '.png');
        }

        Integraciones::where('tipo', 5)
            ->update(['parametros' => json_encode($settings)]);

        flash("Actualizado Correctamente")->success();
        return back();
    }

    public function footer()
    {
        return view('backend.configuraciones-footer');
    }

    public function update_footer(Request $request)
    {

        $business_settings = Integraciones::where('tipo', 5)->first();

        $settings = json_decode($business_settings->parametros);

        $settings->acerca_nosotros = $request->get('acerca_nosotros');
        $settings->direccion_contacto = $request->get('direccion_contacto');
        $settings->telefono_contacto = $request->get('telefono_contacto');
        $settings->email_contacto = $request->get('email_contacto');
        $settings->show_social_links = $request->get('show_social_links');
        $settings->facebook_link = $request->get('facebook_link');
        $settings->twitter_link = $request->get('twitter_link');
        $settings->instagram_link = $request->get('instagram_link');

        if ($request->file('footer_logo')) {
            $settings->footer_logo = base64_encode(file_get_contents($request->file('footer_logo')));
        }

        Integraciones::where('tipo', 5)
            ->update(['parametros' => json_encode($settings)]);

        flash("Actualizado Correctamente")->success();
        return back();
    }

    public function apariencia()
    {
        return view('backend.configuraciones-apariencia');
    }

    public function update_apariencia(Request $request)
    {

        $business_settings = Integraciones::where('tipo', 5)->first();

        $settings = json_decode($business_settings->parametros);

        $settings->nombre_sitio = $request->get('nombre_sitio');
        $settings->lema_sitio = $request->get('lema_sitio');
        $settings->color_sitio = $request->get('color_sitio');
        $settings->color_hover_sitio = $request->get('color_hover_sitio');
        $settings->header_script = $request->get('header_script');
        $settings->footer_script = $request->get('footer_script');

        if ($request->file('icono_sitio')) {
            $settings->icono_sitio = base64_encode(file_get_contents($request->file('icono_sitio')));
        }
        Integraciones::where('tipo', 5)
            ->update(['parametros' => json_encode($settings)]);

        flash("Actualizado Correctamente")->success();
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
        $business_settings = Integraciones::where('tipo', 5)->first();

        $settings = json_decode($business_settings->parametros);
        $pagina = $request->get('pagina');
        $settings->$pagina = $request->get('contenido');

        Integraciones::where('tipo', 5)
            ->update(['parametros' => json_encode($settings)]);

        flash("Actualizado Correctamente")->success();
        return back();
    }

    public function update_inicio(Request $request)
    {
        $business_settings = Integraciones::where('tipo', 5)->first();

        $settings = json_decode($business_settings->parametros);
        $sliders = [];

        if (isset($request->links)) {
            foreach ($request->links as $key => $type) {
                if (isset($request->imagenes[$key])) {
                    $slider = [
                        "imagen" => base64_encode(file_get_contents($request->imagenes[$key])),
                        "link" => $request->links[$key],
                        "inicio" => $request->inicio[$key],
                        "fin" => $request->fin[$key]
                    ];
                } else {
                    $slider = [
                        "imagen" => $request->imagen[$key],
                        "link" => $request->links[$key],
                        "inicio" => $request->inicio[$key],
                        "fin" => $request->fin[$key]
                    ];
                }
                array_push($sliders, $slider);
            }
        }

        $settings->home_slider = json_encode($sliders);
        $settings->top10_categories = $request->get('top10_categories');
        Integraciones::where('tipo', 5)
            ->update(['parametros' => json_encode($settings)]);

        flash("Actualizado Correctamente")->success();
        return back();
    }

    public function analytics()
    {
        return view('backend.configuraciones-analytics');
    }

    public function update_analytics(Request $request)
    {

        $business_settings = Integraciones::where('tipo', 5)->first();

        $settings = json_decode($business_settings->parametros);

        $settings->facebook_pixel = $request->get('facebook_pixel');
        $settings->FACEBOOK_PIXEL_ID = $request->get('FACEBOOK_PIXEL_ID');

        $settings->google_analytics = $request->get('google_analytics');
        $settings->TRACKING_ID = $request->get('TRACKING_ID');

        Integraciones::where('tipo', 5)
            ->update(['parametros' => json_encode($settings)]);

        flash("Actualizado Correctamente")->success();
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

        $business_settings = Integraciones::where('tipo', 5)->first();

        $settings = json_decode($business_settings->parametros);
        $settings->login_google = $request->login_google;
        $settings->login_facebook = $request->login_facebook;

        Integraciones::where('tipo', 5)
            ->update(['parametros' => json_encode($settings)]);

        flash('Guardado Correctamente')->success();
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
        $parametros->save();

        flash('Guardado Correctamente')->success();
        return back();
    }

    public function testEmail(Request $request)
    {

        configurar_smtp();

        $array['view'] = 'emails.test';
        $array['subject'] = "SMTP Test";
        $array['from'] = Config::get('mail.from.address');
        $array['content'] = "Esto es un email de prueba.";

        try {
            Mail::to($request->email)->queue(new Test($array));
        } catch (\Exception $e) {
            flash('Error enviando email, revise los parametros')->error();
            return back();
        }

        flash('Email enviado correctamente')->success();
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
                //dd($address);
                $address->save();
            }
        }
        return 1;
    }
}
