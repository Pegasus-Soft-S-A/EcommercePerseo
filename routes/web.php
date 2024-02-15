<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\HistorialFacturasController;
use App\Http\Controllers\HistorialPedidosController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

$base = Request::segment(2);
if ($base) {
    Route::group(['prefix' => $base, 'middleware' => ['conexion:' . $base]], function () {

        // HOME
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Auth::routes();

        // /* LOGIN */
        Route::get('/users/login', [HomeController::class, 'login'])->name('user.login');
        Route::post('/users/process-login', [HomeController::class, 'process_login'])->name('login.cliente');
        Route::get('/users/registration', [HomeController::class, 'registration'])->name('user.registration');
        Route::post('/users/post-registration', [HomeController::class, 'register'])->name('register');
        Route::post('/password/reset/email/submit', [HomeController::class, 'reset_password_with_code'])->name('password.update');
        Route::get('/social-login/redirect/{provider}', [LoginController::class, 'redirectToProvider'])->name('social.login');
        Route::get('/social-login/{provider}/callback', [LoginController::class, 'handleProviderCallback'])->name('social.callback');
        Route::get('/logout', [LoginController::class, 'logout']);

        /* Validacion  */
        Route::post('/validacion', [HomeController::class, 'validacion'])->name('validacionCampos');

        // /* PRODUCTOS INDEX */
        Route::post('/home/section/featured', [HomeController::class, 'load_featured_section'])->name('home.section.featured');
        Route::post('/home/section/best_selling', [HomeController::class, 'load_best_selling_section'])->name('home.section.best_selling');

        // /* Categorias */
        Route::get('/categories', [HomeController::class, 'all_categories'])->name('categories.all');


        // /* CATEGORIA PRODUCTOS */
        Route::get('/category/{productos_categoriasid}', [HomeController::class, 'listingByCategory'])->name('products.category');

        // /* PRODUCTOS */
        Route::get('/product/{productosid}', [HomeController::class, 'product'])->name('product');

        /* TERMINOS Y CONDICIONES */
        Route::get('/terminos_condiciones', [HomeController::class, 'terminos_condiciones'])->name('terminos_condiciones');
        Route::get('/politicas_privacidad', [HomeController::class, 'politicas_privacidad'])->name('politicas_privacidad');
        Route::get('/politicas_devoluciones',  [HomeController::class, 'politicas_devoluciones'])->name('politicas_devoluciones');
        Route::get('/politicas_soporte',  [HomeController::class, 'politicas_soporte'])->name('politicas_soporte');

        // /*SEARCH*/
        Route::get('/search', [HomeController::class, 'search'])->name('search');
        Route::post('/product/variant_price',  [HomeController::class, 'variant_price'])->name('products.variant_price');
        Route::post('/ajax-search', [HomeController::class, 'ajax_search'])->name('search.ajax');

        /*CARRITO */
        Route::get('/cart', [CartController::class, 'index'])->name('cart');
        Route::post('/cart/addtocart', [CartController::class, 'addToCart'])->name('cart.addToCart');
        Route::post('/cart/nav-cart-items', [CartController::class, 'updateNavCart'])->name('cart.nav_cart');
        Route::post('/cart/removeFromCart',  [CartController::class, 'removeFromCart'])->name('cart.removeFromCart');
        Route::post('/cart/show-cart-modal', [CartController::class, 'showCartModal'])->name('cart.showCartModal');
        Route::post('/cart/updateQuantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');

        //Direcciones
        Route::resource('addresses', DireccionController::class);
        Route::post('/addresses/update/{id}', [DireccionController::class, 'update'])->name('addresses.update');
        Route::get('/addresses/destroy/{id}', [DireccionController::class, 'destroy'])->name('addresses.destroy');

        //comentarios
        Route::resource('/reviews',  ComentariosController::class);


        Route::get('invoice/{order_id}', [HistorialPedidosController::class, 'descargar_pedido'])->name('invoice.download');
        Route::get('/orders/destroy/{id}', [PedidoController::class, 'destroy'])->name('orders.destroy');

        Route::group(['middleware' => ['auth']], function () {
            //Cliente
            Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
            Route::resource('purchase_history', HistorialPedidosController::class);
            Route::post('/purchase_history/details', [HistorialPedidosController::class, 'purchase_history_details'])->name('purchase_history.details');
            Route::get('/purchase_history/destroy/{id}', [HistorialPedidosController::class, 'destroy'])->name('purchase_history.destroy');
            Route::resource('factura_history', HistorialFacturasController::class);
            Route::post('/factura_history/details', [HistorialFacturasController::class, 'factura_history_details'])->name('factura_history.details');

            Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
            Route::post('/customer/update-profile', [HomeController::class, 'update_profile'])->name('profile.update');

            //Checkout Routes
            Route::get('/checkout', [CheckoutController::class, 'get_shipping_info'])->name('checkout.shipping_info');
            Route::get('/checkout/existencias/{cliente}', [CheckoutController::class, 'verificar_existencias'])->name('verificarexistencias.shipping_info');
            Route::any('/checkout/delivery_info', [CheckoutController::class, 'store_shipping_info'])->name('checkout.store_shipping_infostore');
            Route::post('/checkout/payment_select', [CheckoutController::class, 'store_delivery_info'])->name('checkout.store_delivery_info');
            Route::post('/checkout/payment', [CheckoutController::class, 'checkout'])->name('payment.checkout');
            Route::get('/checkout/order-confirmed/{ordenid}/{clientesid}', [CheckoutController::class, 'order_confirmed'])->name('order_confirmed');

            Route::post('/checkout/factura', [CheckoutController::class, 'crear_factura'])->name('factura.crear');



            /* WISHLISTS */
            Route::resource('wishlist', WishlistController::class);
            Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');

            //estado de cartera
            Route::get('/estado-cartera', [HomeController::class, 'estado_cartera'])->name('estado_cartera');
            Route::post('/documento/detalle', [HomeController::class, 'detalle_documento'])->name('detalle_documento');

            //Verificar existencias
            Route::post('/verificar-existencia', [HomeController::class, 'verificar_existencia'])->name('verificar_existencia');
        });

        /* Validacion  */
        Route::post('/validacion', [HomeController::class, 'validacion'])->name('validacionCampos');
        Route::post('/verificar-identificacion', [HomeController::class, 'validarIdentificacion'])->name('verificar.identificacion');
        Route::post('/recuperar-Post', [HomeController::class, 'recuperarPost'])->name('recuperarInformacionPost');

        /*ALmacenes */
        Route::get('/almacenes', [HomeController::class, 'almacenes'])->name('almacenes.index');
    });
}
