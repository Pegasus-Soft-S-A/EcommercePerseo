<!DOCTYPE html>
<html lang="ES">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">
    <title>{{ get_setting('nombre_sitio') . ' | ' . get_setting('lema_sitio') }}</title>
    <meta property="fb:app_id" content="{{ get_setting('FACEBOOK_PIXEL_ID') }}">
    @yield('meta')


    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/jpg;base64,{{ get_setting('icono_sitio') }}">

    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
        rel="stylesheet">

    <!-- CSS Files -->
    {{--
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}"> --}}
    <link href="{{ static_asset('css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <script>
        var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: 'Nada seleccionado',
            nothing_found: 'No se han encontrado registros',
            choose_file: 'Escoja un archivo',
            file_selected: 'Archivo seleccionado',
            files_selected: 'Archivos seleccionados',
            add_more_files: 'Añadir mas archivos',
            adding_more_files: 'Añadiendo archivos',
            drop_files_here_paste_or: 'Soltar archivos aqui',
            browse: 'Navegar',
            upload_complete: 'Carga completa',
            upload_paused: 'Carga pausada',
            resume_upload: 'Continuar con la carga',
            pause_upload: 'Pausar carga',
            retry_upload: 'Reintentar carga',
            cancel_upload: 'Carga cancelada',
            uploading: 'Subiendo',
            processing: 'Procesando',
            complete: 'Completo',
            file: 'Archivo',
            files: 'Archivos',
        }
    </script>

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
        }

        :root {
            --primary: @php echo get_setting('color_sitio') @endphp;

            --hov-primary: @php echo get_setting('color_hover_sitio') @endphp;

            --soft-primary: @php echo hex2rgba(get_setting('color_sitio'), .15) @endphp;
        }
    </style>

    @if (get_setting('google_analytics') == 1)
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ get_setting('TRACKING_ID') }}"></script>

    <script>
        window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ get_setting('TRACKING_ID') }}');
    </script>
    @endif

    @if (get_setting('facebook_pixel') == 1)
    <!-- Facebook Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ get_setting('FACEBOOK_PIXEL_ID') }}');
            fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ get_setting('FACEBOOK_PIXEL_ID') }}&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Facebook Pixel Code -->
    @endif

    @php
    echo get_setting('header_script');
    @endphp

</head>

<body>
    <div class="aiz-main-wrapper d-flex flex-column">


        @include('frontend.inc.nav')
        @yield('content')
        @include('frontend.inc.footer')

    </div>
    @include('frontend.partials.modal')

    <div class="modal" id="addToCart">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
            role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader text-center p-3">
                    <i class="las la-spinner la-spin la-3x"></i>
                </div>
                <button type="button" class="close absolute-top-right btn-icon close z-1" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true" class="la-2x">&times;</span>
                </button>
                <div id="addToCart-modal-body">

                </div>
            </div>
        </div>
    </div>
    @if (get_setting('controla_stock') == 2 && Auth::check())
    <div class="modal almacen" id="modal-almacen" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">

                    <h4 class="modal-title" id="myModalLabel">Cambiar Sucursal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered table-head-custom table-hover w-100 text-center"
                        id="kt_datatable">
                        <thead>
                            <tr>

                                <th>id</th>
                                <th data-priority="1">Almacen</th>
                                <th data-priority="2">Existencias</th>
                                <th data-priority="3">Accion</th>

                            </tr>
                        </thead>
                    </table>
                </div>

                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    @endif

    @if (get_setting('controla_stock') == 2 && Auth::check())
    <style>
        .modal:nth-of-type(even) {
            z-index: 1052 !important;
        }

        .modal-backdrop.show:nth-of-type(even) {
            z-index: 1051 !important;
        }
    </style>
    @endif

    @yield('modal')

    {{-- <script src="{{ static_asset('assets/js/vendors.js') }}"></script> --}}
    {{-- <script src="{{ static_asset('assets/js/aiz-core.js') }}"></script> --}}
    <script src="{{ static_asset('js/plugins.js') }}"></script>

    @if (get_setting('pago_plux') == 'on')
    <script src="https://paybox.pagoplux.com/paybox/index.js"></script>
    @endif
    <script>
        @foreach (session('flash_notification', collect())->toArray() as $message)
            AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach

        function addToWishList(id) {
            @if (Auth::check())
                $.post('{{ route('wishlist.store') }}', {
                    _token: AIZ.data.csrf,
                    id: id
                }, function(data) {
                    if (data != 0) {
                        $('#wishlist').html(data);
                        AIZ.plugins.notify('success', "Item añadido correctamente a la lista de deseos.");
                    } else {
                        AIZ.plugins.notify('warning', "Por favor inicie sesion primero.");
                    }
                });
            @else
                AIZ.plugins.notify('warning', "Por favor inicie sesion primero.");
            @endif
        }

        function getVariantPrice() {

            if ($('#option-choice-form input[name=quantity]').val() > 0) {

                $.ajax({
                    type: "POST",
                    url: '{{ route('products.variant_price') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data) {
                        $('.product-gallery-thumb .carousel-box').each(function(i) {
                            if ($(this).data('medidasid') && data.medidasid == $(this).data(
                                    'medidasid')) {
                                $('.product-gallery-thumb').slick('slickGoTo', i);
                                return false
                            }
                        })
                        let preciovisible =
                            {{ \App\Models\ParametrosEmpresa::first()->tipopresentacionprecios }};
                        if (preciovisible == 1) {
                            $('#precio').html('$ ' + parseFloat(data.precioiva).toFixed(2));
                        } else {
                            $('#precio').html('$ ' + parseFloat(data.precio).toFixed(2));
                        }
                        if (data.factor != 0) {
                            var cantidadFinal = data.cantidad / data.factor;

                        } else {
                            var cantidadFinal = data.cantidad;
                        }
                        $('#precionormal').html('$ ' + parseFloat(data.precionormal).toFixed(2));
                        $('#option-choice-form #chosen_price_div').removeClass('d-none');
                        $('#preciocompleto').val(parseFloat(data.precio));
                        $('#precioiva').val(parseFloat(data.precioiva));
                        $('#factor').val(parseFloat(data.factor));
                        $('#cantidad').html(parseFloat(cantidadFinal).toFixed(2));
                        $('#existencias').val(parseFloat(cantidadFinal).toFixed(2));

                        if ("{{ get_setting('controla_stock') != 0 }}") {
                            $('#botonMas').attr("max", parseFloat(cantidadFinal).toFixed(2));
                        }

                        $('#option-choice-form #chosen_price_div #chosen_price').html('$ ' + parseFloat(data
                            .total).toFixed(2));

                    }
                });
            } else {
                $('#option-choice-form input[name=quantity]').val('1');
            }
        }

        function addToCart() {
            var cantidad = $('#existencias').val()
            if (cantidad > 0 || '{{ get_setting('controla_stock') }}' == 0) {
                if (checkAddToCartValidity()) {
                    $('#addToCart').modal();
                    $('.c-preloader').show();
                    $.ajax({
                        type: "POST",
                        url: '{{ route('cart.addToCart') }}',
                        data: $('#option-choice-form').serializeArray(),
                        success: function(data) {
                            $('#addToCart-modal-body').html(null);
                            $('.c-preloader').hide();
                            $('#modal-size').removeClass('modal-lg');
                            $('#addToCart-modal-body').html(data.view);
                            updateNavCart();
                            $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) + 1);
                        }
                    });
                } else {
                    AIZ.plugins.notify('warning', 'Por favor escoja todas las opciones');
                }
            } else if ('{{ get_setting('controla_stock') }}' != 0) {
                AIZ.plugins.notify('warning', 'El producto no tiene existencias');

            }


        }

        function buyNow() {
            if (checkAddToCartValidity()) {
                $('#addToCart-modal-body').html(null);
                $('#addToCart').modal();
                $('.c-preloader').show();
                $.ajax({
                    type: "POST",
                    url: '{{ route('cart.addToCart') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data) {
                        if (data.status == 1) {
                            updateNavCart();
                            $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) + 1);
                            window.location.replace("{{ route('cart') }}");
                        } else {
                            $('#addToCart-modal-body').html(null);
                            $('.c-preloader').hide();
                            $('#modal-size').removeClass('modal-lg');
                            $('#addToCart-modal-body').html(data.view);
                        }
                    }
                });
            } else {
                AIZ.plugins.notify('warning', 'Por favor escoja todas las opciones');
            }
        }

        $('#search').on('keyup', function() {
            search();
        });

        $('#search').on('focus', function() {
            search();
        });

        function search() {
            var searchKey = $('#search').val();
            if (searchKey.length > 0) {
                $('body').addClass("typed-search-box-shown");

                $('.typed-search-box').removeClass('d-none');
                $('.search-preloader').removeClass('d-none');
                $.post('{{ route('search.ajax') }}', {
                    _token: AIZ.data.csrf,
                    search: searchKey
                }, function(data) {
                    if (data == 0) {
                        $('#search-content').html(null);
                        $('.typed-search-box .search-nothing').removeClass('d-none').html(
                            'No se ha podido encontrar <strong>"' + searchKey + '"</strong>');
                        $('.search-preloader').addClass('d-none');

                    } else {
                        $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                        $('#search-content').html(data);
                        $('.search-preloader').addClass('d-none');
                    }
                });
            } else {
                $('.typed-search-box').addClass('d-none');
                $('body').removeClass("typed-search-box-shown");
            }
        }

        function checkAddToCartValidity() {
            var names = {};
            $('#option-choice-form input:radio').each(function() {
                names[$(this).attr('name')] = true;
            });
            var count = 0;
            $.each(names, function() {
                count++;
            });

            if ($('#option-choice-form input:radio:checked').length == count) {
                return true;
            }

            return false;
        }

        function updateNavCart() {
            $.post('{{ route('cart.nav_cart') }}', {
                _token: AIZ.data.csrf
            }, function(data) {
                $('#cart_items').html(data);
            });
        }

        function showAddToCartModal(productosid) {
            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }
            $('#addToCart-modal-body').html(null);
            $('#addToCart').modal();
            $('.c-preloader').show();
            $.post('{{ route('cart.showCartModal') }}', {
                _token: AIZ.data.csrf,
                productosid: productosid
            }, function(data) {

                $('.c-preloader').hide();
                $('#addToCart-modal-body').html(data);
                AIZ.plugins.slickCarousel();
                AIZ.plugins.zoom();
                AIZ.extra.plusMinus();
                getVariantPrice();
            });
        }

        function checkAddToCartValidity() {
            var names = {};
            $('#option-choice-form input:radio').each(function() {
                names[$(this).attr('name')] = true;
            });
            var count = 0;
            $.each(names, function() {
                count++;
            });

            if ($('#option-choice-form input:radio:checked').length == count) {
                return true;
            }

            return false;
        }

        function removeFromCart(key) {
            $.post('{{ route('cart.removeFromCart') }}', {
                _token: AIZ.data.csrf,
                id: key
            }, function(data) {
                updateNavCart();
                $('#cart-summary').html(data);
                AIZ.plugins.notify('success', 'El producto ha sido removido del carrito');
                $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html()) - 1);
            });
        }

        function cartQuantityInitialize() {
            $('.btn-number').click(function(e) {
                e.preventDefault();

                fieldName = $(this).attr('data-field');
                type = $(this).attr('data-type');
                var input = $("input[name='" + fieldName + "']");
                var currentVal = parseInt(input.val());
                if (!isNaN(currentVal)) {
                    if (type == 'minus') {

                        if (currentVal > input.attr('min')) {
                            input.val(currentVal - 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('min')) {
                            $(this).attr('disabled', true);
                        }

                    } else if (type == 'plus') {

                        if (currentVal < input.attr('max')) {
                            input.val(currentVal + 1).change();
                        }
                        if (parseInt(input.val()) == input.attr('max')) {
                            $(this).attr('disabled', true);
                        }

                    }
                } else {
                    input.val(0);
                }
            });

            $('.input-number').focusin(function() {
                $(this).data('oldValue', $(this).val());
            });

            $('.input-number').change(function() {

                minValue = parseInt($(this).attr('min'));
                maxValue = parseInt($(this).attr('max'));
                valueCurrent = parseInt($(this).val());

                name = $(this).attr('name');
                if (valueCurrent >= minValue) {
                    $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    alert('Sorry, the minimum value was reached');
                    $(this).val($(this).data('oldValue'));
                }
                if (valueCurrent <= maxValue) {
                    $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
                } else {
                    alert('Lo sentimos, se alcanzo el valor máximo');
                    $(this).val($(this).data('oldValue'));
                }


            });
            $(".input-number").keydown(function(e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                    // Allow: Ctrl+A
                    (e.keyCode == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        }

        function show_purchase_history_details(pedido_id) {
            $('#order-details-modal-body').html(null);

            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }

            $.post('{{ route('purchase_history.details') }}', {
                _token: AIZ.data.csrf,
                pedido_id: pedido_id
            }, function(data) {
                $('#order-details-modal-body').html(data);
                $('#order_details').modal();
                $('.c-preloader').hide();
            });
        }

        function show_facturas_history_details(factura_id) {
            $('#factura-details-modal-body').html(null);

            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }

            $.post('{{ route('factura_history.details') }}', {
                _token: AIZ.data.csrf,
                factura_id: factura_id
            }, function(data) {
                $('#factura-details-modal-body').html(data);
                $('#factura_details').modal();
                $('.c-preloader').hide();
            });
        }

        function detalle_documento(documentoid, secuencia) {
            $('#detalle_documento').html(null);

            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }

            $.post('{{ route('detalle_documento') }}', {
                _token: AIZ.data.csrf,
                documentoid: documentoid,
                secuencia: secuencia
            }, function(data) {
                $('#detalle_documento').html(data);
                $('#order_details').modal();
                $('.c-preloader').hide();
            });
        }

        $.extend(true, $.fn.dataTable.defaults, {
            "language": {
                "processing": "<button type='button' class='btn  spinner spinner-white spinner-right'>Cargando</button>",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "Ningún dato disponible en esta tabla",
                "infoThousands": ",",
                "loadingRecords": "Cargando...",
            }
        });
    </script>

    @yield('script')

    @php
    echo get_setting('footer_script');
    @endphp

</body>

</html>