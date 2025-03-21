<form class="form-default" role="form" action="{{ route('addresses.update', $address_data->clientes_sucursalesid) }}" method="POST">
    @csrf
    <div class="p-3">
        <div class="row">
            <div class="col-md-2">
                <label>Descripción</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="Casa, Trabajo, etc." value="{{ $address_data->descripcion }}"
                    name="descripcion" autocomplete="off" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <label>Provincia</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="provinciasid" id="edit_provinciasid" required>
                    <option value="">Seleccione Provincia</option>
                    @foreach ($provincias as $provincia)
                        <option value="{{ $provincia->provinciasid }}" @if ($provincia->provinciasid == $address_data->provinciasid) selected @endif>{{ $provincia->provincia }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Ciudad</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="ciudadesid" id="edit_ciudadesid" required>
                    <option value="">Seleccione Ciudad</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Parroquias</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="parroquiasid" id="edit_parroquiasid" required>
                    <option value="">Seleccione Parroquia</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Direccion</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3" placeholder="Su Direccion" rows="2" name="direccion" onkeydown="controlar(event)" required>{{ $address_data->direccion }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <label>Telefono</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3" placeholder="9999999999" value="{{ $address_data->telefono1 }}" name="telefono"
                    required>
            </div>
        </div>
        <div class="form-group text-right">
            <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
        </div>
    </div>

    <input type="hidden" id="edit_provincia_inicial" value="{{ $address_data->provinciasid }}">
    <input type="hidden" id="edit_ciudad_inicial" value="{{ $address_data->ciudadesid }}">
    <input type="hidden" id="edit_parroquia_inicial" value="{{ $address_data->parroquiasid }}">
</form>

<script>
    // Este script se ejecutará cuando el formulario se cargue en el modal
    $(document).ready(function() {
        // Inicializar los selectores en el formulario de edición
        initEditFormSelectors();
    });
</script>
