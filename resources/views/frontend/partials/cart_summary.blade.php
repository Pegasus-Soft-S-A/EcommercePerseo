<div class="card border-0 shadow-sm rounded">
    <div class="card-header">
        <h3 class="fs-16 fw-600 mb-0">Resumen</h3>
        <div class="text-right">
            <span class="badge badge-inline badge-primary">
                {{ count($carts) }}
                Items
            </span>
        </div>
    </div>

    <div class="card-body">

        <div id="existencias">

        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class="product-name">Producto</th>
                    <th class="product-total text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                $parametros = \App\Models\ParametrosEmpresa::first();
                $subtotal = 0;
                $descuento = 0;
                $subtotalneto = 0;
                $subtotalnetoconiva = 0;
                $subtotalnetosiniva = 0;
                $totalIVA = 0;
                $total = 0;
                @endphp
                @foreach ($carts as $key => $cartItem)
                @php
                $product = \App\Models\Producto::find($cartItem['productosid']);
                $subtotal = $subtotal + ($cartItem['precio'] * $cartItem['cantidad']);
                $descuento = $descuento+(($cartItem['precio'] *
                $cartItem['cantidad'])*($cartItem['descuento']/100));
                $subtotalneto = $subtotalneto+($cartItem['precio'] *
                $cartItem['cantidad'])-(($cartItem['precio'] *
                $cartItem['cantidad'])*($cartItem['descuento']/100));

                if ($cartItem['iva']>0){
                $subtotalnetoconiva = $subtotalnetoconiva+($cartItem['precio'] *
                $cartItem['cantidad'])-(($cartItem['precio'] *
                $cartItem['cantidad'])*($cartItem['descuento']/100));
                $totalIVA = $subtotalnetoconiva*($cartItem['iva']/100);
                }else{
                $subtotalnetosiniva = $subtotalnetosiniva+($cartItem['precio'] *
                $cartItem['cantidad'])-(($cartItem['precio'] *
                $cartItem['cantidad'])*($cartItem['descuento']/100));
                }
                $total=$subtotalneto+$totalIVA;
                $preciovisible= \App\Models\ParametrosEmpresa::first()->tipopresentacionprecios == 1 ?
                $cartItem['precioiva'] : $cartItem['precio'];
                @endphp
                <tr class="cart_item">
                    <td class="product-name">
                        {{ $product->descripcion }}
                        <strong class="product-quantity">
                            × {{ round($cartItem['cantidad'],2) }}
                        </strong>
                    </td>
                    <td class="product-total text-right">
                        <span
                            class="pl-4 pr-0">{{ number_format(round( $preciovisible * $cartItem['cantidad'],2),2) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="table">

            <tfoot>
                <tr class="cart-subtotal">
                    <th>Subtotal</th>
                    <td class="text-right">
                        <span
                            class="fw-600">{{ number_format(round($subtotal,$parametros->fdv_subtotales),$parametros->fdv_subtotales) }}</span>
                        <input type="hidden" value="{{round($subtotal,$parametros->fdv_subtotales)}}" name="subtotal"
                            id="subtotal">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Descuento</th>
                    <td class="text-right">
                        <span class="fw-600">{{ number_format(round($descuento,2),2) }}</span>
                        <input type="hidden" value="{{$descuento}}" name="descuento" id="descuento">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Subtotal Neto</th>
                    <td class="text-right">
                        <span
                            class="fw-600">{{ number_format(round($subtotalneto,$parametros->fdv_subtotales),$parametros->fdv_subtotales) }}</span>
                        <input type="hidden" value="{{round($subtotalneto,$parametros->fdv_subtotales)}}"
                            name="subtotalneto" id="subtotalneto">
                        <input type="hidden" value="{{round($subtotalnetoconiva,$parametros->fdv_subtotales)}}"
                            name="subtotalnetoconiva" id="subtotalnetoconiva">
                        <input type="hidden" value="{{round($subtotalnetosiniva,$parametros->fdv_subtotales)}}"
                            name="subtotalnetosiniva" id="subtotalnetosiniva">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>IVA</th>
                    <td class="text-right">
                        <span
                            class="fw-600">{{ number_format(round($totalIVA,$parametros->fdv_iva),$parametros->fdv_iva) }}</span>
                        <input type="hidden" value="{{round($totalIVA,$parametros->fdv_iva)}}" name="totalIVA"
                            id="totalIVA">
                        <input type="hidden" value="0" name="inputCero" id="inputCero">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Total</th>
                    <td class="text-right">
                        <span class="fw-600">{{ number_format(round($total,2),2) }}</span>
                        <input type="hidden" value="{{round($total,2)}}" name="total" id="total">
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>