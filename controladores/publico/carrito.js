const API_CARRITO = SERVER + 'sitio_publico/api_carrito.php?action=';

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
    leerDetalleOrden();
});


// Función para obtener el detalle del pedido (carrito de compras).
function leerDetalleOrden() {
    // Petición para solicitar los datos del pedido en proceso.
    fetch(API_CARRITO + 'leerDetallePedido', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se declara e inicializa una variable para concatenar las filas de la tabla en la vista.
                    var producto = '';
                    let subtotal1 = '';
                    let subtotal2 = '';
                    // Se declara e inicializa una variable para calcular el importe por cada producto.
                    var precio = 0;
                    var subtotal = 0;
                    // Se declara e inicializa una variable para ir sumando cada subtotal y obtener el monto final a pagar.
                    var totalP = 0;
                    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        precio = (row.precio_producto * row.cantidad_producto);
                        subtotal += precio;
                        totalP += row.cantidad_producto;
                        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
                        producto += `
                            <form>
                                <div class="producto_imagen">
                                    <img src="${SERVER}images/productos/${row.imagen_principal}"
                                        alt="producto">
                                </div>
                                <div class="detalle_producto">
                                    <h6>${row.nombre_producto}</h6>
                                    <p>${row.estado_producto}</p>
                                    <div>
                                        <h5>Cantidad</h5>
                                        <div class="cantidad">
                                            <span class="numero">${row.cantidad_producto}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="precio_producto">
                                    <h5>US $${row.total_producto}</h5>
                                </div>
                                <div class="eliminar_producto">
                                    <a onclick="openDeleteDialog(${row.iddetalle_factura})">Eliminar</a>
                                </div>
                            </form>
                            <hr>`;
                        // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
                        document.getElementById('producto_datos').innerHTML = producto;
                    });
                    subtotal1 += `
                        <h5>Subtotal (${totalP} Productos): </h5>
                        <h5>US $${subtotal.toFixed(2)}</h5>`;
                    subtotal2 += `
                        <form method="post">
                            <h6>Subtotal (${totalP} Productos):</h6>
                            <h5>US $${subtotal.toFixed(2)}</h5>
                            <a class="btn-small waves-effect waves-orange" href="direccion_envio.html">Confirmar Pedido</a>
                        </form>`;
                    // Se agregan las los campos del subtotal
                    document.getElementById('subtotal_productos').innerHTML = subtotal1;
                    // Se agregan las los campos del subtotal
                    document.getElementById('contenedor_subtotal_carrito').innerHTML = subtotal2;
                } else {
                    sweetAlert(4, response.exception, 'dashboard.html');
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}


// Función para mostrar un mensaje de confirmación al momento de eliminar un producto del carrito.
function openDeleteDialog(id) {
    swal({
        title: 'Advertencia',
        text: '¿Está seguro de remover el producto?',
        icon: 'warning',
        buttons: ['No', 'Sí'],
        closeOnClickOutside: false,
        closeOnEsc: false
    }).then(function (value) {
        // Se verifica si fue cliqueado el botón Sí para realizar la petición respectiva, de lo contrario no se hace nada.
        if (value) {
            // Se define un objeto con los datos del producto seleccionado.
            const data = new FormData();
            data.append('id_detalle', id);
            // Petición para remover un producto del pedido.
            fetch(API_CARRITO + 'eliminarDetalle', {
                method: 'post',
                body: data
            }).then(function (request) {
                // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                if (request.ok) {
                    request.json().then(function (response) {
                        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                        if (response.estado) {
                            // Se regresan sus valores a 0
                            subtotal = 0;
                            // Se regresan sus valores a 0
                            totalP = 0;
                            leerDetalleOrden();
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
    });
}

function eliminarProductos() {
    swal({
        title: 'Advertencia',
        text: '¿Está seguro de remover todos los productos?',
        icon: 'warning',
        buttons: ['No', 'Sí'],
        closeOnClickOutside: false,
        closeOnEsc: false
    }).then(function (value) {
        // Se verifica si fue cliqueado el botón Sí para realizar la petición respectiva, de lo contrario no se hace nada.
        if (value) {
            // Se define un objeto con los datos del producto seleccionado.
            fetch(API_CARRITO + 'eliminarDetalles', {
                method: 'post'
            }).then(function (request) {
                // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                if (request.ok) {
                    request.json().then(function (response) {
                        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                        if (response.estado) {
                            // Se regresan sus valores a 0
                            subtotal = 0;
                            // Se regresan sus valores a 0
                            totalP = 0;
                            leerDetalleOrden();
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
    });
}

//Funciones para aumentar y reducir existencias
/*
var existencias_productos;
var precio_productos;
var aumentar = 1;
function sumaCarrito() {
    if (aumentar < 10) {
        aumentar = aumentar + 1;
        cantidad_precio(aumentar);
        document.querySelector('.numero').innerHTML = aumentar;
        document.getElementById('cantidad').value = aumentar;
    } else {
    }
};

function restar() {
    if (aumentar > 1) {
        aumentar = aumentar - 1;
        cantidad_precio(aumentar);
        document.querySelector('.numero').innerHTML = aumentar;
        document.getElementById('cantidad').value = aumentar;
    } else {

    }
};

//Según las existencias se multiplica por el precio del producto
function cantidad_precio(valor) {
    let resultado;
    resultado = precio_productos * valor
    document.getElementById('total_cantidad').innerHTML = ('$' + resultado.toFixed(2));
    document.getElementById('precio_total').value = (resultado.toFixed(2));
};
*/
