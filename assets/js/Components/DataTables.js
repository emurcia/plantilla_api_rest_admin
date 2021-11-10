export default () => {
    let options = {
        "language": {
            decimal: "",
            emptyTable: "Sin resultados para mostrar...",
            info: "Mostrando _START_ a _END_ de _TOTAL_ resultados",
            infoEmpty: "Mostrando 0 a 0 de 0 resultados",
            infoFiltered: "(filtrado de _MAX_ resultados en total)",
            infoPostFix: "",
            thousands: ",",
            lengthMenu: "Mostrar _MENU_ resultados",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron resultados",
            url: "",
            infoThousands: ",",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            aria: {
                sortAscending: ": activar para ordenar la columna ascendentemente",
                sortDescending: ": activar para ordenar la columna descendentemente"
            },
            select: {
                rows: {
                    _: "%d filas seleccionadas",
                    1: "1 fila seleccionada"
                }
            }
        },
        autoWidth: true,
        responsive: true
    };

    return options;
}