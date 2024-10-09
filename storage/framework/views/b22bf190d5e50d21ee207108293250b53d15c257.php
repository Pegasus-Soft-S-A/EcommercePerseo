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
                                                <td align="center"
                                                    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                                    <?php
                                                    $base = Request::segment(1);
                                                    ?>
                                                    <?php if($base): ?>
                                                    <img src="<?php echo e($message->embed(public_path() . '/assets/img/logo-'.$base.'.png')); ?>"
                                                        alt="" width="500" height="100"
                                                        style="display: block; margin-left: auto; margin-right: auto;">
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <td class="grid__col"
                                                    style="font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif; padding: 32px 40px; border-radius: 6px 6px 0 0;"
                                                    align="">
                                                    <h2 style="color: #404040; font-weight: 300; margin: 0 0 12px 0; font-size: 24px; line-height: 30px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                        class="">Hola, <strong><?php echo e($array['cliente']); ?>.</strong> Su pedido
                                                        ha sido
                                                        receptado y se procederá a realizar su despacho lo más pronto
                                                        posible.</h2>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="table__ridge table__ridge--top"><img
                                                        src="http://login.sendpulse.com/files/emailservice/userfiles/23ab6c2c08dcd7c46f80036f928853757242085/ridges_top_fullx2.jpg"
                                                        alt="eventbrite" height="7"
                                                        style="height: 7px; border: none; display: block;" border="0">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="grid__col"
                                                    style="font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif; padding: 32px 40px; background-color: #ededed;">
                                                    <table cellpadding="0" cellspacing="0" border="0"
                                                        style="width: 100%; margin-bottom: 12px;"
                                                        class="no_text_resize">
                                                        <tbody>
                                                            <tr>
                                                                <td style="border-bottom: 1px dashed #d3d3d3;">
                                                                    <h2 style="color: #404040; font-weight: 300; margin: 0 0 12px 0; font-size: 24px; line-height: 30px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                        class="">Resumen del pedido</h2>
                                                                </td>
                                                                <td colspan="2"
                                                                    style="text-align: right; border-bottom: 1px dashed #d3d3d3;">
                                                                    <div style="color: #666666; font-weight: 400; font-size: 13px; line-height: 18px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                        class=""><span
                                                                            style="font-size: 16px;"><strong><?php echo e($pedido->emision); ?></strong></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">
                                                                    <p style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif; margin-bottom: 18px;"
                                                                        class=""><span style="font-size: 16px;">Nº de
                                                                            pedido:
                                                                            <strong><?php echo e($pedido->pedidos_codigo); ?></strong></span>
                                                                    </p>
                                                                    <table cellpadding="0" cellspacing="0" border="0"
                                                                        style="width: 100%;">
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
                                                                            <?php
                                                                            $detallepedido=\App\Models\PedidosDetalles::where('pedidos_detalles.pedidosid',$pedido->pedidosid)->get();
                                                                            ?>
                                                                            <?php $__currentLoopData = $detallepedido; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                            $producto =
                                                                            \App\Models\PedidosDetalles::select('productos.descripcion
                                                                            as producto', 'medidas.descripcion')
                                                                            ->join('medidas', 'medidas.medidasid', '=',
                                                                            'pedidos_detalles.medidasid')
                                                                            ->join('productos', 'productos.productosid',
                                                                            '=', 'pedidos_detalles.productosid')
                                                                            ->where('pedidos_detalles.productosid',
                                                                            $detalle->productosid)
                                                                            ->where('pedidos_detalles.medidasid',
                                                                            $detalle->medidasid)
                                                                            ->where('pedidos_detalles.pedidosid',
                                                                            $pedido->pedidosid)
                                                                            ->first();
                                                                            ?>
                                                                            <tr>

                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 69%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><?php echo e($producto->producto); ?>

                                                                                    </div>
                                                                                </td>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class="">
                                                                                        <?php echo e($producto->descripcion); ?>

                                                                                    </div>
                                                                                </td>
                                                                                <td
                                                                                    style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class="">
                                                                                        <?php echo e(number_format(round($detalle->cantidaddigitada,2),2)); ?>

                                                                                    </div>
                                                                                </td>
                                                                                <td
                                                                                    style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class="">
                                                                                        <?php echo e(number_format(round($detalle->preciovisible,2),2)); ?>

                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                            <tr>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Subtotal
                                                                                                Con IVA</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td
                                                                                    style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong><?php echo e(number_format(round($pedido->subtotalconiva,2),2)); ?></strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Subtotal
                                                                                                Sin IVA</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td
                                                                                    style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong><?php echo e(number_format(round($pedido->subtotalsiniva,2),2)); ?></strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Descuento</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td
                                                                                    style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong><?php echo e(number_format(round($pedido->total_descuento,2),2)); ?></strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Total
                                                                                                IVA</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td
                                                                                    style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong><?php echo e(number_format(round($pedido->total_iva,2),2)); ?></strong></span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 23%;">
                                                                                </td>

                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 132.865%;">
                                                                                </td>
                                                                                <td
                                                                                    style="padding: 12px 3px 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong>Total</strong></span>
                                                                                    </div>
                                                                                </td>
                                                                                <td
                                                                                    style="text-align: right; padding: 12px 0px; width: 10%;">
                                                                                    <div style="color: #666666; font-weight: 400; font-size: 15px; line-height: 21px; font-family: 'Benton Sans', -apple-system, BlinkMacSystemFont, Roboto, 'Helvetica neue', Helvetica, Tahoma, Arial, sans-serif;"
                                                                                        class=""><span
                                                                                            style="font-size: 16px;"><strong><?php echo e(number_format(round($pedido->total,2),2)); ?></strong></span>
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
                                                        alt="eventbrite" height="7"
                                                        style="height: 7px; border: none; display: block;" border="0"
                                                        width="600"></td>
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

</html><?php /**PATH C:\laragon\www\tienda\resources\views/emails/pedido.blade.php ENDPATH**/ ?>