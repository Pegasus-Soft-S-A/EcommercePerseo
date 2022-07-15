@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">Editar Informacion de la Pagina</h1>
        </div>
    </div>
</div>
<div class="card">

    <form class="p-4" action="{{ route('configuracion.paginas.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-header px-0">
            <input type="hidden" name="pagina" value="{{$pagina}}">
            <h6 class="fw-600 mb-0">Contenido de Pagina</h6>
        </div>
        <div class="card-body px-0">
            <div class="form-group row">
                <label class="col-sm-2 col-from-label" for="name">Titulo <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Titulo" name="titulo" value="{{ $pagina }}"
                        disabled>
                </div>
            </div>


            <div class="form-group row">
                <label class="col-sm-2 col-from-label" for="name">Enlace <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <div class="input-group d-block d-md-flex">
                        @if($pagina == 'inicio')
                        <div class="input-group-prepend"><span
                                class="input-group-text flex-grow-1">{{ route('home') }}/</span></div>
                        <input type="text" class="form-control w-100 w-md-auto" name="enlace" value="{{ $pagina }}">
                        @else
                        <input class="form-control w-100 w-md-auto" value="{{ route('home') }}/{{ $pagina }}" disabled>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 col-from-label" for="name">Agregar Contenido <span
                        class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <textarea class="aiz-text-editor form-control" placeholder="Contenido.."
                        data-buttons='[["font", ["bold", "underline", "italic", "clear"]],["para", ["ul", "ol", "paragraph"]],["style", ["style"]],["color", ["color"]],["table", ["table"]],["insert", ["link", "picture", "video"]],["view", ["fullscreen", "codeview", "undo", "redo"]]]'
                        data-min-height="300" name="contenido" required>@php echo $contenido; @endphp</textarea>
                </div>
            </div>
        </div>

        <div class="card-body px-0">
            <div class="text-right">
                <button type="submit" class="btn btn-primary">Actualizar Pagina</button>
            </div>
        </div>
    </form>
</div>
@endsection