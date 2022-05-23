// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_PEDIDOS = SERVER + "sitio_privado/api_pedidos.php?action=";

//Evento que se ejecuta cuando se carga la página web
document.addEventListener('DOMContentLoaded', function () {
    let menu = document.getElementById("menu");
    window.onscroll = function () {
        if (window.pageYOffset >= 80) {
            menu.classList.add("sticky");
        }
        else {
            menu.classList.remove("sticky");
        }
    }
    //Var se crea para variables globales
    //let es variables locales

    //Instanciar el menú
    M.Sidenav.init(document.querySelectorAll('.sidenav'));

    //Instanciar Dropdown Menú
    var elems = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(elems, { coverTrigger: false, hover: true });

    //Instanciar ToolTips pedido
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));

    // Petición para consultar si existen pedidos registrados.
    fetch(API_PEDIDOS + 'existenciaPedidos', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    data = response.dataset;
                } else {
                    sweetAlert(4, response.exception, null);
                }
                // Se envían los datos a la función del controlador para llenar la tabla en la vista.
                fillTable(data);
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
            <tr>
                <td><img src="${SERVER}images/categorias/${row.imagen_categoria}" class="materialboxed" height="100"></td>
                <td>${row.nombre_categoria}</td>
                <td>${row.descripcion_categoria}</td>
                <td>
                    <a onclick="openUpdate(${row.id_categoria})" class="btn-floating blue tooltipped" data-tooltip="Actualizar">
                        <i class="material-icons">mode_edit</i>
                    </a>
                    <a onclick="openDelete(${row.id_categoria})" class="btn-floating red tooltipped" data-tooltip="Eliminar">
                        <i class="material-icons">delete</i>
                    </a>
                    <a onclick="openReport(${row.id_categoria})" class="btn-floating amber tooltipped" data-tooltip="Reporte">
                        <i class="material-icons">assignment</i>
                    </a>
                </td>
            </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows').innerHTML = content;
    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
    M.Materialbox.init(document.querySelectorAll('.materialboxed'));
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
}
