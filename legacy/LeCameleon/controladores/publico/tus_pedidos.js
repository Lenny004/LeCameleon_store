// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_PEDIDOS_CLIENTE = SERVER + "sitio_publico/api_pedidos_cliente.php?action=";

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    let menu = document.getElementById("menu");
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
    CargarPedidos();
});


function CargarPedidos() {
    // Petición para consultar si existen pedidos registrados.
    fetch(API_PEDIDOS_CLIENTE + 'pedidosRealizadosCliente', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    //Mandamos los resultados obtenidos de la sentencia de pedidos realizados
                    data = response.dataset;
                } else {
                    //Si no se tienen pedidos realizados se muestra una alerta
                    sweetAlert(4, response.exception, null);
                }
                // Se envían los datos a la función para crear la tarjeta de pedidos realizados
                crearPedidosRealizados(data);
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });

    // Petición para consultar si existen pedidos con fecha de entrega actual.
    fetch(API_PEDIDOS_CLIENTE + 'pedidosClienteEntregaHoy', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    //Mandamos los resultados obtenidos de la sentencia de pedidos realizados con fecha de entrega actual y un mensaje
                    sweetAlert(1, response.message, null);
                    data = response.dataset;
                } else {
                    //Se imprime en consola un mensaje que no se tienen pedidos entregados
                    console.log(response.exception);
                }
                // Se envían los datos a la función para crear la tarjeta de pedidos realizados con fecha de entrega hoy
                crearPedidoEntregaHoy(data);
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });

    // Petición para consultar si existen pedidos con fecha de entrega actual.
    fetch(API_PEDIDOS_CLIENTE + 'pedidosEntregadosCliente', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    //Mandamos los resultados obtenidos de la sentencia de pedidos entregados
                    data = response.dataset;
                    console.log(data);
                } else {
                    console.log(response.exception);
                }
                // Se envían los datos a la función para crear la tarjeta de pedidos entregados
                crearPedidosEntregados(data);
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

//Función para crear tarjeta de pedidos realizados por el cliente
function crearPedidosRealizados(dataset) {
    let pedidos_realizados = '';
    dataset.map(function (row) {
        pedidos_realizados += `
        <div id="pedidos" class="z-depth-2">
            <div class="imagen_producto">
                <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
            </div>
            <div class="datos_producto">
                <p><b>${row.nombre_producto}</b></p>
                <p><b>Dirección de entrega:</b> ${row.direccion_entrega_pedido}</p>
                <p><b>Fecha de entrega:</b> ${row.fecha_entrega_pedido}</p>
                <p><b>Cantidad:</b> ${row.cantidad_producto}</p>
                <p><b>Total:</b> $${row.total_producto}</p>
            </div>
        </div>`;
        document.getElementById('realizados').innerHTML = pedidos_realizados;
    });
}

//Función para crear tarjeta de pedidos realizados con fecha de entrega actual
function crearPedidoEntregaHoy(dataset) {
    let pedido_entrega_hoy = '';
    dataset.map(function (row) {
        pedido_entrega_hoy += `
        <div id="pedidos_entrega_hoy" class="z-depth-2">
            <div class="imagen_producto">
                <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
            </div>
            <div class="datos_producto">
                <p class="anuncio_entrega_hoy"><b>ENTREGA HOY</b></p>
                <p><b>${row.nombre_producto}</b></p>
                <p><b>Dirección de entrega:</b> ${row.direccion_entrega_pedido}</p>
                <p><b>Fecha de entrega:</b> ${row.fecha_entrega_pedido}</p>
                <p><b>Cantidad:</b> ${row.cantidad_producto}</p>
                <p><b>Total:</b> $${row.total_producto}</p>
            </div>
        </div>`;
        document.getElementById('realizados').innerHTML += pedido_entrega_hoy;
    });
}

//Función para crear tarjeta de pedidos entregados al cliente
function crearPedidosEntregados(dataset) {
    let pedidos_entregados = '';
    dataset.map(function (row) {
        //Si el producto ya posee una reseña por este cliente
        pedidos_entregados += `
            <div id="pedidos" class="z-depth-2">
                <div class="imagen_producto">
                    <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
                </div>
                <div class="datos_producto">
                    <p><b>${row.nombre_producto}</b></p>
                    <p><b>${row.fecha_entrega_pedido}</b></p>
                    <p><b>Cantidad:</b> ${row.cantidad_producto}</p>
                    <p><b>Total:</b> $${row.total_producto}</p>
                </div>
                <div class="boton_azul">
                    <button onclick="agregarResenia(${row.idproducto})" class="btn-small waves-effect waves-blue tooltipped" data-tooltip="Agregar Reseña" type="submit">Escribir Reseña</button>
                </div>
            </div>`;
        document.getElementById('entregados').innerHTML = pedidos_entregados;

        //Si el producto no posee una reseña por este cliente, se crea un boton para agregar una
        /*
        else if (row.idusuario_c != null){
            pedidos_entregados += `
            <div id="pedidos">
                <div class="imagen_producto">
                    <img src="../../recursos/img/pedidos/camion_pedidos.jpg" alt="camnion">
                </div>
                <div class="datos_producto">
                    <p><b>${row.nombre_producto}</b></p>
                    <p><b>Entregado el ${row.fecha_entrega_pedido}</b></p>
                    <p><b>Cantidad:</b> ${row.cantidad_producto}</p>
                    <p><b>Total:</b> $${row.total_producto}</p>
                    <input class="hide" type="number" id="id_imagen" name="id">
                </div>
            </div>`;
            document.getElementById('entregados').innerHTML += pedidos_entregados;
        }*/
    });
}

// Función para preparar el formulario al momento de agregar reseña
function agregarResenia(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('modal_resenia_producto')).open();
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('ide', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_PEDIDOS_CLIENTE + 'leerProducto', {
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
                    document.getElementById('idpedido').value = response.dataset.idproducto;
                    document.getElementById('nombre_producto').value = response.dataset.nombre_producto;
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

//Al presionar en el boton de agregar reseña
document.getElementById('form-resenia').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Se agrega al input invisible el valor del 1 - 5 según la estrella seleccionada, por defecto será 1
    document.getElementById('valoracion').value = valoracion;
    fetch(API_PEDIDOS_CLIENTE + 'agregarResenia', {
        method: 'post',
        body: new FormData(document.getElementById('form-resenia'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se cierra la caja de dialogo (modal) del formulario.
                    M.Modal.getInstance(document.getElementById('modal_resenia_producto')).close();
                    // Se cargan nuevamente las filas en la tabla de la vista después de guardar un registro y se muestra un mensaje de éxito.
                    sweetAlert(1, response.message, null);
                    CargarPedidos();
                } else {
                    sweetAlert(2, response.exception, null);
                }
                CargarPedidos();
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
});

var valoracion = 1;

function valoraciones(valor) {
    //Se crean las variables que almacenaran los valores de las estrellas
    let image1;
    let image2;
    let image3;
    let image4;
    let image5;
    switch (valor) {
        //Caso en que presione solo una estrella
        case 1:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image4 = document.getElementById("estrella4");
            image5 = document.getElementById("estrella5");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella_borde.png";
            image3.src = "../../recursos/iconografia/estrella_borde.png";
            image4.src = "../../recursos/iconografia/estrella_borde.png";
            image5.src = "../../recursos/iconografia/estrella_borde.png";
            valoracion = 1;
            break;
        //Caso en que presione dos estrellas
        case 2:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image4 = document.getElementById("estrella4");
            image5 = document.getElementById("estrella5");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella.png";
            image3.src = "../../recursos/iconografia/estrella_borde.png";
            image4.src = "../../recursos/iconografia/estrella_borde.png";
            image5.src = "../../recursos/iconografia/estrella_borde.png";
            valoracion = 2;
            break;
        //Caso en que presione tres estrellas
        case 3:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image4 = document.getElementById("estrella4");
            image5 = document.getElementById("estrella5");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella.png";
            image3.src = "../../recursos/iconografia/estrella.png";
            image4.src = "../../recursos/iconografia/estrella_borde.png";
            image5.src = "../../recursos/iconografia/estrella_borde.png";
            valoracion = 3;
            break;
        //Caso en que presione cuatro estrellas
        case 4:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image4 = document.getElementById("estrella4");
            image5 = document.getElementById("estrella5");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella.png";
            image3.src = "../../recursos/iconografia/estrella.png";
            image4.src = "../../recursos/iconografia/estrella.png";
            image5.src = "../../recursos/iconografia/estrella_borde.png";
            valoracion = 4;
            break;
        //Caso en que presione cinco estrellas
        case 5:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image4 = document.getElementById("estrella4");
            image5 = document.getElementById("estrella5");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella.png";
            image3.src = "../../recursos/iconografia/estrella.png";
            image4.src = "../../recursos/iconografia/estrella.png";
            image5.src = "../../recursos/iconografia/estrella.png";
            valoracion = 5;
            break;
        //Default
        default:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image4 = document.getElementById("estrella4");
            image5 = document.getElementById("estrella5");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella_borde.png";
            image3.src = "../../recursos/iconografia/estrella_borde.png";
            image4.src = "../../recursos/iconografia/estrella_borde.png";
            image5.src = "../../recursos/iconografia/estrella_borde.png";
            valoracion = 1;
            break;
    }
}