// Constantes para establecer las rutas y parámetros de comunicación con la API.
const API_USUARIO_CLIENTE = SERVER + 'sitio_privado/api_usuario_cliente.php?action=';
// Estas constantes son para establecer conexión con lo SELECT
const ENDPOINT_ESTADOU = SERVER + 'sitio_privado/api_usuario_cliente.php?action=obtenerEstadoUsuarioCliente';

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
    M.Dropdown.init(elems, { coverTrigger: true, closeOnClick: false });

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
    readRows(API_USUARIO_CLIENTE);
    // Se define una variable para establecer las opciones del componente Modal.
    let options = {
		dismissible: false,
		onOpenStart: function () {
			// Se restauran los elementos del formulario.
			document.getElementById('form_modificar').reset();
		}
	}
    // Se inicializa el componente Modal para que funcionen las cajas de diálogo.
	M.Modal.init(document.querySelectorAll('.modal'), options);
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset){
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
            <tr>
                <td>${row.idusuario_c}</td>
                <td>${row.usuario_c}</td>
                <td>
                    <div id="estado_usuario_cliente">
                        <a class='dropdown-trigger btn' href='#' data-target='dropestado'>${row.estado_usuario_c}</a>
                    </div>
                </td>
                <td>
                    <div id="acciones">
                        <a onclick="openUpdate(${row.idusuario_c})" class="tooltipped" data-tooltip="Actualizar">
                            <img src="../../recursos/iconografia/editar.png" alt="editar">
                        </a>
                    </div>
                </td>
            </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbusuarioc').innerHTML = content;
}

// Función para preparar el formulario al momento de modificar un registro.
function openUpdate(id) {
	// Se abre la caja de diálogo (modal) que contiene el formulario.
	M.Modal.getInstance(document.getElementById('modificar_modal_usuarioC')).open();
	// Se define un objeto con los datos del registro seleccionado.
	const data = new FormData();
	data.append('ide', id);
	// Petición para obtener los datos del registro solicitado.
	fetch(API_USUARIO_CLIENTE + 'leerUno', {
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
					document.getElementById('ide').value = response.dataset.idusuario_c;
					document.getElementById('usuarioC').value = response.dataset.usuario_c;
					fillSelect(ENDPOINT_ESTADOU, "un estado",'estadoUsuarioC', response.dataset.idestado_usuario_c);
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

document.getElementById('form_modificar').addEventListener('submit', function (event) {
	// Se evita recargar la página web después de enviar el formulario.
	event.preventDefault();
	let action = 'actualizarEmpleado';
	saveRow(API_USUARIO_CLIENTE, action, 'form_modificar', 'modificar_modal_usuarioC');
});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar.
document.getElementById('thesearch').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
    searchRows(API_USUARIO_CLIENTE, 'thesearch');
});