<?php

namespace App\Http\Middleware;

use App\Models\ParametrosEmpresa;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Conexion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $base)
    {
        $base_encriptada = strtr($base, '._-', '+/=');
        $base_desencriptada = decrypt_openssl($base_encriptada, "Perseo1232*");

        Config::set('database.connections.empresa.database', $base_desencriptada); // Asigno la DB que voy a usar

        Config::set('services.google.redirect', env('APP_URL') . '/' . $base . '/social-login/google/callback');
        Config::set('services.facebook.redirect', env('APP_URL') . '/' . $base . '/social-login/facebook/callback');

        DB::connection('empresa'); //Asigno la nueva conexión al sistema. 
        DB::purge('empresa');
        return $next($request);
    }
}
