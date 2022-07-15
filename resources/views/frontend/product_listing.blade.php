@extends('frontend.layouts.app')

@section('content')

<section class="mb-4 pt-3">
    <div class="container sm-px-0">
        <form class="" id="search-form" action="" method="GET">
            <div class="row">
                <div class="col-xl-3">
                    <div class="aiz-filter-sidebar collapse-sidebar-wrap sidebar-xl sidebar-right z-1035">
                        <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle"
                            data-target=".aiz-filter-sidebar" data-same=".filter-sidebar-thumb"></div>
                        <div class="collapse-sidebar c-scrollbar-light text-left">
                            <div class="d-flex d-xl-none justify-content-between align-items-center pl-3 border-bottom">
                                <h3 class="h6 mb-0 fw-600">Filtros</h3>
                                <button type="button" class="btn btn-sm p-2 filter-sidebar-thumb"
                                    data-toggle="class-toggle" data-target=".aiz-filter-sidebar">
                                    <i class="las la-times la-2x"></i>
                                </button>
                            </div>
                            <div class="bg-white shadow-sm rounded mb-3">
                                <div class="fs-15 fw-600 p-3 border-bottom">
                                    {{ucfirst(get_setting('grupo_productos'))}}
                                </div>
                                <div class="p-3">
                                    <ul class="list-unstyled">
                                        @if (!isset($id))
                                        @foreach (grupoProductos() as $category)
                                        <li class="mb-2 ml-2">
                                            <a class="text-reset fs-14"
                                                href="{{ route('products.category', $category->id) }}">{{ $category->descripcion }}</a>
                                        </li>
                                        @endforeach
                                        @else
                                        <li class="mb-2">
                                            <a class="text-reset fs-14 fw-600" href="{{route('search')}}">
                                                <i class="las la-angle-left"></i>
                                                Todas las {{ucfirst(get_setting('grupo_productos'))}}
                                            </a>
                                        </li>
                                        <li class="mb-2">
                                            <label class="text-reset fs-14 fw-600" href="#">
                                                <i class="las la-angle-down"></i>
                                                {{  $descripcion }}
                                            </label>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="bg-white shadow-sm rounded mb-3">
                                <div class="fs-15 fw-600 p-3 border-bottom">
                                    Rango de Precios
                                </div>

                                <div class="p-3">
                                    <div class="aiz-range-slider">
                                        <div id="input-slider-range" data-range-value-min="{{$preciominimo}}"
                                            data-range-value-max="{{$preciomaximo+0.99}}"></div>

                                        <div class="row mt-2">
                                            <div class="col-6">
                                                <span class="range-slider-value value-low fs-14 fw-600 opacity-70   "
                                                    @if (isset($min_price)) data-range-value-low="{{ $min_price }}"
                                                    @elseif( "{{$preciominimo  > 0 }}" )
                                                    data-range-value-low="{{$preciominimo}}" @else
                                                    data-range-value-low="0" @endif
                                                    id="input-slider-range-value-low"></span>
                                            </div>
                                            <div class="col-6 text-right">
                                                <span class="range-slider-value value-high fs-14 fw-600 opacity-70  "
                                                    @if (isset($max_price)) data-range-value-high="{{ $max_price }}"
                                                    @elseif("{{$preciomaximo  > 0 }}")
                                                    data-range-value-high="{{$preciomaximo + 0.99 }}" @else
                                                    data-range-value-high="0" @endif
                                                    id="input-slider-range-value-high"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">

                    <ul class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href="{{ route('home') }}">Inicio</a>
                        </li>
                        @if(!isset($id))
                        <li class="breadcrumb-item fw-600  text-dark">
                            <a class="text-reset" href="{{ route('search') }}">Todas las
                                {{ucfirst(get_setting('grupo_productos'))}}</a>
                        </li>
                        @else
                        <li class="breadcrumb-item opacity-50">
                            <a class="text-reset" href="{{ route('search') }}">Todas las
                                {{ucfirst(get_setting('grupo_productos'))}}</a>
                        </li>
                        @endif
                        @if(isset($id))
                        <li class="text-dark fw-600 breadcrumb-item">
                            <label class="text-reset" href="">
                                {{$descripcion}}</label>
                        </li>
                        @endif

                    </ul>

                    <div class="text-left">
                        <div class="d-flex align-items-center">
                            <div>
                                <h1 class="h6 fw-600 text-body">

                                    @if(isset($id))
                                    {{ $descripcion }}
                                    @elseif(isset($query))
                                    Buscar Resultados por "{{ $query }}"
                                    {{-- @else
                                    Todos los productos --}}
                                    @endif


                                </h1>
                            </div>
                            <div class="d-xl-none ml-auto ml-xl-3 mr-0 form-group align-self-end">
                                <button type="button" class="btn btn-icon p-0" data-toggle="class-toggle"
                                    data-target=".aiz-filter-sidebar">
                                    <i class="la la-filter la-2x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="min_price" value="">
                    <input type="hidden" name="max_price" value="">

                    <div class="row gutters-5 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-2">
                        @foreach ($products as $key => $product)
                        <div class="col">
                            @include('frontend.partials.product_box_1',['product' => $product])
                        </div>
                        @endforeach
                    </div>

                    <div class="aiz-pagination aiz-pagination-center mt-4">
                        {{$products->appends(request()->input())->links('pagination::bootstrap-4')}}
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection

@section('script')
<script type="text/javascript">
    function filter(){
            $('#search-form').submit();
        }
        function rangefilter(arg){
            $('input[name=min_price]').val(arg[0]);
            $('input[name=max_price]').val(arg[1]);
            filter();
        }
</script>
@endsection