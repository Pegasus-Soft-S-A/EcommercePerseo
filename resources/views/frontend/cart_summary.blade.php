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
        <table class="table">
            <thead>
                <tr>
                    <th class="product-name">Producto</th>
                    <th class="product-total text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($carts as $key => $cartItem)
                @php
                $product = \App\Models\Producto::find($cartItem['productosid']);
                $subtotal = $subtotal + ($cartItem['precio'] * $cartItem['cantidad']);
                $descuento = $subtotal*($cartItem['descuento']/100);
                $subtotalneto = $subtotal-$descuento;
                $totalIVA = $subtotalneto*($cartItem['iva']/100);
                $total=round($subtotalneto,2)+round($totalIVA,2);

                @endphp
                <tr class="cart_item">
                    <td class="product-name">
                        {{ $product->descripcion }}
                        <strong class="product-quantity">
                            × {{ $cartItem['cantidad'] }}
                        </strong>
                    </td>
                    <td class="product-total text-right">
                        <span
                            class="pl-4 pr-0">{{ number_format(round($cartItem['precio'] * $cartItem['cantidad'],2),2) }}</span>
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
                        <span class="fw-600">{{ number_format(round($subtotal,2),2) }}</span>
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Descuento</th>
                    <td class="text-right">
                        <span class="fw-600">{{ number_format(round($descuento,2),2) }}</span>
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Subtotal Neto</th>
                    <td class="text-right">
                        <span class="fw-600">{{ number_format(round($subtotalneto,2),2) }}</span>
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Total IVA</th>
                    <td class="text-right">
                        <span class="fw-600">{{ number_format(round($totalIVA,2),2) }}</span>
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Total</th>
                    <td class="text-right">
                        <span class="fw-600">{{ number_format(round($total,2),2) }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>