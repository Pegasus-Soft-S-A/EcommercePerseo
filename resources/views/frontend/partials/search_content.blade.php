<div class="">
    @if (count($categories) > 0)
    <div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">
        {{get_setting('grupo_productos')}} Sugeridas</div>
    <ul class="list-group list-group-raw">
        @foreach ($categories as $key => $category)
        <li class="list-group-item py-1">
            <a class="text-reset hov-text-primary"
                href="{{ route('products.category', $category->id) }}">{{ $category->descripcion }}</a>
        </li>
        @endforeach
    </ul>
    @endif
</div>
<div class="">
    @if (count($products) > 0)
    <div class="px-2 py-1 text-uppercase fs-10 text-right text-muted bg-soft-secondary">Productos</div>
    <ul class="list-group list-group-raw">
        @foreach ($products as $key => $product)
        <li class="list-group-item">
            <a class="text-reset" href="{{ route('product', $product->productosid) }}">
                <div class="d-flex search-product align-items-center">
                    <div class="mr-3">
                        <img class="size-40px img-fit rounded"
                            src="data:image/jpg;base64,@if ($product->imagen) {{ base64_encode($product->imagen) }} @else {{ get_setting('imagen_defecto') }} @endif">
                    </div>
                    <div class="flex-grow-1 overflow--hidden minw-0">
                        <div class="product-name text-truncate fs-14 mb-5px">
                            @if(get_setting('ver_codigo')==1)
                            {{$product->productocodigo}}-@endif{{  $product->descripcion  }}
                        </div>
                        <div class="">
                            @if (Auth::check())
                            @if ($product->precio<$product->precio2)
                                <del
                                    class="fw-600  fs-16 opacity-50 mr-1">${{ number_format(round($product->precio2,2),2) }}</del>
                                @endif
                                <span class="fw-600 fs-16 text-primary">$
                                    {{ number_format(round($product->precio,2),2) }}</span>
                                @else
                                @if (get_setting('tipo_tienda')=='publico')
                                <span class="fw-600 fs-16 text-primary">$
                                    {{ number_format(round($product->precio,2),2) }}</span>
                                @endif
                                @endif
                        </div>
                    </div>
                </div>
            </a>
        </li>
        @endforeach
    </ul>
    @endif
</div>