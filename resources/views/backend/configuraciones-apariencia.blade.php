@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-600 mb-0">General</h6>
            </div>
            <form action="{{ route('business_settings.update_apariencia') }}" method="POST"
                enctype="multipart/form-data">
                <div class="card-body">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Nombre del Sitio Web</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="nombre_sitio">
                            <input type="text" name="nombre_sitio" class="form-control" placeholder="Nombre del Sitio"
                                value="{{ get_setting('nombre_sitio') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Lema del Sitio</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="lema_sitio">
                            <input type="text" name="lema_sitio" class="form-control" placeholder="Lema del Sitio"
                                value="{{  get_setting('lema_sitio') }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Icono del Sitio</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="file" name="icono_sitio" class="form-group selected-files" value=""
                                    id="file">
                            </div>
                            <small class="text-muted">Favicon. 32x32 .png</small>
                            <div class="file-preview">
                                <img src="@if (get_setting('icono_sitio')!="") data:image/jpg;base64,{{ get_setting('icono_sitio') }} @endif"
                                    id="icono_sitio" alt="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Color Base del Sitio</label>
                        <div class="col-md-3">
                            <input type="hidden" name="types[]" value="color_sitio">
                            <input type="color" name="color_sitio" class="form-control" placeholder="#377dff"
                                value="{{ get_setting('color_sitio') }}">
                            {{-- <small class="text-muted">Color Hexadecimal</small> --}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Color Hover Sitio </label>
                        <div class="col-md-3">
                            <input type="hidden" name="types[]" value="color_hover_sitio">
                            <input type="color" name="color_hover_sitio" class="form-control" placeholder="#377dff"
                                value="{{  get_setting('color_hover_sitio') }}">
                            {{-- <small class="text-muted">Color Hexadecimal</small> --}}
                        </div>
                    </div>
                </div>

                <div class="card-header">
                    <h6 class="fw-600 mb-0">Scripts Personalizados</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Script personalizado de encabezado antes de cierre de
                            etiqueta head</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="header_script">
                            <textarea name="header_script" rows="4" class="form-control"
                                placeholder="<script>&#10;...&#10;</script>">{{ get_setting('header_script') }}</textarea>
                            <small>Escribir con etiquieta script</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Script personalizado de pie de pagina antes de cierre de
                            etiqueta body</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="footer_script">
                            <textarea name="footer_script" rows="4" class="form-control"
                                placeholder="<script>&#10;...&#10;</script>">{{ get_setting('footer_script') }}</textarea>
                            <small>Escribir con etiquieta script</small>
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


@section('script')
<script type="text/javascript">
    document.getElementById('file').addEventListener('change',cambiarImagen);

    function cambiarImagen(){
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = (event) =>{
            document.getElementById('icono_sitio').setAttribute('src',event.target.result);
        };
        reader.readAsDataURL(file);
    }

</script>
@endsection