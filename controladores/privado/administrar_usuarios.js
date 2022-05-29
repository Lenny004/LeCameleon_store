// Constantes para establecer las rutas y parámetros de comunicación con la API.
const API_USUARIO = SERVER + 'sitio_privado/api_usuarios.php?action=';
// Estas constantes son para establecer conexión con los SELECT
const ENDPOINT_EMPLEADOS = SERVER + 'sitio_privado/api_empleados.php?action=readAll';
const ENDPOINT_TIPOU = SERVER + 'sitio_privado/api_usuarios.php?action=obtenerTipoU';
const ENDPOINT_ESTADOU = SERVER + 'sitio_privado/api_usuarios.php?action=obtenerEstadoU';

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

    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
    M.Materialbox.init(document.querySelectorAll('.materialboxed'));

    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
});

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
                <td>${row.idusuario_e}</td>
                <td>${row.usuario_e}</td>
                <td>${row.nombre_empleado + " " + row.apellido_empleado}</td>
                <td>${row.tipo_usuario_e}</td>
                <td>${row.estado_usuario_e}</td>
                <td>
                    <div id="acciones">
                        <a onclick="openUpdate(${row.idusuario_e})" class="tooltipped" data-tooltip="Actualizar">
                        <img src="../../recursos/iconografia/editar.png" alt="editar">
                        </a>
                        <a onclick="openDelete(${row.idusuario_e})" class="tooltipped" data-tooltip="Eliminar">
                        <img src="../../recursos/iconografia/eliminar.png" alt="eliminar"></a>
                        </a>
                    </div>
                </td>
            </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbusuarioe').innerHTML = content;
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
    let action = 'crearUsuarioE';
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
    let action = 'actualizarUsuarioE';
    saveRow(API_USUARIO, action, 'form_modificar', 'modal_modificar_usuario');
});

function openDelete(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario de eliminar registro.
    M.Modal.getInstance(document.getElementById('modal_eliminar_usuario')).open();
    document.getElementById('ide').value = '';
    document.getElementById('ide').value = id;
    const data = new FormData();
    data.append('ide', id);
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
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}
document.getElementById('form_eliminar').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    var valor = document.getElementById('ide').value;
    const DATA = new FormData();
    DATA.append('ide', valor);
    // Se llama a la función para eliminar el registro.
    confirmDelete(API_USUARIO, DATA, 'modal_eliminar_usuario');
});