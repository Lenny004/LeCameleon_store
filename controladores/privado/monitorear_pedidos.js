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

    //Instanciar ToolTips pedido
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));

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
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function CrearTarjetas(dataset) {
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        let contenido_retraso = '';
        let contenido_entrega_hoy = '';
        let contenido_entregandose = '';
        let contenido_pedidos = '';
        switch (4){
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
                            <p>${row.direccion_entrega_pedido}
                            </p>
                            <h6>Nombre Cliente:</h6>
                            <p>${row.nombre_cliente}
                            </p>
                            <h6>Fecha y Hora de Entrega:</h6>
                            <p>${row.fecha_entrega_pedido}
                            </p>
                        </div>
                        <div id="tomar">
                            <a class="waves-effect waves-light btn-small disabled">Tomar Pedido</a>
                        </div>
                    </div>`;
                    document.getElementById('realizado_hoy').innerHTML = contenido_pedidos;
                break;
            case 3://Retrasada
                contenido_retraso +=`
                    <div id="pedidos">
                        <div id="retrasado">
                            <a href=""></a>
                            <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
                        </div>
                        <div id="direccion">
                            <h6>Dirección de entrega:</h6>
                            <p>${row.direccion_entrega_pedido}
                            </p>
                            <h6>Nombre Cliente:</h6>
                            <p>${row.nombre_cliente}
                            </p>
                            <h6>Fecha y Hora de Entrega:</h6>
                            <p>${row.fecha_entrega_pedido}
                            </p>
                        </div>
                        <div id="tomar">
                            <a class="waves-effect waves-light btn-small">Tomar Pedido</a>
                        </div>
                    </div>`;
                    document.getElementById('retrasado').innerHTML = contenido_retraso;
                break;
            case 4://Entregando
                contenido_entregandose +=`
                    <div id="pedidos">
                        <div id="entregando">
                            <a href=""></a>
                            <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
                        </div>
                        <div id="direccion">
                            <h6>Dirección de entrega:</h6>
                            <p>${row.direccion_entrega_pedido}
                            </p>
                            <h6>Nombre Cliente:</h6>
                            <p>${row.nombre_cliente}
                            </p>
                            <h6>Fecha y Hora de Entrega:</h6>
                            <p>${row.fecha_entrega_pedido}
                            </p>
                        </div>
                        <div id="tomar2">
                            <a class="btn tooltipped waves-effect waves-light btn-small" data-position="left"
                                data-tooltip="Ya estoy en camino. Alguien ya me ha tomado">Tomar Pedido</a>
                        </div>
                    </div>`;
                    document.getElementById('entregandose').innerHTML = contenido_entregandose;
                break;
        }
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('retrasado').innerHTML = contenido_retraso;
    document.getElementById('realizado_hoy').innerHTML = contenido_pedidos;
    document.getElementById('entregandose').innerHTML = contenido_entregandose;
    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
    M.Materialbox.init(document.querySelectorAll('.materialboxed'));
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
}
