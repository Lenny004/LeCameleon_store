// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_PEDIDOS = SERVER + "sitio_privado/api_pedidos.php?action=";

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

    //Instanciar ToolTips footer
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));

    CargarPedidos();
});

function CargarPedidos(){
    // Petición para consultar si existen pedidos registrados.
    fetch(API_PEDIDOS + 'existenciaPedidos', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    data = response.dataset;
                } else {
                    sweetAlert(4, response.exception, null);
                }
                // Se envían los datos a la función del controlador para llenar la tabla en la vista.
                CrearTarjetas(data);
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });

    // Petición para consultar si existen pedidos con fecha de entrega actual.
    fetch(API_PEDIDOS + 'existenciaPedidosHoy', {
        method: 'get'
    }).then(function (request){
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if(request.ok){
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado){
                    data = response.dataset;
                } else {
                    sweetAlert(4, response.exception, null);
                }
                // Se envían los datos a la función del controlador para llenar la tabla en la vista.
                CrearPedidoHoy(data);
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

function CrearPedidoHoy(dataset){
    let contenido_entrega_hoy = '';
    dataset.map(function (row){
        contenido_entrega_hoy += `
            <div id="pedidos">
                <div id="disponible">
                    <a href=""></a>
                    <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
                </div>
                <div id="direccion">
                    <h6>Dirección de entrega:</h6>
                    <p>${row.direccion_entrega_pedido}</p>
                    <h6>Nombre Cliente:</h6>
                    <p>${row.nombre_cliente + " " + row.apellido_cliente}</p>
                    <h6>Fecha y Hora de Entrega:</h6>
                    <p>${row.fecha_entrega_pedido}</p>
                </div>
                <div id="tomar">
                    <a onclick="AsignarPedido(${row.idenvio_pedido})" class="waves-effect waves-light btn-small">Tomar Pedido</a>
                </div>
            </div>`;
        document.getElementById('entrega_hoy').innerHTML = contenido_entrega_hoy;
    });
}

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function CrearTarjetas(dataset) {
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        let contenido_retraso = '';
        let contenido_entregandose = '';
        let contenido_pedidos = '';
        dataset.map(function (row) {
            switch (row.idestado_factura) {
                case 1://Cancelada
                    break;
                case 2://Pendiente
                    contenido_pedidos += `
                        <div id="pedidos">
                            <div id="no_disponible">
                                <a href=""></a>
                                <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
                            </div>
                            <div id="direccion">
                                <h6>Dirección de entrega:</h6>
                                <p>${row.direccion_entrega_pedido}</p>
                                <h6>Nombre Cliente:</h6>
                                <p>${row.nombre_cliente + " " + row.apellido_cliente}</p>
                                <h6>Fecha y Hora de Entrega:</h6>
                                <p>${row.fecha_entrega_pedido}</p>
                            </div>
                            <div id="tomar">
                                <a class="waves-effect waves-light btn-small disabled">No es la fecha de entrega</a>
                            </div>
                        </div>`;
                    document.getElementById('realizado_hoy').innerHTML = contenido_pedidos;
                    break;
                case 3://Retrasada
                    contenido_retraso += `
                        <div id="pedidos">
                            <div id="pendiente">
                                <a href=""></a>
                                <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
                            </div>
                            <div id="direccion">
                                <h6>Dirección de entrega:</h6>
                                <p>${row.direccion_entrega_pedido}</p>
                                <h6>Nombre Cliente:</h6>
                                <p>${row.nombre_cliente + " " + row.apellido_cliente}</p>
                                <h6>Fecha y Hora de Entrega:</h6>
                                <p>${row.fecha_entrega_pedido}</p>
                            </div>
                            <div id="tomar">
                                <a onclick="AsignarPedido(${row.idenvio_pedido})" class="waves-effect waves-light btn-small">Tomar Pedido Retrasado</a>
                            </div>
                        </div>`;
                    document.getElementById('retrasado').innerHTML = contenido_retraso;
                    break;
                case 4://Entregando
                    contenido_entregandose += `
                        <div id="pedidos">
                            <div id="entregando">
                                <a href=""></a>
                                <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
                            </div>
                            <div id="direccion">
                                <h6>Dirección de entrega:</h6>
                                <p>${row.direccion_entrega_pedido}</p>
                                <h6>Nombre Cliente:</h6>
                                <p>${row.nombre_cliente + " " + row.apellido_cliente}</p>
                                <h6>Fecha y Hora de Entrega:</h6>
                                <p>${row.fecha_entrega_pedido}</p>
                            </div>
                            <div id="tomar2">
                                <a class="btn tooltipped waves-effect waves-light btn-small" data-position="left"
                                    data-tooltip="Ya estoy en camino. Alguien ya me ha tomado">Entregando</a>
                            </div>
                        </div>`;
                    document.getElementById('entregandose').innerHTML = contenido_entregandose;
                    break;
                default:
                    break;
            }
        });
    });
};

function AsignarPedido(id) {
	document.getElementById('id').value = '';
	document.getElementById('id').value = id;
	const DATA = new FormData();
	DATA.append('id', id);
	fetch(API_PEDIDOS + 'asignarFacturaEmpleado', {
		method: 'post',
		body: DATA
	}).then(function (request) {
		// Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
		if (request.ok) {
			// Se obtiene la respuesta en formato JSON.
			request.json().then(function (response) {
				// Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
				if (response.estado) {
                    CargarPedidos();
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
