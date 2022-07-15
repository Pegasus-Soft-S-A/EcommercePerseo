<?php

use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\ConfiguracionesController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PedidoController;
use App\Models\ParametrosEmpresa;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

$base = Request::segment(1);
$base_encriptada = strtr($base, '._-', '+/=');
$base_desencriptada = decrypt_openssl($base_encriptada, "Perseo1232*");

if ($base_desencriptada) {

    Route::group(['prefix' =>  $base, 'middleware' => ['conexion:' . $base, 'admin']], function () {
        Route::get('/admin', [HomeController::class, 'admin_dashboard'])->name('admin.dashboard');
    });

    Route::group(['prefix' =>  $base . '/' . 'admin/', 'middleware' => ['conexion:' . $base]], function () {
        Route::post('/login-submit',  [HomeController::class, 'admin_login'])->name('admin.login');
    });

    Route::group(['prefix' =>  $base . '/' . 'admin/', 'middleware' => ['admin', 'conexion:' . $base]], function () {


        //Rutas configuraciones
        Route::get('/configuracion-general', [ConfiguracionesController::class, 'general'])->name('configuracion.general');
        Route::post('/business-settings/update_general', [ConfiguracionesController::class, 'update_general'])->name('business_settings.update_general');

        Route::get('/configuracion-header', [ConfiguracionesController::class, 'header'])->name('configuracion.header');
        Route::post('/business-settings/update_header', [ConfiguracionesController::class, 'update_header'])->name('business_settings.update_header');

        Route::get('/configuracion-footer', [ConfiguracionesController::class, 'footer'])->name('configuracion.footer');
        Route::post('/business-settings/update_footer', [ConfiguracionesController::class, 'update_footer'])->name('business_settings.update_footer');

        Route::get('/configuracion-apariencia', [ConfiguracionesController::class, 'apariencia'])->name('configuracion.apariencia');
        Route::post('/business-settings/update_apariencia', [ConfiguracionesController::class, 'update_apariencia'])->name('business_settings.update_apariencia');

        Route::get('/configuracion-paginas', [ConfiguracionesController::class, 'paginas'])->name('configuracion.paginas');
        Route::get('/configuracion-paginas/edit/{pagina}', [ConfiguracionesController::class, 'edit_paginas'])->name('configuracion.paginas.edit');
        Route::post('/configuracion-paginas/update', [ConfiguracionesController::class, 'update_paginas'])->name('configuracion.paginas.update');
        Route::post('/business-settings/update_inicio', [ConfiguracionesController::class, 'update_inicio'])->name('business_settings.update_inicio');

        Route::get('/configuracion-analytics', [ConfiguracionesController::class, 'analytics'])->name('configuracion.analytics');
        Route::post('/business-settings/update_analytics', [ConfiguracionesController::class, 'update_analytics'])->name('business_settings.update_analytics');

        Route::get('/reviews', [ComentariosController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/publicado', [ComentariosController::class, 'actualizarPublicado'])->name('reviews.publicado');

        Route::get('/social-login', [ConfiguracionesController::class, 'social_login'])->name('social_login.index');
        Route::post('/social-login', [ConfiguracionesController::class, 'update_social_login'])->name('social.login.update');

        Route::get('/pedidos', [PedidoController::class, 'verTodos'])->name('pedidos.index');
        Route::get('/pedidos/{id}/show', [PedidoController::class, 'pedidos_show'])->name('pedidos.show');
        Route::post('/pedidos/actualizar-estado', [PedidoController::class, 'actualizar_estado'])->name('pedido.actualizarestado');

        Route::get('/facturas', [FacturaController::class, 'verTodos'])->name('facturas.index');
        Route::get('/facturas/{id}/show', [FacturaController::class, 'facturas_show'])->name('facturas.show');
        Route::post('/facturas/actualizar-estado', [FacturaController::class, 'actualizar_estado'])->name('factura.actualizarestado');

        Route::get('/smtp-settings', [ConfiguracionesController::class, 'smtp_settings'])->name('smtp_settings.index');
        Route::post('/smtp-settings/update', [ConfiguracionesController::class, 'update_smtp'])->name('smtp_settings.update');
        Route::post('/test/smtp', [ConfiguracionesController::class, 'testEmail'])->name('test.smtp');
        Route::get('/asignar-claves', [ConfiguracionesController::class, 'asignar_claves'])->name('asignar_claves');
        Route::get('/asignar-direcciones', [ConfiguracionesController::class, 'asignar_direcciones'])->name('asignar_direcciones');
    });
} else {

    Route::group(['middleware' => ['admin']], function () {
        Route::get('/admin', [HomeController::class, 'admin_dashboard'])->name('admin.dashboard');
    });

    Route::group(['prefix' => 'admin/'], function () {
        Route::post('/login-submit',  [HomeController::class, 'admin_login'])->name('admin.login');
    });

    Route::group(['prefix' => 'admin/', 'middleware' => ['admin']], function () {
        //Rutas configuraciones
        Route::get('/configuracion-general', [ConfiguracionesController::class, 'general'])->name('configuracion.general');
        Route::post('/business-settings/update_general', [ConfiguracionesController::class, 'update_general'])->name('business_settings.update_general');

        Route::get('/configuracion-header', [ConfiguracionesController::class, 'header'])->name('configuracion.header');
        Route::post('/business-settings/update_header', [ConfiguracionesController::class, 'update_header'])->name('business_settings.update_header');

        Route::get('/configuracion-footer', [ConfiguracionesController::class, 'footer'])->name('configuracion.footer');
        Route::post('/business-settings/update_footer', [ConfiguracionesController::class, 'update_footer'])->name('business_settings.update_footer');

        Route::get('/configuracion-apariencia', [ConfiguracionesController::class, 'apariencia'])->name('configuracion.apariencia');
        Route::post('/business-settings/update_apariencia', [ConfiguracionesController::class, 'update_apariencia'])->name('business_settings.update_apariencia');

        Route::get('/configuracion-paginas', [ConfiguracionesController::class, 'paginas'])->name('configuracion.paginas');
        Route::get('/configuracion-paginas/edit/{pagina}', [ConfiguracionesController::class, 'edit_paginas'])->name('configuracion.paginas.edit');
        Route::post('/configuracion-paginas/update', [ConfiguracionesController::class, 'update_paginas'])->name('configuracion.paginas.update');
        Route::post('/business-settings/update_inicio', [ConfiguracionesController::class, 'update_inicio'])->name('business_settings.update_inicio');

        Route::get('/configuracion-analytics', [ConfiguracionesController::class, 'analytics'])->name('configuracion.analytics');
        Route::post('/business-settings/update_analytics', [ConfiguracionesController::class, 'update_analytics'])->name('business_settings.update_analytics');

        Route::get('/reviews', [ComentariosController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/publicado', [ComentariosController::class, 'actualizarPublicado'])->name('reviews.publicado');

        Route::get('/social-login', [ConfiguracionesController::class, 'social_login'])->name('social_login.index');
        Route::post('/social-login', [ConfiguracionesController::class, 'update_social_login'])->name('social.login.update');

        Route::get('/pedidos', [PedidoController::class, 'verTodos'])->name('pedidos.index');
        Route::get('/pedidos/{id}/show', [PedidoController::class, 'pedidos_show'])->name('pedidos.show');
        Route::post('/pedidos/actualizar-estado', [PedidoController::class, 'actualizar_estado'])->name('pedido.actualizarestado');

        Route::get('/facturas', [FacturaController::class, 'verTodos'])->name('facturas.index');
        Route::get('/facturas/{id}/show', [FacturaController::class, 'facturas_show'])->name('facturas.show');
        Route::post('/facturas/actualizar-estado', [FacturaController::class, 'actualizar_estado'])->name('factura.actualizarestado');

        Route::get('/smtp-settings', [ConfiguracionesController::class, 'smtp_settings'])->name('smtp_settings.index');
        Route::post('/smtp-settings/update', [ConfiguracionesController::class, 'update_smtp'])->name('smtp_settings.update');
        Route::post('/test/smtp', [ConfiguracionesController::class, 'testEmail'])->name('test.smtp');
        Route::get('/asignar-claves', [ConfiguracionesController::class, 'asignar_claves'])->name('asignar_claves');
        Route::get('/asignar-direcciones', [ConfiguracionesController::class, 'asignar_direcciones'])->name('asignar_direcciones');
    });
}
