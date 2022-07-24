// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_PRODCUTO = SERVER + 'sitio_privado/api_producto.php?action=';
const ENDPOINT_CATEGORIA = SERVER + "sitio_privado/api_categorias.php?action=readAll";
const ENDPOINT_SUBC = SERVER + 'sitio_privado/api_subcategoria.php?action=readAll';
const ENDPOINT_MARCA = SERVER + 'sitio_privado/api_marca.php?action=readAll';
const ENDPOINT_PROVEEDOR = SERVER + 'sitio_privado/api_proveedor.php?action=readAll';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {

    // Se llaman las funciones para mostrar las gráficas en la página.
});

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    let menu = document.getElementById("menu");
    graficoBarrasClientes();
    window.onscroll = function () {
        if (window.pageYOffset >= 100) {
            menu.classList.add("sticky");
        }
        else {
            menu.classList.remove("sticky");
        }
    }
    //Inicializar el componente del Tab
    M.AutoInit();
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));

    //Instanciar Datepicker
    M.Datepicker.init(document.querySelectorAll('.datepicker'), {
        format: 'yyyy-mm-dd', i18n: {
            months: ['Enero', 'Febrero', 'Marzo', 'April', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthsShort: ['En', 'Febr', 'Mzo', 'Abr', 'My', 'Jun', 'Jul', 'Ag', 'Sept', 'Oct', 'Nov', 'Dic'],
            weekdaysShort: ['Dom', 'Lun', 'Mar', 'Miérc', 'Juev', 'Vier', 'Sáb'],
            weekdaysAbbrev: ['D', 'L', 'M', 'X', 'J', 'V', 'S']
        }
    });
});

// Función para mostrar la cantidad de clientes creados por mes mediante gráfico de lineas
function graficoBarrasClientes() {
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODCUTO + 'graficoCreadoxmes', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a graficar.
                    let fecha = [];
                    let cantidades = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        fecha.push(row.fecha_creacion);
                        cantidades.push(row.usuarios_creados);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    lineGraph('chart1', fecha, cantidades, 'Cantidad de Clientes', 'Clientes creados por mes');
                } else {
                    document.getElementById('chart1').remove();
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function MandarFechas(api, action, form) {
    fetch(api + action, {
        method: 'post',
        body: new FormData(document.getElementById(form))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se cargan nuevamente las filas en la tabla de la vista después de guardar un registro y se muestra un mensaje de éxito.
                    sweetAlert(1, response.message, null);
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

// Función para preparar el formulario al momento de modificar un registro.
function openGraficaVenta() {
	// Se define un objeto con los datos del registro seleccionado.
	const data = new FormData();
	/*data.append('fecha_inicio', fecha_inicio);
    data.append('fecha_final', fecha_final);*/
	// Petición para obtener los datos del registro solicitado.
    
	fetch(API_PRODCUTO + 'graficoInventarioRango', {
		method: 'post',
		body: new FormData(document.getElementById('MandarFechas'))
	}).then(function (request) {
		// Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
		if (request.ok) {
			// Se obtiene la respuesta en formato JSON.
			request.json().then(function (response) {
				// Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
				if (response.estado) {
					// Se inicializan los campos del formulario con los datos del registro seleccionado.
                    let fecha = [];
                    let cantidades = [];
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        fecha.push(row.fecha_entrega);
                        cantidades.push(row.cantidad);
                        console.log(fecha);
                        console.log(cantidades);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    barGraph('grafica7', fecha, cantidades, 'Cantidad de inventario por fecha', 'Inventario por fecha');
                    lineGraph('grafica8', fecha, cantidades, 'Cantidad de inventario por fecha', 'Inventario por fecha');
				} else {
					sweetAlert(2, response.exception, null);
				}
			});
		} else {
			console.log(request.estado + ' ' + request.statusText);
		}
	});
}
//función para poder mandar los parametros de fecha inicio y final al reporte
    document.getElementById('MandarFechas').addEventListener('submit', function (event) {
        // Se evita recargar la página web después de enviar el formulario.
        event.preventDefault();
        let action = 'graficoInventarioRango';
        MandarFechas(API_PRODCUTO, action, 'MandarFechas');
    });

function seleccionFiltro(opcion) {
    console.log(opcion);
    switch (opcion) {
        case "1":
            //Traemos todas las categorias existentes
            fillSelect(ENDPOINT_CATEGORIA, 'una categoría', 'opciones', null);
            break;
        case "2":
            //Traemos todas las subcategorias existentes
            fillSelect(ENDPOINT_SUBC, 'una subcategoría', 'opciones', null);
            break;
        case "3":
            //Traemos todas las marcas existentes
            fillSelect(ENDPOINT_MARCA, 'una marca', 'opciones', null);
            break;
        case "4":
            //Traemos todos los proveedores existentes
            fillSelect(ENDPOINT_PROVEEDOR, 'un proveedor', 'opciones', null);
            break;
        default:
            console.log("no entre a ningún caso");
            break;
    }
}
