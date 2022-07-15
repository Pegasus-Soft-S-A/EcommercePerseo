<div class="aiz-card-box border border-light rounded hov-shadow-md mt-1 mb-2 has-transition bg-white">
    <div class="position-relative">
        <a href="{{route('product',$product->productosid)}}" class="d-block">
            <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                src="data:image/jpg;base64,@if ($product->imagen) {{ base64_encode($product->imagen) }} @else {{ get_setting('imagen_defecto') }} @endif"
                alt="{{$product->descripcion}}">
        </a>
        <div class="absolute-top-right aiz-p-hov-icon">
            <a href="javascript:void(0)" onclick="addToWishList({{ $product->productosid}})" data-toggle="tooltip"
                data-title="" data-placement="left">
                <i class="la la-heart-o"></i>
            </a>
            @if (get_setting('tipo_tienda')=='publico')
            <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->productosid}})" data-toggle="tooltip"
                data-title="" data-placement="left">
                <i class="las la-shopping-cart"></i>
            </a>
            @else
            @auth
            <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->productosid}})" data-toggle="tooltip"
                data-title="" data-placement="left">
                <i class="las la-shopping-cart"></i>
            </a>
            @endauth
            @endif
        </div>
    </div>
    <div class="p-md-3 p-2 text-left">
        <div class="fs-15">
            @if(Auth::check())
            @if ($product->precio<$product->precio2)
                <del class="fw-600 opacity-50 mr-1">${{ number_format(round($product->precio2,2),2) }}</del>
                @endif
                <span class="fw-700 text-primary">${{ number_format(round($product->precio,2),2) }}</span>
                @else
                @if (get_setting('tipo_tienda')=='publico')
                <span class="fw-700 text-primary">${{ number_format(round($product->precio,2),2) }}</span>
                @endif
                @endif
        </div>
        <div class="rating rating-sm mt-1">
            {{ renderStarRating(json_decode($product->parametros_json)->rating) }}
        </div>
        <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
            <a href="{{route('product',$product->productosid)}}"
                class="d-block text-reset">@if(get_setting('ver_codigo')==1) {{$product->productocodigo}}-@endif
                {{$product->descripcion }}</a>
        </h3>


    </div>
</div>