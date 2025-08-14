<?php

namespace App\Http\Controllers;

use App\Mail\Registro;
use App\Models\Almacenes;
use App\Models\Carrito;
use App\Models\Categorias;
use App\Models\Ciudades;
use App\Models\Clientes;
use App\Models\ClientesSucursales;
use App\Models\Comentarios;
use App\Models\Facturas;
use App\Models\FacturasDetalles;
use App\Models\Integraciones;
use App\Models\Lineas;
use App\Models\MovimientosInventariosAlmacenes;
use App\Models\ParametrosEmpresa;
use App\Models\Parroquias;
use App\Models\Pedidos;
use App\Models\Producto;
use App\Models\ProductoImagen;
use App\Models\ProductoTarifa;
use App\Models\Provincias;
use App\Models\Secuenciales;
use App\Models\Subcategorias;
use App\Models\Subgrupos;
use App\Models\User;
use App\Models\Usuarios;
use App\Models\Wishlist;
use App\Rules\ValidacionIdentificacionEcuatoriana;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables as DataTables;
use Intervention\Image\ImageManagerStatic as Image;


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

        if (!$cliente || empty($cliente->clave)) {
            flash("Identificación o contraseña incorrecta")->warning();
            return back();
        }

        if ($cliente->estado == 0) {
            flash("Usuario Inactivo")->warning();
            return back();
        }

        // Si se necesita seleccionar la sucursal y no lo hizo
        if ($request->almacenesid == 0 && get_setting('controla_stock') == 2) {
            flash("Seleccione Sucursal")->warning();
            return back();
        }

        // Autenticamos al cliente
        $authenticated = $this->authenticateClient($request, $cliente);

        if (!$authenticated) {
            flash("Identificación o contraseña incorrecta")->warning();
            return back();
        }

        // Procesamos el carrito si es necesario
        $this->processCart($request);

        return redirect()->route('home'); // Redirecciona donde prefieras luego del login exitoso
    }

    private function authenticateClient(Request $request, $cliente)
    {
        $clave_cliente = encrypt_openssl($request->clave, "Perseo1232*");

        // Si no maneja sucursales
        if (get_setting('maneja_sucursales') != "on") {
            $clave = json_decode($cliente->clave);
            if ($clave->ecommerce != $clave_cliente) {
                return false;
            }
            $this->loginClient($request, $cliente);
            return true;
        }

        // Si maneja sucursales
        $sucursales = ClientesSucursales::where('clientesid', $cliente->clientesid)->get();
        $sucursalCoincidente = $sucursales->first(function ($sucursal) use ($clave_cliente) {
            return $sucursal->clave == $clave_cliente;
        });

        if (!$sucursalCoincidente) {
            return false;
        }

        session(['sucursalid' => $sucursalCoincidente->clientes_sucursalesid]);
        $this->loginClient($request, $cliente);
        return true;
    }

    private function loginClient(Request $request, $cliente)
    {
        if ($request->has('remember')) {
            Auth::login($cliente, true);
        } else {
            Auth::login($cliente, false);
        }

        session(['almacenesid' => $request->almacenesid]);
    }

    private function processCart(Request $request)
    {
        $carrito = Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))->get();
        if ($carrito->isEmpty()) {
            return;
        }

        foreach ($carrito as $carro) {
            $precioProducto = ProductoTarifa::select('productos_tarifas.precio', 'productos_tarifas.precioiva')
                ->join('productos', 'productos.productosid', '=', 'productos_tarifas.productosid')
                ->where('productos_tarifas.productosid', $carro->productosid)
                ->where('productos_tarifas.medidasid', '=', $carro->medidasid)
                ->where('productos_tarifas.tarifasid', '=', auth()->user()->tarifasid)
                ->first();

            if ($precioProducto) {
                $carro->update([
                    'precio' => $precioProducto->precio,
                    'precioiva' => $precioProducto->precioiva,
                    'descuento' => auth()->user()->descuento,
                ]);
            }
        }

        Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))
            ->update([
                'clientesid' => auth()->user()->clientesid,
                'clientes_sucursalesid' => session('sucursalid', 0),
                'usuario_temporalid' => null
            ]);

        Session::forget('usuario_temporalid');

        // Consolidar productos repetidos en el carrito
        $this->consolidateCart(auth()->user()->clientesid);
    }

    private function consolidateCart($clientesid)
    {
        $carritos = Carrito::where('clientesid', $clientesid)->get();

        foreach ($carritos as $carro) {
            $duplicados = Carrito::where('clientesid', $clientesid)
                ->where('almacenesid', $carro->almacenesid)
                ->where('productosid', $carro->productosid)
                ->where('medidasid', $carro->medidasid)
                ->get();

            if ($duplicados->count() > 1) {
                $cantidadTotal = $duplicados->sum('cantidad');
                $duplicados->first()->update(['cantidad' => $cantidadTotal]);

                // Eliminamos los duplicados excepto el primero
                foreach ($duplicados->skip(1) as $duplicado) {
                    $duplicado->delete();
                }
            }
        }
    }


    public function admin_login(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $usuario = Usuarios::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)
            ->where('sis_clientesid', sis_cliente())
            ->where('sis_licenciasid', sis_licencia())
            ->first();

        if (!$usuario) {
            flash("Identificacion o contraseña incorrecta")->error();
            return back();
        }

        $clave_usuario = encrypt_openssl($request->contrasena, "Perseo1232*" . sis_cliente());
        if ($usuario->contrasena !== $clave_usuario) {
            flash("Identificacion o contraseña incorrecta")->error();
            return back();
        }

        $integraciones = Integraciones::where('tipo', 5)->first();

        if (!$integraciones) {
            $integracion = new Integraciones();
            $integracion->descripcion = "Tienda Ecommerce";
            $integracion->tipo = 5;
            $integracion->parametros = json_encode($this->getDefaultParameters());
            $integracion->save();

            $this->updateDatabaseParameters();
        } elseif ($integraciones->parametros === null) {
            $integraciones->parametros = json_encode($this->getDefaultParameters());
            $integraciones->save();

            $this->updateDatabaseParameters();
        } else {
            $parametrosExisten = json_decode($integraciones->parametros, true);
            $parametros = $this->getDefaultParameters();

            foreach ($parametros as $clave => $valor) {
                if (!array_key_exists($clave, $parametrosExisten)) {
                    $parametrosExisten[$clave] = $valor;
                }
            }

            $integraciones->parametros = json_encode($parametrosExisten);
            $integraciones->save();
        }

        Auth::guard('admin')->login($usuario, false);
        return redirect()->route('admin.dashboard');
    }

    private function getDefaultParameters()
    {
        return [
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
            'imagen_defecto' => base64_encode(file_get_contents(static_asset('assets/img/placeholder.jpg'))),
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
            "vista_categorias" => 1,
            "cupo_sucursal" => 0,
            "cliente_pedidos" => null,
            "maneja_sucursales" => null,
            "pago_plux_pruebas" => '0',
        ];
    }

    private function updateDatabaseParameters()
    {
        // Los arreglos $productos, $lineas, $categorias, $subcategorias y $subgrupos deberían definirse aquí, si es que cambian entre llamadas.
        // De lo contrario, considera definirlos en getDefaultParameters o en otra función si se reutilizan.
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
        // ... otros arreglos// Actualización en masa para no repetir código y reducir conexiones a la base de datos
        $tablas = [
            'productos' => $productos,
            'productos_lineas' => $lineas,
            'productos_categorias' => $categorias,
            'productos_subcategoria' => $subcategorias,
            'productos_subgrupo' => $subgrupos,
            // ... otros mapeos de tabla a arreglo de parámetros
        ];

        foreach ($tablas as $tabla => $parametros) {
            DB::connection('empresa')->table($tabla)->update(['parametros_json' => json_encode($parametros)]);
        }
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

        try {
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
        } catch (\Exception $e) {
            flash('Ocurrió un error vuelva a intentarlo')->warning();
        }
    }

    public function register(Request $request)
    {

        // Toda la validación se realiza aquí
        $validator = Validator::make(
            $request->all(),
            [
                'identificacion' => ['required', 'string', 'max:13', new ValidacionIdentificacionEcuatoriana],
                'razonsocial' => 'required|string',
                'email' => 'required|email',
                'telefono1' => 'required|regex:/^[0-9]{7,15}$/',
            ],
            [
                'identificacion.required' => 'El campo identificación es obligatorio.',
                'razonsocial.required' => 'El campo razón social es obligatorio.',
                'email.required' => 'El campo email es obligatorio.',
                'email.email' => 'El campo email no es una dirección de correo válida.',
                'telefono1.required' => 'El campo teléfono es obligatorio.',
                'telefono1.regex' => 'El campo teléfono debe contener entre 7 y 15 dígitos.',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $user = $this->createUser($request);
        Auth::login($user, false);

        // Obtener parámetros de la empresa
        $parametros = ParametrosEmpresa::first();



        // Funcionalidad específica para Merkato
        if (get_setting('controla_stock') == 2) {
            $almacenes = DB::connection('empresa')->table('facturadores_almacenes')
                ->where('facturadoresid', get_setting('facturador'))
                ->where('principal', '1')
                ->first();

            session(['almacenesid' => $almacenes->almacenesid]);
        }

        // Preparar lista de correos
        $emails = array_filter(
            array_unique(
                array_map('trim', array_merge(
                    explode(',', get_setting('email_pedidos')),
                    [$user->email_login]
                ))
            ),
            fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL)
        );

        // Seleccionar método de envío según el tipo SMTP
        if ($parametros->smtp_tipo == 1) {
            // Método SMTP tradicional
            configurar_smtp();
            // Preparar datos del correo
            $array = [
                'view' => 'emails.registro',
                'subject' => "Registro",
                'from' => Config::get('mail.from.address'),
                // Datos para la plantilla Blade
                'identificacion' => $user->identificacion,
                'telefono' => $user->telefono1,
                'razonsocial' => $user->razonsocial
            ];
            try {
                Mail::mailer('smtp')->to($emails)->send(new Registro($array));
                flash('Email enviado correctamente')->success();
            } catch (\Exception $e) {
                flash('Error enviando email: ' . $e->getMessage())->error();
            }
        } elseif ($parametros->smtp_tipo == 2) {
            // Preparar datos del correo
            $array = [
                'view' => 'emails.registro',
                'subject' => "Registro",
                'from' => Config::get('mail.from.address'),
                // Datos para la plantilla Blade
                'identificacion' => $user->identificacion,
                'telefono' => $user->telefono1,
                'razonsocial' => $user->razonsocial
            ];
            // Enviar por API de Gmail usando el helper
            [$success, $message] = enviar_por_gmail_api($emails, $array, $parametros);
            if ($success) {
                flash($message)->success();
            } else {
                flash($message)->error();
            }
        } else {
            flash('Tipo de configuración de correo no válido')->error();
        }

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
        if (get_setting('maneja_sucursales') == "on") {
            $cart = Carrito::where('clientesid',  Auth::user()->clientesid)->where('clientes_sucursalesid', session('sucursalid'))->get();
            $wishlist = Wishlist::where('clientes_sucursalesid', session('sucursalid'))->get();
            $orders = Pedidos::where('usuariocreacion', 'Ecommerce')->where('clientes_sucursalesid', session('sucursalid'))->where('pedidos.documentosid', 0)->get();
        } else {
            $cart = Carrito::where('clientesid',  Auth::user()->clientesid)->get();
            $wishlist = Wishlist::where('clientesid', Auth::user()->clientesid)->get();
            $orders = Pedidos::where('clientesid', Auth::user()->clientesid)->where('usuariocreacion', 'Ecommerce')->where('pedidos.documentosid', 0)->get();
        }
        return view('frontend.cliente.dashboard', ['cart' => $cart, 'wishlist' => $wishlist, 'orders' => $orders]);
    }

    public function profile()
    {
        if (get_setting('maneja_sucursales') == "on") {
            $sucursales = ClientesSucursales::select(
                'clientes_sucursales.clientes_sucursalesid',
                'clientes_sucursales.direccion',
                'clientes_sucursales.telefono1',
                'ciudades.ciudad',
                'clientes_sucursales.descripcion'
            )
                ->join('ciudades', 'ciudades.ciudadesid', 'clientes_sucursales.ciudadesid')
                ->where('clientes_sucursalesid', session('sucursalid'))
                ->get();
            $cliente = Clientes::select('clientes.identificacion', 'clientes.razonsocial', 'clientes.email_login', 'clientes.telefono1', 'clientes.telefono2', 'clientes_sucursales.telefono1 as telefono3')
                ->join('clientes_sucursales', 'clientes_sucursales.clientesid', 'clientes.clientesid')
                ->where('clientes.clientesid', Auth::user()->clientesid)
                ->where('clientes_sucursales.clientes_sucursalesid', session('sucursalid'))
                ->first();
        } else {
            $sucursales = ClientesSucursales::select(
                'clientes_sucursales.clientes_sucursalesid',
                'clientes_sucursales.direccion',
                'clientes_sucursales.telefono1',
                'ciudades.ciudad',
                'clientes_sucursales.descripcion'
            )
                ->join('ciudades', 'ciudades.ciudadesid', 'clientes_sucursales.ciudadesid')
                ->where('clientes_sucursales.clientesid', Auth::user()->clientesid)
                ->get();
            $cliente = Clientes::where('clientesid', Auth::user()->clientesid)->first();
        }
        $provincias = Provincias::all();

        return view('frontend.cliente.profile', ['sucursales' => $sucursales, 'cliente' => $cliente, 'provincias' => $provincias]);
    }

    public function getCiudades($provinciaId)
    {
        $ciudades = Ciudades::where('ciudadesid', 'like', $provinciaId . '%')
            ->get();
        return response()->json($ciudades);
    }

    public function getParroquias($ciudadId)
    {
        $parroquias = Parroquias::where('parroquiasid', 'like', $ciudadId . '%')
            ->get();
        return response()->json($parroquias);
    }

    public function update_profile(Request $request)
    {
        if (get_setting('maneja_sucursales') == "on") {
            $sucursal = ClientesSucursales::where('clientes_sucursalesid', session('sucursalid'))->first();
            $sucursal->telefono1 = $request->telefono3;
            if ($request->new_password != null) {
                if ($request->new_password == $request->confirm_password) {
                    $sucursal->clave = encrypt_openssl($request->new_password, "Perseo1232*");
                } else {
                    flash('Las contraseñas no coinciden')->error();
                    return back();
                }
            }
            if ($sucursal->save()) {
                flash('Perfil Actualizado Correctamente')->success();
                return back();
            }
        } else {
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
            ->where('productos.ecommerce_estado', 1)
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
        $fecha_inicio = Carbon::now()->subDays(30); // 30 días hacia atrás desde la fecha actual
        // Subconsulta para las ventas
        $ventasSubquery = DB::connection('empresa')->table('facturas_detalles as fd')
            ->select('fd.productosid', DB::raw('SUM(CASE WHEN (f.sri_documentoscodigo = \'04\') THEN (fd.cantidad * -1) ELSE fd.cantidad END) AS total_cantidad'))
            ->join('facturas as f', 'fd.facturasid', '=', 'f.facturasid')
            ->where('f.emision', '>=', Carbon::now()->subDays(30))
            ->groupBy('fd.productosid')
            ->orderBy('total_cantidad', 'desc')
            ->limit(10);

        $parametros = ParametrosEmpresa::first();

        if (Auth::check()) {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = DB::connection('empresa')->table('productos as p')
                    ->select('p.productosid', 'p.productocodigo', 'p.descripcion', 'pt.precioiva as precio2', 'pi.imagen', 'p.parametros_json', 'ventas.total_cantidad', DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = p.productosid AND tarifain.medidasid = p.unidadinterna) AS precio"));
            } else {
                $products = DB::connection('empresa')->table('productos as p')
                    ->select('p.productosid', 'p.productocodigo', 'p.descripcion', 'pt.precio as precio2', 'pi.imagen', 'p.parametros_json', 'ventas.total_cantidad', DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . auth()->user()->tarifasid . " and tarifain.productosid = p.productosid AND tarifain.medidasid = p.unidadinterna) AS precio"));
            }
        } else {
            if ($parametros->tipopresentacionprecios == 1) {
                $products = DB::connection('empresa')->table('productos as p')
                    ->select('p.productosid', 'p.productocodigo', 'p.descripcion', 'pi.imagen', 'p.parametros_json', 'ventas.total_cantidad', DB::raw("(SELECT tarifain.precioiva FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . get_setting('tarifa_productos') . " and tarifain.productosid = p.productosid AND tarifain.medidasid = p.unidadinterna) AS precio"));
            } else {
                $products = DB::connection('empresa')->table('productos as p')
                    ->select('p.productosid', 'p.productocodigo', 'p.descripcion', 'pi.imagen', 'p.parametros_json', 'ventas.total_cantidad', DB::raw("(SELECT tarifain.precio FROM productos_tarifas as tarifain WHERE tarifain.tarifasid = " . get_setting('tarifa_productos') . " and tarifain.productosid = p.productosid AND tarifain.medidasid = p.unidadinterna) AS precio"));
            }
        }

        // Consulta principal
        $products = $products->join('productos_tarifas as pt', 'pt.productosid', '=', 'p.productosid')
            ->leftJoin('productos_imagenes as pi', function ($join) {
                $join->on('pi.productosid', '=', 'p.productosid')
                    ->where('pi.principal', '=', 1);
            })
            ->joinSub($ventasSubquery, 'ventas', function ($join) {
                $join->on('ventas.productosid', '=', 'p.productosid');
            })
            ->where('p.ecommerce_estado', 1)
            ->where('p.existenciastotales', '>', 0)
            ->where('pt.tarifasid', '=', get_setting('tarifa_productos'))
            ->where('p.venta', '=', 1)
            ->where('p.servicio', '=', 0)
            ->where('p.bien', '=', 0)
            ->whereColumn('pt.medidasid', 'p.unidadinterna')
            ->orderBy('ventas.total_cantidad', 'desc')
            ->limit(10)
            ->get();

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

        $imagenProducto = $imagenProducto->map(function ($item) {
            $image = Image::make($item->imagen);
            $compressedImage = $image->encode('webp', 100);
            return [
                'imagen' => ($compressedImage),
                'medidasid' => $item->medidasid,
            ];
        });

        if (get_setting('maneja_sucursales') == "on") {
            $comentarios = Comentarios::select('ecommerce_comentarios.comentario', 'ecommerce_comentarios.valoracion', 'ecommerce_comentarios.fechacreacion', 'clientes_sucursales.descripcion as razonsocial')
                ->join('clientes_sucursales', 'clientes_sucursales.clientes_sucursalesid', '=', 'ecommerce_comentarios.clientes_sucursalesid')
                ->where('ecommerce_comentarios.productosid', $productosid)
                ->where('ecommerce_comentarios.estado', 1)
                ->get();
        } else {
            $comentarios = Comentarios::select('ecommerce_comentarios.comentario', 'ecommerce_comentarios.valoracion', 'ecommerce_comentarios.fechacreacion', 'clientes.razonsocial')
                ->join('clientes', 'clientes.clientesid', '=', 'ecommerce_comentarios.clientesid')
                ->where('ecommerce_comentarios.productosid', $productosid)
                ->where('ecommerce_comentarios.estado', 1)
                ->get();
        }

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
            ->where('productos.ecommerce_estado', 1)
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

        $fecha_inicio = Carbon::now()->subDays(30);
        $top = $top->join('productos', 'productos.productosid', '=', 'facturas_detalles.productosid')
            ->join('facturas', 'facturas_detalles.facturasid', '=', 'facturas.facturasid')
            ->join('productos_tarifas', 'productos_tarifas.productosid', '=', 'productos.productosid')
            ->leftJoin('productos_imagenes', function ($products) {
                $products->on('productos_imagenes.productosid', '=', 'productos.productosid')
                    ->where('productos_imagenes.principal', '=', "1");
            })
            ->where('productos.ecommerce_estado', 1)
            ->when(get_setting('productos_existencias') != "todos", function ($products) {
                return $products->where('productos.existenciastotales', '>', '0');
            })
            ->where([
                // ['facturas.emision', '>=', $fecha_inicio],
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

        $user = Auth::user();
        $commentable = false;

        if ($user) {
            if (get_setting('maneja_sucursales') == "on") {
                $productoCompradoSinComentario = DB::connection('empresa')->table('facturas')
                    ->join('facturas_detalles', 'facturas.facturasid', '=', 'facturas_detalles.facturasid')
                    ->leftJoin('ecommerce_comentarios', function ($join) use ($user, $productosid) {
                        $join->on('facturas_detalles.productosid', '=', 'ecommerce_comentarios.productosid')
                            ->where('ecommerce_comentarios.clientes_sucursalesid', '=', session('sucursalid'));
                    })
                    ->where('facturas.clientes_sucursalesid', session('sucursalid'))
                    ->where('facturas_detalles.productosid', $productosid)
                    ->whereNull('ecommerce_comentarios.ecommerce_comentariosid') // Si no hay comentario
                    ->exists();
            } else {
                $productoCompradoSinComentario = DB::connection('empresa')->table('facturas')
                    ->join('facturas_detalles', 'facturas.facturasid', '=', 'facturas_detalles.facturasid')
                    ->leftJoin('ecommerce_comentarios', function ($join) use ($user, $productosid) {
                        $join->on('facturas_detalles.productosid', '=', 'ecommerce_comentarios.productosid')
                            ->where('ecommerce_comentarios.clientesid', '=', $user->clientesid);
                    })
                    ->where('facturas.clientesid', $user->clientesid)
                    ->where('facturas_detalles.productosid', $productosid)
                    ->whereNull('ecommerce_comentarios.ecommerce_comentariosid') // Si no hay comentario
                    ->exists();
            }

            if ($productoCompradoSinComentario) {
                $commentable = true;
            }
        }

        return view('frontend.product_details', compact('detallesProducto', 'precioProducto', 'precioProducto2', 'imagenProducto', 'min_qty', 'comentarios', 'medidas', 'numerocomentarios', 'relacionados', 'top', 'commentable', 'user', 'productosid'));
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
            ->where('productos.ecommerce_estado', 1)
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
            ->where('productos.ecommerce_estado', 1)
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
        $categories = $categories->where('productos.ecommerce_estado', 1)
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

            // Preparar lista de correos
            $emails = array_filter(
                array_unique(
                    array_map('trim', array_merge(
                        explode(',', get_setting('email_pedidos')),
                        [$cliente->email_login]
                    ))
                ),
                fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL)
            );

            // Seleccionar método de envío según el tipo SMTP
            if ($parametros->smtp_tipo == 1) {
                // Método SMTP tradicional
                configurar_smtp();
                // Preparar datos del correo
                $array = [
                    'view' => 'emails.registro',
                    'subject' => "Registro",
                    'from' => Config::get('mail.from.address'),
                    // Datos para la plantilla Blade
                    'identificacion' => $cliente->identificacion,
                    'telefono' => $cliente->telefono1,
                    'razonsocial' => $cliente->razonsocial
                ];
                try {
                    Mail::mailer('smtp')->to($emails)->send(new Registro($array));
                    flash('Email enviado correctamente')->success();
                } catch (\Exception $e) {
                    flash('Error enviando email: ' . $e->getMessage())->error();
                }
            } elseif ($parametros->smtp_tipo == 2) {
                // Preparar datos del correo
                $array = [
                    'view' => 'emails.registro',
                    'subject' => "Registro",
                    'from' => Config::get('mail.from.address'),
                    // Datos para la plantilla Blade
                    'identificacion' => $cliente->identificacion,
                    'telefono' => $cliente->telefono1,
                    'razonsocial' => $cliente->razonsocial
                ];
                // Enviar por API de Gmail usando el helper
                [$success, $message] = enviar_por_gmail_api($emails, $array, $parametros);
                if ($success) {
                    flash($message)->success();
                } else {
                    flash($message)->error();
                }
            } else {
                flash('Tipo de configuración de correo no válido')->error();
            }

            auth()->login($cliente, true);
            request()->session()->regenerate();
            $clienteid = $cliente->clientesid;
            $tarifaid = $cliente->tarifasid;
        }

        $carrito = Carrito::where('usuario_temporalid', $request->session()->get('usuario_temporalid'))->get();
        if (count($carrito) > 0) {

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
        if (get_setting('maneja_sucursales') == "on") {
            $documentos = DB::connection('empresa')->table('cuentasporcobrar')
                ->select(
                    'cuentasporcobrar.secuencial',
                    'cuentasporcobrar.documentosid AS documentoid',
                    'cuentasporcobrar.secuencial AS secuencia',
                    DB::raw('max(cuentasporcobrar.importe) AS valor'),
                    DB::raw('SUM(cuentasporcobrar.importe) AS saldo'),
                    DB::raw('CASE WHEN MAX(cuentasporcobrar.importe)=0 THEN cuentasporcobrar.emision ELSE cuentasporcobrar.emision END AS emision'),
                    DB::raw('CASE WHEN MAX(cuentasporcobrar.importe)=0 THEN cuentasporcobrar.vence ELSE cuentasporcobrar.vence END AS vence'),
                    DB::raw('case WHEN DATEDIFF(now(),cuentasporcobrar.vence) > 0 then DATEDIFF(now(),cuentasporcobrar.vence) ELSE 0 END as diasvence')
                )
                ->join('clientes', 'cuentasporcobrar.clientesid', '=', 'clientes.clientesid') // Suponiendo que la relación es con clientes.clientesid
                ->join('clientes_sucursales', 'clientes.clientesid', '=', 'clientes_sucursales.clientesid') // Relacionar con clientes_sucursales
                // ->where('cuentasporcobrar.clientesid', auth()->user()->clientesid)
                ->where('clientes_sucursales.clientes_sucursalesid', session('sucursalid')) // Filtrar por la sucursal de la sesión
                ->groupBy('cuentasporcobrar.secuencial', 'cuentasporcobrar.documentosid')
                ->orderBy('emision', 'desc')
                ->paginate(9);
        } else {
            $documentos = DB::connection('empresa')->table('cuentasporcobrar')
                ->select('cuentasporcobrar.secuencial', 'cuentasporcobrar.documentosid AS documentoid', 'cuentasporcobrar.secuencial AS secuencia', DB::raw('max(cuentasporcobrar.importe) AS valor'), DB::raw('SUM(cuentasporcobrar.importe) AS saldo'), DB::raw('CASE WHEN MAX(cuentasporcobrar.importe)=0 THEN cuentasporcobrar.emision ELSE cuentasporcobrar.emision END AS emision'), DB::raw('CASE WHEN MAX(cuentasporcobrar.importe)=0 THEN cuentasporcobrar.vence ELSE cuentasporcobrar.vence END AS vence'), DB::raw('case WHEN DATEDIFF(now(),cuentasporcobrar.vence) > 0 then DATEDIFF(now(),cuentasporcobrar.vence) ELSE 0 END as diasvence'))
                ->where('cuentasporcobrar.clientesid', auth()->user()->clientesid)
                ->groupBy('cuentasporcobrar.secuencial', 'cuentasporcobrar.documentosid')
                ->orderBy('emision', 'desc')
                ->paginate(9);
        }

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

    public function profile_delete($id)
    {
        $clientes = Clientes::findOrFail($id);
        $clientes->estado = 0;
        $clientes->save();

        Auth::logout();
        Session::forget('almacenesid');
        return redirect()->route('home');
    }
}
