@extends('backend.layouts.app')
@section('content')

<div class="row">
    <div class="col-xl-12 mx-auto">
        <h6 class="fw-600">Configuracion Pagina de Inicio</h6>

        {{-- Home Slider --}}
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Slider de Inicio</h6>
            </div>
            <form action="{{ route('business_settings.update_inicio') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    {{-- <div class="alert alert-info">
                        Tenemos una altura de banner limitada para mantener la interfaz de usuario. Tuvimos que recortar
                        desde el lado izquierdo y derecho a la vista para que se adapte a diferentes dispositivos. Antes
                        de
                        diseñar un banner, tenga en cuenta estos puntos.
                    </div> --}}

                    <div class="form-group">
                        <label>Fotos y Enlaces</label>

                        <div class="home-slider-target">
                            @foreach (json_decode(get_setting('home_slider')) as $key => $slider)

                            <div class="row gutters-5">
                                <div class="col-md-3">
                                    <label>Imagen</label>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="file" name="imagenes[]" class="selected-files" value="" accept="image/png">
                                            <small class="text-muted">Imagen 1000x500.png</small>
                                        </div>
                                    </div>
                                    <div class="position-relative">
                                        <input type="hidden" value="{{ $slider->imagen }}" name="imagen[]">
                                        <img class="img-fit lazyload mx-auto "
                                            src=" data:image/jpg;base64,{{ $slider->imagen }}" alt="">
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-3">
                                    <label>Link</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="http://" name="links[]"
                                            autocomplete="off" value="{{$slider->link}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Fecha Inicio</label>
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="inicio[]" autocomplete="off"
                                            value="{{$slider->inicio}}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>Fecha Fin</label>
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="fin[]" autocomplete="off"
                                            value="{{$slider->fin}}" required>
                                    </div>
                                </div>
                                <div class="col-md-auto">
                                    <div class="form-group">
                                        <button type="button"
                                            class="mt-4 btn btn-icon btn-circle btn-sm btn-soft-danger"
                                            data-toggle="remove-parent" data-parent=".row">
                                            <i class="las la-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <br>
                        <button type="button" class="btn btn-soft-secondary btn-sm" data-toggle="add-more" data-content='
                        <div class="row gutters-5">
                            <div class="col-md-3">
                                <label>Imagen</label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="file" name="imagenes[]" class="selected-files" accept="image/png"
                                        value="" >
                                        <small class="text-muted">Imagen 1000x500.png</small>
                                    </div>
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </br>
                            </div>
                            <div class="col-md-3">
                                <label>Link</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="http://" name="links[]">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label>Fecha Inicio</label>
                                <div class="form-group">
                                    <input type="date" class="form-control"  name="inicio[]" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label>Fecha Fin</label>
                                <div class="form-group">
                                    <input type="date" class="form-control"  name="fin[]" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="form-group">
                                    <button type="button" class="mt-4 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
                                        <i class="las la-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>' data-target=".home-slider-target">
                            Agregar Nuevo
                        </button>
                    </div>
                </div>

                <div class="card-header">
                    <h6 class="mb-0">Top 10</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-from-label">Top {{ucfirst(get_setting('grupo_productos'))}} (Max
                            10)</label>
                        <div class="col-md-10">
                            <select name="top10_categories[]" class="form-control aiz-selectpicker" multiple
                                data-max-options="10" data-live-search="true" @if(get_setting('top10_categories')))
                                data-selected="[{{implode("," ,get_setting('top10_categories'))}}]" @endif>
                                @foreach ($grupos as $category)
                                <option value="{{ $category->$grupoid }}">{{ $category->descripcion }}
                                </option>
                                @endforeach
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
