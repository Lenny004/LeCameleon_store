//Evento que se ejecuta cuando se carga la página web
document.addEventListener('DOMContentLoaded', function() {
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
    M.AutoInit();
    
    M.Materialbox.init(document.querySelectorAll('.materialboxed'));

    //Instanciar Datepicker
	M.Datepicker.init(document.querySelectorAll('.datepicker'), {
		format: 'yyyy-mm-dd', i18n: {
			months: ['Enero', 'Febrero', 'Marzo', 'April', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			monthsShort: ['En', 'Febr', 'Mzo', 'Abr', 'My', 'Jun', 'Jul', 'Ag', 'Sept', 'Oct', 'Nov', 'Dic'],
			weekdaysShort: ['Dom', 'Lun', 'Mar', 'Miérc', 'Juev', 'Vier', 'Sáb'],
			weekdaysAbbrev: ['D', 'L', 'M', 'X', 'J', 'V', 'S']
	}});

    readAllCategorias();
    readAllsubCategorias();
});


// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_CATALOGO = SERVER + 'sitio_publico/api_catalogo.php?action=';

// Función para obtener y mostrar las categorías disponibles.
function readAllCategorias() {
    // Petición para solicitar los datos de las categorías.
    fetch(API_CATALOGO + 'readAll', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es satisfactoria, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es correcta, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    let content = '';
                    let url = '';
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se define una dirección con los datos de cada categoría para mostrar sus productos en otra página web.
                        url = `productosc.html?id=${row.idcategoria_producto}&nombre=${row.categoria_producto}`;
                        // Se crean y concatenan las tarjetas con los datos de cada categoría.
                        content += `
                            <!--Card-->
                            <div class="col s6 m4 l4 xl4">
                                <div class="card hoverable" id="tarjetas">
                                    <div>
                                        <!--Titulo de la Card-->
                                        <span class="card-title">${row.categoria_producto}</span>
                                    </div>
                                    <div class="card-image">
                                        <!--Imagen de la tarjeta-->
                                        <img src="${SERVER}images/categorias/${row.imagen_categoria}" class="activator">
                                    </div>
                                    <div class="card-action center-align">
                                        <!--Botón para redireccionar-->
                                        <a href="${url}" class="tooltipped" data-tooltip="Ver ${row.categoria_producto}">Ver Más...</a>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    // Se agregan las tarjetas a la etiqueta div mediante su id para mostrar las categorías.
                    document.getElementById('categorias').innerHTML = content;
                    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
                    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
                } else {
                    // Se asigna al título del contenido un mensaje de error cuando no existen datos para mostrar.
                    let title = `<i class="material-icons small">error</i><span class="red-text">${response.exception}</span>`;
                    document.getElementById('title').innerHTML = title;
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function readAllsubCategorias() {
    // Petición para solicitar los datos de las categorías.
    fetch(API_CATALOGO + 'readAllS', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es satisfactoria, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es correcta, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    let content = '';
                    let url = '';
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se define una dirección con los datos de cada categoría para mostrar sus productos en otra página web.
                        url = `productoss.html?id=${row.idsubcategoria_producto}&nombre=${row.subcategoria_producto}`;
                        // Se crean y concatenan las tarjetas con los datos de cada categoría.
                        content += `
                            <!--Card-->
                            <div class="col s6 m4 l4 xl4">
                                <div class="card hoverable" id="tarjetas">
                                    <div>
                                        <!--Titulo de la Card-->
                                        <span class="card-title">${row.subcategoria_producto}</span>
                                    </div>
                                    <div class="card-image">
                                        <!--Imagen de la tarjeta-->
                                        <img src="${SERVER}images/subcategoria/${row.imagen_subcategoria}" class="activator">
                                    </div>
                                    <div class="card-action center-align">
                                        <!--Botón para redireccionar-->
                                        <a href="${url}" class="tooltipped" data-tooltip="Ver ${row.subcategoria_producto}">Ver Más...</a>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    // Se agregan las tarjetas a la etiqueta div mediante su id para mostrar las categorías.
                    document.getElementById('subcategoria').innerHTML = content;
                    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
                    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
                } else {
                    // Se asigna al título del contenido un mensaje de error cuando no existen datos para mostrar.
                    let title = `<i class="material-icons small">error</i><span class="red-text">${response.exception}</span>`;
                    document.getElementById('title').innerHTML = title;
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}
