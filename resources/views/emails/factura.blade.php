<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans&amp;display=swap" rel="stylesheet">
    <style type="text/css">
        body {
            display: flex !important;
            flex-direction: column !important;
            margin: 0 !important;
        }
    </style>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><!-- Turn off iOS phone number autodetect -->
    <meta name="format-detection" content="telephone=no">
    <style>
        body,
        p {
            font-family: "Benton Sans", -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", Helvetica, Tahoma, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            border: 0;
            padding: 0;
        }

        img {
            margin: 0;
            padding: 0;
        }

        .content {
            width: 600px;
        }

        .no_text_resize {
            -moz-text-size-adjust: none;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
            text-size-adjust: none;
        }

        /* Media Queries */
        @media all and (max-width: 600px) {

            table[class="content"] {
                width: 100% !important;
            }

            tr[class="grid-no-gutter"] td[class="grid__col"] {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            td[class="grid__col"] {
                padding-left: 15px !important;
                padding-right: 15px !important;
            }

            tr[class="small_full_width"] td[class="grid__col"] {
                padding-left: 0px !important;
                padding-right: 0px !important;
            }

            table[class="small_full_width"] {
                width: 100% !important;
                padding-bottom: 10px;
            }

            a[class="header-link"] {
                margin-right: 0 !important;
                margin-left: 10px !important;
            }

            a[class="btn"] {
                width: 100%;
                border-left-width: 0px !important;
                border-right-width: 0px !important;
            }

            table[class="col-layout"] {
                width: 100% !important;
            }

            td[class="col-container"] {
                display: block !important;
                width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            td[class="col-nav-items"] {
                display: inline-block !important;
                padding-left: 0 !important;
                padding-right: 10px !important;
                background: none !important;
            }

            img[class="col-img"] {
                height: auto !important;
                max-width: 520px !important;
                width: 100% !important;
            }

            td[class="col-center-sm"] {
                text-align: center;
            }

            tr[class="footer-attendee-cta"]>td[class="grid__col"] {
                padding: 24px 0 0 !important;
            }

            td[class="col-footer-cta"] {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            td[class="footer-links"] {
                text-align: left !important;
            }

            .hide-for-small {
                display: none !important;
            }

            .ribbon-mobile {
                line-height: 1.3 !important;
            }

            .small_full_width {
                width: 100% !important;
                padding-bottom: 10px;
            }

            .table__ridge {
                height: 7px !important;
            }

            .table__ridge img {
                display: none !important;
            }

            .table__ridge--top {
                background-image: url(http://login.sendpulse.com/files/emailservice/userfiles/23ab6c2c08dcd7c46f80036f928853757242085/ridges_top_fullx2.jpg) !important;
                background-size: 170% 7px;
            }

            .table__ridge--bottom {
                background-image: url(http://login.sendpulse.com/files/emailservice/userfiles/23ab6c2c08dcd7c46f80036f928853757242085/ridges_bottom_fullx2.jpg) !important;
                background-size: 170% 7px;
            }

            .summary-table__total {
                padding-right: 10px !important;
            }

            .app-cta {
                display: none !important;
            }

            .app-cta__mobile {
                width: 100% !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                float: none !important;
                display: block !important;
                margin-top: 12px !important;
                visibility: visible;
                font-size: inherit !important;
            }

            /* List Event Cards */
            .list-card__header {
                width: 130px !important;
            }

            .list-card__label {
                width: 130px !important;
            }

            .list-card__image-wrapper {
                width: 130px !important;
                height: 65px !important;
            }

            .list-card__image {
                max-width: 130px !important;
                max-height: 65px !important;
            }

            .list-card__body {
                padding-left: 10px !important;
            }

            .list-card__title {
                margin-bottom: 10px !important;
            }

            .list-card__date {
                padding-top: 0 !important;
            }
        }

        @media all and (device-width: 768px) and (device-height: 1024px) and (orientation:landscape) {
            .ribbon-mobile {
                line-height: 1.3 !important;
            }

            .ribbon-mobile__text {
                padding: 0 !important;
            }
        }

        @media all and (device-width: 768px) and (device-height: 1024px) and (orientation:portrait) {
            .ribbon-mobile {
                line-height: 1.3 !important;
            }

            .ribbon-mobile__text {
                padding: 0 !important;
            }
        }

        @media screen and (min-device-height:480px) and (max-device-height:568px),
        (min-device-width : 375px) and (max-device-width : 667px) and (-webkit-min-device-pixel-ratio : 2),
        (min-device-width : 414px) and (max-device-width : 736px) and (-webkit-min-device-pixel-ratio : 3) {

            .hide_for_iphone {
                display: none !important;
            }

            .passbook {
                width: auto !important;
                height: auto !important;
                line-height: auto !important;
                visibility: visible !important;
                display: block !important;
                max-height: none !important;
                overflow: visible !important;
                float: none !important;
                text-indent: 0 !important;
                font-size: inherit !important;
            }
        }
    </style>

    <style>
        @media (max-device-width: 640px) {
            .organized_by {
                font-size: 14px !important;
            }

            .ticket-section__or {
                padding: 12px 0 14px 0 !important;
            }

            .your-account__logo {
                margin-top: 8px;
            }
        }

        @media screen and (min-device-height:480px) and (max-device-height:568px) {
            h2 {
                font-weight: 600 !important;
            }

            .header_defer {
                font-size: 12px;
            }
        }
    </style>
    <title></title>
</head>
<!-- Global container with background styles. Gmail converts BODY to DIV so we lose properties like BGCOLOR. -->

<body border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" bgcolor="#F7F7F7" style="margin: 0;">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" bgcolor="#F7F7F7">
        <tbody>
            <tr>
                <td style="padding-right: 10px; padding-left: 10px;"></td>
            </tr>
            <tr>
                <td>
                    <table class="content" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#F7F7F7"
                        style="width: 600px; max-width: 600px;">
                        <tbody>
                            <tr>
                                <td colspan="2" style="background: #fff; border-radius: 8px;">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="center" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                                    @php
                                                        $base = Request::segment(1);
                                                    @endphp
                                                    @if ($base)
                                                        <img src="{{ url('/assets/img/logo-' . $base . '.png') }}" alt="Logo" width="500"
                                                            height="100" style="display: block; margin-left: auto; margin-right: auto;">
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <td class="grid__col"
                                                    style="font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif; padding: 32px 40px; border-radius: 6px 6px 0 0;"
                                                    align="">
                                                    <h2 style="color: #404040; font-weight: 300; margin: 0 0 12px 0; font-size: 24px; line-height: 30px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                        class="">Hola, <strong>{{ $array['cliente'] }}.</strong> Su pedido
                                                        ha sido
                                                        receptado y se procederá a realizar su despacho lo más pronto
                                                        posible.</h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table__ridge table__ridge--top"><img
                                                        src="http://login.sendpulse.com/files/emailservice/userfiles/23ab6c2c08dcd7c46f80036f928853757242085/ridges_top_fullx2.jpg"
                                                        alt="eventbrite" height="7" style="height: 7px; border: none; display: block;"
                                                        border="0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="grid__col"
                                                    style="font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif; padding: 32px 40px; background-color: #ededed;">
                                                    <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-bottom: 12px;"
                                                        class="no_text_resize">
                                                        <tbody>
                                                            <tr>
                                                                <td style="border-bottom: 1px dashed #d3d3d3;">
                                                                    <h2 style="color: #404040; font-weight: 300; margin: 0 0 12px 0; font-size: 24px; line-height: 30px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                        class="">Resumen del pedido</h2>
                                                                </td>
                                                                <td colspan="2" style="text-align: right; border-bottom: 1px dashed #d3d3d3;">
                                                                    <div style="color: #666666; font-weight: 400; font-size: 13px; line-height: 18px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                        class=""><span
                                                                            style="font-size: 16px;"><strong>{{ $factura->emision }}</strong></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">
                                                                    <p style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif; margin-bottom: 18px;"
                                                                        class=""><span style="font-size: 16px;">Secuencial:
                                                                            <strong>{{ $factura->establecimiento . '-' . $factura->puntoemision . '-' . $factura->secuencial }}</strong></span>
                                                                    </p>
                                                                    <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                                                        <thead>
                                                                            <tr>

                                                                                <th
                                                                                    style="border-bottom: 1px dashed #d3d3d3; text-align: left; padding-bottom: 12px; padding-right: 12px; width: 69%;">
                                                                                    <div style="color: #666666; font-weight: 500; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Descripcion</strong></span>
                                                                                    </div>
                                                                                </th>
                                                                                <th
                                                                                    style="border-bottom: 1px dashed #d3d3d3; text-align: left; padding-bottom: 12px; padding-right: 12px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 500; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Medida</strong></span>
                                                                                    </div>
                                                                                </th>
                                                                                <th
                                                                                    style="border-bottom: 1px dashed #d3d3d3; text-align: left; padding-bottom: 12px; padding-right: 12px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 500; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Cant.</strong></span>
                                                                                    </div>
                                                                                </th>
                                                                                <th
                                                                                    style="border-bottom: 1px dashed #d3d3d3; text-align: right; padding-bottom: 12px; padding-right: 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 500; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Precio</strong></span>
                                                                                    </div>
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @php
                                                                                $detallepedido = \App\Models\FacturasDetalles::where(
                                                                                    'facturas_detalles.facturasid',
                                                                                    $factura->facturasid,
                                                                                )->get();
                                                                            @endphp
                                                                            @foreach ($detallepedido as $detalle)
                                                                                @php
                                                                                    $producto = \App\Models\FacturasDetalles::select(
                                                                                        'productos.descripcion
                                                                            as producto',
                                                                                        'medidas.descripcion',
                                                                                    )
                                                                                        ->join(
                                                                                            'medidas',
                                                                                            'medidas.medidasid',
                                                                                            '=',
                                                                                            'facturas_detalles.medidasid',
                                                                                        )
                                                                                        ->join(
                                                                                            'productos',
                                                                                            'productos.productosid',
                                                                                            '=',
                                                                                            'facturas_detalles.productosid',
                                                                                        )
                                                                                        ->where(
                                                                                            'facturas_detalles.productosid',
                                                                                            $detalle->productosid,
                                                                                        )
                                                                                        ->where('facturas_detalles.medidasid', $detalle->medidasid)
                                                                                        ->where('facturas_detalles.facturasid', $factura->facturasid)
                                                                                        ->first();

                                                                                @endphp
                                                                                <tr>

                                                                                    <td style="padding: 12px 3px 12px 0px; width: 69%;">
                                                                                        <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                            class="">{{ $producto->producto }}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                        <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                            class="">
                                                                                            {{ $producto->descripcion }}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                        <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                            class="">
                                                                                            {{ number_format(round($detalle->cantidaddigitada, 2), 2) }}
                                                                                        </div>
                                                                                    </td>
                                                                                    <td style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                        <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                            class="">
                                                                                            {{ number_format(round($detalle->precio, 2), 2) }}
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                            <tr>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Subtotal
                                                                                                Con IVA</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>{{ number_format(round($factura->subtotalconiva, 2), 2) }}</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Subtotal
                                                                                                Sin IVA</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>{{ number_format(round($factura->subtotalsiniva, 2), 2) }}</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Descuento</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>{{ number_format(round($factura->total_descuento, 2), 2) }}</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span style="font-size: 16px;"><strong>Total
                                                                                                IVA</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>{{ number_format(round($factura->total_iva, 2), 2) }}</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Total</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>{{ number_format(round($factura->total, 2), 2) }}</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table__ridge table__ridge--bottom"><img
                                                        src="http://login.sendpulse.com/files/emailservice/userfiles/23ab6c2c08dcd7c46f80036f928853757242085/ridges_bottom_fullx2.jpg"
                                                        alt="eventbrite" height="7" style="height: 7px; border: none; display: block;"
                                                        border="0" width="600"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!--[if (gte mso 9)|(IE)]>
                </td>
            </tr>
        </table>
        <![endif]-->
                    <!--[if (gte mso 9)|(IE)]>
<table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
<![endif]-->
                    <!--[if (gte mso 9)|(IE)]>
        </td>
    </tr>
</table>
<![endif]--><img src="https://www.eventbrite.com/emails/action/?recipient=jhusep95%40gmail.com&amp;type_id=65&amp;type=open&amp;send_id=2018-08-20&amp;list_id=9"
                        alt="" width="1" height="1" border="0" style="border: 0;">
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
