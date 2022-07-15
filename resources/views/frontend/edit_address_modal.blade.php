<form class="form-default" role="form" action="{{ route('addresses.update', $address_data->clientes_sucursalesid) }}"
    method="POST">
    @csrf
    <div class="p-3">
        <div class="row">
            <div class="col-md-2">
                <label>Ciudad</label>
            </div>
            <div class="col-md-10">
                <div class="mb-3">
                    <select class="form-control aiz-selectpicker" data-live-search="true"
                        data-placeholder="Seleccione su Ciudad" name="ciudad" id="edit_country" required>
                        @foreach (\App\Models\Ciudades::get() as $key => $ciudad)
                        <option value="{{ $ciudad->ciudadesid }}" @if ($address_data->ciudadesid == $ciudad->ciudadesid)
                            selected
                            @endif>
                            {{ $ciudad->ciudad }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Direccion</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3" placeholder="Su Direccion" rows="2" name="direccion"
                    onkeydown="controlar(event)" required>{{ $address_data->direccion }} </textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Telefono</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="9999999999"
                    value="{{ $address_data->telefono1 }}" name="telefono" value="" required>
            </div>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
    </div>
</form>
@section('script')
<script type="text/javascript">
    function controlar(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                return false;
            }
        }
</script>
@endsection