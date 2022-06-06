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

    //Instanciar Select
    M.FormSelect.init(document.querySelectorAll('select'));

    //Instanciar Slider del main;
    let options = { indicators: false, height: 500 };
    M.Slider.init(document.querySelectorAll('.slider'), options);

    //Instanciar ToolTips footer
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));

    M.Datepicker.init(document.querySelectorAll('.datepicker'));
});

// Constantes para establecer las rutas y parámetros de comunicación con la API.
const API_PRODUCTOS = SERVER + 'sitio_privado/api_producto.php?action=';
// Estas constantes son para establecer conexión con lo SELECT
const ENDPOINT_COLOR = SERVER + 'sitio_privado/api_producto.php?action=obtenerColor';
const ENDPOINT_MARCA = SERVER + 'sitio_privado/api_marca.php?action=readAll';
const ENDPOINT_PROVEEDOR = SERVER + 'sitio_privado/api_proveedor.php?action=readAll';
const ENDPOINT_ESTADO = SERVER + 'sitio_privado/api_producto.php?action=obtenerEstadoProducto';
const ENDPOINT_SUBC = SERVER + 'sitio_privado/api_subcategoria.php?action=readAll';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_PRODUCTOS);
    // Se define una variable para establecer las opciones del componente Modal.
    let options = {
        dismissible: false,
        onOpenStart: function () {
            // Se restauran los elementos del formulario.
            document.getElementById('Agregar_forms').reset();
            document.getElementById('modificar_forms').reset();
            document.getElementById('eliminar_forms').reset();
        }
    }
    // Se inicializa el componente Modal para que funcionen las cajas de diálogo.
    M.Modal.init(document.querySelectorAll('.modal'), options);
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
            <tr>
                <td><img src="${SERVER}images/productos/"></td>
                <td>${row.nombre_producto}</td>
                <td>${row.estado_producto}</td>
                <td>${row.subcategoria_producto}</td>
                <td>${row.precio_producto}</td>
                <td>
                    <div id="acciones">
                        <a onclick="openUpdate(${row.idproducto})" class="tooltipped" data-tooltip="Actualizar">
                        <img src="../../recursos/iconografia/editar.png" alt="editar">
                        </a>
                        <a onclick="openDelete(${row.idproducto})" class="tooltipped" data-tooltip="Eliminar">
                        <img src="../../recursos/iconografia/eliminar.png" alt="eliminar"></a>
                        </a>
                    </div>
                </td>
            </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbproducto').innerHTML = content;
    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
    M.Materialbox.init(document.querySelectorAll('.materialboxed'));
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
}
// Función para preparar el formulario al momento de insertar un registro.
function openCreate() {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('agregar_modal')).open();
    // Se establece el campo de archivo como obligatorio.
    document.getElementById('archivo').required = true;
    // Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js
    fillSelect(ENDPOINT_COLOR, 'color', null);
    fillSelect(ENDPOINT_MARCA, 'marca', null);
    fillSelect(ENDPOINT_PROVEEDOR, 'distribuidor', null);
    fillSelect(ENDPOINT_ESTADO, 'estado', null);
    fillSelect(ENDPOINT_SUBC, 'subc', null);
}
document.getElementById('Agregar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    let action = 'create';
    saveRow(API_PRODUCTOS, action, 'Agregar_forms', 'agregar_modal');
});

// Función para preparar el formulario al momento de modificar un registro.
function openUpdate(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('modificar_modal')).open();
    // Se asigna el título para la caja de diálogo (modal).
    document.getElementById('archivop').required = false;
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('ide', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_PRODUCTOS + 'readOne', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById('ide').value = response.dataset.idProducto;
                    document.getElementById('nombreM').value = response.dataset.nombreProducto;
                    document.getElementById('descripcionM').value = response.dataset.descripcion;
                    document.getElementById('materialM').value = response.dataset.material;
                    document.getElementById('tamañoM').value = response.dataset.tamaño;
                    document.getElementById('existenciasM').value = response.dataset.existencias;
                    document.getElementById('descuentoM').value = response.dataset.porcentajeDescuento;
                    document.getElementById('precioM').value = response.dataset.precioProducto;
                    fillSelect(ENDPOINT_COLOR, 'colorM', response.dataset.idColor);
                    fillSelect(ENDPOINT_MARCA, 'marcaM', response.dataset.idMarca);
                    fillSelect(ENDPOINT_PROVEEDOR, 'distribuidorM', response.dataset.idDistribuidor);
                    fillSelect(ENDPOINT_ESTADO, 'estadoM', response.dataset.idEstadoProducto);
                    fillSelect(ENDPOINT_SUBC, 'subcM', response.dataset.idSubCategoriaProducto);
                    // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.
                    M.updateTextFields();
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}


document.getElementById('modificar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    let action = 'update';
    saveRow(API_PRODUCTOS, action, 'modificar_forms', 'modificar_modal');
});

//funcion que captura imagen para ponerla en editar e eliminar
function openDelete(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario de eliminar registro.
    M.Modal.getInstance(document.getElementById('eliminar_modal')).open();
    document.getElementById('ide').value = '';
    document.getElementById('ide').value = id;

    const data = new FormData();
    data.append('ide', id);

    fetch(API_PRODUCTOS + 'readOne', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById('ide').value = response.dataset.idProducto;
                    document.getElementById('nombreD').value = response.dataset.nombreProducto;
                    document.getElementById('descripcionD').value = response.dataset.descripcion;
                    document.getElementById('materialD').value = response.dataset.material;
                    document.getElementById('tamañoD').value = response.dataset.tamaño;
                    document.getElementById('existenciasD').value = response.dataset.existencias;
                    document.getElementById('descuentoD').value = response.dataset.porcentajeDescuento;
                    document.getElementById('precioD').value = response.dataset.precioProducto;
                    fillSelect(ENDPOINT_COLOR, 'colorD', response.dataset.idColor);
                    fillSelect(ENDPOINT_MARCA, 'marcaD', response.dataset.idMarca);
                    fillSelect(ENDPOINT_PROVEEDOR, 'distribuidorD', response.dataset.idDistribuidor);
                    fillSelect(ENDPOINT_ESTADO, 'estadoD', response.dataset.idEstadoProducto);
                    fillSelect(ENDPOINT_SUBC, 'subcD', response.dataset.idSubCategoriaProducto);
                    // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.
                    M.updateTextFields();
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

document.getElementById('eliminar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    var valor = document.getElementById('ide').value;

    const data = new FormData();
    data.append('ide', valor);
    // Se llama a la función para guardar el registro.
    eliminateRow(API_PRODUCTOS, data, 'eliminar_modal');
});
