<?php

namespace App\Http\Controllers;

use App\Mail\Registro;
use App\Models\Almacenes;
use App\Models\Carrito;
use App\Models\Categorias;
use App\Models\Clientes;
use App\Models\Comentarios;
use App\Models\Facturas;
use App\Models\Integraciones;
use App\Models\Lineas;
use App\Models\MovimientosInventariosAlmacenes;
use App\Models\ParametrosEmpresa;
use App\Models\Pedidos;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\ProductoTarifa;
use App\Models\Secuenciales;
use App\Models\Subcategorias;
use App\Models\Subgrupos;
use App\Models\User;
use App\Models\Usuarios;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables as DataTables;

use function GuzzleHttp\json_decode;

class HomeController extends Controller
{

    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.user_login');
    }

    public function process_login(Request $request)
    {

        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $cliente = Clientes::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->first();
        if ($request->almacenesid == 0 && get_setting('controla_stock') == 2) {
            flash("Seleccione Sucursal")->warning();
            return back();
        }

        if ($cliente != null) {
            $clave = json_decode($cliente->clave);
            $clave_cliente = encrypt_openssl($request->clave, "Perseo1232*");
            if ($clave->ecommerce == $clave_cliente) {
                if ($request->has('remember')) {
                    Auth::login($cliente, true);
                } else {
                    Auth::login($cliente, false);
                }
                session(['almacenesid' => $request->almacenesid]);

                $carrito = Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))->get();
                if (count($carrito) > 0) {
                    foreach ($carrito as $key => $carro) {
                        $precioProducto = ProductoTarifa::select('productos_tarifas.precio', 'productos_tarifas.precioiva')
                            ->join('productos', 'productos.productosid', '=', 'productos_tarifas.productosid')
                            ->where('productos_tarifas.productosid', $carro->productosid)
                            ->where('productos_tarifas.medidasid', '=', $carro->medidasid)
                            ->where('productos_tarifas.tarifasid', '=', auth()->user()->tarifasid)
                            ->first();

                        Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))
                            ->where('productosid', $carro->productosid)
                            ->where('medidasid', $carro->medidasid)
                            ->update(
                                [
                                    'precio' => $precioProducto->precio,
                                    'precioiva' => $precioProducto->precioiva,
                                    'descuento' => auth()->user()->descuento,
                                ]
                            );
                    }

                    Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))
                        ->update(
                            [
                                'clientesid' => auth()->user()->clientesid,
                                'usuario_temporalid' => null
                            ]
                        );

                    Session::forget('usuario_temporalid');

                    $carro = Carrito::where('clientesid', auth()->user()->clientesid)->get();


                    foreach ($carro as $key => $valor) {
                        $carrito = Carrito::select('ecommerce_carritosid')->where('clientesid', auth()->user()->clientesid)->where('almacenesid', $valor->almacenesid)->where('productosid', $valor->productosid)->where('medidasid', $valor->medidasid)->get();
                        if (count($carrito) > 1) {
                            $carritoCantidad = Carrito::select(DB::raw('SUM(cantidad) as cantidadtotal'))->where('clientesid', auth()->user()->clientesid)->where('almacenesid', $valor->almacenesid)->where('productosid', $valor->productosid)->where('medidasid', $valor->almacenesid)->get();
                            Carrito::where('productosid', $valor->productosid)->where('clientesid', $valor->clientesid)->where('almacenesid', $valor->almacenesid)->where('medidasid', $valor->medidasid)->where('productosid', $valor->productosid)
                                ->update(
                                    [
                                        'cantidad' => $carritoCantidad[0]['cantidadtotal']

                                    ]
                                );
                            foreach ($carrito as $key => $value) {
                                if ($key != 0) {

                                    $value->delete();
                                }
                            }
                            $carro = Carrito::where('clientesid', auth()->user()->clientesid)->get();
                        }
                    }
                }
            } else {
                flash("Identificacion o contraseña incorrecta")->warning();
            }
        } else {
            flash("Identificacion o contraseña incorrecta")->warning();
        }
        return back();
    }

    public function admin_login(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $usuario = Usuarios::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->where('sis_clientesid', sis_cliente())->first();

        if ($usuario != null) {

            $clave_usuario = encrypt_openssl($request->contrasena, "Perseo1232*" . sis_cliente());
            if ($usuario->contrasena == $clave_usuario) {
                $integraciones = Integraciones::where('tipo', 5)->first();
                if ($integraciones == null) {
                    $integracion = new Integraciones();
                    $integracion->descripcion = "Tienda Ecommerce";
                    $integracion->tipo = 5;
                    $parametros = [];
                    $topcategorias = [];
                    $productos = [
                        'rating' => 0,
                        'destacado' => 0,
                        'oferta' => 0,
                        'inicio_oferta' => null,
                        'fin_oferta' => null
                    ];
                    $lineas = [
                        'destacado' => 0
                    ];
                    $categorias = [
                        'destacado' => 0
                    ];
                    $subcategorias = [
                        'destacado' => 0
                    ];
                    $subgrupos = [
                        'destacado' => 0
                    ];
                    $parametros = [
                        'header_stikcy' => null,
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
                        'top10_categories' => $topcategorias,
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
                        'imagen_defecto' => base64_encode(file_get_contents(static_asset('assets/img/placeholder.jpg'))),
                        'login_google' => null,
                        'login_facebook' => null,
                        "pago_pedido" => '1',
                        "pago_plux" => '0',
                        "email_pago_plux" => null,
                        "pedido_pago_plux" => "pedido",
                        "productos_disponibles" => 'En Stock',
                        "productos_no_disponibles" => 'Bajo Pedido',
                        "ver_codigo" => 0,
                        "tipo_tienda" => 'publico',
                        "controla_stock" => 0

                    ];
                    $integracion->parametros = json_encode($parametros);
                    $integracion->save();

                    DB::connection('empresa')->table('productos')->update(array(
                        'parametros_json' => json_encode($productos)
                    ));

                    DB::connection('empresa')->table('productos_lineas')->update(array(
                        'parametros_json' => json_encode($lineas)
                    ));

                    DB::connection('empresa')->table('productos_categorias')->update(array(
                        'parametros_json' => json_encode($categorias)
                    ));

                    DB::connection('empresa')->table('productos_subcategoria')->update(array(
                        'parametros_json' => json_encode($subcategorias)
                    ));

                    DB::connection('empresa')->table('productos_subgrupo')->update(array(
                        'parametros_json' => json_encode($subgrupos)
                    ));
                } else {
                    if ($integraciones->parametros == null) {
                        $parametros = [];
                        $topcategorias = [];
                        $productos = [
                            'rating' => 0,
                            'destacado' => 0,
                            'oferta' => 0,
                            'inicio_oferta' => null,
                            'fin_oferta' => null
                        ];
                        $lineas = [
                            'destacado' => 0
                        ];
                        $categorias = [
                            'destacado' => 0
                        ];
                        $subcategorias = [
                            'destacado' => 0
                        ];
                        $subgrupos = [
                            'destacado' => 0
                        ];
                        $parametros = [
                            'header_stikcy' => null,
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
                            'top10_categories' => $topcategorias,
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
                            'imagen_defecto' => base64_encode(file_get_contents(static_asset('assets/img/placeholder.jpg'))),
                            'login_google' => null,
                            'login_facebook' => null,
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
                        $integraciones->parametros = json_encode($parametros);
                        $integraciones->save();

                        DB::connection('empresa')->table('productos')->update(array(
                            'parametros_json' => json_encode($productos)
                        ));

                        DB::connection('empresa')->table('productos_lineas')->update(array(
                            'parametros_json' => json_encode($lineas)
                        ));

                        DB::connection('empresa')->table('productos_categorias')->update(array(
                            'parametros_json' => json_encode($categorias)
                        ));

                        DB::connection('empresa')->table('productos_subcategoria')->update(array(
                            'parametros_json' => json_encode($subcategorias)
                        ));

                        DB::connection('empresa')->table('productos_subgrupo')->update(array(
                            'parametros_json' => json_encode($subgrupos)
                        ));
                    }
                }
                Auth::guard('admin')->login($usuario, false);
                return redirect()->route('admin.dashboard');
            }
        }
        flash("Identificacion o contraseña incorrecta")->error();
        return back();
    }

    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('frontend.user_registration');
    }

    public function createUser(Request $request)
    {

        $parametros = ParametrosEmpresa::first();
        $secuencial = Secuenciales::where('secuencial', 'CLIENTES')->first();
        Secuenciales::where('secuenciales.secuencial', 'CLIENTES')
            ->update(
                [
                    'valor' => $secuencial->valor + 1
                ]
            );

        $cliente = new Clientes;
        $cliente->identificacion = $request->identificacion;
        $cliente->razonsocial = $request->razonsocial;
        $cliente->email_login = $request->email;
        $cliente->telefono1 = $request->telefono1;
        $clave = [
            'ecommerce' => encrypt_openssl($request->password, "Perseo1232*"),
            'documentos' => null
        ];
        $cliente->clave = json_encode($clave);
        $cliente->fechacreacion = now();
        $cliente->usuariocreacion = "Ecommerce";
        $cliente->codigocontable = $parametros->codigocontable_clientes;
        $cliente->clientescodigo = $secuencial->prefijo . str_pad($secuencial->valor, $secuencial->numeroceros, "0", STR_PAD_LEFT);
        $cliente->estado = 1;
        $cliente->clientes_zonasid = 1;
        $cliente->cobradoresid = 3;
        $cliente->vendedoresid = 3;
        $cliente->provinciasid = $parametros->provinciasid;
        $cliente->ciudadesid = $parametros->ciudadesid;
        $cliente->parroquiasid = $parametros->parroquiasid;
        $cliente->tipoidentificacion = strlen($request->identificacion) == 10 ? 'C' : 'R';
        $cliente->tarifasid = get_setting('tarifa_clientes');
        $cliente->clientes_gruposid = get_setting('grupo_clientes');

        if ($cliente->save()) {
            if (session('usuario_temporalid') != null) {
                Carrito::where('usuario_temporalid', session('usuario_temporalid'))
                    ->update([
                        'clientesid' => $cliente->clientesid,
                        'usuario_temporalid' => null
                    ]);

                Session::forget('usuario_temporalid');
            }
            return $cliente;
        }
    }

    public function register(Request $request)
    {

        $user = $this->createUser($request);
        Auth::login($user, false);

        configurar_smtp();
        $array['view'] = 'emails.registro';
        $array['from'] = Config::get('mail.from.address');
        $array['subject'] = 'Registro';
        $array['identificacion'] = $user->identificacion;
        $array['telefono'] = $user->telefono1;
        $array['razonsocial'] = $user->razonsocial;

        if (get_setting('controla_stock') == 2) {
            $almacenes = DB::connection('empresa')->table('facturadores_almacenes')
                ->where('facturadoresid', get_setting('facturador'))
                ->where('principal', '1')
                ->first();

            session(['almacenesid' => $almacenes->almacenesid]);
        }

        Mail::to($user->email_login)->queue(new Registro($array));
        flash('Registrado Correctamente')->success();
        return redirect()->route("home");
    }

    public function reset_password_with_code(Request $request)
    {
        if (($user = User::where('codigo_verificacion', $request->code)->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $clave = json_decode($user->clave);
                $clave->ecommerce = encrypt_openssl($request->password, "Perseo1232*");
                $user->clave = json_encode($clave);
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);
                request()->session()->regenerate();
                flash('Contraseña Actualizada Correctamente')->success();
                return redirect()->route('home');
            } else {
                flash("La contraseña y la contraseña de confirmación no coinciden ")->warning();
                return redirect()->route('password.request');
            }
        } else {
            flash("No existe codigo de verificacion")->error();
            return redirect()->route('password.request');
        }
    }

    public function validacion(Request $request)
    {
        $identificacionIngresada = substr($request->cedula, 0, 10);
        if (isset($request->cedula)) {
            $persona = Clientes::where(DB::raw('substr(identificacion,1,10)'), $identificacionIngresada)->get();
        }
        if (isset($request->email)) {
            $persona = Clientes::where('email_login', $request->email)->get();
        }

        return $persona;
    }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('frontend.cliente.dashboard');
        } else {
            abort(404);
        }
    }

    public function profile()
    {
        return view('frontend.cliente.profile');
    }

    public function update_profile(Request $request)
    {
        $cliente = Clientes::findOrFail(Auth::user()->clientesid);
        $cliente->identificacion = $request->identificacion;
        $cliente->tipoidentificacion = strlen($request->identificacion) == 10 ? 'C' : 'R';
        $cliente->razonsocial = $request->razonsocial;
        $cliente->email_login = $request->email;
        $cliente->telefono1 = $request->telefono1;
        $cliente->telefono2 = $request->telefono2;
        $cliente->telefono3 = $request->telefono3;
        $cliente->usuariomodificacion = 'Ecommerce';
        $cliente->fechamodificacion = now();

        if ($request->new_password != null) {
            if ($request->new_password == $request->confirm_password) {
                $clave = json_decode($cliente->clave);
                $clave->ecommerce = encrypt_openssl($request->new_password, "Perseo1232*");
                $cliente->clave = json_encode($clave);
            } else {
                flash('Las contraseñas no coinciden')->error();
                return back();
            }
        }

        if ($cliente->save()) {
            flash('Perfil Actualizado Correctamente')->success();
            return back();
        }

        flash('Algo ha salido mal')->error();
        return back();
    }

    public function admin_dashboard(Request $request)
    {
        $fecha = $request->fecha;

        $totalPedidos = Pedidos::where('pedidos.usuariocreacion', 'Ecommerce');
        $totalFacturas = Facturas::where('facturas.usuariocreacion', 'Ecommerce');
        $totalPedidosRealizados = Pedidos::where('pedidos.usuariocreacion', 'Ecommerce')
            ->where('pedidos.estado', 1);
        $totalPedidosConfirmados = Pedidos::where('pedidos.usuariocreacion', 'Ecommerce')
            ->where('pedidos.estado', 2);
        $totalPedidosFacturados = Pedidos::where('pedidos.usuariocreacion', 'Ecommerce')
            ->where('pedidos.estado', 3);
        $totalPedidosEnLaEntrega = Pedidos::where('pedidos.usuariocreacion', 'Ecommerce')
            ->where('pedidos.estado', 4);
        $totalPedidosEntregados = Pedidos::where('pedidos.usuariocreacion', 'Ecommerce')
            ->where('pedidos.estado', 5);


        if ($fecha != null) {
            $totalPedidos = $totalPedidos->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])))->count();

            $totalFacturas = $totalFacturas->whereDate('facturas.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('facturas.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])))->count();

            $totalPedidosRealizados = $totalPedidosRealizados->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])))->count();

            $totalPedidosConfirmados = $totalPedidosConfirmados->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])))->count();

            $totalPedidosFacturados = $totalPedidosFacturados->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])))->count();

            $totalPedidosEnLaEntrega = $totalPedidosEnLaEntrega->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])))->count();

            $totalPedidosEntregados = $totalPedidosEntregados->whereDate('pedidos.emision', '>=', date('Y-m-d', strtotime(explode(" a ", $fecha)[0])))
                ->whereDate('pedidos.emision', '<=', date('Y-m-d', strtotime(explode(" a ", $fecha)[1])))->count();
        }

        return view('backend.dashboard', compact('totalPedidos', 'fecha', 'totalPedidosRealizados', 'totalFacturas', 'totalPedidosConfirmados', 'totalPedidosFacturados', 'totalPedidosEnLaEntrega', 'totalPedidosEntregados'));
    }

    public function index()
    {
        return view('frontend.index');
    }

    public function load_featured_section()
    {
        //cargar seccion de productos destacados
        $parametros = ParametrosEmpresa::first();
        if (Auth::check()) {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precioiva as precio2', 'productos_imagenes.imagen', 'parametros_json', DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            } else {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precio as precio2', 'productos_imagenes.imagen', 'parametros_json', DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            }
        } else {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precioiva as precio', 'productos_imagenes.imagen', 'parametros_json');
            } else {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precio', 'productos_imagenes.imagen', 'parametros_json');
            }
        }

        $products = $products->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
            ->leftJoin('productos_imagenes', function ($products) {
                $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                    ->where('productos_imagenes.principal', '=', "1");
            })->when(get_setting('productos_existencias') != "todos", function ($products) {
                return $products->where('productos.existenciastotales', '>', '0');
            })
            ->whereIn('productos.ecommerce_estado', array(1, 2))
            ->where([
                ['productos_tarifas.tarifasid', '=', get_setting('tarifa_productos')],
                ['productos.venta', '=', '1'],
                ['productos.servicio', '=', '0'],
                ['productos.bien', '=', '0'],
                ['productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna')],
                ['parametros_json->destacado', 1]
            ])
            ->limit(12)
            ->get();

        return view('frontend.partials.featured_products_section', compact('products'));
    }

    public function load_best_selling_section()
    {
        $parametros = ParametrosEmpresa::first();
        if (Auth::check()) {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = DB::connection('empresa')->table('facturas_detalles')
                    ->select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precioiva as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("SUM(CASE WHEN ( facturas.sri_documentoscodigo = '04' ) THEN ( facturas_detalles.cantidad * -( 1 ) ) ELSE		facturas_detalles.cantidad END ) AS total_cantidad"), DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            } else {
                $products = DB::connection('empresa')->table('facturas_detalles')
                    ->select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precio as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("SUM(CASE WHEN ( facturas.sri_documentoscodigo = '04' ) THEN ( facturas_detalles.cantidad * -( 1 ) ) ELSE		facturas_detalles.cantidad END ) AS total_cantidad"), DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            }
            $products = $products->join('productos', 'productos.productosid', '=', 'facturas_detalles.productosid')
                ->join('facturas', 'facturas_detalles.facturasid', '=', 'facturas.facturasid')
                ->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
                ->leftJoin('productos_imagenes', function ($products) {
                    $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                        ->where('productos_imagenes.principal', '=', "1");
                })
                ->whereIn('productos.ecommerce_estado', array(1, 2))
                ->when(get_setting('productos_existencias') != "todos", function ($products) {
                    return $products->where('productos.existenciastotales', '>', '0');
                })
                ->where([
                    ['productos_tarifas.tarifasid', '=', get_setting('tarifa_productos')],
                    ['productos.venta', '=', '1'],
                    ['productos.servicio', '=', '0'],
                    ['productos.bien', '=', '0'],
                    ['productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna')],
                ])
                ->groupBy('productos.productosid')
                ->orderBy('total_cantidad', 'desc')
                ->take(10)
                ->get();
        } else {

            if ($parametros->tipopresentacionprecios == 1) {
                $products = DB::connection('empresa')->table('facturas_detalles')
                    ->select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precioiva as precio', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("SUM(CASE WHEN ( facturas.sri_documentoscodigo = '04' ) THEN ( facturas_detalles.cantidad * -( 1 ) ) ELSE		facturas_detalles.cantidad END ) AS total_cantidad"));
            } else {
                $products = DB::connection('empresa')->table('facturas_detalles')
                    ->select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precio', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("SUM(CASE WHEN ( facturas.sri_documentoscodigo = '04' ) THEN ( facturas_detalles.cantidad * -( 1 ) ) ELSE		facturas_detalles.cantidad END ) AS total_cantidad"));
            }
            $products = $products->join('productos', 'productos.productosid', '=', 'facturas_detalles.productosid')
                ->join('facturas', 'facturas_detalles.facturasid', '=', 'facturas.facturasid')
                ->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
                ->leftJoin('productos_imagenes', function ($products) {
                    $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                        ->where('productos_imagenes.principal', '=', "1");
                })
                ->whereIn('productos.ecommerce_estado', array(1, 2))
                ->when(get_setting('productos_existencias') != "todos", function ($products) {
                    return $products->where('productos.existenciastotales', '>', '0');
                })
                ->where([
                    ['productos_tarifas.tarifasid', '=', get_setting('tarifa_productos')],
                    ['productos.venta', '=', '1'],
                    ['productos.servicio', '=', '0'],
                    ['productos.bien', '=', '0'],
                    ['productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna')],
                ])
                ->groupBy('productos.productosid')
                ->orderBy('total_cantidad', 'desc')
                ->take(10)
                ->get();
            // Cache::put('top', $products, 1440);
        }

        return view('frontend.partials.best_selling_section', compact('products'));
    }

    public function all_categories()
    {
        return view('frontend.all_category');
    }

    public function product($productosid)
    {
        //presentar un producto
        $parametros = ParametrosEmpresa::first();
        $detallesProducto = Producto::where('productosid', $productosid)->first();
        $precioProducto2 = "";
        if ($parametros->tipopresentacionprecios == 1) {
            $precioProducto = ProductoTarifa::select('productos_tarifas.precioiva as precio', 'productos_tarifas.factor');
        } else {
            $precioProducto = ProductoTarifa::select('productos_tarifas.precio', 'productos_tarifas.factor');
        }
        $precioProducto = $precioProducto->join('productos', 'productos.productosid', '=', 'productos_tarifas.productosid')
            ->where([
                ['productos_tarifas.productosid', $productosid],
                ['productos_tarifas.tarifasid', '=', get_setting('tarifa_productos')],
                ['productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna')]
            ])
            ->first();

        $imagenProducto =  ProductoImagen::select('productos_imagenes.imagen', 'productos_imagenes.medidasid')
            ->where('productos_imagenes.productosid', '=', $productosid)
            ->where('productos_imagenes.ecommerce_visible', '=', '1')
            ->orderBy('productos_imagenes.medidasid')
            ->get();

        $comentarios = Comentarios::select('ecommerce_comentarios.comentario', 'ecommerce_comentarios.valoracion', 'ecommerce_comentarios.fechacreacion', 'clientes.razonsocial')
            ->join('clientes', 'clientes.clientesid', '=', 'ecommerce_comentarios.clientesid')
            ->where('ecommerce_comentarios.productosid', $productosid)
            ->where('ecommerce_comentarios.estado', 1)
            ->get();

        $numerocomentarios = Comentarios::where('ecommerce_comentarios.productosid', $productosid)
            ->where('ecommerce_comentarios.estado', 1)
            ->count();

        $medidas = ProductoTarifa::select('medidas.descripcion', 'medidas.medidasid', 'productos_tarifas.factor')
            ->join('medidas', 'medidas.medidasid', '=', 'productos_tarifas.medidasid')
            ->where('productos_tarifas.productosid', '=', $productosid)
            ->groupBy('medidas.descripcion', 'medidas.medidasid', 'productos_tarifas.factor')
            ->orderBy('medidas.medidasid')
            ->get();


        $grupo = get_setting('grupo_productos');
        $grupoid = getGrupoID();

        switch ($grupo) {
            case 'lineas':
                $id = Lineas::where($grupoid, $detallesProducto->productos_lineasid)->first()->productos_lineasid;
                break;
            case 'categorias':
                $id = Categorias::where($grupoid, $detallesProducto->productos_categoriasid)->first()->productos_categoriasid;
                break;
            case 'subcategorias':
                $id = Subcategorias::where($grupoid, $detallesProducto->productos_subcategoriasid)->first()->productos_subcategoriasid;
                break;
            case 'subgrupos':
                $id = Subgrupos::where($grupoid, $detallesProducto->productos_subgruposid)->first()->productos_subgruposid;
                break;
            default:
                break;
        }

        if (Auth::check()) {
            if ($parametros->tipopresentacionprecios == 1) {
                $precioProducto2 = ProductoTarifa::select('productos_tarifas.precioiva as precio', 'productos_tarifas.factor');
                $relacionados = Producto::select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precioiva as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
                $top = DB::connection('empresa')->table('facturas_detalles')
                    ->select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precioiva as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("SUM(CASE WHEN ( facturas.sri_documentoscodigo = '04' ) THEN ( facturas_detalles.cantidad * -( 1 ) ) ELSE		facturas_detalles.cantidad END ) AS total_cantidad"), DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            } else {
                $precioProducto2 = ProductoTarifa::select('productos_tarifas.precio', 'productos_tarifas.factor');
                $relacionados = Producto::select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precio as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
                $top = DB::connection('empresa')->table('facturas_detalles')
                    ->select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precio as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("SUM(CASE WHEN ( facturas.sri_documentoscodigo = '04' ) THEN ( facturas_detalles.cantidad * -( 1 ) ) ELSE		facturas_detalles.cantidad END ) AS total_cantidad"), DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            }

            $precioProducto2 = $precioProducto2->join('productos', 'productos.productosid', '=', 'productos_tarifas.productosid')
                ->where([
                    ['productos_tarifas.productosid', $productosid],
                    ['productos_tarifas.tarifasid', '=', auth()->user()->tarifasid],
                    ['productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna')]
                ])
                ->first();
        } else {
            if ($parametros->tipopresentacionprecios == 1) {
                $relacionados = Producto::select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precioiva as precio', 'productos_imagenes.imagen', 'productos.parametros_json');
                $top = DB::connection('empresa')->table('facturas_detalles')
                    ->select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precioiva as precio', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("SUM(CASE WHEN ( facturas.sri_documentoscodigo = '04' ) THEN ( facturas_detalles.cantidad * -( 1 ) ) ELSE		facturas_detalles.cantidad END ) AS total_cantidad"));
            } else {
                $relacionados = Producto::select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precio', 'productos_imagenes.imagen', 'productos.parametros_json');
                $top = DB::connection('empresa')->table('facturas_detalles')
                    ->select('productos.productosid', 'productos.descripcion', 'productos_tarifas.precio', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("SUM(CASE WHEN ( facturas.sri_documentoscodigo = '04' ) THEN ( facturas_detalles.cantidad * -( 1 ) ) ELSE		facturas_detalles.cantidad END ) AS total_cantidad"));
            }
        }

        $relacionados = $relacionados->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
            ->leftJoin('productos_imagenes', function ($products) {
                $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                    ->where('productos_imagenes.principal', '=', "1");
            })
            ->when(get_setting('productos_existencias') != "todos", function ($products) {
                return $products->where('productos.existenciastotales', '>', '0');
            })
            ->whereIn('productos.ecommerce_estado', array(1, 2))
            ->where([
                ['productos.' . $grupoid, '=', $id],
                ['productos_tarifas.tarifasid', '=', get_setting('tarifa_productos')],
                ['productos.venta', '=', '1'],
                ['productos.servicio', '=', '0'],
                ['productos.bien', '=', '0'],
                ['productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna')]
            ])
            ->orderByRaw('RAND()')
            ->paginate(10);

        $top = $top->join('productos', 'productos.productosid', '=', 'facturas_detalles.productosid')
            ->join('facturas', 'facturas_detalles.facturasid', '=', 'facturas.facturasid')
            ->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
            ->leftJoin('productos_imagenes', function ($products) {
                $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                    ->where('productos_imagenes.principal', '=', "1");
            })
            ->whereIn('productos.ecommerce_estado', array(1, 2))
            ->when(get_setting('productos_existencias') != "todos", function ($products) {
                return $products->where('productos.existenciastotales', '>', '0');
            })
            ->where([
                ['productos.' . $grupoid, '=', $id],
                ['productos_tarifas.tarifasid', '=', get_setting('tarifa_productos')],
                ['productos.venta', '=', '1'],
                ['productos.servicio', '=', '0'],
                ['productos.bien', '=', '0'],
                ['productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna')],
                ['productos_imagenes.principal', '=', '1'],
            ])
            ->groupBy('productos.productosid')
            ->orderBy('total_cantidad', 'desc')
            ->take(4)
            ->get();
        $min_qty = 1;

        return view('frontend.product_details', compact('detallesProducto', 'precioProducto', 'precioProducto2', 'imagenProducto', 'min_qty', 'comentarios', 'medidas', 'numerocomentarios', 'relacionados', 'top'));
    }

    public function terminos_condiciones()
    {
        return view("frontend.policies.terminos_condiciones");
    }

    public function politicas_privacidad()
    {
        return view("frontend.policies.politicas_privacidad");
    }

    public function politicas_devoluciones()
    {
        return view("frontend.policies.politicas_devoluciones");
    }

    public function politicas_soporte()
    {
        return view("frontend.policies.politicas_soporte");
    }

    public function search(Request $request, $id = null)
    {
        $parametros = ParametrosEmpresa::first();
        $query = $request->q;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $descripcion = "";
        $grupo = get_setting('grupo_productos');
        $grupoid = getGrupoID();
        $conditions = [];

        switch ($grupo) {
            case 'lineas':
                if ($id != null) $descripcion = Lineas::where($grupoid, $id)->first()->descripcion;
                break;
            case 'categorias':
                if ($id != null) $descripcion = Categorias::where($grupoid, $id)->first()->descripcion;
                break;
            case 'subcategorias':
                if ($id != null) $descripcion = Subcategorias::where($grupoid, $id)->first()->descripcion;
                break;
            case 'subgrupos':
                if ($id != null) $descripcion = Subgrupos::where($grupoid, $id)->first()->descripcion;
                break;
            default:
                break;
        }

        if (Auth::check()) {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precioiva as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            } else {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precio as precio2', 'productos_imagenes.imagen', 'productos.parametros_json', DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            }
        } else {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precioiva as precio', 'productos_imagenes.imagen', 'productos.parametros_json');
            } else {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precio', 'productos_imagenes.imagen', 'productos.parametros_json');
            }
        }

        $products = $products->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
            ->leftJoin('productos_imagenes', function ($products) {
                $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                    ->where('productos_imagenes.principal', '=', "1");
            })
            ->whereIn('productos.ecommerce_estado', array(1, 2))
            ->when(get_setting('productos_existencias') != "todos", function ($products) {
                return $products->where('productos.existenciastotales', '>', '0');
            })
            ->where([
                ['productos_tarifas.tarifasid', '=', get_setting('tarifa_productos')],
                ['productos.venta', '=', '1'],
                ['productos.servicio', '=', '0'],
                ['productos.bien', '=', '0'],
                ['productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna')]
            ]);

        if ($id != null) {
            $conditions = array_merge($conditions, ['productos.' . $grupoid => $id]);
        }
        $products = $products->where($conditions);
        $preciomaximo = $products->max('precio');
        $preciominimo = $products->min('precio');

        if ($min_price != null && $max_price != null) {
            $products = $products->where('productos_tarifas.precio', '>=', $min_price)->where('productos_tarifas.precio', '<=', $max_price);
        }
        $keywords = explode(" ", $query);
        if ($keywords != null) {
            $products->where(function ($query) use ($keywords) {
                foreach ($keywords as $key => $keyword) {
                    $query->where('productos.descripcion', 'like', '%' . $keyword . '%');
                }
                $query->orWhere('productos.productocodigo', 'like', '%' . $keyword . '%');
            });
        }

        $products = $products->orderBy('productos.descripcion', 'asc')->paginate(12);
        return view('frontend.product_listing', compact('products', 'query', 'id', 'min_price', 'max_price', 'preciomaximo', 'preciominimo', 'grupoid', 'descripcion'));
    }

    public function ajax_search(Request $request)
    {
        $parametros = ParametrosEmpresa::first();
        $keywords = explode(" ", $request->search);

        if (Auth::check()) {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precioiva as precio2', 'productos_imagenes.imagen', DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            } else {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precio as precio2', 'productos_imagenes.imagen', DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = productos.productosid AND tarifain.medidasid = productos.unidadinterna) AS precio"));
            }
        } else {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precioiva as precio', 'productos_imagenes.imagen');
            } else {
                $products = Producto::select('productos.productosid', 'productos.productocodigo', 'productos.descripcion', 'productos_tarifas.precio', 'productos_imagenes.imagen');
            }
        }
        $products = $products->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
            ->leftJoin('productos_imagenes', function ($products) {
                $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                    ->where('productos_imagenes.principal', '=', "1");
            })
            ->where('productos_tarifas.tarifasid', '=', get_setting('tarifa_productos'))
            ->whereIn('productos.ecommerce_estado', array(1, 2))
            ->where('productos.venta', '=', '1')
            ->where('productos.servicio', '=', '0')
            ->where('productos.bien', '=', '0')
            ->where('productos_tarifas.medidasid', '=', DB::raw('productos.unidadinterna'))
            ->when(get_setting('productos_existencias') != "todos", function ($products) {
                return $products->where('productos.existenciastotales', '>', '0');
            });

        $products->where(function ($query) use ($keywords) {
            foreach ($keywords as $key => $keyword) {
                $query->where('productos.descripcion', 'like', '%' . $keyword . '%');
            }
            $query->orWhere('productos.productocodigo', 'like', '%' . $keyword . '%');
        });

        $products = $products->get()->take(5);

        $grupo = get_setting('grupo_productos');

        switch ($grupo) {
            case 'lineas':
                $categories = Lineas::select('productos_lineas.productos_lineasid as id', 'productos_lineas.descripcion')
                    ->join('productos', 'productos.productos_lineasid', '=', 'productos_lineas.productos_lineasid')
                    ->where('productos_lineas.descripcion', 'like', '%' . $request->search . '%');
                break;
            case 'categorias':
                $categories = Categorias::select('productos_categorias.productos_categoriasid as id', 'productos_categorias.descripcion')
                    ->join('productos', 'productos.productos_categoriasid', '=', 'productos_categorias.productos_categoriasid')
                    ->where('productos_categorias.descripcion', 'like', '%' . $request->search . '%');
                break;
            case 'subcategorias':
                $categories = Subcategorias::select('productos_subcategoria.productos_subcategoriasid as id', 'productos_subcategoria.descripcion')
                    ->join('productos', 'productos.productos_subcategoriasid', '=', 'productos_subcategoria.productos_subcategoriasid')
                    ->where('productos_subcategoria.descripcion', 'like', '%' . $request->search . '%');
                break;
            case 'subgrupos':
                $categories = Subgrupos::select('productos_subgrupo.productos_subgruposid as id', 'productos_subgrupo.descripcion')
                    ->join('productos', 'productos.productos_subgruposid', '=', 'productos_subgrupo.productos_subgruposid')
                    ->where('productos_subgrupo.descripcion', 'like', '%' . $request->search . '%');
                break;
            default:
                break;
        }
        $categories = $categories->whereIn('productos.ecommerce_estado', array(1, 2))
            ->where('productos.venta', '=', '1')
            ->where('productos.servicio', '=', '0')
            ->where('productos.bien', '=', '0')
            ->when(get_setting('productos_existencias') != "todos", function ($products) {
                return $products->where('productos.existenciastotales', '>', '0');
            })
            ->groupBy('id')
            ->get()
            ->take(5);

        if (sizeof($categories) > 0 || sizeof($products) > 0) {
            return view('frontend.partials.search_content', compact('products', 'categories'));
        }
        return 0;
    }

    public function listingByCategory(Request $request, $id)
    {
        $grupo = get_setting('grupo_productos');

        switch ($grupo) {
            case 'lineas':
                $category = Lineas::where('productos_lineasid', $id)->first();
                break;
            case 'categorias':
                $category = Categorias::where('productos_categoriasid', $id)->first();
                break;
            case 'subcategorias':
                $category = Subcategorias::where('productos_subcategoriasid', $id)->first();
                break;
            case 'subgrupos':
                $category = Subgrupos::where('productos_subgruposid', $id)->first();
                break;
            default:
                break;
        }

        if ($category != null) {
            return $this->search($request, $id);
        }
        abort(404);
    }

    public function variant_price(Request $request)
    {
        if (get_setting('controla_stock') == 1) {
            $cantidad = Producto::select('existenciastotales')->where('productosid', $request->id)->first();
            $cantidadFinal = $cantidad->existenciastotales;
        } else {
            $cantidadFinal = 0;
        }

        $parametros = ParametrosEmpresa::first();
        $producto = ProductoTarifa::select('productos_tarifas.precio', 'productos_tarifas.precioiva', 'productos_tarifas.factor');

        if (Auth::check()) {
            $producto = $producto->where('productos_tarifas.productosid', '=', $request->id)
                ->where('productos_tarifas.tarifasid', '=', auth()->user()->tarifasid)
                ->where('productos_tarifas.medidasid', '=', $request->medidasid)
                ->first();
            if (get_setting('controla_stock') == 2) {
                if ($request->cantidadCambio == null) {
                    $cantidad = MovimientosInventariosAlmacenes::where('productosid', $request->id)
                        ->where('almacenesid', $request->variableinicio)
                        ->first();
                }
                if ($cantidad) {
                    $cantidadFinal = $cantidad->existencias;
                } else {
                    $cantidadFinal  = 0;
                }
            }

            $producto2 = ProductoTarifa::select('productos_tarifas.precio', 'productos_tarifas.precioiva', 'productos_tarifas.factor')
                ->where('productos_tarifas.productosid', '=', $request->id)
                ->where('productos_tarifas.tarifasid', '=', get_setting('tarifa_productos'))
                ->where('productos_tarifas.medidasid', '=', $request->medidasid)
                ->first();
            $precio1 = $parametros->tipopresentacionprecios == 1 ? $producto->precioiva : $producto->precio;
            $precio2 = $parametros->tipopresentacionprecios == 1 ? $producto2->precioiva : $producto2->precio;
            return array('precio' => $producto->precio, 'precionormal' => $precio2, 'medidasid' => $request->medidasid, 'total' => $precio1 * $request->quantity, 'precioiva' => $producto->precioiva, 'factor' => $producto->factor, 'cantidad' => $cantidadFinal);
        } else {

            $producto = $producto->where('productos_tarifas.productosid', '=', $request->id)
                ->where('productos_tarifas.tarifasid', '=', get_setting('tarifa_productos'))
                ->where('productos_tarifas.medidasid', '=', $request->medidasid)
                ->first();

            $precio1 = $parametros->tipopresentacionprecios == 1 ? $producto->precioiva : $producto->precio;

            return array('precio' => $producto->precio, 'medidasid' => $request->medidasid, 'total' => $precio1 * $request->quantity, 'precioiva' => $producto->precioiva, 'factor' => $producto->factor, 'cantidad' => $cantidadFinal);
        }
    }

    public function recuperarPost(Request $request)
    {
        $cliente = new Client();
        $res = $cliente->request(
            'POST',
            'https://www.perseo.app/api/datos/datos_consulta',
            [
                'verify' => false,
                'headers' => ['Content-Type' => 'application/json; charset=UTF-8', "Usuario" => "perseo", "Clave" => "Perseo1232*"],
                'json' => ['identificacion' => $request->cedula]
            ]
        );
        $resultado = json_decode($res->getBody()->getContents());
        $estado = $res->getStatusCode();
        return response()->json($resultado);
    }

    public function validarIdentificacion(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $usuario = User::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->first();

        if ($usuario != null) {
            User::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->update(
                [
                    'email_login' => $request->email
                ]
            );
            auth()->login($usuario, true);
            request()->session()->regenerate();
            $clienteid = $usuario->clientesid;
            $tarifaid = $usuario->tarifasid;
        } else {
            //  crear nuevo usuario
            $parametros = ParametrosEmpresa::first();
            $secuencial = Secuenciales::where('secuencial', 'CLIENTES')->first();
            Secuenciales::where('secuenciales.secuencial', 'CLIENTES')
                ->update(
                    [
                        'valor' => $secuencial->valor + 1
                    ]
                );

            $cliente = new Clientes;
            $cliente->identificacion = $request->identificacion;
            $cliente->tipoidentificacion = strlen($request->identificacion) == 10 ? 'C' : 'R';
            $cliente->razonsocial = $request->nombre;
            $cliente->email_login = $request->email;
            $cliente->fechacreacion = now();
            $cliente->usuariocreacion = "Ecommerce";
            $cliente->codigocontable = $parametros->codigocontable_clientes;
            $cliente->clientescodigo = $secuencial->prefijo . str_pad($secuencial->valor, $secuencial->numeroceros, "0", STR_PAD_LEFT);
            $cliente->estado = 1;
            $cliente->descuento = 0;
            $cliente->clientes_zonasid = 1;
            $cliente->cobradoresid = 3;
            $cliente->vendedoresid = 3;
            $cliente->provinciasid = $parametros->provinciasid;
            $cliente->ciudadesid = $parametros->ciudadesid;
            $cliente->parroquiasid = $parametros->parroquiasid;
            $cliente->tarifasid = get_setting('tarifa_clientes');
            $cliente->clientes_gruposid = get_setting('grupo_clientes');
            $cliente->save();

            configurar_smtp();

            $array['view'] = 'emails.registro';
            $array['from'] = Config::get('mail.from.address');
            $array['subject'] = 'Registro';
            $array['identificacion'] = $cliente->identificacion;
            $array['telefono'] = $cliente->telefono1;
            $array['razonsocial'] = $cliente->razonsocial;

            try {
                Mail::to($cliente->email_login)->queue(new Registro($array));
            } catch (\Exception $e) {
                flash('Error enviando email')->error();
            }

            auth()->login($cliente, true);
            request()->session()->regenerate();
            $clienteid = $cliente->clientesid;
            $tarifaid = $cliente->tarifasid;
        }

        $carrito = Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))->get();
        if (count($carrito) > 0) {
            //dd(auth()->user());
            foreach ($carrito as $key => $carro) {
                $precioProducto = ProductoTarifa::select('productos_tarifas.precio', 'productos_tarifas.precioiva')
                    ->join('productos', 'productos.productosid', '=', 'productos_tarifas.productosid')
                    ->where('productos_tarifas.productosid', $carro->productosid)
                    ->where('productos_tarifas.medidasid', '=', $carro->medidasid)
                    ->where('productos_tarifas.tarifasid', '=', $tarifaid)
                    ->first();

                Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))
                    ->where('productosid', $carro->productosid)
                    ->where('medidasid', $carro->medidasid)
                    ->update(
                        [
                            'precio' => $precioProducto->precio,
                            'precioiva' => $precioProducto->precioiva,
                            'descuento' => auth()->user()->descuento,
                        ]
                    );
            }

            Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))
                ->update(
                    [
                        'clientesid' => $clienteid,
                        'usuario_temporalid' => null
                    ]
                );

            Session::forget('usuario_temporalid');
        }
        if (session('ruta') == 'login') {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('cart');
        }
    }

    public function estado_cartera()
    {
        $documentos = DB::connection('empresa')->table('cuentasporcobrar')
            ->select('cuentasporcobrar.documentosid AS documentoid', 'cuentasporcobrar.secuencial AS secuencia', DB::raw('max(cuentasporcobrar.importe) AS valor'), DB::raw('SUM(cuentasporcobrar.importe) AS saldo'), DB::raw('CASE WHEN MAX(cuentasporcobrar.importe)=0 THEN cuentasporcobrar.emision ELSE cuentasporcobrar.emision END AS emision'), DB::raw('CASE WHEN MAX(cuentasporcobrar.importe)=0 THEN cuentasporcobrar.vence ELSE cuentasporcobrar.vence END AS vence'), DB::raw('case WHEN DATEDIFF(now(),cuentasporcobrar.vence) > 0 then DATEDIFF(now(),cuentasporcobrar.vence) ELSE 0 END as diasvence'))
            ->where('cuentasporcobrar.clientesid', auth()->user()->clientesid)
            ->groupBy('cuentasporcobrar.secuencial', 'cuentasporcobrar.documentosid')
            ->orderBy('emision', 'desc')
            ->paginate(9);
        return view('frontend.cliente.estado_cartera', compact('documentos'));
    }

    public function detalle_documento(Request $request)
    {

        $documentoid = $request->documentoid;
        $detalles = DB::connection('empresa')->table('cuentasporcobrar')
            ->select('cuentasporcobrar.emision AS emision', 'cuentasporcobrar.importe AS importe', DB::raw('CASE WHEN cuentasporcobrar.tipo = "AB" THEN "ABONO" WHEN cuentasporcobrar.tipo = "FC" THEN "FACTURA" WHEN cuentasporcobrar.tipo = "NC" THEN "NOTA CREDITO" WHEN cuentasporcobrar.tipo = "IR" THEN "RETENCION"  WHEN cuentasporcobrar.tipo = "ND" THEN "NOTA DEBITO" END AS tipo'))
            ->where('cuentasporcobrar.documentosid', $request->documentoid)
            ->where('cuentasporcobrar.clientesid', auth()->user()->clientesid)
            ->get();

        return view('frontend.cliente.detalle_documento', compact('detalles', 'documentoid'));
    }

    public function verificar_existencia(Request $request)
    {



        $productos = [];
        $almacenes = DB::connection('empresa')->table('facturadores_almacenes')
            ->where('facturadoresid', get_setting('facturador'))
            ->where('principal', '1')
            ->first();
        foreach ($request->productosid as $key => $productoid) {
            $existencias = DB::connection('empresa')->table('movinventarios_almacenes')
                ->select('movinventarios_almacenes.movinventarios_almacenesid', 'movinventarios_almacenes.existencias', 'productos.descripcion')
                ->join('productos', 'productos.productosid', '=', 'movinventarios_almacenes.productosid')
                ->where('movinventarios_almacenes.productosid', $productoid)
                ->where('movinventarios_almacenes.almacenesid', $almacenes->almacenesid)
                ->first();
            if ($existencias->existencias < $request->cantidad[$key]) {
                array_push($productos, $existencias->descripcion);
            }
        }
        return $productos;
    }

    public function almacenes(Request $request)
    {


        if ($request->ajax()) {


            $data = MovimientosInventariosAlmacenes::select('movinventarios_almacenes.almacenesid', 'almacenes.descripcion')->selectRaw('(movinventarios_almacenes.existencias)/' . $request->factorValor . ' AS existencias')->where('productosid', $request->producto)->where('disponibleventa', 1)->join('almacenes', 'almacenes.almacenesid', 'movinventarios_almacenes.almacenesid')->orderBy('almacenesid', 'asc')->get();


            return DataTables::of($data)

                ->editColumn('existencias', function ($almacen) {

                    $existencias = number_format(round($almacen->existencias, 2), 2);

                    return $existencias;
                })
                ->editColumn('action', function ($almacen) {
                    $nombreAlmacen = Almacenes::select('descripcion')->where('almacenesid', $almacen->almacenesid)->first();

                    if ($almacen->almacenesid == session('almacenesid')) {
                        return '<input checked class="form-control mx-auto" type="radio" id="' . $almacen->almacenesid . '" nombrealmacen="' . $nombreAlmacen->descripcion . '" name="cambioalmacen" value="' . number_format(round($almacen->existencias, 2), 2) . '" style="height:25px; width:25px;" onclick="cambiarAlmacen(event)"  ></input>';
                    } else {
                        return '<input class="form-control mx-auto" type="radio" id="' . $almacen->almacenesid . '"  nombrealmacen="' . $nombreAlmacen->descripcion . '"  name="cambioalmacen" value="' . number_format(round($almacen->existencias, 2), 2) . '" style="height:25px; width:25px;" onclick="cambiarAlmacen(event)"  ></input>';
                    }
                })
                ->rawColumns(['action', 'existencias'])
                ->make(true);
        }
    }
}
