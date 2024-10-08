<!DOCTYPE html>
<html lang="es">

<head>
    @php
        $movimientosbasico = App\Models\Movimientosbasico::find($TipoMovimiento);
        $caja = App\Models\Caja::find($caja);
        $parametizarcajas = App\Models\Parametizarcaja::where('caja_id', $caja->id)
                      ->where('estado', 'Activo')
                      ->get();
    @endphp
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('../resources/css/movimientoscreate.css') }}">
</head>
<body>
    @include('movimientos.pront')

<!-- Custom Script -->
<script src="{{ asset('../resources/js/datatableCreat.js') }}"></script>

<script>
    $(document).ready(function () {// Inicializa DataTable
        $(document).on('keydown', function(event) {
        // Verificar si se presiona Control y X al mismo tiempo
        if (event.ctrlKey && event.key === 'd') {
            event.preventDefault(); // Prevenir el comportamiento por defecto de la combinación de teclas
            buscarMovimientosPendientes(); // Llamar a la función para buscar los movimientos
        }
        if (event.ctrlKey && event.key === 'x') {
            event.preventDefault(); // Prevenir el comportamiento por defecto de la combinación de teclas
            buscarMovimientosPendientes(); // Llamar a la función para buscar los movimientos
        }
        if (event.ctrlKey && event.key === 'y') {
            event.preventDefault(); // Prevenir el comportamiento por defecto de la combinación de teclas
            buscarMovimientosPendientes(); // Llamar a la función para buscar los movimientos
        }
    });
    var table = $('#ventasTable').DataTable({
        responsive: true, 
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        // Hace la tabla responsiva
        columns: [
            { data: 'Producto_id' },
            { data: 'Descripcion' }, 
            { data: 'Cantidad_Ingreso' },
            { data: 'Impuesto_id' },
            { data: 'Descuento' },
            { data: 'Valor_Unitario' },
            { data: 'TotalValor' },
            { data: 'Observacion' },
            {
                data: 'Acciones',
                defaultContent: 'Acciones',
                orderable: false,
                searchable: false
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'collection',
                text: 'Exportar',
                buttons: [
                    'copy', 'excel'
                ],
                className: 'btn btn-secondary'
            }
        ],
        
        autoWidth: false, // Desactiva el ajuste automático del ancho
    });

    // Ajuste para los botones en dispositivos móviles
    table.buttons().container().appendTo('#ventasTable_wrapper .col-md-6:eq(0)');

    
    window.impuestosActualizar = async function (event, id) {
        var inputElement = event.target;
            var impuesto = parseFloat(inputElement.value) || 0;
            var row = $(inputElement).closest('tr');
            
            var cantidadIngreso = row.find('td:eq(2) input').val()|| 0;// Aseguramos que la cantidad mínima sea 1
            if (cantidadIngreso <= 0) {
                cantidadIngreso = 1;
                inputElement.value = 0;
            }
            
            var valorUnitario = parseFloat(row.find('td:eq(5) input').val()) || 0;
            var descuento = parseFloat(row.find('td:eq(4) input').val()) || 0;

            const cantidad = cantidadIngreso;
            const totalSinDescuento = cantidad * valorUnitario;
            const iva = 1 + (impuesto / 100);
            const subtotal = totalSinDescuento / iva;  // Subtotal antes de aplicar el IVA
            const impuestoTotal = totalSinDescuento - subtotal; // Impuesto calculado
            const totalConDescuento = subtotal - descuento; // Subtotal menos el descuento
            const totalFinal = totalConDescuento + impuestoTotal;

            $.ajax({
                url: `{{ route("movimientosdatallados.update", "") }}/${id}`,
                method: 'PATCH',
                data: {
                    Cantidad_Ingreso: cantidadIngreso,
                    TotalValor: totalFinal,
                    Impuesto_id: impuestoTotal,
                    Impuesto :impuesto,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Actualizar el total de la fila
                    row.find('.total').text(totalFinal.toFixed(2));

                    // Recalcular y actualizar totales
                    actualizarTotales();
                },
                error: function (error) {
                    console.error('Error al actualizar el movimiento:', error);
                }
            });
        }
    
        window.updateCuentaI = async function (event, id) {
            var inputElement = event.target;
            var cantidadIngreso = parseFloat(inputElement.value) || 1; // Aseguramos que la cantidad mínima sea 1
            if (cantidadIngreso <= 0) {
                cantidadIngreso = 1;
                inputElement.value = 1;
            }
            var row = $(inputElement).closest('tr');
            var valorUnitario = parseFloat(row.find('td:eq(5) input').val()) || 0;
            var impuesto = parseFloat(row.find('td:eq(3) input').val()) || 0;
            var descuento = parseFloat(row.find('td:eq(4) input').val()) || 0;

            const cantidad = cantidadIngreso;
            const totalSinDescuento = cantidad * valorUnitario;
            const iva = 1 + (impuesto / 100);
            const subtotal = totalSinDescuento / iva;  // Subtotal antes de aplicar el IVA
            const impuestoTotal = totalSinDescuento - subtotal; // Impuesto calculado
            const totalConDescuento = subtotal - descuento; // Subtotal menos el descuento
            const totalFinal = totalConDescuento + impuestoTotal;

            $.ajax({
                url: `{{ route("movimientosdatallados.update", "") }}/${id}`,
                method: 'PATCH',
                data: {
                    Cantidad_Ingreso: cantidadIngreso,
                    TotalValor: totalFinal,
                    Impuesto_id: impuestoTotal,
                    Impuesto :impuesto,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Actualizar el total de la fila
                    row.find('.total').text(totalFinal.toFixed(2));

                    // Recalcular y actualizar totales
                    actualizarTotales();
                },
                error: function (error) {
                    console.error('Error al actualizar el movimiento:', error);
                }
            });
        }

        window.precio = async function (event, id) {
            var inputElement = event.target;
            var valorUnitario = parseFloat(inputElement.value) || 0;
            var row = $(inputElement).closest('tr');
            var cantidadIngreso = parseFloat(row.find('td:eq(2) input').val()) || 0;
            var impuesto = parseFloat(row.find('td:eq(3) input').val()) || 0;
            var descuento = parseFloat(row.find('td:eq(4) input').val()) || 0;

            const cantidad = cantidadIngreso;
            const totalSinDescuento = cantidad * valorUnitario;
            const iva = 1 + (impuesto / 100);
            const subtotal = totalSinDescuento / iva;  // Subtotal antes de aplicar el IVA
            const impuestoTotal = totalSinDescuento - subtotal; // Impuesto calculado
            const totalConDescuento = subtotal - descuento; // Subtotal menos el descuento
            const totalFinal = totalConDescuento + impuestoTotal;

            $.ajax({
                url: `{{ route("movimientosdatallados.update", "") }}/${id}`,
                method: 'PATCH',
                data: {
                    Valor_Unitario: valorUnitario,
                    TotalValor: totalFinal,
                    Impuesto_id: impuestoTotal,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Actualizar el total de la fila
                    row.find('.total').text(totalFinal.toFixed(2));

                    // Recalcular y actualizar totales
                    actualizarTotales();
                },
                error: function (error) {
                    console.error('Error al actualizar el movimiento:', error);
                }
            });
        }

    
        window.Observacion = async function (event, id) {
            var inputElement = event.target;
            var cantidadEgreso = inputElement.value;
            var row = $(inputElement).closest('tr');
            var precioUnitario = parseFloat(row.find('td:eq(5) input ').val());

            $.ajax({
                url: `{{ route("movimientosdatallados.update", "") }}/${id}`,
                method: 'PATCH',
                data: {
                    Obervacion: cantidadEgreso,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Actualizar el total de la fila



                    // Recalcular y actualizar totales
                    actualizarTotales();
                },
                error: function (error) {
                    console.error('Error al actualizar el movimiento:', error);
                }
            });
        }
        window.EliminarDetalle = async function ( id) {
            $.ajax({
                url: `{{ route("movimientosdatallados.destroy", "") }}/${id}`,
                method: 'DELETE',
                data: {
                  
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Actualizar el total de la fila
                  
                    // Recalcular y actualizar totales
                    actualizarTotales();
                },
                error: function (error) {
                    console.error('Error al actualizar el movimiento:', error);
                }
            });
        }
        window.Descuentos = async function (event, id) {
            var inputElement = event.target;
            var descuento = parseFloat(inputElement.value) || 0;
            var row = $(inputElement).closest('tr');
            var precioUnitario = parseFloat(row.find('td:eq(5) input').val());
            var cantidad = parseFloat(row.find('td:eq(2) input').val()) || parseFloat(row.find('td:eq(3) input').val()) || 1;
            
            var precioConDescuento = precioUnitario - descuento;
            var nuevoTotal = precioConDescuento * cantidad;
                console.log(descuento);
           
            

               $.ajax({
                url: `{{ route("movimientosdatallados.update", "") }}/${id}`,
                method: 'PATCH',
                data: {
                    Descuento: descuento,
                    TotalValor:nuevoTotal,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    // Actualizar el total de la fila
                    row.find('.total').text(nuevoTotal.toFixed(2));

                    console.log(response);
                    // Recalcular y actualizar totales
                    actualizarTotales();
                },
                error: function (error) {
                    console.error('Error al actualizar el movimiento:', error);
                }
            });
            
        }
       



        let Movimientos = null;
        const searchModal = new bootstrap.Modal(document.getElementById('searchModal'));
        const pendientesModal = new bootstrap.Modal(document.getElementById('pendientesModal'));
        let currentSearchType = '';

        // Funciones de búsqueda y modal
        function openSearchModal(type) {
            currentSearchType = type;
            let labelText, placeholderText;
            switch (type) {
                case 'cliente':
                    labelText = 'Cliente';
                    placeholderText = 'Ingrese nombre o ID de cliente';
                    // Agregar botón de crear usuario o proveedor solo para clientes
                    $('#searchModal .modal-body').append('<button id="crearUsuarioProveedorBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Crear Usuario </button>');
                    break;
                case 'proveedor':
                    labelText = 'Proveedor';
                    placeholderText = 'Ingrese nombre o ID de proveedor';
                    $('#searchModal .modal-body').append('<button id="crearUsuarioProveedorBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#Proveedor">Crear Proveedor</button>');
                    
                    break;
                case 'caja':
                    labelText = 'Caja';
                    placeholderText = 'Ingrese nombre o ID de caja';
                    break;
                case 'ocaja':
                    labelText = 'OCaja';
                    placeholderText = 'Ingrese nombre o ID de OCaja';
                    break;
                default:
                    labelText = 'Bodega';
                    placeholderText = 'Ingrese nombre o ID de bodega';
                    break;
            }
            $('#searchModalLabel').text('Buscar ' + labelText);
            $('#searchModalInput').val('').attr('placeholder', placeholderText);
            $('#searchResults').empty(); // Limpiar resultados anteriores
            searchModal.show();
        }

        $('#OrigenBodega_id, #DestinoBodega_id').on('change', function () {
            var origenValue = $('#OrigenBodega_id').val();
            var destinoValue = $('#DestinoBodega_id').val();
            $('#BodegaOrigen').val(origenValue);
            $('#BodegaDestino').val(destinoValue);
        });

        $('#buscarCliente, #buscarProveedor, #Caja, #OCaja').on('click focus', function () {
            const type = this.id.replace('buscar', '').toLowerCase().replace('_id', '');
            openSearchModal(type);
        });

        $('#searchModalInput').on('input', function () {
            const termino = $(this).val();
            buscar(termino);
        });

        function buscar(termino) {
            let url;
            switch (currentSearchType) {
                case 'cliente':
                    url = '{{ route("cliente.buscar") }}';
                    break;
                case 'proveedor':
                    url = '{{ route("proveedor.BuscarProveedor") }}';
                    break;
                case 'caja':
                case 'ocaja':
                    url = '{{ route("cuenta.buscar") }}';
                    break;
            }

            $.ajax({
                url: url,
                method: 'GET',
                data: { term: termino },
                success: mostrarResultados,
                error: error => console.error('Error en la búsqueda:', error)
            });
        }

        function mostrarResultados(resultados) {
            const html = resultados.map(item =>
                `<div class="search-item" data-id="${item.id}" data-nombre="${ item.RazonSocial || item.Nombre || item.NumeroDocumento|| item.descripcion || item.Nombre1 + " " + item.Nombre2 + " " + item.Apellido1 + " " + item.Apeelido2 || item.Descripcion || item.descripcion}">
                    ${item.descripcion || item.RazonSocial || item.NumeroDocumento || item.Nombre1 + " " + item.Nombre2 + " " + item.Apellido1 + " " + item.Apeelido2 || item.Descripcion} - ${item.NDocumento || item.NumeroDocumento || item.numero}
                </div>`
            ).join('');
            $('#searchResults').html(html);
        }

        $(document).on('click', '.search-item', function () {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');
            let inputId, hiddenInputId;

            switch (currentSearchType) {
                case 'cliente':
                    inputId = '#buscarCliente';
                    hiddenInputId = '#Users';
                    break;
                case 'proveedor':
                    inputId = '#buscarProveedor';
                    hiddenInputId = '#Proveedor';
                    break;
                case 'caja':
                    inputId = '#Caja';
                    hiddenInputId = '#CajaInput';
                    break;
                case 'ocaja':
                    inputId = '#OCaja';
                    hiddenInputId = '#CajaOnput';
                    break;
                default:
                    inputId = '#buscarBodega';
                    hiddenInputId = '#Bodega';
                    break;
            }

            $(inputId).val(nombre);
            $(hiddenInputId).val(id);
            searchModal.hide();
        });

        // Eliminar el botón de crear usuario o proveedor al cerrar el modal
        $('#searchModal').on('hidden.bs.modal', function () {
            $('#crearUsuarioProveedorBtn').remove();
        });

        // Actualización de fecha
        function updateDate() {
            const date = new Date();
            const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
            document.getElementById('currentDate').textContent = date.toLocaleDateString('es-ES', options).replace(',', ' -');
        }

        updateDate();
        setInterval(updateDate, 60000);  // Actualizar cada minuto

        // Autocomplete de productos
        $("#buscarProducto").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: '{{ route("producto.buscar") }}',
                    data: { term: request.term },
                    success: function (data) {
                        
                        response(data.map(function (item) {
                            return {
                                label: `${item.Descripcion} - ${item.producto.id} - ${item.producto.Descripcion}`,
                                value: item.producto.Descripcion,
                                id: item.producto.id,
                                codigo: item.Codigo,
                                impuesto:item.producto.actualizarprecios && item.producto.actualizarprecios[0] ? item.producto.actualizarprecios[0].ImpuestoPorcentageTotal : 0,
                                
                                precio: item.producto.actualizarprecios && item.producto.actualizarprecios[0] ? item.producto.actualizarprecios[0].ImpuestoPublico : 0
                            };
                        }));
                    },
                    error: error => console.error('Error en la búsqueda:', error)
                });
            },
            select: function (event, ui) {
                agregarProducto(ui.item).catch(error => console.error('Error al agregar producto:', error));
                $(this).val(''); // Limpiar el campo después de seleccionar
                return false;
            }
        });

        $('#buscarProducto').on('keypress', function (event) {
            if (event.keyCode === 13) {
                const autocompleteInstance = $(this).autocomplete('instance');
                const firstItem = autocompleteInstance.menu.element.find('li:first').data('ui-autocomplete-item');
                if (firstItem) {
                    agregarProducto(firstItem).catch(error => console.error('Error al agregar producto:', error));
                    $(this).val('');
                    event.preventDefault();
                }
            }
        });
        $('#CantidadIngreso').on('keypress', function (event) {
            if (event.keyCode === 13) {
                const autocompleteInstance = $(this).autocomplete('instance');
                const firstItem = autocompleteInstance.menu.element.find('li:first').data('ui-autocomplete-item');
                if (firstItem) {
                    agregarProducto(firstItem).catch(error => console.error('Error al agregar producto:', error));
                    $(this).val('');
                    event.preventDefault();
                }
            }
        });
        $('#Impuesto_id').on('keypress', function (event) {
            if (event.keyCode === 13) {
                const autocompleteInstance = $(this).autocomplete('instance');
                const firstItem = autocompleteInstance.menu.element.find('li:first').data('ui-autocomplete-item');
                if (firstItem) {
                    agregarProducto(firstItem).catch(error => console.error('Error al agregar producto:', error));
                    $(this).val('');
                    event.preventDefault();
                }
            }
        });

        // Funciones de manejo de movimientos y productos
        function CrearDetalle() {
            return new Promise((resolve, reject) => {
                const origenBodega = $('#BodegaOrigen').val();
                const proveedor = $('#Proveedor').val();
                const usuarioDestino = $('#Users').val();
                const destinoBodega = $('#BodegaDestino').val();
                const CuentaEn = $('#CajaInput').val();
                const CuentaSL = $('#CajaOnput').val();
                const TipoMovimientos =$('#TipoMovimiento').val();
                const data = {
                    users_id: '{{$users}}',
                    Caja_id: '{{$caja->id}}',
                    TipoMovimiento_id: '{{$TipoMovimiento}}',
                    OrigenBodega_id: origenBodega,
                    Cuenta_Salida: CuentaSL,
                    Cuenta_Entrada: CuentaEn,
                    OrigenProveedor_id: proveedor,
                    UsuarioDestino_id: usuarioDestino,
                    DestinoBodega_id: destinoBodega,
                    TipoMovimiento:TipoMovimientos,
                    estado: 'Pendiente',
                    estadoMovimientosCaja:'EnEjecucion'
                };

                fetch('{{Route("movimientos.CrearMovimientosDetalle")}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Error en la solicitud');
                        return response.json();
                    })
                    .then(resolve)
                    .catch(reject);
            });
        }

        function CrearMovimiento(Movimientos_ids, Producto_ids, Cantidad_Ingresos,Descuento, Valor_Unitarios, TotalValors, Impuesto_ids, users_ids,Impuesto) {
            
            return new Promise((resolve, reject) => {
                const data = {
                    Movimientos_id: Movimientos_ids,
                    Producto_id: Producto_ids,
                    Cantidad_Ingreso: Cantidad_Ingresos,
                    Valor_Unitario: Valor_Unitarios,
                    TotalValor: TotalValors,
                    Descuento:Descuento,
                    Impuesto:Impuesto,

                    Impuesto_id: Impuesto_ids,
                    users_id: users_ids,
                };

                fetch('{{Route("movimientosdatallados.store")}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Error en la solicitud');
                        return response.json();
                    })
                    .then(resolve)
                    .catch(reject);
            });
        }

        async function agregarProducto(producto) {
            // Si no hay un movimiento activo, creamos uno nuevo
            if (Movimientos == null) {
                try {
                    Movimientos = await CrearDetalle();
                } catch (error) {
                    console.error('Error al crear detalle:', error);
                    return;
                }
            }

            try {
                var cantidI = $("#CantidadIngreso").val() || "1";
                var Impuestos = producto.Impuesto_id ||  $("#Impuesto_id").val() || "0";

                // Usamos el ID del movimiento existente
                const nuevoMovimiento = await CrearMovimiento(
                    Movimientos.id,
                    producto.id,
                    cantidI,
                    Descuento,
                    producto.precio,
                    producto.precio * cantidI ,
                    
                    Impuestos,
                    {{ $users }},Impuestos

        );
    agregarProductoATabla(nuevoMovimiento);
    } catch (error) {
        console.error('Error al crear movimiento:', error);
        return;
    }

    actualizarTotales();
    }

    // Eventos de tabla de productos
    // Esta función se ejecuta cuando se modifica el valor de un campo con la clase 'cantidad'
    


    $(document).on('click', '.eliminar', function () {
    var table = $('#ventasTable').DataTable();
    table
        .row($(this).parents('tr'))
        .remove()
        .draw();
    actualizarTotales();
    
    });

 function actualizarTotales () {
    let totalUnidades = 0, totalFilas = 0, totalGeneral = 0, totalImpuestos = 0;
    
    // Obtener las filas como nodos DOM
    var rows = $('#ventasTable').DataTable().rows().nodes();
    
    $(rows).each(function (index, row) {
        const cantidadIngreso = parseFloat($(row).find('td:eq(2) input').val()) || 0;
        const impuesto = parseFloat($(row).find('td:eq(3) input').val()) || 0;
        const valorUnitario = parseFloat($(row).find('td:eq(5) input').val()) || 0;
        const descuento = parseFloat($(row).find('td:eq(4) input').val()) || 0;

        const cantidad = cantidadIngreso;
        const totalSinDescuento = cantidad * valorUnitario;
        const iva = 1 + (impuesto / 100);
        const subtotal = totalSinDescuento / iva;  // Subtotal antes de aplicar el IVA
        const impuestoTotal = totalSinDescuento - subtotal; // Impuesto calculado
        const totalConDescuento = subtotal - descuento; // Subtotal menos el descuento
        const totalFinal = totalConDescuento + impuestoTotal; // Total final después del descuento e IVA

        // Acumular totales generales
        totalUnidades += cantidad;
        totalImpuestos += impuestoTotal;
        totalGeneral += totalFinal;
        totalFilas++;

        // Actualizar el total en la fila sin cambiar el foco
        $(row).find('.total').text(totalFinal.toFixed(2));

    });
    
    // Actualizar los totales en el DOM sin cambiar el foco
    $('#rowCount').text(totalFilas);
    $('#unitCount').text(totalUnidades);
    $('#grandTotal').text('$' + totalGeneral.toFixed(2));
    $('#grandImpuestos').text('$' + totalImpuestos.toFixed(2));
    
    // Actualizar la tabla sin redibujarla completamente
    $('#ventasTable').DataTable().columns.adjust();
}

    // Buscar movimientos pendientes
    $('#buscarPendientesBtn').on('click', buscarMovimientosPendientes);

   function buscarMovimientosPendientes() {
    $.ajax({
        url: '{{ route("movimientos.pendientes", ["caja" => $caja->id, "movimiento" => 1, "users" => $users]) }}',
        method: 'GET',
        success: function (data) {
            mostrarMovimientosPendientes(data);
            console.log(data);
        },
        error: function (error) {
            console.error('Error al buscar movimientos pendientes:', error);
        }
    });
    }
        function mostrarMovimientosPendientes(movimientos) {
        let html = '';
        console.log(movimientos);
        
        // Verificar si movimientos es un array
        if (Array.isArray(movimientos)) {
            movimientos.forEach(movimiento => {
                html += crearFilaMovimiento(movimiento);
            });
        }
        // Si es un objeto, podría ser un solo movimiento o una respuesta con estructura diferente
        else if (typeof movimientos === 'object' && movimientos !== null) {
            // Si tiene una propiedad 'data', asumimos que es la estructura de respuesta de Laravel
            if (Array.isArray(movimientos.data)) {
                movimientos.data.forEach(movimiento => {
                    html += crearFilaMovimiento(movimiento);
                });
            } else {
                // Si no es un array, tratarlo como un solo movimiento
                html += crearFilaMovimiento(movimientos);
            }
        } else {
            console.error('Formato de movimientos no reconocido:', movimientos);
            html = '<tr><td colspan="4">No se pudieron cargar los movimientos</td></tr>';
        }

        $('#pendientesTableBody').html(html);
        pendientesModal.show();
    }

    function crearFilaMovimiento(movimiento) {
        console.log(movimiento);

        return `
        <tr>
            <td>${movimiento.id}</td>
            <td>${movimiento.created_at || 'N/A'}</td>
            <td>$${(movimiento.Total || 0)}</td>
            <td>
                <button class="btn btn-primary btn-sm editarMovimiento" data-id="${movimiento.id}">Editar</button>
            </td>
        </tr>
    `;
    }

    // Evento para cuando se hace clic en un cliente
    $(document).on('click', '.cliente-row', function () {
        const clienteId = $(this).data('cliente-id');
        cargarMovimientosCliente(clienteId);
    });

    function cargarMovimientosCliente(clienteId) {
        $.ajax({
            url: `{{ url('movimientos.cliente', '') }}/${clienteId}`,
            method: 'GET',
            success: function (response) {
                console.log('Movimientos del cliente cargados:', response);
                if (response && response.length > 0) {
                    mostrarMovimientosPendientes(response);
                } else {
                    alert('No se encontraron movimientos para este cliente');
                }
            },
            error: function (error) {
                console.error('Error al cargar los movimientos del cliente:', error);
                alert('Error al cargar los movimientos del cliente');
            }
        });
    }
    window.imprimirs = async function ( id) {
        var pdfUrl = '{{ route("movimientos.pdf", ":id") }}'.replace(':id', id) + '?size=carta';
        window.open(pdfUrl, '_blank', 'width=800,height=600');
    }

    function mostrarMovimientosPendientes(movimientos) {
        console.log(movimientos);
        
        let htmlMovimientos = '';

        movimientos.forEach(movimiento => {
            htmlMovimientos += `
            <tr class="movimiento-row" data-movimiento-id="${movimiento.id}">
                <td>${movimiento.id || 'N/A'}</td>
                <td>${movimiento.created_at ? new Date(movimiento.created_at).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }) : 'N/A'}</td>
                <td>${movimiento.usuariobasico ? `${movimiento.usuariobasico.Apellido1 || ''} ${movimiento.usuariobasico.Apellido2 || ''} ${movimiento.usuariobasico.Nombre1 || ''} ${movimiento.usuariobasico.Nombre2 || ''}`.trim() : 'N/A'}</td>
                <td>$${(movimiento.Total || 0)}</td>
                <td><button class="btn btn-sm btn-primary imprimir-movimiento" onclick=imprimirs(${movimiento.id})>Imprimir</button></td>
            </tr>
        `;
        });

        // Actualizar la tabla de movimientos en el modal
        $('#movimientosTableBody').html(htmlMovimientos);

        // Mostrar el modal
        $('#pendientesModal').modal('show');
    }

    $(document).on('contextmenu', '.movimiento-row', function (e) {
        e.preventDefault(); // Evita que se abra el menú contextual por defecto en el navegador

        const movimientoId = $(this).data('movimiento-id');
        cargarMovimientoPendiente(movimientoId);


        // Aquí puedes agregar el código que deseas ejecutar al detectar un clic derecho
        // Por ejemplo, mostrar un menú contextual personalizado

        return false;
    })

    // Evento para cuando se hace clic en un movimiento
    $(document).on('click', '.movimiento-row', function () {
        const movimientoId = $(this).data('movimiento-id');
        cargarProductosMovimiento(movimientoId);
    });

    function cargarProductosMovimiento(movimientoId) {
        $.ajax({
            url: `{{ route('movimientos.mostrars', '') }}/${movimientoId}`,
            method: 'GET',
            success: function (response) {
                console.log('Productos del movimiento cargados:', response);
                if (response) {
                    mostrarProductosEnTabla(response);
                } else {
                    alert('No se encontraron productos para este movimiento');
                }
            },
            error: function (error) {
                console.error('Error al cargar los productos del movimiento:', error);
                alert('Error al cargar los productos del movimiento');
            }
        });
    }

    function mostrarProductosEnTabla(productos) {
        let htmlProductos = '';

        productos.forEach(producto => {
            htmlProductos += `
            <tr>
                <td>${producto.id || 'N/A'}</td>
                <td>${producto.productos.Descripcion || 'N/A'}</td>
                <td>${producto.Cantidad_Egreso || producto.Cantidad_Ingreso || 'N/A'}</td>
                <td>$${producto.TotalValor || 'N/A'}</td>
            </tr>
        `;
        });

        // Actualizar la tabla de productos en el modal
        $('#productosTableBody').html(htmlProductos);
    }

    // Evento para editar movimiento
    $('#editarMovimientoBtn').on('click', function () {
        const movimientos = $(this).data('movimientos');
        if (movimientos && movimientos.length > 0) {
            // Tomamos el primer movimiento para editar
            const primerMovimiento = movimientos[0];
            cargarMovimientoPendiente(primerMovimiento.id);
        } else {
            alert('No hay movimientos para editar');
        }
    });

    function cargarMovimientoPendiente(movimientoId) {
        $.ajax({
            url: `{{ route('movimientos.obtener', '') }}/${movimientoId}`,
            method: 'GET',
            success: function (response) {
                console.log('Movimiento cargado:', response);
                if (response && response.id) {
                    llenarFormularioConMovimiento(response);
                    $('#pendientesModal').modal('hide');
                } else {
                    alert('No se pudo cargar el movimiento');
                }
            },
            error: function (error) {
                console.error('Error al cargar el movimiento:', error);
                alert('Error al cargar el movimiento');
            }
        });
    }

    function llenarFormularioConMovimiento(movimiento) {
        console.log(movimiento);

        // Actualizar la variable global Movimientos
        Movimientos = movimiento;

        // Llena los campos del formulario con los datos del movimiento
        $('#BodegaOrigen').val(movimiento.OrigenBodega_id);
        $('#BodegaDestino').val(movimiento.DestinoBodega_id);
        $('#OrigenBodega_id').val(movimiento.OrigenBodega_id).trigger('change');
        $('#DestinoBodega_id').val(movimiento.DestinoBodega_id).trigger('change');
        $('#Proveedor').val(movimiento.OrigenProveedor_id);
        $('#Users').val(movimiento.UsuarioDestino_id);
        $('#CajaInput').val(movimiento.Cuenta_Entrada);
        $('#CajaOnput').val(movimiento.Cuenta_Salida);

        // Limpiar la tabla de productos actual
        $('#ventasTable').DataTable().clear().draw();

        // Cargar los productos del movimiento
        if (movimiento.movimientosdatallados && Array.isArray(movimiento.movimientosdatallados)) {
            movimiento.movimientosdatallados.forEach(detalle => {
                agregarProductoATabla(detalle);
            });
        }

        actualizarTotales();
    }

    function agregarProductoATabla(detalle) {
        console.log(detalle);
        const newRow = {
        Producto_id: detalle.Producto_id,
        Descripcion: detalle.productos.Descripcion || 'N/A',
        Cantidad_Ingreso: `<input type="number" class="form-control cantidadI" onkeyup="updateCuentaI(event, ${detalle.id})" value="${detalle.Cantidad_Ingreso}" min="0">`,
        Impuesto_id: `<input type="number" class="form-control cantidad" onkeyup="impuestosActualizar(event, ${detalle.id})" value="${detalle.Impuesto_id}" min="0">`,
       
        Descuento: `<input type="number" class="form-control descuento" onkeyup="Descuentos(event, ${detalle.id})" value="${detalle.Descuento}" min="0">`,
        
        Valor_Unitario: `<input type="number" class="form-control  " onkeyup="precio(event, ${detalle.id})" value="${detalle.Valor_Unitario}" min="0">`,
        TotalValor:`  <div class="total" >${detalle.TotalValor}</div>`,
        Observacion: `<input type="text" class="form-control" onchange="Observacion(event, ${detalle.id})" value="${detalle.Observacion || ''}" min="0">`,
        Acciones: `<button onclick="EliminarDetalle(${detalle.id})" class="btn btn-danger  btn-sm eliminar"><i class="fas fa-trash"></i></button>`
    };

    // Agregar la nueva fila a la DataTable
    table.row.add(newRow).draw();
    }


    // Asegúrate de que esta función esté actualizada para manejar la edición
    async function agregarProducto(producto) {
        if (Movimientos == null) {
            try {
                Movimientos = await CrearDetalle();
            } catch (error) {
                console.error('Error al crear detalle:', error);
                return;
            }
        }

        try {
            var cantidI = $("#CantidadIngreso").val();
            
           
            var Descuento= $("#Descuento").val();
            if (cantidI == null || cantidI == "") {
                cantidI = 1;
            }
            console.log("agregando productos "  + producto.impuesto);
            var impuestos =  producto.impuesto;
            if (impuestos == null || impuestos == "") {
                impuestos =  $("#Impuesto_id").val() ;
            }

            const nuevoMovimiento = await CrearMovimiento(Movimientos.id, producto.id, cantidI,Descuento ,producto.precio, producto.precio,  impuestos, {{ $users }},impuestos);
        agregarProductoATabla(nuevoMovimiento);
    } catch (error) {
        console.error('Error al crear movimiento:', error);
        return;
    }

    actualizarTotales();
    }

    // Actualiza esta función si es necesario

    // Finalizar movimiento
    $('#finalizarMovimientoBtn').on('click', function () {
        if (Movimientos) {
            // Obtener los valores justo antes de usarlos
            var Total = parseFloat($('#grandTotal').text().replace('$', '').replace(',', ''));
            var Impuestos = parseFloat($('#grandImpuestos').text().replace('$', '').replace(',', ''));
            var Proveedor = $('#Proveedor').val();
            var Users = $('#Users').val();
            var CajaInput = $('#CajaInput').val();
            var CajaOnput = $('#CajaOnput').val();
            var OrigenBodega_id = $('#OrigenBodega_id').val();
            var DestinoBodega_id = $('#DestinoBodega_id').val();
            var TipoMovimientos =$('#TipoMovimiento').val();
            console.log('Valores obtenidos:', {
                Total, Impuestos, Proveedor, Users, CajaInput, CajaOnput, OrigenBodega_id, DestinoBodega_id
            });

            $.ajax({
                url: `{{ route("movimientos.update", "") }}/${Movimientos.id}`,
                method: 'PUT',
                data: {
                    estado: 'Pendiente',
                    ValorImpuesto: Impuestos,
                    ValorSinImpuesto: Total - Impuestos,
                    OrigenBodega_id: OrigenBodega_id,
                    DestinoBodega_id: DestinoBodega_id,
                    UsuarioDestino_id: Users,
                    OrigenProveedor_id: Proveedor,
                    Cuenta_Salida: CajaOnput,
                    Cuenta_Entrada: CajaInput,
                    TipoMovimiento:TipoMovimientos,
                    Total: Total,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    console.log(response);
                    // Abrir una nueva ventana emergente con el PDF
                    var pdfUrl = '{{ route("movimientos.pdf", ":id") }}'.replace(':id', response.id) + '?size=tirilla';
                    window.open(pdfUrl, '_blank', 'width=800,height=600');
                    // Limpiar la interfaz o redirigir según sea necesario
                    limpiarInterfaz();
                },
                error: function (error) {
                    console.error('Error al finalizar el movimiento:', error);
                }
            });
        } else {
            alert('No hay un movimiento activo para finalizar');
        }
    });

    // Abrir modal de cobro
    $('#abrirCobroBtn').on('click', abrirModalCobro);

    function abrirModalCobro() {
        let totalGeneral = parseFloat($('#grandTotal').text().replace('$', '').replace(',', '').trim());
        let totalImpuestos = parseFloat($('#grandImpuestos').text().replace('$', '').replace(',', '').trim());
        var usuario = document.getElementById('Users').value;
        var proveedor = document.getElementById('Proveedor').value;
        var bodegaOrigen = document.getElementById('OrigenBodega_id').value;
        var bodegaDestino = document.getElementById('BodegaDestino').value;
        var carteraOrigen = document.getElementById('CajaInput').value;
        var carteraDestino = document.getElementById('CajaOnput').value;
        if (isNaN(totalGeneral)) {
            console.error('El total no es un número válido:', $('#grandTotal').text());
            totalGeneral = 0;
        }

        let valorSinImpuesto = totalGeneral - totalImpuestos; // Asumiendo un IVA del 19%
        let valorImpuesto = totalImpuestos;

        let modalHtml = `
        <div class="modal fade" id="cobroModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cobro</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <h2 class="text-center mb-4">Total a cobrar: $<span id="totalACobrar">${totalGeneral.toFixed(2)}</span></h2>
                        <div class="form-group mb-3">
                            <label for="montoRecibido" class="form-label">Monto recibido:</label>
                            <input type="number" class="form-control form-control-lg" id="montoRecibido" placeholder="Ingrese el monto recibido">
                            <label for="ObservacionesMovimientos" class="form-label">Observaciones:</label>
                            <input type="text" class="form-control form-control-lg" id="ObservacionesMovimientos" placeholder="Observaciones">
                        </div>
                        <h3 class="mt-4">Cambio a devolver: $<span id="cambioADevolver">0.00</span></h3>
                        <p>Valor sin Impuesto: $${valorSinImpuesto.toFixed(2)}</p>
                        <p>Valor Impuesto: $${valorImpuesto.toFixed(2)}</p>
                        <select id="metodoPago" class="form-select mt-3">
                            <option value="EFECTIVO">Efectivo</option>
                            <option value="TARJETA">Tarjeta</option>
                            <option value="CUENTA BANCARIA">Cuenta Bancaria</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="confirmarCobroBtn">Confirmar Cobro</button>
                    </div>
                </div>
            </div>
        </div>
    `;

        $('#cobroModal').remove();
        $('body').append(modalHtml);

        let cobroModal = new bootstrap.Modal(document.getElementById('cobroModal'));
        cobroModal.show();

        $('#cobroModal').on('shown.bs.modal', function () {
            $('#montoRecibido').focus();
        });

        $('#montoRecibido').on('input', function () {
            let montoRecibido = parseFloat($(this).val()) || 0;
            let cambio = montoRecibido - totalGeneral;
            $('#cambioADevolver').text(cambio.toFixed(2));
        });

        $('#confirmarCobroBtn').on('click', function () {
            let metodoPago = $('#metodoPago').val();
            let ObservacionesMovi = $('#ObservacionesMovimientos').val();
            let montoRecibido = parseFloat($('#montoRecibido').val()) || 0;
            const TipoMovimientos= $('#TipoMovimiento').val();
            if (montoRecibido >= totalGeneral) {
                finalizarCobro(
                    usuario,
                    proveedor,
                    bodegaOrigen,
                    bodegaDestino,
                    carteraOrigen,
                    carteraDestino,
                    
                    totalGeneral, valorSinImpuesto, valorImpuesto, metodoPago, montoRecibido,ObservacionesMovi,TipoMovimientos);
                cobroModal.hide();
            } else {
                alert('El monto recibido debe ser igual o mayor al total a cobrar.');
            }
        });
    }

    function finalizarCobro(
                    usuario,
                    proveedor,
                    bodegaOrigen,
                    bodegaDestino,
                    carteraOrigen,
                    carteraDestino,
                    total, valorSinImpuesto, valorImpuesto, metodoPago, montoRecibido,Observacion,TipoMovimientos) {
       if (Movimientos) {
            $.ajax({
                url: `{{ route("movimientos.update", "") }}/${Movimientos.id}`,
                method: 'PUT',
                data: {
                    UsuarioDestino_id:usuario,
                    OrigenProveedor_id:proveedor,
                    OrigenBodega_id:bodegaOrigen,
                    DestinoBodega_id:bodegaDestino,
                    Cuenta_Entrada: carteraOrigen,
                    Cuenta_Salida:carteraDestino,
                    TipoMovimiento:TipoMovimientos,

                    estado: 'Finalizado',
                    Total: total,
                    ValorSinImpuesto: valorSinImpuesto,
                    ValorImpuesto: valorImpuesto,
                    metodoPago: metodoPago,
                    montoRecibido: montoRecibido,
                    Observacion: Observacion,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    
                    window.location.href = '{{ route("movimientos.pdf", ":id") }}'.replace(':id', response.id) + '?size=carta';
                    limpiarInterfaz();
                },
                error: function (error) {
                    console.error('Error al finalizar el cobro:', error);
                }
            });
        } else {
            alert('No hay un movimiento activo para cobrar');
        }
    }



    function limpiarInterfaz() {
        // Limpiar la tabla de productos
        $('#ventasTable').DataTable().clear().draw();
        // Resetear totales
        $('#rowCount').text('0');
        $('#unitCount').text('0');
        $('#pointCount').text('0');
        $('#weightCount').text('0');
        $('#grandImpuestos').text('0');
        $('#grandTotal').text('$0.00');
        // Limpiar campos de búsqueda
        $('#buscarProveedor, #buscarCliente, #OrigenBodega_id, #DestinoBodega_id').val('');
        // Resetear el objeto Movimientos
        Movimientos = null;
    }

    // Evento para la tecla F4 en PC (buscar pendientes)
    $(document).on('keydown', function (e) {
        if (e.which === 115) { // F4
            e.preventDefault();
            buscarMovimientosPendientes();
        }
    });




    // Evento para la tecla F2 en PC (abrir cobro)
    $(document).on('keydown', function (e) {
        if (e.which === 113) { // F2
            e.preventDefault();
            abrirModalCobro();
        }
    });
    });
</script>
</body>
</html>