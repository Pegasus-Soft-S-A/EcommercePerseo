@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col">
            <h1 class="h3">Paginas del Sitio</h1>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0 fw-600">Todas las Paginas</h6>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th>Nombre</th>
                    <th data-breakpoints="md">URL</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><a href="{{ route('configuracion.paginas.edit',['inicio']) }}" class="text-reset">Inicio</a>
                    </td>
                    <td>{{ route('home') }}</td>
                    <td class="text-right">
                        <a href="{{ route('configuracion.paginas.edit',['inicio']) }}"
                            class="btn btn-icon btn-circle btn-sm btn-soft-primary" title="Editar">
                            <i class="las la-pen"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><a href="{{ route('configuracion.paginas.edit',['terminos_condiciones']) }}"
                            class="text-reset">Terminos y Condiciones</a>
                    </td>
                    <td>{{ route('home') }}/terminos_condiciones</td>
                    <td class="text-right">
                        <a href="{{ route('configuracion.paginas.edit',['terminos_condiciones']) }}"
                            class="btn btn-icon btn-circle btn-sm btn-soft-primary" title="Editar">
                            <i class="las la-pen"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><a href="{{ route('configuracion.paginas.edit',['politica_devoluciones']) }}"
                            class="text-reset">Politica Devoluciones</a>
                    </td>
                    <td>{{ route('home') }}/politica_devoluciones</td>
                    <td class="text-right">
                        <a href="{{ route('configuracion.paginas.edit',['politica_devoluciones']) }}"
                            class="btn btn-icon btn-circle btn-sm btn-soft-primary" title="Editar">
                            <i class="las la-pen"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><a href="{{ route('configuracion.paginas.edit',['politica_soporte']) }}"
                            class="text-reset">Politica Soporte</a>
                    </td>
                    <td>{{ route('home') }}/politica_soporte</td>
                    <td class="text-right">
                        <a href="{{ route('configuracion.paginas.edit',['politica_soporte']) }}"
                            class="btn btn-icon btn-circle btn-sm btn-soft-primary" title="Editar">
                            <i class="las la-pen"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td><a href="{{ route('configuracion.paginas.edit',['politica_privacidad']) }}"
                            class="text-reset">Politica Privacidad</a>
                    </td>
                    <td>{{ route('home') }}/politica_privacidad</td>
                    <td class="text-right">
                        <a href="{{ route('configuracion.paginas.edit',['politica_privacidad']) }}"
                            class="btn btn-icon btn-circle btn-sm btn-soft-primary" title="Editar">
                            <i class="las la-pen"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection