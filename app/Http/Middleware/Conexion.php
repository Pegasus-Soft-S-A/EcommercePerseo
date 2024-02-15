<?php

namespace App\Http\Middleware;

use App\Models\ParametrosEmpresa;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $empresa = DB::connection('sistema')->table('sis_empresas')
            ->where('sis_empresasid', $base)
            ->first();

        if (!$empresa || $empresa->accesoweb != 1) {
            abort(404);
        }

        Config::set('database.connections.empresa.database', $empresa->nombredb);
        Config::set('services.google.redirect', env('APP_URL') . '/' . $base . '/social-login/google/callback');
        Config::set('services.facebook.redirect', env('APP_URL') . '/' . $base . '/social-login/facebook/callback');

        // Asegúrate de purgar y reconectar para reflejar los cambios inmediatamente
        DB::purge('empresa');
        DB::reconnect('empresa');

        // Aquí se maneja la lógica de la caché
        $lastEmpresaId = session('last_empresa_id');
        if ($base != $lastEmpresaId) {
            //Invalidar la caché de la empresa anterior
            Cache::forget("settings");

            // Actualiza el ID de la empresa en la sesión para futuras referencias
            session(['last_empresa_id' => $base]);
        }

        return $next($request);
    }
}
