//Se crea la ruta constante para la API
const API_INVENTARIO_ENTREGA = SERVER + "sitio_privado/api_inventario_entrega.php?action=";
const ENDPOINT_PRODUCTOS = SERVER + "sitio_privado/api_inventario_entrega.php?action=cargarProductos";
const ENDPOINT_PROVEEDOR = SERVER + "sitio_privado/api_inventario_entrega.php?action=cargarProveedores";
var accion = null;

document.addEventListener("DOMContentLoaded", function () {
    let menu = document.getElementById("menu");
    window.onscroll = function () {
        if (window.pageYOffset >= 80) {
            menu.classList.add("sticky");
        }
        else {
            menu.classList.remove("sticky");
        }
    }
    //Se carga la tabla con los datos de la DB
    readRows(API_INVENTARIO_ENTREGA);
    //Se cargan los productos en el select
    fillSelect(ENDPOINT_PRODUCTOS, 'un producto', "producto", null);
    fillSelect(ENDPOINT_PROVEEDOR, 'un proveedor', "proveedores", null);
});

//Función para cargar los datos en la tabla
function fillTable(dataset) {
    //Se crea la variable donde se guardarán los datos
    let contenido = "";
    //Se explora el vector fila por fila
    dataset.map(function (row) {
        contenido += `
            <tr>
                <td>${row.idinventario}</td>
                <td>${row.nombre_producto}</td>
                <td>${row.fecha_entrega}</td>
                <td>${row.fecha_inicio_ventas}</td>
                <td>${row.cantidad}</td>
            </tr>
            `;
    });
    //Se le insertan las filas a la tabla en la vista
    document.getElementById("tabla_cuerpo").innerHTML = contenido;
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar para encontrar unos productos.
document.getElementById('thesearch').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Se hace la petición para el caso de llenar el select según un parametro
    fetch(API_INVENTARIO_ENTREGA + 'leerProductosBuscador', {
        method: 'post',
        body: new FormData(document.getElementById('thesearch'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let content = '';
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se obtiene el dato del primer campo de la sentencia SQL (valor para cada opción).
                        value = Object.values(row)[0];
                        // Se obtiene el dato del segundo campo de la sentencia SQL (texto para cada opción).
                        text = Object.values(row)[1];
                        // Se verifica si el valor de la API es diferente al valor seleccionado para enlistar una opción, de lo contrario se establece la opción como seleccionada.
                        if (value != null) {
                            content += `<option value="${value}">${text}</option>`;
                        } else {
                            content += `<option value="${value}" selected>${text}</option>`;
                        }
                    });
                } else {
                    content += '<option>No hay opciones disponibles</option>';
                }
                // Se agregan las opciones a la etiqueta select mediante su id.
                document.getElementById("producto").innerHTML = content;
                // Se inicializa el componente Select del formulario para que muestre las opciones.
                M.FormSelect.init(document.querySelectorAll('select'));
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    })
});

document.getElementById("formulario_entrega").addEventListener("submit", function (event) {
    //Se evita que se recargue la página
    event.preventDefault();
    //Se crea la variable para la modalidad
    let modalidad = null;
    if (accion) {
        modalidad = "crear_entrega";
    } else {
        modalidad = "actualizar_entrega";
    }
    fetch(API_INVENTARIO_ENTREGA + modalidad, {
        method: "post",
        body: new FormData(document.getElementById("formulario_entrega")),
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se cargan nuevamente las filas en la tabla de la vista después de guardar un registro y se muestra un mensaje de éxito.
                    readRows(API_INVENTARIO_ENTREGA);
                    sweetAlert(1, response.message, null);
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + " " + request.statusText);
        }
    });
});

function cambiar_variables1() {
    accion = true;
}

function cambiar_variables2() {
    accion = false;
}
