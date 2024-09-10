@php
$almacenes = App\Models\Almacenes::where('disponibleventa', 1)->get();
$user = Session::get('user');
@endphp

<div class="modal-body p-4 c-scrollbar-light">
    <div class="row">
        <div class="col-lg-6">
            <div class="row gutters-10 flex-row-reverse">

                <div class="col">
                    <div class="aiz-carousel product-gallery" data-nav-for='.product-gallery-thumb' data-fade='true'
                        data-auto-height='true'>
                        @if (count($imagenProducto) > 0)
                        @foreach ($imagenProducto as $imagenProduct)
                        <div class="carousel-box img-zoom rounded">
                            <img class="img-fluid lazyload"
                                src="data:image/jpg;base64,{{ base64_encode($imagenProduct['imagen']) }}">
                        </div>
                        @endforeach
                        @else
                        <div class="carousel-box img-zoom rounded">
                            <img class="img-fluid lazyload"
                                src="data:image/jpg;base64,{{ get_setting('imagen_defecto') }}">
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-auto w-90px">
                    <div class="aiz-carousel carousel-thumb product-gallery-thumb" data-items='5'
                        data-nav-for='.product-gallery' data-vertical='true' data-focus-select='true'>
                        @if (count($imagenProducto) > 0)
                        @foreach ($imagenProducto as $imagenProduct)
                        <div class="carousel-box c-pointer border p-1 rounded"
                            data-medidasid="{{ $imagenProduct->medidasid }}">
                            <img class="lazyload mw-100 size-50px mx-auto"
                                src="data:image/jpg;base64,{{ base64_encode($imagenProduct->imagen) }}"
                                data-src="data:image/jpg;base64,{{ base64_encode($imagenProduct->imagen) }}" onerror="">
                        </div>
                        @endforeach
                        @else
                        <div class="carousel-box c-pointer border p-1 rounded" data-medidasid="">
                            <img class="lazyload mw-100 size-50px mx-auto"
                                src="data:image/jpg;base64,{{ get_setting('imagen_defecto') }}"
                                data-src="data:image/jpg;base64,{{ get_setting('imagen_defecto') }}" onerror="">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="text-left">
                <h2 class="mb-2 fs-20 fw-600">
                    {{ $product->descripcion }}
                </h2>

                <hr>
                @if (get_setting('controla_stock') == 2 && Auth::check())
                <div class="row no-gutters mt-3 d-flex ">
                    <div>
                        <div class="opacity-50 my-2">Sucursal Actual:</div>
                    </div>
                    @php
                    $nombreAlmacen = App\Models\Almacenes::select('descripcion')
                    ->where('almacenesid', session('almacenesid'))
                    ->first();
                    @endphp
                    <div class="mx-2 mx-md-1">
                        <div class="fs-20 opacity-60">
                            <span id="nombreAlmacen" class="">
                                {{ $nombreAlmacen->descripcion }}

                            </span>
                        </div>
                    </div>
                </div>
                <hr>
                @endif
                @if (get_setting('productos_existencias') == 'todos' ||
                get_setting('controla_stock') == 1 ||
                (get_setting('controla_stock') == 2 && Auth::check()))
                <div class="row no-gutters mt-3">
                    <div class="col-sm-2">
                        <div class="opacity-50 my-2">Existencias:</div>
                    </div>
                    <div class="d-flex">
                        @if ((get_setting('controla_stock') == 0 && !Auth::check()) ||
                        (get_setting('controla_stock') == 2 && !Auth::check()))

                        @if ($product->existenciastotales > 0)
                        <div class="d-inline-block rounded px-2 border-success mt-1 text-success border ml-2">
                            <span>{{ get_setting('productos_disponibles') }}</span>
                        </div>
                        @else
                        <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border ml-2">
                            <span>{{ get_setting('productos_no_disponibles') }}</span>
                        </div>
                        @endif
                        @elseif(get_setting('controla_stock') == 0 && Auth::check())
                        @if ($product->existenciastotales > 0)
                        <div class="d-inline-block rounded px-2 border-success mt-1 text-success border ml-2">
                            <span>{{ get_setting('productos_disponibles') }}</span>
                        </div>
                        @else
                        <div class="d-inline-block rounded px-2 border-danger mt-1 text-danger border ml-2">
                            <span>{{ get_setting('productos_no_disponibles') }}</span>
                        </div>
                        @endif
                        @else
                        <div class="">

                            <span id="cantidad" class="h2 fw-600 text-primary ml-3"></span>


                        </div>

                        @endif
                        @if (get_setting('controla_stock') == 2 && Auth::check())
                        <div class="mx-4">
                            <button type="button" id="cambiarAlmacen" class="my-2 btn btn-soft-primary btn-xs">
                                Ver Sucursales
                                <i class="las la-eye"></i>

                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <hr>
                @endif

                @if (Auth::check())
                @if ($precioProducto->precio <= $precioProducto2->precio)
                    <div class="row no-gutters mt-3">
                        <div class="col-sm-2">
                            <div class="opacity-50 my-2">Precio:</div>
                        </div>
                        <div class="col-sm-10">
                            <div class="">
                                <strong class="h2 fw-600 text-primary" id="precio">

                                </strong>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row no-gutters mt-3">
                        <div class="col-sm-2">
                            <div class="opacity-50 my-2">Precio:</div>
                        </div>
                        <div class="col-sm-10">
                            <div class="fs-20 opacity-60">
                                <del id="precionormal">

                                </del>
                            </div>
                        </div>
                    </div>

                    <div class="row no-gutters my-3">
                        <div class="col-sm-2">
                            <div class="opacity-50">Precio cliente:</div>
                        </div>
                        <div class="col-sm-10">
                            <div class="">
                                <strong class="h2 fw-600 text-primary" id="precio">

                                </strong>
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="row no-gutters mt-3">
                        <div class="col-sm-2">
                            <div class="opacity-50 my-2">Precio:</div>
                        </div>
                        <div class="col-sm-10">
                            <div class="">
                                <strong class="h2 fw-600 text-primary" id="precio">

                                </strong>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>
                    @php
                    $almacenes = DB::connection('empresa')
                    ->table('facturadores_almacenes')
                    ->where('facturadoresid', get_setting('facturador'))
                    ->where('principal', '1')
                    ->first();

                    @endphp
                    <form id="option-choice-form">
                        @csrf
                        <input type="hidden" id="variableinicio" name="variableinicio"
                            @if(get_setting('controla_stock')==2) value="{{ session('almacenesid') }}" @else
                            value="{{ $almacenes->almacenesid }}" @endif>
                        <input type="hidden" id="existencias" name="existencias" value="">
                        <input type="hidden" id="id" name="id" value="{{ $product->productosid }}">
                        <input type="hidden" name="preciocompleto" value="{{ $precioProducto->precio }}"
                            id="preciocompleto">
                        <input type="hidden" name="precioIVA" value="{{ $precioProducto->precioiva }}" id="precioiva">
                        <input type="hidden" id="factor" name="factor" value="{{ $precioProducto->factor }}">
                        {{-- <input type="hidden" name="IVA" value="{{$precioProducto->iva}}" id="IVA">
                        <input type="hidden" name="descuento" value="{{$precioProducto->descuento}}" id="descuento">
                        --}}

                        <div class="row no-gutters">
                            <div class="col-2">
                                <div class="opacity-50 mt-2 ">
                                    Medida:</div>
                            </div>
                            <div class="col-10">
                                <div class="aiz-radio-inline ">
                                    @foreach ($medidas as $key => $medida)
                                    <label class="aiz-megabox pl-0 mr-2">
                                        <input type="radio" name="medidasid" value="{{ $medida->medidasid }}"
                                            @if($key==0) checked @endif>
                                        <span
                                            class="aiz-megabox-elem rounded d-flex align-items-center justify-content-center py-2 px-3 mb-2">
                                            {{ $medida->descripcion }}
                                        </span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row no-gutters">
                            <div class="col-2">
                                <div class="opacity-50 mt-2">Cantidad:</div>
                            </div>
                            <div class="col-10">
                                <div class="product-quantity d-flex align-items-center">
                                    <div class="row no-gutters align-items-center aiz-plus-minus mr-3"
                                        style="width: 130px;">
                                        <button class="btn col-auto btn-icon btn-sm btn-circle btn-light" type="button"
                                            data-type="minus" data-field="quantity" disabled="">
                                            <i class="las la-minus"></i>
                                        </button>
                                        <input type="text" name="quantity" id="botonMas"
                                            class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                            placeholder="1" value="{{ $min_qty }}" min="{{ $min_qty }}"
                                            @if(get_setting('controla_stock')==0)
                                            max="{{ get_setting('cantidad_maxima') }}" @else max="" @endif
                                            autocomplete="off">
                                        <button class="btn  col-auto btn-icon btn-sm btn-circle btn-light" id="plus"
                                            type="button" data-type="plus" data-field="quantity">
                                            <i class="las la-plus"></i>


                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row no-gutters pb-3 d-none" id="chosen_price_div">
                            <div class="col-2">
                                <div class="opacity-50">Precio Total:</div>
                            </div>
                            <div class="col-10">
                                <div class="product-price">
                                    <strong id="chosen_price" class="h4 fw-600 text-primary">

                                    </strong>
                                </div>
                            </div>
                        </div>

                    </form>
                    <div class="mt-3">
                        @if (!Auth::check() && get_setting('controla_stock') == 2)
                        <a href="{{ route('user.login') }}">
                            <button type="button" class="btn btn-primary buy-now fw-600 add-to-cart">

                                <i class="la la-shopping-cart"></i>
                                <span class="d-none d-md-inline-block"> Añadir al Carrito</span>
                            </button>
                        </a>
                        @else
                        <button type="button" class="btn btn-primary buy-now fw-600 add-to-cart" onclick="addToCart()">

                            <i class="la la-shopping-cart"></i>
                            <span class="d-none d-md-inline-block"> Añadir al Carrito</span>
                        </button>
                        @endif
                    </div>

            </div>
        </div>
    </div>
</div>





<script type="text/javascript">
    cartQuantityInitialize();
    $('#option-choice-form input').on('change', function() {
        getVariantPrice();
    });
    $(document).ready(function() {
        @if (isset($user))
            $('#modalIdentificacion').modal()
        @endif



        window.setTimeout(datatable, 700);


    });



    function datatable() {
        if ('{{ get_setting('controla_stock') == 2 }}') {
            var table = $('#kt_datatable').DataTable({
                paging: false,
                searching: false,
                bInfo: false,
                responsive: true,
                //Combo cantidad de registros a mostrar por pantalla
                lengthMenu: [
                    [15, 25, 50, -1],
                    [15, 25, 50, 'Todos']
                ],
                //Registros por pagina
                pageLength: 15,
                //Orden inicial
                order: [
                    [0, 'asc']
                ],

                //Trabajar del lado del server
                serverSide: true,
                //Peticion ajax que devuelve los registros
                ajax: {
                    url: "{{ route('almacenes.index') }}",
                    type: 'GET',
                    data: function(data) {

                        data.factorValor = $('#factor').val();
                        data.producto = $('#id').val();
                    }
                },
                columns: [{
                        data: 'almacenesid',
                        name: 'almacenesid',
                        searchable: false,
                        visible: false

                    },
                    {
                        data: 'descripcion',
                        name: 'descripcion',
                        searchable: false,

                    },
                    {
                        data: 'existencias',
                        name: 'existencias',
                        searchable: false,


                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]

            });



        }
        $("input[name='medidasid']").click(function() {

            window.setTimeout(existencias, 700);

            function existencias() {
                table.draw();
                var almacen = $('#variableinicio').val();
                $('#' + almacen).attr("checked", "checked");
            }

        });
        $("#cambiarAlmacen").click(function() {
            $("#modal-almacen").modal();
            var almacen = $('#variableinicio').val();
            $('#' + almacen).attr("checked", "checked");
        });



    }

    function cambiarAlmacen(e) {
        $('#variableinicio').val(e.target.id);

        $('#cantidad').empty();
        $('#cantidad').append(e.target.value);
        $('#existencias').val(e.target.value);
        if ($('#botonMas').val() > e.target.value) {
            $('#botonMas').val(1);
            $('#plus').prop("disabled", false);

        } else {
            $('#plus').prop("disabled", false);
        }
        var id = document.getElementById(e.target.id);

        $('#nombreAlmacen').empty();
        $('#nombreAlmacen').append(id.getAttribute('nombrealmacen'));


    }

    $("#modal-almacen").on('hidden.bs.modal', function(event) {
        getVariantPrice();
    })
    $("#addToCart").on('hidden.bs.modal', function(event) {
        $('#kt_datatable').DataTable().destroy();
    })
</script>