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
                @foreach ($carts as $key => $cartItem)
                    <tr class="cart_item">
                        <td class="product-name">
                            {{ $cartItem['producto_descripcion'] }}
                            <strong class="product-quantity">
                                × {{ round($cartItem['cantidad'], 2) }}
                            </strong>
                        </td>
                        <td class="product-total text-right">
                            <span
                                class="pl-4 pr-0">{{ number_format(round($cartItem['precio_visible'] * $cartItem['cantidad'], 2), 2) }}</span>
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
                        <span class="fw-600">{{ $totales['subtotal'] }}</span>
                        <input type="hidden" value="{{ $totales['subtotal'] }}" name="subtotal" id="subtotal">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Descuento</th>
                    <td class="text-right">
                        <span class="fw-600">{{ $totales['descuento'] }}</span>
                        <input type="hidden" value="{{ $totales['descuento'] }}" name="descuento" id="descuento">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Subtotal Neto</th>
                    <td class="text-right">
                        <span class="fw-600">{{ $totales['subtotalNeto'] }}</span>
                        <input type="hidden" value="{{ $totales['subtotalNeto'] }}" name="subtotalneto" id="subtotalneto">
                        <input type="hidden" value="{{ $totales['subtotalNetoConIva'] }}" name="subtotalnetoconiva" id="subtotalnetoconiva">
                        <input type="hidden" value="{{ $totales['subtotalNetoSinIva'] }}" name="subtotalnetosiniva" id="subtotalnetosiniva">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>IVA</th>
                    <td class="text-right">
                        <span class="fw-600">{{ $totales['totalIVA'] }}</span>
                        <input type="hidden" value="{{ $totales['totalIVA'] }}" name="totalIVA" id="totalIVA">
                        <input type="hidden" value="0" name="inputCero" id="inputCero">
                    </td>
                </tr>
                <tr class="cart-subtotal">
                    <th>Total</th>
                    <td class="text-right">
                        <span class="fw-600">{{ $totales['total'] }}</span>
                        <input type="hidden" value="{{ $totales['total'] }}" name="total" id="total">
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
