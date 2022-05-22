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

	//Instaciar el modal o pow up
	M.Modal.init(document.querySelectorAll('.modal'));

	M.Modal.init(document.querySelectorAll('.moda2'));

	M.Modal.init(document.querySelectorAll('.moda3'));
});

//hago que cuando se seleccione una fecha el calendario se cierre automatica, también cambie el formato de esta por yyyy-mm-dd
document.addEventListener('DOMContentLoaded', function () {
	var elems = document.querySelectorAll('.datepicker');
	M.Datepicker.init(elems, {
		autoClose: true,
		format: 'yyyy-mm-dd'
	});
});


// Constantes para establecer las rutas y parámetros de comunicación con la API.
const API_EMPLEADO = SERVER + 'sitio_privado/api_empleados.php?action=';
// Estas constantes son para establecer conexión con lo SELECT
const ENDPOINT_TIPO = SERVER + 'sitio_privado/api_tipo_empleado.php?action=readAll';
const ENDPOINT_ESTADO = SERVER + 'sitio_privado/api_estado_empleado.php?action=readAll';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
	// Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
	readRows(API_EMPLEADO);
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
            <td>${row.nombre_empleado}</td>
        	<td>${row.apellido_empleado}</td>
            <td>${row.nombre_estado}</td>
        	<td>${row.tipo_empleado}</td>
            <td>${row.telefono_empleado}</td>
            <td>
				<div id="acciones">
                	<a onclick="openUpdate(${row.idempleado})" class="tooltipped" data-tooltip="Actualizar">
                	<img src="../../recursos/iconografia/editar.png" alt="editar">
                	</a>
                	<a onclick="openDelete(${row.idempleado})" class="tooltipped" data-tooltip="Eliminar">
                	<img src="../../recursos/iconografia/eliminar.png" alt="eliminar"></a>
                	</a>
				</div>
            </td>
        </tr>
        `;
	});
	// Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
	document.getElementById('tbempleado').innerHTML = content;
	// Se inicializa el componente Material Box para que funcione el efecto Lightbox.
	M.Materialbox.init(document.querySelectorAll('.materialboxed'));
	// Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
	M.Tooltip.init(document.querySelectorAll('.tooltipped'));
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar.
document.getElementById('thesearch').addEventListener('submit', function (event) {
	// Se evita recargar la página web después de enviar el formulario.
	event.preventDefault();
	// Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
	searchRows(API_EMPLEADO, 'thesearch');
});

// Función para preparar el formulario al momento de insertar un registro.
function openCreate() {
	// Se abre la caja de diálogo (modal) que contiene el formulario.
	M.Modal.getInstance(document.getElementById('agregar_modal')).open();
	// Se asigna el título para la caja de diálogo (modal).
	// Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js
	fillSelect(ENDPOINT_TIPO, 'tipo', null);
	fillSelect(ENDPOINT_ESTADO, 'estado', null);
}
document.getElementById('form_agregar').addEventListener('submit', function (event) {
	// Se evita recargar la página web después de enviar el formulario.
	event.preventDefault();
	let action = 'create';
	saveRow(API_EMPLEADO, action, 'form_agregar', 'agregar_modal');
});


// Función para preparar el formulario al momento de modificar un registro.
function openUpdate(id) {
	// Se abre la caja de diálogo (modal) que contiene el formulario.
	M.Modal.getInstance(document.getElementById('modificar_modal')).open();
	// Se define un objeto con los datos del registro seleccionado.
	const data = new FormData();
	data.append('ide', id);
	// Petición para obtener los datos del registro solicitado.
	fetch(API_EMPLEADO + 'readOne', {
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
					document.getElementById('ide').value = response.dataset.idempleado;
					document.getElementById('nombreM').value = response.dataset.nombre_empleado;
					document.getElementById('apellidoM').value = response.dataset.apellido_empleado;
					document.getElementById('DUIM').value = response.dataset.duiempleado;
					document.getElementById('NITM').value = response.dataset.nitempleado;
					document.getElementById('telefonoM').value = response.dataset.telefono_empleado;
					document.getElementById('correoM').value = response.dataset.correo_empleado;
					document.getElementById('fechaM').value = response.dataset.fecha_nacimiento_empleado;
					fillSelect(ENDPOINT_TIPO, 'tipoM', response.dataset.tipo_empleado);
					fillSelect(ENDPOINT_ESTADO, 'estadoM', response.dataset.nombre_estado);
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
	let action = 'update';
	saveRow(API_EMPLEADO, action, 'form_modificar', 'modificar_modal');
});

function openDelete(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario de eliminar registro.
    M.Modal.getInstance(document.getElementById('eliminar_modal')).open();
    document.getElementById('idd').value = '';
    document.getElementById('idd').value = id;
    const data = new FormData();
    data.append('idd', id);
    fetch(API_EMPLEADO + 'readOneE', {
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
                    document.getElementById('idd').value = response.dataset.idempleado;
                    document.getElementById('nombreD').value = response.dataset.nombre_empleado;
                    document.getElementById('apellidoD').value = response.dataset.apellido_empleado;
                    document.getElementById('DUID').value = response.dataset.duiempleado;
                    document.getElementById('NITD').value = response.dataset.nitempleado;
                    document.getElementById('telefonoD').value = response.dataset.telefono_empleado;
                    document.getElementById('correoD').value = response.dataset.correo_empleado;
                    document.getElementById('fechaD').value = response.dataset.fecha_nacimiento_empleado;
                    fillSelect(ENDPOINT_TIPO, 'tipoD', response.dataset.idtipo_empleado);
                    fillSelect(ENDPOINT_ESTADO, 'estadoD', response.dataset.idestado_empleado);
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
    eliminateRow(API_EMPLEADO, data);
});