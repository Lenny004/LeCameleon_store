//Evento que se ejecuta cuando se carga la página web
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
    //Var se crea para variables globales
    //let es variables locales

    //Instanciar el menú
    M.Sidenav.init(document.querySelectorAll(".sidenav"));

    //Instanciar Dropdown Menú
    var elems = document.querySelectorAll(".dropdown-trigger");
    M.Dropdown.init(elems, { coverTrigger: false, hover: true });

    //Instanciar Slider del main;
    let options = { indicators: false, height: 500 };
    M.Slider.init(document.querySelectorAll(".slider"), options);

    //Instanciar ToolTips footer
    M.Tooltip.init(document.querySelectorAll(".tooltipped"));
});
const API_SUBCATEGORIA = SERVER + "sitio_privado/api_subcategoria.php?action=";
const ENDPOINT_CATEGORIA = SERVER + "sitio_privado/api_categorias.php?action=readAll";

//Constantes Selects

document.addEventListener("DOMContentLoaded", function () {
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_SUBCATEGORIA);
    // Se define una variable para establecer las opciones del componente Modal.
    let options = {
        dismissible: false,
        onOpenStart: function () {
            // Se restauran los elementos del formulario.
            document.getElementById("Agregar_forms").reset();
            document.getElementById("modificar_forms").reset();
            document.getElementById("eliminar_forms").reset();
        },
    };
    // Se inicializa el componente Modal para que funcionen las cajas de diálogo.
    M.Modal.init(document.querySelectorAll(".modal"), options);
    // Se inicializa el componente Modal para que funcionen las cajas de diálogo.
});

function fillTable(dataset) {
    let content = "";
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
                <tr>
                    <td><img src="${SERVER}images/subcategoria/${row.imagen_subcategoria}"></td>
                    <td>${row.subcategoria_producto}</td>
                    <td>${row.categoria_producto}</td>
                    <td>
                        <div id="acciones">
                            <a onclick="openUpdate(${row.idsubcategoria_producto})" class="tooltipped" data-tooltip="Actualizar">
                            <img src="../../recursos/iconografia/editar.png" alt="editar">
                            </a>
                            <a onclick="openDelete(${row.idsubcategoria_producto})"class="tooltipped" data-tooltip="Eliminar">
                            <img src="../../recursos/iconografia/eliminar.png" alt="eliminar"></a>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById("tabla_subcategoria").innerHTML = content;
    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
    M.Materialbox.init(document.querySelectorAll(".materialboxed"));
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll(".tooltipped"));
}

//Funcion que permite abrir el modal de agregar subcategoria e insertar datos a la bd
function openCreate() {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(
        document.getElementById("modal_agregar_subcategoria")
    ).open();
    // Se establece el campo de archivo como obligatorio.
    document.getElementById("archivo").required = true;
    fillSelect(ENDPOINT_CATEGORIA, "select_categoria", null);
}

document.getElementById("Agregar_forms").addEventListener("submit", function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    let action = "create";
    saveRow(
        API_SUBCATEGORIA,
        action,
        "Agregar_forms",
        "modal_agregar_subcategoria"
    );
});

//Funcion que permite abrir el modal de modificar subcategoria y haga un update en la bd
function openUpdate(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('modal_modificar_subcategoria')).open();
    // Se asigna el título para la caja de diálogo (modal).
    document.getElementById('archivop').required = false;
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    console.log(id);
    data.append('ide', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_SUBCATEGORIA + 'readOne', {
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
                    console.log(id);
                    document.getElementById('id').value = response.dataset.idsubcategoria_producto;
                    document.getElementById('nombrearchivo').value = response.dataset.imagen_subcategoria;
                    document.getElementById('subcategoria_modificar').value = response.dataset.subcategoria_producto;
                    //fillSelect(ENDPOINT_CATEGORIA, "select_categoria", response.dataset.idCategoriaProducto);
                    fillSelect(ENDPOINT_CATEGORIA, 'select_subcategoria', response.dataset.idcategoria_producto);
                    // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.      
                    M.updateTextFields();
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.estadoText);
        }
    });
}

document.getElementById('modificar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    //console.table(document.getElementById('modificar_forms'));
    event.preventDefault();
    let action = 'update';
    saveRow(API_SUBCATEGORIA, action, 'modificar_forms', 'modal_modificar_subcategoria');
});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de guardar.

document.getElementById('modificar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id').value) ? action = 'update' : action = 'create';
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_SUBCATEGORIA, action, 'modificar_forms', 'modal_modificar_subcategoria');
});

//funcion para elimnar subcategoria
function openDelete(id) {
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('idd', id);

    // Se llama a la función que elimina un registro. Se encuentra en el archivo components.js
    confirmDelete(API_SUBCATEGORIA, data);
}
document.getElementById('eliminar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    var valor = document.getElementById('idd').value;

    const data = new FormData();
    data.append('idd', valor);
    // Se llama a la función para guardar el registro.
    eliminateRow(API_SUBCATEGORIA, data);
});