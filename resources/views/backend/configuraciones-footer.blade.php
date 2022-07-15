@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">Pie de Página</h1>
        </div>
    </div>
</div>
<form action="{{ route('business_settings.update_footer') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card">
        <div class="card-header">
            <h6 class="fw-600 mb-0">Widget Pie de Página</h6>
        </div>
        <div class="card-body">
            <div class="row gutters-10">
                <div class="col-lg-6">
                    <div class="card shadow-none bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">Acerca del Widget</h6>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label class="form-label" for="signinSrEmail">Logo Pie de Pagina</label>
                                <div class="input-group ">
                                    <input type="file" name="footer_logo" class="selected-files" value="" id="file">
                                </div>
                                <small class="text-muted">Imagen 500x100 .png</small>
                                <div class="file-preview">
                                    <img src="@if (get_setting('footer_logo')!="") data:image/jpg;base64,{{ get_setting('footer_logo') }} @endif"
                                        id="footer_logo" alt="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Descripcion</label>
                                <textarea class="aiz-text-editor form-control" name="acerca_nosotros"
                                    data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["view", ["undo","redo"]]]'
                                    placeholder="Descripcion" data-min-height="150">
                                        @php echo get_setting('acerca_nosotros'); @endphp
                                    </textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-none bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">Widget de información de contacto</h6>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label>Direccion de Contacto</label>
                                <input type="hidden" name="types[]" value="direccion_contacto">
                                <input type="text" class="form-control" placeholder="Direccion"
                                    name="direccion_contacto" value="{{ get_setting('direccion_contacto') }}">
                            </div>
                            <div class="form-group">
                                <label>Telefono de Contacto</label>
                                <input type="hidden" name="types[]" value="telefono_contacto">
                                <input type="text" class="form-control" placeholder="Telefono" name="telefono_contacto"
                                    value="{{ get_setting('telefono_contacto') }}">
                            </div>
                            <div class="form-group">
                                <label>Email de Contacto</label>
                                <input type="hidden" name="types[]" value="email_contacto">
                                <input type="text" class="form-control" placeholder="Email" name="email_contacto"
                                    value="{{ get_setting('email_contacto') }}">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card shadow-none bg-light">
                        <div class="card-header">
                            <h6 class="mb-0">Redes Sociales</h6>
                        </div>
                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-2 col-from-label">Mostrar Links Redes Sociales</label>
                                <div class="col-md-9">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="hidden" name="types[]" value="show_social_links">
                                        <input type="checkbox" name="show_social_links" @if(
                                            get_setting('show_social_links')=='on' ) checked @endif>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Links Redes Sociales</label>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="lab la-facebook-f"></i></span>
                                    </div>
                                    <input type="hidden" name="types[]" value="facebook_link">
                                    <input type="text" class="form-control" placeholder="http://" name="facebook_link"
                                        value="{{ get_setting('facebook_link')}}">
                                </div>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="lab la-twitter"></i></span>
                                    </div>
                                    <input type="hidden" name="types[]" value="twitter_link">
                                    <input type="text" class="form-control" placeholder="http://" name="twitter_link"
                                        value="{{ get_setting('twitter_link')}}">
                                </div>
                                <div class="input-group form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="lab la-instagram"></i></span>
                                    </div>
                                    <input type="hidden" name="types[]" value="instagram_link">
                                    <input type="text" class="form-control" placeholder="http://" name="instagram_link"
                                        value="{{ get_setting('instagram_link')}}">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </div>
</form>
@endsection

@section('script')
<script type="text/javascript">
    document.getElementById('file').addEventListener('change',cambiarImagen);

    function cambiarImagen(){
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = (event) =>{
            document.getElementById('footer_logo').setAttribute('src',event.target.result);
        };
        reader.readAsDataURL(file);
    }

</script>
@endsection