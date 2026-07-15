const API_ADMIN_MARCA = SERVER + 'sitio_privado/api_marca.php?action=';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    let menu = document.getElementById("menu");
    window.onscroll = function () {
        if (window.pageYOffset >= 100) {
            menu.classList.add("sticky");
        }
        else {
            menu.classList.remove("sticky");
        }
    }
    cargarMarcas();
});

function cargarMarcas() {
    // Petición para consultar si existen pedidos registrados.
    fetch(API_ADMIN_MARCA + 'readAll', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let dataset = [];
                let contenido_marca = '';
                let url = '';
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    dataset = response.dataset;
                    dataset.map(function (row) {
                        // Se define una dirección con los datos de cada categoría para mostrar sus productos en otra página web.
                        url = `productos_marca.html?id=${row.id_marca}&nombre=${row.nombre_marca}`;
                        contenido_marca +=
                            `<div class="col s6 m4 l3 imagen_marca"><a href="${url}"><img src="${SERVER}images/marca/${row.imagen_marca}"></a></div>`;
                    });
                    document.getElementById('cuadro_marca').innerHTML = contenido_marca;
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar.
document.getElementById('buscador_marca').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se llama a la función que realiza la búsqueda.
    fetch(API_ADMIN_MARCA + 'search', {
        method: 'post',
        body: new FormData(document.getElementById('buscador_marca'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let dataset = [];
                let contenido_marca = '';
                let url = '';
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    dataset = response.dataset;
                    dataset.map(function (row) {
                        // Se define una dirección con los datos de cada categoría para mostrar sus productos en otra página web.
                        url = `productos_marca.html?id=${row.id_marca}&nombre=${row.nombre_marca}`;
                        contenido_marca +=
                            `<div class="col s6 m4 l3 imagen_marca"><a href="${url}"><img src="${SERVER}images/marca/${row.imagen_marca}"></a></div>`;
                    });
                    document.getElementById('cuadro_marca').innerHTML = contenido_marca;
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
});

// Ordenar de la A-Z
function ordenAZ(tipo_orden) {
    let valor = tipo_orden;
    const data = new FormData();
    data.append('orden', valor);
    // Se llama a la función que realiza la búsqueda por categoria
    busquedaOrden(data);
}

//Ordenar las marcas por orden alfabetico
function busquedaOrden(form) {
    fetch(API_ADMIN_MARCA + 'busquedaOrden', {
        method: 'post',
        body: form
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let dataset = [];
                let contenido_marca = '';
                let url = '';
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    dataset = response.dataset;
                    dataset.map(function (row) {
                        // Se define una dirección con los datos de cada categoría para mostrar sus productos en otra página web.
                        url = `productos_marca.html?id=${row.id_marca}&nombre=${row.nombre_marca}`;
                        contenido_marca +=
                            `<div class="col s6 m4 l3 imagen_marca"><a href="${url}"><img src="${SERVER}images/marca/${row.imagen_marca}"></a></div>`;
                    });
                    document.getElementById('cuadro_marca').innerHTML = contenido_marca;
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}