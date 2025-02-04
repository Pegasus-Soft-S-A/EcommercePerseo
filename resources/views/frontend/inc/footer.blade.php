<section class="bg-white border-top mt-auto">
    <div class="container">
        <div class="row no-gutters">
            <div class="col-lg-3 col-md-6">
                <a class="text-reset border-left text-center p-4 d-block" href="{{route('terminos_condiciones')}}">
                    <i class="la la-file-text la-3x text-primary mb-2"></i>
                    <h4 class="h6">Términos y Condiciones</h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a class="text-reset border-left text-center p-4 d-block" href="{{ route('politicas_devoluciones') }}">
                    <i class="la la-mail-reply la-3x text-primary mb-2"></i>
                    <h4 class="h6">Política de devoluciones</h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a class="text-reset border-left text-center p-4 d-block" href="{{route('politicas_soporte')}}">
                    <i class="la la-support la-3x text-primary mb-2"></i>
                    <h4 class="h6">Política de Soporte</h4>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a class="text-reset border-left border-right text-center p-4 d-block"
                    href="{{route('politicas_privacidad')}}">
                    <i class="las la-exclamation-circle la-3x text-primary mb-2"></i>
                    <h4 class="h6">Política de Privacidad</h4>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-dark py-5 text-light footer-widget">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-xl-4 text-center text-md-left">
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="d-block">
                        <img class="lazyload"
                            src="@if (get_setting('footer_logo')!=null) data:image/jpg;base64,{{ get_setting('footer_logo') }} @endif"
                            height="44">
                    </a>
                    <div class="my-3">
                        @php
                        echo get_setting('acerca_nosotros');
                        @endphp
                    </div>
                </div>
            </div>
            <div class="col-lg-3 ml-xl-auto col-md-4 mr-0">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                        DATOS DE CONTACTO
                    </h4>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <span class="d-block opacity-30">Dirección:</span>
                            <span class="d-block opacity-70">{{get_setting('direccion_contacto')}}</span>
                        </li>
                        <li class="mb-2">
                            <span class="d-block opacity-30">Teléfono:</span>
                            <span class="d-block opacity-70">{{get_setting('telefono_contacto')}}</span>
                        </li>
                        <li class="mb-2">
                            <span class="d-block opacity-30">Email:</span>
                            <span class="d-block opacity-70">
                                <a href="mailto:" class="text-reset">{{get_setting('email_contacto')}}</a>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                    </h4>
                    <ul class="list-unstyled">
                    </ul>
                </div>
            </div>

            <div class="col-md-4 col-lg-2">
                <div class="text-center text-md-left mt-4">
                    <h4 class="fs-13 text-uppercase fw-600 border-bottom border-gray-900 pb-2 mb-4">
                        Mi cuenta
                    </h4>
                    <ul class="list-unstyled">
                        @auth
                        <li class="mb-2">
                            <a class="opacity-50 hov-opacity-100 text-reset" href="{{ route('logout') }}">
                                Cerrar Sesion
                            </a>
                        </li>
                        @else
                        <li class="mb-2">
                            <a class="opacity-50 hov-opacity-100 text-reset" href="{{ route('user.login') }}">
                                Iniciar Sesion
                            </a>
                        </li>
                        @endauth
                        <li class="mb-2">
                            <a class="opacity-50 hov-opacity-100 text-reset"
                                href="{{ route('purchase_history.index') }}">
                                Historial de Pedidos
                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="opacity-50 hov-opacity-100 text-reset" href="{{ route('wishlist.index') }}">
                                Mi Lista de Deseos
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="pt-3 pb-7 pb-xl-3 bg-black text-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="@if (get_setting('show_social_links')!=null) col-lg-4 @else col-lg-12 @endif">
                <div
                    class="text-center @if (get_setting('show_social_links')!=null) text-md-left @else text-md-center @endif">
                    Todos los derechos reservados. <a href="https://www.perseo.ec" target="_blank">Perseo Software</a>
                </div>
            </div>
            <div class="col-lg-4">
                <ul class="list-inline my-3 my-md-0 social colored text-center">
                    @if (get_setting('show_social_links')!=null)
                    @if ( get_setting('facebook_link') != null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('facebook_link') }}" target="_blank" class="facebook"><i
                                class="lab la-facebook-f"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('twitter_link') != null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('twitter_link') }}" target="_blank" class="twitter"><i
                                class="lab la-twitter"></i></a>
                    </li>
                    @endif
                    @if ( get_setting('instagram_link') != null )
                    <li class="list-inline-item">
                        <a href="{{ get_setting('instagram_link') }}" target="_blank" class="instagram"><i
                                class="lab la-instagram"></i></a>
                    </li>
                    @endif
                    @endif
                </ul>
            </div>
        </div>
    </div>
</footer>

<div class="aiz-mobile-bottom-nav d-xl-none  fixed-bottom bg-white shadow-lg border-top">
    <div class="d-flex justify-content-around align-items-center">
        <a href="{{ route('home') }}"
            class="text-reset flex-grow-1 text-center py-3 border-right {{ areActiveRoutes(['home'],'bg-soft-primary')}}">
            <i class="las la-home la-2x"></i>
        </a>
        <a href="@if (get_setting('vista_categorias')==1) {{ route('categories.all') }} @else {{ route('search') }} @endif"
            class="text-reset flex-grow-1 text-center py-3 border-right {{ areActiveRoutes(['search'],'bg-soft-primary')}}">
            <span class="d-inline-block position-relative px-2">
                <i class="las la-list-ul la-2x"></i>
            </span>
        </a>
        @php
        if(auth()->user() != null) {
        if (get_setting('maneja_sucursales') == "on") {
        $cart = \App\Models\Carrito::where('clientes_sucursalesid',session('sucursalid'))->get();
        }else{
        $cart = \App\Models\Carrito::where('clientesid', Auth::user()->clientesid)->get();
        }
        } else {
        $usuario_temporalid = Session()->get('usuario_temporalid');
        if($usuario_temporalid) {
        $cart = \App\Models\Carrito::where('usuario_temporalid', $usuario_temporalid)->get();
        }
        }

        @endphp
        <a href="{{ route('cart') }}"
            class="text-reset flex-grow-1 text-center py-3 border-right {{ areActiveRoutes(['cart'],'bg-soft-primary')}}">
            <span class="d-inline-block position-relative px-2">
                <i class="las la-shopping-cart la-2x"></i>
                @if(isset($cart) && count($cart) > 0)
                <span class="badge badge-circle badge-primary position-absolute absolute-top-right"
                    id="cart_items_sidenav">{{ count($cart)}}</span>
                @else
                <span class="badge badge-circle badge-primary position-absolute absolute-top-right"
                    id="cart_items_sidenav">0</span>
                @endif
            </span>
        </a>

        @auth
        <a href="javascript:void(0)" class="text-reset flex-grow-1 text-center py-2 mobile-side-nav-thumb"
            data-toggle="class-toggle" data-target=".aiz-mobile-side-nav">
            <span class="avatar avatar-sm d-block mx-auto">
                <img src="{{ Auth::user()->getAvatarBase64($size=null) }}">
            </span>
        </a>
        @else
        <a href="{{ route('user.login') }}" class="text-reset flex-grow-1 text-center py-2">
            <span class="avatar avatar-sm d-block mx-auto">
                <img src="{{ static_asset('assets/img/avatar-place.png') }}">
            </span>
        </a>
        @endauth
    </div>
</div>

@auth
<div class="aiz-mobile-side-nav collapse-sidebar-wrap sidebar-xl d-xl-none z-1035">
    <div class="overlay dark c-pointer overlay-fixed" data-toggle="class-toggle" data-target=".aiz-mobile-side-nav"
        data-same=".mobile-side-nav-thumb"></div>
    <div class="collapse-sidebar bg-white">
        @include('frontend.inc.user_side_nav')
    </div>
</div>
@endauth