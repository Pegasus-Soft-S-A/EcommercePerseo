@extends('frontend.layouts.app')


@section('content')
<section class="mb-4 pt-3">
    <div class="container sm-px-0">
        <form class="" id="search-form" action="" method="GET">
            <div class="row">
                <div class="col-xl-3 side-filter d-xl-block">
                    <div class="aiz-filter-sidebar collapse-sidebar-wrap sidebar-xl sidebar-right z-1035">
                        <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle"
                            data-target=".aiz-filter-sidebar" data-same=".filter-sidebar-thumb"></div>
                        <div class="collapse-sidebar c-scrollbar-light text-left">
                            <div class="d-flex d-xl-none justify-content-between align-items-center pl-3 border-bottom">
                                <h3 class="h6 mb-0 fw-600">Filtros</h3>
                                <button type="button" class="btn btn-sm p-2 filter-sidebar-thumb"
                                    data-toggle="class-toggle" data-target=".aiz-filter-sidebar" type="button">
                                    <i class="las la-times la-2x"></i>
                                </button>
                            </div>
                            <div class="bg-white shadow-sm rounded mb-3 text-left">
                                <div class="fs-15 fw-600 p-3 border-bottom">
                                    Categorías
                                </div>
                                <div class="p-3">
                                    <ul class="list-unstyled">


                                        <li class="mb-2 ml-2">
                                            <a class="text-reset fs-14" href="">Moda</a>
                                        </li>

                                        <li class="mb-2">
                                            <a class="text-reset fs-14 fw-600" href="">
                                                <i class="las la-angle-left"></i>
                                                Todas las categorías
                                            </a>
                                        </li>

                                        <li class="mb-2">
                                            <a class="text-reset fs-14 fw-600" href="">
                                                <i class="las la-angle-left"></i>
                                                Moda
                                            </a>
                                        </li>

                                        <li class="mb-2">
                                            <a class="text-reset fs-14 fw-600" href="">
                                                <i class="las la-angle-left"></i>
                                                Entretenimiento
                                            </a>
                                        </li>

                                    </ul>
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

                        <li class="breadcrumb-item fw-600  text-dark">
                            <a class="text-reset" href="{{ route('customer.products') }}">"Todas las categorías"</a>
                        </li>


                        <li class="text-dark fw-600 breadcrumb-item">
                            <a class="text-reset" href="">Teconologia</a>
                        </li>

                    </ul>

                    <div class="text-left">
                        <div class="d-flex">
                            <div class="form-group w-200px">
                                <label class="mb-0 opacity-50">{{ translate('Ordenar por')}}</label>
                                <select class="form-control form-control-sm aiz-selectpicker" name="sort_by"
                                    onchange="filter()">
                                    <option value="1" @isset($sort_by) @if ($sort_by=='1' ) selected @endif @endisset>Lo
                                        mas nuevo</option>
                                    <option value="2" @isset($sort_by) @if ($sort_by=='2' ) selected @endif @endisset>Lo
                                        mas viejo</option>
                                    <option value="3" @isset($sort_by) @if ($sort_by=='3' ) selected @endif @endisset>
                                        Precios de barato a caro</option>
                                    <option value="4" @isset($sort_by) @if ($sort_by=='4' ) selected @endif @endisset>
                                        Precios de caro a barato</option>
                                </select>
                            </div>
                            <div class="form-group ml-auto mr-0 w-200px d-none d-md-block">
                                <label class="mb-0 opacity-50">Condición</label>
                                <select class="form-control form-control-sm aiz-selectpicker" name="condition"
                                    onchange="filter()">
                                    <option value="">Todos los tipos</option>
                                    <option value="new" @isset($condition) @if ($condition=='new' ) selected @endif
                                        @endisset>Nuevo</option>
                                    <option value="used" @isset($condition) @if ($condition=='used' ) selected @endif
                                        @endisset>Usado</option>
                                </select>
                            </div>

                            <div class="d-xl-none ml-auto ml-md-3 mr-0 form-group align-self-end">
                                <button type="button" class="btn btn-icon p-0" data-toggle="class-toggle"
                                    data-target=".aiz-filter-sidebar">
                                    <i class="la la-filter la-2x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row gutters-5 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-2">

                        <div class="col mb-2">
                            <div
                                class="aiz-card-box border border-light rounded shadow-sm hov-shadow-md h-100 has-transition bg-white">
                                <div class="position-relative">
                                    <a href="" class="d-block">
                                        <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                                            src="https://images.deprati.com.ec/sys-master/images/h9d/hee/9975676043294/16023961-0_product_300Wx450H"
                                            data-src="" alt="Camiseta de colores">
                                    </a>
                                    <div class="absolute-top-left pt-2 pl-2">

                                        <span class="badge badge-inline badge-danger">Usado</span>

                                    </div>
                                </div>
                                <div class="p-md-3 p-2 text-left">
                                    <div class="fs-15">
                                        <span class="fw-700 text-primary">$5.99</span>
                                    </div>
                                    <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0">
                                        <a href="" class="d-block text-reset">Camiseta de colores</a>
                                    </h3>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="aiz-pagination aiz-pagination-center mt-4">

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
</script>
@endsection