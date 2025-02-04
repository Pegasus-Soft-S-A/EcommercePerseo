<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Carrito;
use App\Models\ProductoTarifa;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToProvider($provider, Request $request)
    {
        $base = $request->segment(1);
        $dynamicRedirectUrl = env('APP_URL') . "/{$base}/social-login/{$provider}/callback";

        // Configurar la URL de redirección dinámicamente
        config(["services.$provider.redirect" => $dynamicRedirectUrl]);
        return Socialite::driver($provider)->redirect();
    }

    public function handleAppleCallback()
    {
        $provider = 'apple'; // Establecer el proveedor específico si es necesario
        return $this->authenticateUser($provider);
    }

    public function handleProviderCallback($provider)
    {
        return $this->authenticateUser($provider);
    }

    private function authenticateUser($provider)
    {
        try {
            // Guarda los datos devueltos por la autenticación de face, google, apple
            // Si el proveedor es 'apple', configura el cliente HTTP con 'verify' => false
            $socialiteDriver = Socialite::driver($provider)
                ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));

            $user = $socialiteDriver->user();
        } catch (\Exception $e) {
            dd($e->getMessage());
            flash("Algo salio mal, intentelo nuevamente.")->error();
            if (Session::get('ruta') == 'login') {
                return redirect()->route('user.login');
            } else {
                return redirect()->route('cart');
            }
        }

        // Verifica si existe cliente y autenticar o redirigir según corresponda
        $existingUser = User::where('email_login', $user->email)->first();
        $clienteid = "";
        $tarifaid = "";
        if ($existingUser) {
            // si existe inicia sesion
            auth()->login($existingUser, true);
            request()->session()->regenerate();
            $clienteid = $existingUser->clientesid;
            $tarifaid = $existingUser->tarifasid;
        } else {
            if (Session::get('ruta') == 'login') {
                return redirect()->route('user.login')->with(['user' => $user]);
            } else {
                return redirect()->route('cart')->with(['user' => $user]);
            }
        }

        // Actualizar el carrito si existe
        $carrito = Carrito::where('usuario_temporalid', Session::get('usuario_temporalid'))->get();
        if (count($carrito) > 0) {
            foreach ($carrito as $key => $carro) {
                $precioProducto = ProductoTarifa::select('productos_tarifas.precio', 'productos_tarifas.precioiva')
                    ->join('productos', 'productos.productosid', '=', 'productos_tarifas.productosid')
                    ->where('productos_tarifas.productosid', $carro->productosid)
                    ->where('productos_tarifas.medidasid', '=', $carro->medidasid)
                    ->where('productos_tarifas.tarifasid', '=', $tarifaid)
                    ->first();

                Carrito::where('usuario_temporalid', Session::get('usuario_temporalid'))
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

            Carrito::where('usuario_temporalid', Session::get('usuario_temporalid'))
                ->update(
                    [
                        'clientesid' => $clienteid,
                        'usuario_temporalid' => null
                    ]
                );

            Session::forget('usuario_temporalid');
        }

        // Redirigir al usuario al final del proceso
        if (session('ruta') == 'login') {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('cart');
        }
    }


    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $redirect_route = 'login';
            Auth::guard('admin')->logout();
        } else {
            $redirect_route = 'home';
            Auth::logout();
            Session::forget(['almacenesid', 'sucursalid']);
        }

        //$request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route($redirect_route);
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'identificacion';
    }
}
