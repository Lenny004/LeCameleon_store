//Evento que se ejecuta cuando se carga la página web
document.addEventListener('DOMContentLoaded', function() {
    //Var se crea para variables globales
    //let es variables locales

    //Instanciar el menú
    M.Sidenav.init(document.querySelectorAll('.sidenav'));

    //Instanciar Dropdown Menú
    var elems = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(elems, {coverTrigger:false, hover: true});

    //Instanciar ToolTips footer
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
});

//Constante para establecer la ruta y parámetros de comunicación con la API.
const API_EXISTENCIA = SERVER + 'sitio_privado/api_existencias.php?action=';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {

    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_EXISTENCIA);

    // Se define una variable para establecer las opciones del componente Modal.
    let options = {
        dismissible: false,
        onOpenStart: function () {
            // Se restauran los elementos del formulario.
            document.getElementById('save-form').reset();
        }
    }

    // Se inicializa el componente Modal para que funcionen las cajas de diálogo.
    M.Modal.init(document.querySelectorAll('.modal'), options);
});

// Para cargar la base de datos.
// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        //////////////Tabla////////////////
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
        <tr>
            <td>${row.nombre_producto}</td>
            <td>${row.estado_producto}</td>
            <td>${row.nombre_marca}</td>
            <td>${row.subcategoria_producto}</td>
            <td>${row.nombre_distribuidor}</td>
            <td>${row.existencias}</td>
            <td>${row.cantidad}</td>
            <td>${row.precio_producto}</td>
        </tr>
        `;
    });
    //Se crea un id para que ahí muestre los datos de las tablas dichos anteriormente
    document.getElementById('existencias').innerHTML = content;
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
}
//////////// Buscador.///////////////
// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar.
document.getElementById('thesearch').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
    searchRows(API_EXISTENCIA, 'thesearch');
});