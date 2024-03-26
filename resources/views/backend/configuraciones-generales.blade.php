@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">General</h1>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <form action="{{ route('business_settings.update_general') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card-header">
                    <h6 class="mb-0">Configuraciones Proyecto</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-lg-3 col-from-label">URL de la App</label>
                        <div class="col-lg-8">
                            <input type="hidden" name="types[]" value="APP_URL">
                            <input type="text" class="form-control" name="APP_URL" value="{{ env('APP_URL')}}"
                                placeholder="Ingrese App" autocomplete="off" readonly>
                        </div>
                    </div>

                </div>

                <div class="card-header">
                    <h6 class="mb-0">Configuraciones Generales</h6>
                </div>
                <div class="card-body">

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Tipo Tienda</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="tipo_tienda">
                                <option value="publico" @if (get_setting('tipo_tienda')=='publico' ) selected @endif>
                                    Publico
                                </option>
                                <option value="distribuidor" @if (get_setting('tipo_tienda')=='distribuidor' ) selected
                                    @endif>
                                    Distribuidor
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Grupo de productos</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="grupo_productos" id="grupo_productos">
                                <option value="lineas" @if (get_setting('grupo_productos')=='lineas' ) selected @endif>
                                    Lineas
                                </option>
                                <option value="categorias" @if (get_setting('grupo_productos')=='categorias' ) selected
                                    @endif>Categorias
                                </option>
                                <option value="subcategorias" @if (get_setting('grupo_productos')=='subcategorias' )
                                    selected @endif>
                                    Subcategorias</option>
                                <option value="subgrupos" @if (get_setting('grupo_productos')=='subgrupos' ) selected
                                    @endif>Subgrupos</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Vista de grupo de productos</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="vista_categorias" id="vista_categorias">
                                <option value="1" @if (get_setting('vista_categorias')=='1' ) selected @endif>
                                    Imágenes Categorías
                                </option>
                                <option value="2" @if (get_setting('vista_categorias')=='2' ) selected @endif>
                                    Lista Categorías
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Facturador con el que se guardara el pedido</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="facturador" id="facturador"
                                data-live-search="true">
                                @foreach ($facturadores as $facturador)
                                <option value="{{$facturador->facturadoresid}}" @if ($facturador->
                                    facturadoresid==get_setting('facturador')) selected @endif>
                                    {{$facturador->nombres}}
                                    @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Ver Codigo Producto</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="ver_codigo">
                                <option value="1" @if (get_setting('ver_codigo')=='1' ) selected @endif>
                                    Si
                                </option>
                                <option value="0" @if (get_setting('ver_codigo')=='0' ) selected @endif>
                                    No
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Visualizacion de Productos</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="productos_existencias"
                                id="productos_existencias" onchange="visualizar()">
                                <option value="todos" @if ($productos_existencias=='todos' ) selected @endif>
                                    Todos
                                </option>
                                <option value="con_existencias" @if ($productos_existencias=='con_existencias' )
                                    selected @endif>Con
                                    Existencias
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" id="controla_stock">
                        <label class="col-md-3 col-from-label">Ver y Controlar Stock</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="controla_stock">
                                <option value="0" @if (get_setting('controla_stock')=='0' ) selected @endif>
                                    No
                                </option>
                                <option value="1" @if (get_setting('controla_stock')=='1' ) selected @endif>
                                    Stock Total
                                </option>
                                <option value="2" @if (get_setting('controla_stock')=='2' ) selected @endif>
                                    Stock Por Almacén
                                </option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group row" id="productos_disponibles">
                        <label class="col-md-3 col-from-label">Visualizacion de Productos Disponibles</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="productos_disponibles"
                                value="{{ get_setting('productos_disponibles')}}" placeholder="Productos Disponibles"
                                maxlength="12">
                        </div>
                    </div>
                    <div class="form-group row" id="productos_no_disponibles">
                        <label class="col-md-3 col-from-label">Visualizacion de Productos no Disponibles</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="productos_no_disponibles"
                                value="{{ get_setting('productos_no_disponibles')}}"
                                placeholder="Productos no disponibles" maxlength="12">

                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Tarifa a mostrar en los productos</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="tarifa_productos" id="tarifa_productos"
                                data-live-search="true">
                                @foreach ($tarifas as $tarifa)
                                <option value="{{$tarifa->tarifasid}}" @if ($tarifa->
                                    tarifasid==get_setting('tarifa_productos')) selected @endif>
                                    {{$tarifa->descripcion}}
                                    @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-from-label">Cantidad maxima para agregar al carrito</label>
                        <div class="col-lg-8">
                            <input type="number" class="form-control" name="cantidad_maxima" id="cantidad_maxima"
                                value="{{ get_setting('cantidad_maxima')}}" placeholder="Ingrese Cantidad">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-from-label">Correo(s) donde llegaran los pedidos</label>
                        <div class="col-lg-8">
                            <input type="text" class="form-control aiz-tag-input" name="email_pedidos[]"
                                id="email_pedidos" value="{{ get_setting('email_pedidos')}}"
                                placeholder="Ingrese Correo">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Imagen por defecto para productos sin imagen</label>
                        <div class="col-md-8">
                            <div class=" input-group ">
                                <input type="file" name="imagen_defecto" class="selected-files" value="" id="file">
                            </div>
                            <small class="text-muted">Imagen 400x400 .png</small>

                            <div class="position-relative">
                                <img class="img-fit lazyload mx-auto "
                                    src="@if (get_setting('imagen_defecto')!="") data:image/jpg;base64,{{ get_setting('imagen_defecto') }} @endif"
                                    id="imagen_defecto" width="200" height="200">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card-header">
                    <h6 class="mb-0">Clientes</h6>
                </div>
                <div class="card-body">

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Registra Clientes</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="registra_clientes" @if(
                                    get_setting('registra_clientes')=='on' ) checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Grupo Clientes al Registrase</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="grupo_clientes" id="grupo_clientes"
                                data-live-search="true">
                                @foreach ($grupos as $grupo)
                                <option value="{{$grupo->clientes_gruposid}}" @if ($grupo->
                                    clientes_gruposid==get_setting('grupo_clientes')) selected @endif>
                                    {{$grupo->descripcion}}
                                    @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Tarifa Clientes al Registrarse</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="tarifa_clientes" id="tarifa_clientes"
                                data-live-search="true">
                                @foreach ($tarifas as $tarifa)
                                <option value="{{$tarifa->tarifasid}}" @if ($tarifa->
                                    tarifasid==get_setting('tarifa_clientes')) selected @endif>
                                    {{$tarifa->descripcion}}
                                    @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Asignar clave a todos los clientes</label>
                        <div class="col-md-8">
                            <a id="asignar" onclick="asignar()" class="btn btn-primary text-white">Asignar</a>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Asignar direccion a todos los clientes</label>
                        <div class="col-md-8">
                            <a id="asignar" onclick="asignarDireccion()" class="btn btn-primary text-white">Asignar</a>
                        </div>
                    </div>
                </div>

                <div class="card-header">
                    <h6 class="mb-0">Formas de Pago</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Pedido</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="pago_pedido" @if( get_setting('pago_pedido')=='on' )
                                    checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Pago Plux</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input id="pagoplux" type="checkbox" name="pago_plux" @if(
                                    get_setting('pago_plux')=='on' ) checked @endif onchange="visualizar()">
                                <span></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group row" id="email_pago_plux">
                        <label class="col-md-3 col-from-label">Email Pago Plux</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="email_pago_plux"
                                value="{{ get_setting('email_pago_plux')}}" placeholder="Ingrese Correo">
                        </div>
                    </div>

                    <div class="form-group row" id="pedido_pago_plux">
                        <label class="col-md-3 col-from-label">Pedido con Pago Plux</label>
                        <div class="col-md-8">
                            <select class="form-control aiz-selectpicker" name="pedido_pago_plux">
                                <option value="pedido" @if (get_setting('pedido_pago_plux')=='pedido' ) selected @endif>
                                    Pedido
                                </option>
                                <option value="factura" @if (get_setting('pedido_pago_plux')=='factura' ) selected
                                    @endif>Factura
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- Modal de carga --}}
@section('modal')
<div class="modal" id="carga">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
        role="document">
        <div class="modal-content position-relative">
            <div class="c-preloader text-center p-3">
                <i class="las la-spinner la-spin la-3x"></i>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        visualizar();
    });
    document.getElementById('file').addEventListener('change',cambiarImagen);

    function cambiarImagen(){
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = (event) =>{
            document.getElementById('imagen_defecto').setAttribute('src',event.target.result);
        };
        reader.readAsDataURL(file);
    }

    function asignar(){
        if (confirm("Se asignaran como clave los primeros 4 digitos de la identificacion a todos los clientes \nDesea Continuar?")){
            $('#carga').modal({backdrop: 'static', keyboard: false});
            $('.c-preloader').show();
            $.ajax({
                    type:"get",
                    url: '{{ route('asignar_claves') }}',
                    success: function(data){
                   if (data==1){
                    $('#carga').modal('hide')
                    AIZ.plugins.notify('success', 'Claves asignadas correctamente');
                   }else{
                    AIZ.plugins.notify('error', 'Error Asignando Claves');
                   }

                }
            });
        }
    }

    function asignarDireccion(){
        if (confirm("Se creara una sucursal con la direccion a todos los clientes \nDesea Continuar?")){
            $('#carga').modal({backdrop: 'static', keyboard: false});
            $('.c-preloader').show();
            $.ajax({
                type:"get",
                url: '{{ route('asignar_direcciones') }}',
                success: function(data){
                    if (data==1){
                        $('#carga').modal('hide')
                        AIZ.plugins.notify('success', 'Direcciones asignadas correctamente');
                    }
                }
            });
        }
    }

    function visualizar(){
        if($('#productos_existencias').val()=='todos'){
            $('#productos_disponibles').show();
            $('#productos_no_disponibles').show();
        }else{
            $('#productos_disponibles').hide();
            $('#productos_no_disponibles').hide();
        }

        if( $('#pagoplux').is(':checked')){
            $('#email_pago_plux').show();
            $('#pedido_pago_plux').show();
        }else{
            $('#email_pago_plux').hide();
            $('#pedido_pago_plux').hide();
        }

    }
</script>
@endsection