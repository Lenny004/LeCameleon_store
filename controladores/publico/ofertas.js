// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_DESCUENTO = SERVER + 'sitio_publico/api_catalogo.php?action=';
const API_OFERTA = SERVER + 'sitio_publico/api_oferta.php?action=';
//Evento que se ejecuta cuando se carga la página web
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
    Descuento();
    //Var se crea para variables globales
    //let es variables locales

    //Instanciar el menú
    M.Sidenav.init(document.querySelectorAll('.sidenav'));

    //Instanciar Dropdown Menú
    var elems = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(elems, { coverTrigger: false, hover: true });

    //Instanciar Select
    M.FormSelect.init(document.querySelectorAll('select'));

    //Instanciar Slider del main;
    let options = { indicators: false, height: 500 };
    M.Slider.init(document.querySelectorAll('.slider'), options);

    //Instanciar ToolTips footer
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));

    M.Collapsible.init(document.querySelectorAll('.collapsible'));
});



// Función para obtener y mostrar los productos de acuerdo a la categoría seleccionada.
function Descuento() {
    fetch(API_DESCUENTO + 'Descuento', {
        method: 'post'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    let content = '';
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se crean y concatenan las tarjetas con los datos de cada producto.
                        content += `
                        <!--Card-->
                        <div class="col s6 m4 l4 xl4">
                            <div class="card hoverable" id="tarjetas_producto">
                                <div class="card-image">
                                    <!--Imagen de la tarjeta-->
                                    <img src="${SERVER}images/productos/${row.imagen_principal}">
                                </div>
                                <div class="card-content">
                                    <p class="nombre_producto">${row.nombre_producto}</p>
                                    <p class="precio_producto">${row.precio_producto}</p>
                                </div>
                                <div class="card-action center-align">
                                    <!--Botón para redireccionar-->
                                    <a href="detalle_producto.html?id=${row.idproducto}">Vista previa</a>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    // Se asigna como título la categoría de los productos.
                   // document.getElementById('title').textContent = 'Descuentos'
                    // Se agregan las tarjetas a la etiqueta div mediante su id para mostrar los productos.
                    document.getElementById('descuentos').innerHTML = content;
                    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
                    M.Materialbox.init(document.querySelectorAll('.materialboxed'));
                    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
                    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
                } else {
                    // Se presenta un mensaje de error cuando no existen datos para mostrar.
                    document.getElementById('title').innerHTML = `<i class="material-icons small">cloud_off</i><span class="red-text">${response.exception}</span>`;
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}


// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar.
document.getElementById('thesearch').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js

    fetch(API_DESCUENTO + 'searchO', {
        method: 'post',
        body: new FormData(document.getElementById('thesearch'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se envían los datos a la función del controlador para que llene la tabla en la vista y se muestra un mensaje de éxito.
                    sweetAlert(1, response.message, null);
                    let content = '';
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se crean y concatenan las tarjetas con los datos de cada producto.
                        content += `
                        <!--Card-->
                        <div class="col s6 m4 l4 xl4">
                            <div class="card hoverable" id="tarjetas_producto">
                                <div class="card-image">
                                    <!--Imagen de la tarjeta-->
                                    <img src="${SERVER}images/productos/${row.imagen_principal}">
                                </div>
                                <div class="card-content">
                                    <p class="nombre_producto">${row.nombre_producto}</p>
                                    <p class="precio_producto">${row.precio_producto}</p>
                                </div>
                                <div class="card-action center-align">
                                    <!--Botón para redireccionar-->
                                    <a href="detalle_producto.html?id=${row.idproducto}">Vista previa</a>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    // Se agregan las tarjetas a la etiqueta div mediante su id para mostrar los productos.
                    document.getElementById('descuentos').innerHTML = content;
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
});


// Función para buscar por rango de precios ya sea 0 - 25, 25 - 50, 50 -100 y 100 a 999
function Rangop(action) {
    fetch(API_OFERTA + action, {
        method: 'post'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    sweetAlert(1, response.message, null);
                    let content = '';
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se crean y concatenan las tarjetas con los datos de cada producto.
                        content += `
                        <!--Card-->
                        <div class="col s6 m4 l4 xl4">
                            <div class="card hoverable" id="tarjetas_producto">
                                <div class="card-image">
                                    <!--Imagen de la tarjeta-->
                                    <img src="${SERVER}images/productos/${row.imagen_principal}">
                                </div>
                                <div class="card-content">
                                    <p class="nombre_producto">${row.nombre_producto}</p>
                                    <p class="precio_producto">${row.precio_producto}</p>
                                </div>
                                <div class="card-action center-align">
                                    <!--Botón para redireccionar-->
                                    <a href="detalle_producto.html?id=${row.idproducto}">Vista previa</a>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                    // Se asigna como título la categoría de los productos.
                    // document.getElementById('title').textContent = 'Descuentos'
                    // Se agregan las tarjetas a la etiqueta div mediante su id para mostrar los productos.
                    document.getElementById('descuentos').innerHTML = content;
                    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
                    M.Materialbox.init(document.querySelectorAll('.materialboxed'));
                    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
                    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function Rango1(){
    let action = 'rangop';
    // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
    Rangop(action);
}

function Rango2(){
    let action = 'rangos';
    // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
    Rangop(action);
}

function Rango3(){
    let action = 'rangot';
    // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
    Rangop(action);
}

function Rango4(){
    let action = 'rangoc';
    // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
    Rangop(action);
}