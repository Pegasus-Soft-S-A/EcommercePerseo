<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Registro;
use App\Models\Carrito;
use App\Models\Clientes;
use App\Models\ParametrosEmpresa;
use App\Models\ProductoTarifa;
use App\Models\Secuenciales;
use App\Models\User;
use Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider, Request $request)
    {
        try {
            //guarda los datos devueltos por la autenticacion de face o google
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            flash("Algo salio mal, intentelo nuevamente.")->error();
            if (session('ruta') == 'login') {
                return redirect()->route('user.login');
            } else {
                return redirect()->route('cart');
            }
        }
        //verifica si existe cliente
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
            // flash("No se encontro el correo.")->error();
            if (session('ruta') == 'login') {
                return redirect()->route('user.login')->with(['user' => $user]);
            } else {
                return redirect()->route('cart')->with(['user' => $user]);
            }
        }

        //Actualizar si es que existe productos en el carrito
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

    public function logout(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $redirect_route = 'login';
            Auth::guard('admin')->logout();
        } else {
            $redirect_route = 'home';
            //Carrito::where('clientesid', auth()->user()->clientesid)->delete();
            Auth::logout();
            Session::forget('almacenesid');
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
