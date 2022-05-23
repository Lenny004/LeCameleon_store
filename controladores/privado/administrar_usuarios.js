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

    //Instaciar el modal o pow up
    M.Modal.init(document.querySelectorAll('.modal'));

    M.Modal.init(document.querySelectorAll('.moda2'));

    M.Modal.init(document.querySelectorAll('.moda3'));
});

// Constantes para establecer las rutas y parámetros de comunicación con la API.
const API_USUARIO = SERVER + 'sitio_privado/usuarios.php?action=';
// Estas constantes son para establecer conexión con lo SELECT
const ENDPOINT_EMPLEADOS = SERVER + 'sitio_privado/api_empleado.php?action=readAll';
const ENDPOINT_TIPOU = SERVER + 'sitio_privado/api_tipousuario.php?action=readAll';
const ENDPOINT_ESTADOU = SERVER + 'sitio_privado/api_estadousuario.php?action=readAll';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_USUARIO);
    // Se define una variable para establecer las opciones del componente Modal.
    let options = {
        dismissible: false,
        onOpenStart: function () {
            // Se restauran los elementos del formulario.
            document.getElementById('form_agregar').reset();
            document.getElementById('form_modificar').reset();
            document.getElementById('form_eliminar').reset();
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
                <td>${row.usuarioE}</td>
                <td>${row.nombreEmpleado}</td>
                <td>${row.tipo_usuario_e}</td>
                <td>${row.estado_usuario_e}</td>
                <td>
                    <a onclick="openUpdate(${row.idusuario_e})" class="btn green tooltipped" data-tooltip="Actualizar">
                    <i class="large material-icons">mode_edit</i>
                    </a>
                    <a onclick="openDelete(${row.idusuario_e})" class="btn  brown tooltipped" data-tooltip="Eliminar">
                    <i class="large material-icons">delete</i>
                    </a>
                </td>
            </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbusuarioe').innerHTML = content;
    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
    M.Materialbox.init(document.querySelectorAll('.materialboxed'));
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
}

// Función para preparar el formulario al momento de insertar un registro.
function openCreate() {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('agregar_modal_usuario')).open();
    // Se asigna el título para la caja de diálogo (modal).
    // Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js
    fillSelect(ENDPOINT_EMPLEADOS, 'empleado', null);
    fillSelect(ENDPOINT_TIPOU, 'tipo', null);
    fillSelect(ENDPOINT_ESTADOU, 'estado', null);
}
document.getElementById('form_agregar').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    let action = 'create';
    saveRow(API_USUARIO, action, 'form_agregar', 'agregar_modal_usuario');
});

// Función para preparar el formulario al momento de modificar un registro.
function openUpdate(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('modal_modificar_usuario')).open();
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('ide', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_USUARIO + 'readOne', {
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
                    document.getElementById('ide').value = response.dataset.idusuario_e;
                    document.getElementById('usuarioeM').value = response.dataset.usuario_e;
                    document.getElementById('contrasenaM').value = response.dataset.contrasena_e;
                    fillSelect(ENDPOINT_EMPLEADOS, 'empleadoM', response.dataset.idempleado);
                    fillSelect(ENDPOINT_TIPOU, 'tipoM', response.dataset.idtipo_usuario_e);
                    fillSelect(ENDPOINT_ESTADOU, 'estadoM', response.dataset.idestado_usuario_e);
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
document.getElementById('form_modificar').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    let action = 'update';
    saveRow(API_USUARIO, action, 'form_modificar', 'modal_modificar_usuario');
});

function openDelete(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario de eliminar registro.
    M.Modal.getInstance(document.getElementById('modal_eliminar_usuario')).open();
    document.getElementById('idd').value = '';
    document.getElementById('idd').value = id;
    const data = new FormData();
    data.append('idd', id);
    fetch(API_USUARIO + 'readOneE', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById('idd').value = response.dataset.idusuario_e;
                    document.getElementById('usuarioD').value = response.dataset.usuario_e;
                    document.getElementById('contrasenaD').value = response.dataset.contrasena_e;
                    fillSelect(ENDPOINT_EMPLEADOS, 'empleadoD', response.dataset.idempleado);
                    fillSelect(ENDPOINT_TIPOU, 'tipoD', response.dataset.idtipo_usuario_e);
                    fillSelect(ENDPOINT_ESTADOU, 'estadoD', response.dataset.idestado_usuario_e);
                    // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.
                    M.updateTextFields();
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}
document.getElementById('form_eliminar').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    var valor = document.getElementById('idd').value;

    const data = new FormData();
    data.append('idd', valor);
    // Se llama a la función para guardar el registro.
    eliminateRow(API_USUARIO, data);
});