@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">Estado de Cartera</h5>
    </div>

    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th data-breakpoints="md">Emision</th>
                    <th data-breakpoints="md">Vencimiento</th>
                    <th data-breakpoints="md">Dias Vencidos</th>
                    <th>Total</th>
                    <th>Saldo</th>
                    <th class="text-right">Opciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documentos as $key => $documento)
                <tr>
                    <td> {{ $documento->documentoid }}</td>
                    <td>{{ $documento->emision }}</td>
                    <td>{{ $documento->vence }}</td>
                    <td>{{ $documento->diasvence }}</td>
                    <td>${{ $documento->valor }}</td>
                    <td>${{ $documento->saldo }}</td>
                    <td class="text-right">
                        <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm"
                            onclick="detalle_documento({{ $documento->documentoid }},{{ $documento->secuencia }})"
                            title="Detalles del Pedido">
                            <i class="las la-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{$documentos->appends(request()->input())->links('pagination::bootstrap-4')}}
        </div>
    </div>

</div>
@endsection

@section('modal')
@include('modals.delete_modal')

<div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered " role="document">
        <div class="modal-content">
            <div id="detalle_documento">

            </div>
        </div>
    </div>
</div>

@endsection