<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"><!-- Turn off iOS phone number autodetect -->
    <meta name="format-detection" content="telephone=no">
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
                                                <td
                                                    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                                    @php
                                                    $base = Request::segment(1);
                                                    @endphp
                                                    @if ($base)
                                                    <img src="{{ $message->embed(public_path() . '/assets/img/logo-'.$base.'.png') }}"
                                                        alt="" width="500" height="100"
                                                        style="display: block; margin-left: auto; margin-right: auto;">
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <td class="grid__col"
                                                    style="font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif; padding: 32px 40px; border-radius: 6px 6px 0 0;"
                                                    align="">
                                                    <h2 style="color: #404040; font-weight: 300; margin: 0 0 12px 0; font-size: 24px; line-height: 30px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                        class="">Hola, {{$array['razonsocial']}}. Gracias por
                                                        registrarte a nuestra
                                                        tienda online.
                                                    </h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" class="grid__col"
                                                    style="font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif; padding: 32px 40px; background-color: #ededed;">
                                                    <h2 style="color: #404040; font-weight: 300; margin: 0 0 12px 0; font-size: 24px; line-height: 30px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                        class=""> Ahora puedes comenzar a realizar tus pedidos.
                                                    </h2><a href="{{ route('home') }}" target="_blank">Ir
                                                        a la Tienda</a>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p><img src="https://www.eventbrite.com/emails/action/?recipient=jhusep95%40gmail.com&amp;type_id=65&amp;type=open&amp;send_id=2018-08-20&amp;list_id=9"
                            alt="" width="1" height="1" border="0" style="border: 0;"></p>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>