//Se crea la ruta constante para la API
const API_inventario_entrega = SERVER + "sitio_privado/api_inventario_entrega.php?action=";
const endpoint = SERVER + "sitio_privado/api_inventario_entrega.php?action=cargar_productos";
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
    readRows(API_inventario_entrega);

    //Se cargan los productos en el select
    fillSelect(endpoint, "id_producto", null);
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

    fetch(API_inventario_entrega + modalidad, {
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
                    readRows(API_inventario_entrega);
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

//Método para buscar registros y colocarlos en la tabla
document.getElementById("buscador_inventario").addEventListener("keyup", function () {
    //Se crea el dato de tipo formulario a enviar
    let datos = new FormData();
    //Se crea la variable para obtener la información del buscador
    let buscador = document.getElementById("buscador_inventario").value;
    //Se llena con el name y el valor del identificador
    datos.append("buscador", buscador);
    //Se ejecuta la busqueda
    fetch(API_inventario_entrega + "buscador", {
        method: "post",
        body: datos,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    data = response.dataset;
                } else {
                    data = response.dataset;
                }
                // Se envían los datos a la función del controlador para llenar la tabla en la vista.
                fillTable(data);
            });
        } else {
            console.log(request.estado + " " + request.statusText);
        }
    });
});

//Método para cargar los datos en el formulario
document.getElementById("buscador_registro").addEventListener("keyup", function () {
    //Se crea el dato de tipo formulario a enviar
    let datos = new FormData();
    //Se crea la variable para obtener la información del buscador
    let buscador = document.getElementById("buscador_registro").value;
    //Se llena con el name y el valor del identificador
    datos.append("buscador", buscador);
    //Se ejecuta la busqueda
    fetch(API_inventario_entrega + "seleccionar", {
        method: "post",
        body: datos,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                //Se crea la variable donde se guardarán los datos
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    //Se cargan los datos en el fomrulario
                    document.getElementById("id_inventario").value = response.dataset.idinventario;
                    document.getElementById("cantidad_formulario").value =
                        response.dataset.cantidad;
                    document.getElementById("fecha_entrega").value = response.dataset.fecha_entrega;
                    document.getElementById("fecha_inicio").value =
                        response.dataset.fecha_inicio_ventas;
                    document.getElementById("precio").value = response.dataset.precio_producto;
                    fillSelect(endpoint, "id_producto", response.dataset.idproducto);
                } else {
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
//Método que actualiza los datos

/*
document.getElementById("formulario_entrega").addEventListener('submit', function (event) { 

    //Se evita que se recargue la página
    event.preventDefault();
    // Se ejecutá la función en la API
    fetch(API_inventario_entrega + "actualizar_entrega", {
        method: 'post',
        body: new FormData(document.getElementById("formulario_entrega")),
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    data = response.dataset;
                } else {
                    data = response.dataset;
                }
                // Se envían los datos a la función del controlador para llenar la tabla en la vista.
                fillTable(data);
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
});
*/
