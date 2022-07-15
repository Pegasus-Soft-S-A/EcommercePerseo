<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ReestablecerContrasena;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(Request $request)
    {
        $identificacionIngresada = substr($request->identificacion, 0, 10);
        $cliente = User::where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->first();
        if ($cliente != null) {
            if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email_login', $request->email)->where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->first();
                if ($user != null) {
                    $user->codigo_verificacion = rand(100000, 999999);
                    $user->save();

                    configurar_smtp();

                    $array['view'] = 'emails.verification';
                    $array['from'] = Config::get('mail.from.address');
                    $array['subject'] = 'Reestablecer Contraseña';
                    $array['codigo'] = $user->codigo_verificacion;

                    Mail::to($user->email_login)->queue(new ReestablecerContrasena($array));
                    flash('Se ha enviado un codigo de verificacion a su correo electronico')->success();
                    return view('auth.passwords.reset');
                } else {
                    flash('El email no corresponde al usuario')->error();
                    return back();
                }
            }
        } else {
            flash('No existe un usuario con esta identificacion')->error();
            return back();
        }
    }
}
