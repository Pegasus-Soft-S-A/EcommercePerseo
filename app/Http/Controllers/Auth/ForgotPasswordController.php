<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ReestablecerContrasena;
use App\Models\ParametrosEmpresa;
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
            // Verificar si el usuario está activo
            if ($cliente->estado == 0) {
                flash('El usuario se encuentra inactivo')->error();
                return back();
            }
            if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email_login', $request->email)->where(DB::raw('substr(identificacion, 1, 10)'), $identificacionIngresada)->first();
                if ($user != null) {
                    $user->codigo_verificacion = rand(100000, 999999);
                    $user->save();

                    $parametros = ParametrosEmpresa::first();

                    // Seleccionar método de envío según el tipo SMTP
                    if ($parametros->smtp_tipo == 1) {
                        // Método SMTP tradicional
                        configurar_smtp();
                        // Preparar datos del correo
                        $array = [
                            'view' => 'emails.verification',
                            'subject' => "Reestablecer Contraseña",
                            'from' => Config::get('mail.from.address'),
                            // Datos para la plantilla Blade
                            'codigo' => $user->codigo_verificacion
                        ];
                        try {
                            Mail::mailer('smtp')->to($user->email_login)->send(new ReestablecerContrasena($array));
                            flash('Email enviado correctamente')->success();
                        } catch (\Exception $e) {
                            flash('Error enviando email: ' . $e->getMessage())->error();
                        }
                    } elseif ($parametros->smtp_tipo == 2) {
                        // Preparar datos del correo
                        $array = [
                            'view' => 'emails.verification',
                            'subject' => "Reestablecer Contraseña",
                            'from' => Config::get('mail.from.address'),
                            // Datos para la plantilla Blade
                            'codigo' => $user->codigo_verificacion
                        ];
                        // Enviar por API de Gmail usando el helper
                        [$success, $message] = enviar_por_gmail_api($user->email_login, $array, $parametros);
                        if ($success) {
                            flash('Se ha enviado un codigo de verificacion a su correo electronico')->success();
                        } else {
                            flash($message)->error();
                        }
                    } else {
                        flash('Tipo de configuración de correo no válido')->error();
                    }

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
