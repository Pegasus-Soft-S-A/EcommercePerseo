<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Documento: {{ $documentoid }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body gry-bg px-3 pt-3">
    <div class="col-lg-12 text-center">
        <table class="table">
            <thead>
                <th width="30%">Tipo</th>
                <th width="30%">Emision</th>
                <th width="30%">Valor</th>
            </thead>
            <tbody>
                @foreach ($detalles as $key => $detalle)
                <tr>
                    <td>{{$detalle->tipo}}</td>
                    <td>{{$detalle->emision}}</td>
                    <td>{{$detalle->importe}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>