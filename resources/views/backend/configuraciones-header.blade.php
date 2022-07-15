@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">Encabezado del sitio</h1>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Configuracion del Encabezado</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('business_settings.update_header') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Logo Cabecera</label>
                        <div class="col-md-8">
                            <div class=" input-group ">
                                <input type="file" name="header_logo" class="selected-files" value="" id="file">
                            </div>
                            <small class="text-muted">Imagen 500x100 .png</small>

                            <div class="position-relative">
                                <img class="img-fit lazyload mx-auto "
                                    src="@if (get_setting('header_logo')!="") data:image/jpg;base64,{{ get_setting('header_logo') }} @endif"
                                    id="header_logo" alt="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">Activar cabecera fija</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" name="header_stikcy" @if( get_setting('header_stikcy')=='on' )
                                    checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>


                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
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
            document.getElementById('header_logo').setAttribute('src',event.target.result);
        };
        reader.readAsDataURL(file);
    }

</script>
@endsection