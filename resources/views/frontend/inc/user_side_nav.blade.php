<div class="aiz-user-sidenav-wrap pt-4 position-relative z-1 shadow-sm">
    <div class="absolute-top-right d-xl-none">
        <button class="btn btn-sm p-2" data-toggle="class-toggle" data-target=".aiz-mobile-side-nav"
            data-same=".mobile-side-nav-thumb">
            <i class="las la-times la-2x"></i>
        </button>
    </div>
    <div class="absolute-top-left d-xl-none">
        <a class="btn btn-sm p-2" href="{{ route('logout') }}">
            <i class="las la-sign-out-alt la-2x"></i>
        </a>
    </div>
    <div class="aiz-user-sidenav rounded overflow-hidden  c-scrollbar-light">
        <div class="px-4 text-center mb-5">
            <br>
            <h4 class="h5 fw-600">{{ Auth::user()->razonsocial }}</h4>
        </div>

        <div class="sidemnenu mb-3">
            <ul class="aiz-side-nav-list" data-toggle="aiz-side-menu">

                <li class="aiz-side-nav-item">
                    <a href="{{ route('dashboard') }}" class="aiz-side-nav-link {{ areActiveRoutes(['dashboard'])}}">
                        <i class="las la-home aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Tablero</span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('purchase_history.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['purchase_history.index'])}}">
                        <i class="las la-file-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Historial de Pedidos</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('factura_history.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['factura_history.index'])}}">
                        <i class="las la-file aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Historial de Facturas</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('wishlist.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['wishlist.index'])}}">
                        <i class="la la-heart-o aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Lista de Deseos</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('estado_cartera') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['estado_cartera'])}}">
                        <i class="la la-wallet aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Estado de Cartera</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('profile') }}" class="aiz-side-nav-link {{ areActiveRoutes(['profile'])}}">
                        <i class="las la-user aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">Administrar Perfil</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>