@extends('frontend.layouts.app')
@section('content')
<div class="home-banner-area mb-4 pt-3">
    <div class="container">
        <div class="row gutters-10 position-relative">
            <div class="col-lg-3 position-static d-none d-lg-block">
                @include('frontend.partials.category_menu')
            </div>
            @php
            $featured_categories = gruposDestacados();
            $productos_oferta = productosOferta();
            $grupoid=getGrupoID();
            @endphp
            <div class="@if (count($productos_oferta)>0) col-lg-7 @else col-lg-9 @endif">
                <div class="aiz-carousel dots-inside-bottom mobile-img-auto-height" data-arrows="true" data-dots="true"
                    data-autoplay="true">
                    @if (get_setting('home_slider')!=null)
                    @foreach (json_decode(get_setting('home_slider')) as $key => $slider)
                    @if (strtotime(date('Y-m-d'))>=strtotime($slider->inicio) &&
                    strtotime(date('Y-m-d'))<=strtotime($slider->fin)) <div class="carousel-box">
                            <a href="{{$slider->link}}" target="_blank">
                                <img class="d-block mw-100 img-fit-slider rounded shadow-sm"
                                    src="data:image/jpg;base64,@if ($slider->imagen) {{ $slider->imagen }} @else {{ get_setting('imagen_defecto')}}  @endif"
                                    alt="promo" @if($featured_categories==null) height="457" @else height="315" @endif>
                            </a>
                        </div>
                        @endif
                        @endforeach
                        @endif
                </div>
                @if ($featured_categories != null)
                <ul class=" list-unstyled mb-0 row gutters-5">
                    @foreach ($featured_categories as $key => $category)
                    <li class="minw-0 col-4 col-md mt-3">
                        <a href="{{ route('products.category', $category->$grupoid) }}"
                            class="d-block rounded bg-white p-2 text-reset shadow-sm">
                            <img src="data:image/jpg;base64,@if ($category->imagen) {{ base64_encode($category->imagen) }} @else {{ get_setting('imagen_defecto') }} @endif"
                                alt="{{$category->descripcion}}" class="lazyload img-fit" height="78">

                            <div class="text-center text-truncate fs-12 fw-600 mt-2 opacity-70">
                                {{$category->descripcion}}</div>
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>


            @if (count($productos_oferta)>0)
            <div class="col-lg-2 order-3 mt-3 mt-lg-0">
                <div class="bg-white rounded shadow-sm">
                    <div class="bg-soft-primary rounded-top p-3 d-flex align-items-center justify-content-center">
                        <span class="fw-600 fs-16 text-center">
                            Ofertas Solo Hoy
                        </span>
                    </div>
                    <div class="c-scrollbar-light overflow-auto h-lg-400px p-2 bg-primary rounded-bottom">
                        <div class="gutters-5 lg-no-gutters row row-cols-2 row-cols-lg-1">
                            @foreach ($productos_oferta as $producto)
                            <div class="col mb-2">
                                <a href="{{route('product',$producto->productosid)}}"
                                    class="d-block p-2 text-reset bg-white h-100 rounded">
                                    <div class="row gutters-5 align-items-center">
                                        <div class="col-xxl">
                                            <div class="img">
                                                <img class="lazyload img-fit h-140px h-lg-80px"
                                                    src="data:image/jpg;base64,{{ base64_encode($producto->imagen) }}">
                                            </div>
                                        </div>
                                        <div class="col-xxl">
                                            <div class="fs-16">
                                                @if(Auth::check())
                                                @if ($producto->precio<$producto->precio2)
                                                    <del
                                                        class="d-block text-center opacity-70">${{number_format(round($producto->precio2,2),2)}}</del>
                                                    @endif
                                                    <span
                                                        class="d-block text-primary text-center fw-600">${{number_format(round($producto->precio,2),2)}}</span>
                                                    @else
                                                    <span
                                                        class="d-block text-primary text-center fw-600">${{number_format(round($producto->precio,2),2)}}</span>
                                                    @endif

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Featured Section --}}
<div id="section_featured">

</div>

{{-- Best Selling --}}
<div id="section_best_selling">
</div>

{{-- Top 10 categories and Brands --}}
@if(get_setting('top10_categories'))
<section class="mb-4">
    <div class="container">
        <div class="row gutters-10 ">
            <div class="col-lg-12">
                <div class="d-flex mb-3 align-items-baseline border-bottom">
                    <h3 class="h5 fw-700 mb-0">
                        <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">Top 10
                            {{ucfirst(get_setting('grupo_productos'))}}</span>
                    </h3>
                    <a href="@if (get_setting('vista_categorias')==1) {{ route('categories.all') }} @else {{ route('search') }} @endif"
                        class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">Ver
                        todas las
                        {{ucfirst(get_setting('grupo_productos'))}}</a>
                </div>
                <div class="row gutters-5">
                    @php
                    $top10_categories = get_setting('top10_categories');
                    @endphp
                    @foreach ($top10_categories as $key => $value)
                    @php
                    $grupo = get_setting('grupo_productos');
                    $grupoid = getGrupoID();
                    $grupos = "";

                    switch ($grupo) {
                    case 'lineas':
                    $grupos = \App\Models\Lineas::where($grupoid,$value)->first();
                    break;
                    case 'categorias':
                    $grupos = \App\Models\Categorias::where($grupoid,$value)->first();
                    break;
                    case 'subcategorias':
                    $grupos = \App\Models\Subcategorias::where($grupoid,$value)->first();
                    break;
                    case 'subgrupos':
                    $grupos = \App\Models\Subgrupos::where($grupoid,$value)->first();
                    break;
                    default:
                    break;
                    }
                    @endphp
                    @if ($grupos != null)
                    <div class="col-sm-6">
                        <a href="{{ route('products.category', $grupos->$grupoid) }}"
                            class="bg-white border d-block text-reset rounded p-2 hov-shadow-md mb-2">
                            <div class="row align-items-center no-gutters">
                                <div class="col-3 text-center">
                                    <img src="data:image/jpg;base64,{{ base64_encode($grupos->imagen) }}"
                                        onerror="this.onerror=null;this.src='data:image/jpg;base64,{{ get_setting('imagen_defecto') }}';"
                                        alt="Teconologia" class="img-fluid img lazyload h-60px">
                                </div>
                                <div class="col-7">
                                    <div class="text-truncat-2 pl-3 fs-14 fw-600 text-left">
                                        {{$grupos->descripcion}}
                                    </div>
                                </div>
                                <div class="col-2 text-center">
                                    <i class="la la-angle-right text-primary"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@endsection

@section('script')
<script>
    $(document).ready(function(){
            $.post('{{ route('home.section.featured') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#section_featured').html(data);
                AIZ.plugins.slickCarousel();
            });
            $.post('{{ route('home.section.best_selling') }}', {_token:'{{ csrf_token() }}'}, function(data){
                $('#section_best_selling').html(data);
                AIZ.plugins.slickCarousel();
            });

        });
</script>
@endsection