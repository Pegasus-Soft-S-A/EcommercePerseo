<?php $__env->startSection('content'); ?>

<style>
    /* Estilos para ambas tablas */
    #pedidos-table .tabulator-row:nth-child(even),
    #tabla-medidas .tabulator-row:nth-child(even),
    #tabla .tabulator-row:nth-child(even) {
        background-color: transparent;
        /* Quita el color de fondo alterno */
    }

    #pedidos-table .tabulator-row:hover,
    #tabla-medidas .tabulator-row:hover,
    #tabla .tabulator-row:hover {
        background-color: rgba(0, 0, 0, 0.075);
        /* Mantener un color de resaltado suave en hover */
    }

    /* Estilo para las filas seleccionadas */
    #pedidos-table .tabulator-row.tabulator-selected,
    #tabla-medidas .tabulator-row.tabulator-selected,
    #tabla .tabulator-row.tabulator-selected {
        background-color: rgba(0, 123, 255, 0.3) !important;
        /* Fondo azul claro para la selección */
        color: #000 !important;
        /* Texto negro para visibilidad */
    }

    .total-row {
        margin-bottom: 0.1rem;
        /* Reducir el margen inferior de cada fila */
    }

    .total-label {
        margin: 0;
        padding: 0;
        font-size: 0.875rem;
        /* Tamaño de letra ajustado para ocupar menos espacio */
        text-align: right;
    }

    .total-value {
        margin: 0;
        padding: 0;
        font-size: 0.875rem;
        /* Tamaño de letra ajustado */
    }

    .mr-2 {
        margin-right: 0.3rem;
        /* Reducir la distancia entre label y span */
    }

    .col-md-6.offset-md-6 {
        padding: 0;
        /* Eliminar padding para optimizar el espacio */
    }

    .row.mt-1 {
        margin-top: 0.25rem;
        /* Reducir margen superior para compactar el contenido */
    }

    .form-group {
        margin-bottom: 0.15rem;
        /* Reducir el margen inferior de los campos */
    }
</style>


<!-- Incluyendo CSS de Tabulator desde CDN -->
<link href="https://unpkg.com/tabulator-tables@5.2.7/dist/css/tabulator.min.css" rel="stylesheet">
<!-- Incluyendo CSS de Tabulator con tema Bootstrap 5 desde CDN -->
<link href="https://unpkg.com/tabulator-tables@5.2.7/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">

<div class="card">
    <div class="card-header">
        <div class="col-md-12">
            <h5 class="mb-2 h6">Pedidos</h5>
        </div>
    </div>
    <div class="card-header">
        <div class="col-md-12">
            <a href="<?php echo e(route('pedidos.index')); ?>" id="btnVolver"
                class="btn btn-sm btn-secondary mr-2 text-white">Volver</a>
            <a id="btnGuardar" class="btn btn-sm btn-success mr-2 text-white">Guardar</a>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-4 col-sm-6">
                <label for="fecha" class="col-form-label">Fecha</label>
                <input type="date" class="form-control form-control-sm" value="<?php echo e($pedido->emision); ?>" name="fecha"
                    id="fecha">
            </div>

            <div class="form-group col-md-4 col-sm-6">
                <label for="codigo" class="col-form-label">Código</label>
                <input type="text" class="form-control form-control-sm" name="codigo" id="codigo"
                    value="<?php echo e($pedido->pedidos_codigo); ?>" readonly>
            </div>

            <div class="form-group col-md-4 col-sm-6">
                <label for="cliente" class="col-form-label">Cliente</label>
                <input type="text" class="form-control form-control-sm" name="cliente" id="cliente"
                    value="<?php echo e($cliente->razonsocial); ?>" readonly>
            </div>

            <div class="form-group col-md-4 col-sm-6">
                <label for="sucursal" class="col-form-label">Sucursal</label>
                <select class="form-control form-control-sm aiz-selectpicker" name="clientes_sucursalesid">

                    <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sucursal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($sucursal->clientes_sucursalesid); ?>"><?php echo e($sucursal->descripcion); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group col-md-4 col-sm-6">
                <label for="destinatario" class="col-form-label">Destinatario</label>
                <input type="text" class="form-control form-control-sm" name="destinatario" id="destinatario"
                    placeholder="Ingrese Destinatario" autocomplete="off">
            </div>

            <div class="form-group col-md-4 col-sm-6">
                <label for="centroCosto" class="col-form-label">Centro Costo</label>
                <select class="form-control form-control-sm aiz-selectpicker" name="centros_costosid">
                    <?php $__currentLoopData = $centros_costos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $centro_costo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($centro_costo->centros_costosid); ?>">
                        <?php echo e($centro_costo->centro_costocodigo); ?>-<?php echo e($centro_costo->descripcion); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group col-md-4 col-sm-6 mb-2">
                <label for="producto" class="col-form-label">Buscar</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" name="producto" id="producto"
                        placeholder="Ingrese Producto" autocomplete="off">
                    <button class="btn btn-sm btn-primary" type="button" id="buscar">
                        <i class="las la-search"></i>
                    </button>
                    <button class="btn btn-sm btn-danger ml-2" type="button" id="eliminarLinea"> <i
                            class="las la-trash"></i></button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Contenedor para la tabla -->
            <div id="pedidos-table" class="table-responsive table-sm"></div>
        </div>

        <div class="row mt-1 justify-content-end">
            <div class="col-md-6 offset-md-6">
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Sub Total:</label>
                    <span id="subtotal" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Descuento % :</label>
                    <span id="descuentoPorcentaje" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Descuento $ :</label>
                    <span id="descuentoDolares" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Sub Total Con IVA:</label>
                    <span id="subtotalNetoConIva" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Sub Total IVA 5%:</label>
                    <span id="subtotalNetoIva5" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Sub Total Sin IVA:</label>
                    <span id="subtotalNetoSinIva" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Sub Total Neto:</label>
                    <span id="subtotalNeto" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Total IVA:</label>
                    <span id="totalIVA" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Total IVA 5%:</label>
                    <span id="totalIVA5" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">TOTAL:</label>
                    <span id="total" class="font-weight-bold total-value">0.00</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Items:</label>
                    <span id="totalItems" class="font-weight-bold total-value">0</span>
                </div>
                <div class="d-flex justify-content-end align-items-center total-row">
                    <label class="total-label mr-2">Cantidad:</label>
                    <span id="totalCantidad" class="font-weight-bold total-value">0.00</span>
                </div>
            </div>
        </div>

    </div>


</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>
<?php echo $__env->make('modals.delete_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="modal fade" id="modalBusqueda" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Búsqueda Producto</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Contenedor de la tabla de Tabulator -->
                <div id="tabla" class="table-responsive table-sm"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalObservacion">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Observación</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="p-3">
                    <div class="form-group">
                        <input type="hidden" value="" name="fila" id="fila">
                        <textarea class="form-control h-auto form-control-lg" placeholder="Observación"
                            name="observacion" id="observacion" autocomplete="off" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarObservacion">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMedidas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-600">Medidas</h6>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Contenedor de la tabla de Tabulator -->
                <div id="tabla-medidas" class="table-responsive"></div>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<!-- Incluyendo JS de Tabulator desde CDN -->
<script src="https://unpkg.com/tabulator-tables@5.2.7/dist/js/tabulator.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializa Tabulator para la tabla de pedidos
        var pedidosTable = new Tabulator("#pedidos-table", {
            placeholder: "Agregue productos",
            height: "300px", // La tabla será de altura completa para permitir scroll
            layout: "fitColumns", // Ajusta las columnas para que encajen en el ancho disponible
            movableColumns: true, // Las columnas se pueden mover
            resizableRows: true, // Permite cambiar el tamaño de las filas
            selectable: 1, // Permitir seleccionar una sola fila
            data: [], // Inicialmente la tabla estará vacía
            columns: [
                {
                    title: "Información",
                    field: "informacion",
                    visible: false // Columna oculta
                },
                {
                    title: "",
                    formatter: function(cell, formatterParams, onRendered) {
                        return `
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Botón de Observación -->
                            <button class="btn btn-icon btn-sm btn-soft-primary btn-observacion" title="Observación">
                                <i class="las la-pen"></i>
                            </button>
                        </div>`;
                    },
                    width: 70,
                    hozAlign: "center",
                    cellClick: function(e, cell) {
                        let row = cell.getRow();
                        let rowData = row.getData();

                        // Determinar cuál botón fue presionado
                        if (e.target.closest('.btn-observacion')) {
                            // Botón Observación
                            $("#observacion").val(rowData.informacion || '');
                            $("#fila").val(row.getIndex());
                            $("#observacion").prop('disabled', false);
                            $("#modalObservacion").modal('show');
                        }
                    }
                },
                { title: "ID", field: "id", visible: false, width: 50 },
                { title: "Medidasid", field: "medidasid", visible: false, width: 50 },
                { title: "Factor", field: "factor", visible: false, width: 50 },
                { title: "Código", field: "productocodigo", width: 110 },
                {
                    title: "Descripción",
                    field: "descripcion",
                    widthGrow: 3, // Ocupa más espacio proporcional
                    minWidth: 200,
                },
                {
                    title: "Medida",
                    field: "medida",
                    width: 100,
                    formatter: function(cell, formatterParams, onRendered) {
                        let medida = cell.getValue() || "N/A";
                        return `
                            <div class="d-flex justify-content-between ">
                                <span>${medida}</span>
                                <button class="btn btn-icon btn-sm btn-soft-info  btn-medidas" title="Seleccionar Medidas">
                                    <i class="las la-ruler"></i>
                                </button>
                            </div>`;
                    },
                    cellClick: function(e, cell) {
                        if (e.target.closest('.btn-medidas')) {
                            let row = cell.getRow();
                            let rowData = row.getData();

                            // Seleccionar la fila antes de abrir el modal
                            pedidosTable.deselectRow();
                            row.select();

                            // Cargar las medidas del producto seleccionado
                            let productId = rowData.id; // ID del producto
                            cargarTablaMedidas(productId);

                            // Mostrar el modal
                            $("#modalMedidas").modal('show');
                        }
                    }
                },
                {
                    title: "Cantidad",
                    field: "cantidad",
                    editor: "number",
                    width: 120,
                    formatter: function(cell) {
                        return parseFloat(cell.getValue()).toFixed(2);
                    },
                    cellEdited: function(cell) {
                        // Recalcular el total de la fila al cambiar la cantidad
                        let row = cell.getRow();
                        calcular_total_fila(row);
                        calcular_total();
                    }
                },
                {
                    title: "Precio",
                    field: "precio",
                    formatter: function(cell) {
                        return parseFloat(cell.getValue()).toFixed(2);
                    },
                    width: 100
                },
                {
                    title: "Precio IVA",
                    field: "precioiva",
                    visible: false,
                    formatter: function(cell) {
                        return parseFloat(cell.getValue()).toFixed(2);
                    },
                    width: 100
                },
                {
                    title: "Desc.",
                    field: "descuento",
                    editor: "number",
                    width: 80,
                    formatter: function(cell) {
                        return parseFloat(cell.getValue()).toFixed(2);
                    },
                    cellEdited: function(cell) {
                        let row = cell.getRow();
                        calcular_total_fila(row);
                        calcular_total();
                    }
                },
                { title: "IVA", field: "valoriva", width: 70 },
                {
                    title: "Total",
                    field: "total",
                    formatter: function(cell) {
                        return parseFloat(cell.getValue()).toFixed(2);
                    },
                    width: 100
                },
            ],
        });

        // Asigna un evento para manejar cuando se agrega una nueva fila a la tabla de pedidos
        pedidosTable.on("rowAdded", function(row) {
            calcular_total(); // Llama a la función para calcular los totales después de agregar una fila
            $('#producto').val(''); // Limpiar el campo de búsqueda después de agregar un producto
        });


        // Agregar después de la inicialización de pedidosTable
        $("#modalObservacion").on('hide.bs.modal', function() {
            // Deshabilitar el textarea al cerrar
            $("#observacion").prop('disabled', true);
        });

        // Agregar botón de guardar al modal
        $("#guardarObservacion").on('click', function() {
            let rowIndex = $("#fila").val();
            let observacion = $("#observacion").val();

            // Actualizar la información en la tabla
            let row = pedidosTable.getRow(rowIndex);
            row.update({informacion: observacion});

            // Cerrar el modal
            $("#modalObservacion").modal('hide');
        });

        // Inicializa Tabulator para la tabla del modal de búsqueda
        var modalTable = new Tabulator("#tabla", {
            //height: "400px", // La tabla será de altura completa para permitir scroll
            layout: "fitColumns", // Ajusta las columnas al ancho disponible
            pagination: "local", // Tipo de paginación (local o remote)
            paginationSize: 10, // Número de filas por página
            paginationButtonCount: 3,
            selectable: 1, // Permitir seleccionar una sola fila
            columns: [
                {
                    title: "Acciones",
                    field: "productosid",
                    visible: false
                },
                { title: "Medidasid", field: "medidasid", visible: false, width: 50 },
                { title: "Medida", field: "medida", visible: false, width: 50 },
                { title: "Factor", field: "factor", visible: false, width: 50 },
                { title: "Código", field: "productocodigo" },
                { title: "Descripción", field: "descripcion" },
                {
                    title: "Precio",
                    field: "precio",
                    formatter: function(cell) {
                        return parseFloat(cell.getValue()).toFixed(2);
                    }
                },
                {
                    title: "Precio IVA",
                    field: "precioiva",
                    formatter: function(cell) {
                        return parseFloat(cell.getValue()).toFixed(2);
                    }
                },
                { title: "IVA", field: "valoriva", visible: false },
            ],
            locale: "es",
            langs: {
                es: {
                    data: { loading: "Cargando", error: "Error" },
                    pagination: {
                        page_size: "Ver",
                        page_title: "Ver Registros",
                        first: "<<",
                        first_title: "Primera página",
                        last: ">>",
                        last_title: "Última página",
                        prev: "<",
                        prev_title: "Página anterior",
                        next: ">",
                        next_title: "Página siguiente",
                        all: "Todos",
                    },
                    headerFilters: { default: "Buscar" },
                },
            },
        });

        // Asigna un evento para manejar la selección de filas
        modalTable.on("rowSelectionChanged", function(data, rows) {
            if (data.length > 0) {  // Si se selecciona al menos una fila
                var rowData = data[0];  // En este caso, solo una fila está permitida para seleccionar

                // Agregar la fila seleccionada a la tabla de pedidos
                pedidosTable.addRow({
                    id: rowData.productosid,
                    productocodigo: rowData.productocodigo,
                    descripcion: rowData.descripcion,
                    cantidad: 1, // Valor inicial para la cantidad
                    precio: rowData.precio,
                    precioiva: rowData.precioiva,
                    valoriva: rowData.valoriva,
                    descuento: 0,
                    total: 0, // Inicialmente en 0, se recalculará
                    medidasid: rowData.medidasid,
                    medida: rowData.medida,
                    factor: rowData.factor,
                }).then(function(row) {
                    // Llamar a la función para calcular el total de la fila
                    calcular_total_fila(row);
                    // Recalcular los totales generales después de agregar la fila
                    calcular_total();
                });

                // Cerrar el modal de búsqueda después de seleccionar un producto
                $('#modalBusqueda').modal('hide');

                // Deseleccionar todas las filas para permitir nuevas selecciones futuras
                modalTable.deselectRow();
            }
        });

         // Evento para hacer la búsqueda al presionar Enter dentro del campo de búsqueda de producto
        $('#producto').on('keydown', function(event) {
            if (event.key === "Enter") {
                event.preventDefault();  // Evita el comportamiento por defecto del Enter en formularios
                $('#buscar').click();    // Simula el clic en el botón de búsqueda
            }
        });

        // Evento para eliminar la línea seleccionada al hacer clic en el botón "Eliminar Línea"
        $('#eliminarLinea').on('click', function() {
            let selectedRows = pedidosTable.getSelectedRows(); // Obtener las filas seleccionadas
            if (selectedRows.length > 0) {
                selectedRows.forEach(function(row) {
                    row.delete(); // Eliminar la fila seleccionada
                });
                // Recalcular los totales generales después de eliminar la fila
                calcular_total();
            } else {
                alert('Por favor, seleccione una línea para eliminar.');
            }
        });

        // Evento para hacer la búsqueda y abrir el modal cuando se presiona el botón buscar
        $('#buscar').on('click', function() {
            // Obtener el valor del producto ingresado
            let producto = $('#producto').val();

            // Realizar solicitud POST para buscar los productos
            $.post('<?php echo e(route('busqueda.producto')); ?>', {
                _token: '<?php echo e(csrf_token()); ?>',
                producto: producto,
            }, function(data) {
                if (data.length === 1) {
                    // Si solo se devuelve un producto, agregarlo automáticamente
                    let productoUnico = data[0];
                    pedidosTable.addRow({
                        id: productoUnico.productosid,
                        productocodigo: productoUnico.productocodigo,
                        descripcion: productoUnico.descripcion,
                        cantidad: 1,
                        precio: productoUnico.precio,
                        precioiva: productoUnico.precioiva,
                        descuento: 0,
                        valoriva: productoUnico.valoriva,
                        total: 0,
                        medidasid: productoUnico.medidasid,
                        medidas: productoUnico.medida,
                        factor: rowData.factor,
                    }).then(function(row) {
                        // Llamar a la función para calcular el total de la fila
                        calcular_total_fila(row);
                        // Recalcular los totales generales después de agregar la fila
                        calcular_total();
                    });
                } else if (data.length > 1) {
                    // Si hay más de un producto, mostrar el modal para seleccionar
                    modalTable.setData(data);
                    $('#modalBusqueda').modal();
                } else {
                    alert('No se encontraron productos.');
                }
            }).fail(function() {
                alert('Error al buscar productos. Intente de nuevo.');
            });
        });

       // Función para calcular el total de una fila
        function calcular_total_fila(row) {
            let rowData = row.getData();
            let subtotal = rowData.cantidad * parseFloat(rowData.precioiva);
            let descuentoPorcentaje = parseFloat(rowData.descuento) || 0;
            let descuentoMonto = subtotal * (descuentoPorcentaje / 100);
            let newTotal = subtotal - descuentoMonto;
            row.update({ total: newTotal });
        }

        function calcular_total() {
            let data = pedidosTable.getData();
            let totales = {
                subtotal: 0,
                descuentoTotal: 0,
                subtotalNetoConIva: 0,  // Subtotal con IVA distinto del 5%
                subtotalNetoIva5: 0,     // Subtotal solo para los que tienen IVA del 5%
                subtotalNetoSinIva: 0,   // Subtotal para los productos sin IVA
                totalIVA: 0,             // Total IVA (distinto del 5%)
                totalIva5: 0,            // Total IVA 5%
                total: 0,
                totalItems: 0,
                totalCantidad: 0,
            };

            data.forEach(function(row) {
                let cantidad = parseFloat(row.cantidad);
                let precioiva = parseFloat(row.precioiva);
                let valoriva = parseFloat(row.valoriva) / 100; // Convertir el porcentaje del IVA a decimal (e.g., 15 -> 0.15)
                let descuentoPorcentaje = parseFloat(row.descuento) || 0;

                // Subtotal del producto (sin IVA aplicado)
                let subtotalProducto = cantidad * parseFloat(row.precio);

                // Calcular el monto de descuento
                let descuentoMonto = subtotalProducto * (descuentoPorcentaje / 100);

                // Calcular subtotal después del descuento pero antes del IVA
                let subtotalConDescuento = subtotalProducto - descuentoMonto;

                // Calcular el monto del IVA correspondiente
                let ivaMonto = subtotalConDescuento * valoriva;

                // Total del producto después del descuento y con el IVA aplicado
                let totalProducto = subtotalConDescuento + ivaMonto;

                // Actualizar los campos de totales
                totales.subtotal += subtotalProducto;
                totales.descuentoTotal += descuentoMonto;

                // Diferenciar los valores por el tipo de IVA aplicado
                if (valoriva > 0) {
                    if (valoriva === 0.05) {
                        // Si el IVA es del 5%, acumular en subtotal IVA 5%
                        totales.subtotalNetoIva5 += subtotalConDescuento;
                        totales.totalIva5 += ivaMonto;  // Acumular solo el IVA al 5%
                    } else {
                        // Si el IVA no es del 5%, acumular en subtotal con IVA distinto del 5%
                        totales.subtotalNetoConIva += subtotalConDescuento;
                        totales.totalIVA += ivaMonto;  // Acumular solo el IVA distinto del 5%
                    }
                } else {
                    // Si no tiene IVA, acumular en subtotal sin IVA
                    totales.subtotalNetoSinIva += subtotalConDescuento;
                }

                totales.total += totalProducto;
                totales.totalItems += 1; // Contar la fila como un ítem
                totales.totalCantidad += cantidad; // Sumar las cantidades

                // Actualizar el total para la fila en la tabla
                row.total = totalProducto;
            });

            totales.subtotalNeto = totales.subtotalNetoConIva + totales.subtotalNetoIva5 + totales.subtotalNetoSinIva;

            // Guardar los valores sin redondear en variables globales para enviarlos posteriormente
            window.totalesSinRedondear = {
                subtotal: totales.subtotal,
                descuentoTotal: totales.descuentoTotal,
                subtotalNetoConIva: totales.subtotalNetoConIva,
                subtotalNetoIva5: totales.subtotalNetoIva5,
                subtotalNetoSinIva: totales.subtotalNetoSinIva,
                subtotalNeto: totales.subtotalNeto,
                totalIVA: totales.totalIVA,
                totalIVA5: totales.totalIva5,
                total: totales.total,
                totalItems: totales.totalItems,
                totalCantidad: totales.totalCantidad
            };

            // Mostrar los valores redondeados visualmente al usuario
            $("#subtotal").text(totales.subtotal.toFixed(2));
            $("#descuentoPorcentaje").text(totales.descuentoTotal.toFixed(2));
            $("#subtotalNetoConIva").text(totales.subtotalNetoConIva.toFixed(2));
            $("#subtotalNetoIva5").text(totales.subtotalNetoIva5.toFixed(2));
            $("#subtotalNetoSinIva").text(totales.subtotalNetoSinIva.toFixed(2));
            $("#subtotalNeto").text(totales.subtotalNeto.toFixed(2));
            $("#totalIVA").text(totales.totalIVA.toFixed(2));
            $("#totalIVA5").text(totales.totalIva5.toFixed(2));
            $("#total").text(totales.total.toFixed(2));
            $("#totalItems").text(totales.totalItems);
            $("#totalCantidad").text(totales.totalCantidad);
        }

        $('#btnGuardar').on('click', function (event) {
            event.preventDefault();  // Evita el comportamiento predeterminado del formulario.

            // Recopilar la información de la cabecera del pedido.
            let cabeceraPedido = {
                fecha: $('#fecha').val(),
                codigo: $('#codigo').val(),
                cliente: $('#cliente').val(),
                sucursal: $('select[name="clientes_sucursalesid"]').val(),
                destinatario: $('#destinatario').val(),
                centroCosto: $('select[name="centros_costosid"]').val()
            };

            // Obtener los datos de la tabla de pedidos (productos).
            let productos = pedidosTable.getData();  // Esto obtiene todas las filas actuales en la tabla.

            // Obtener los totales sin redondear.
            let totales = window.totalesSinRedondear;

            // Enviar los datos usando AJAX.
            $.ajax({
                url: '<?php echo e(route('pedidos.guardar')); ?>',  // La URL a la que enviarás los datos.
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',  // Token CSRF para la seguridad de Laravel.
                    cabecera: cabeceraPedido,
                    productos: productos,
                    totales: totales
                },
                success: function (response) {
                    if (response.status === 'success') {
                       // Redirigir al usuario después de guardar correctamente
                       AIZ.plugins.notify('success', response.message);
                      // Esperar 2 segundos antes de redirigir al usuario
                        setTimeout(function() {
                            window.location.href = response.redirect_url;
                        }, 2000);
                    } else {
                        // Mostrar mensaje de error si el estado no es "success"
                        alert('Ocurrió un problema inesperado.');
                    }
                },
                error: function (xhr, status, error) {
                    // Manejar los errores aquí.
                    AIZ.plugins.notify('danger','Error al guardar el pedido. Intente nuevamente.');
                }
            });
        });

        function cargarTablaMedidas(productId) {
            let tablaMedidas;
            $.ajax({
                url: '<?php echo e(route("producto.medidas")); ?>',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    id: productId
                },
                success: function(response) {
                    // Si la tabla no ha sido inicializada, se crea
                    if (!tablaMedidas) {
                        tablaMedidas = new Tabulator("#tabla-medidas", {
                            height: "300px",
                            layout: "fitColumns",
                            selectable: 1, // Permite seleccionar filas
                            data: response.medidas,
                            columns: [
                                { title: "ID", field: "medidasid", visible: false },
                                { title: "Precio", field: "precio", visible: false },
                                { title: "Precio IVA", field: "precioiva", visible: false },
                                { title: "Factor", field: "factor", visible: false },
                                { title: "Descripción", field: "medida", widthGrow: 2 }
                            ]
                        });

                        // Evento de doble clic
                        tablaMedidas.on("rowDblClick", function(e, row) {
                            let medidaSeleccionada = row.getData();

                            // Obtener la fila seleccionada en pedidosTable
                            let filaSeleccionada = pedidosTable.getSelectedRows();
                            if (filaSeleccionada.length > 0) {
                                let fila = filaSeleccionada[0];

                                // Actualizar la fila en pedidosTable
                                fila.update({
                                    medidasid: medidaSeleccionada.medidasid,
                                    medida: medidaSeleccionada.medida,
                                    precio: medidaSeleccionada.precio,
                                    precioiva: medidaSeleccionada.precioiva,
                                    factor : medidaSeleccionada.factor
                                });

                                // Recalcular totales después de la actualización
                                calcular_total_fila(fila);
                                calcular_total();

                                // Cerrar el modal
                                $("#modalMedidas").modal('hide');
                            } else {
                                alert("Seleccione primero una fila en la tabla principal.");
                            }
                        });
                    } else {
                        // Actualizar datos si la tabla ya fue inicializada
                        tablaMedidas.setData(response.medidas);
                    }
                },
                error: function() {
                    alert('Error al cargar las medidas. Intente de nuevo.');
                }
            });
        }


    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\tienda\resources\views/backend/pedidos/crear.blade.php ENDPATH**/ ?>