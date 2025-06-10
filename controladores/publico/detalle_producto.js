// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_CATALOGO = SERVER + 'sitio_publico/api_catalogo.php?action=';
const API_CARRITO = SERVER + 'sitio_publico/api_carrito.php?action=';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    existencias_productos = 0;
    precio_productos = 0;
    aumentar = 1;
    document.getElementById('cantidad').value = 1;
    document.getElementById('total_cantidad').value = 0;
    let menu = document.getElementById("menu");
    window.onscroll = function () {
        if (window.pageYOffset >= 80) {
            menu.classList.add("sticky");
        }
        else {
            menu.classList.remove("sticky");
        }
    }
    // Se busca en la URL las variables (parámetros) disponibles.
    let params = new URLSearchParams(location.search);
    // Se obtienen los datos localizados por medio de las variables.
    const ID = params.get('id');
    // Se llama a la función que muestra el detalle del producto seleccionado previamente.
    leerUnProducto(ID);
    generarImagenesSecundarias(ID);
    generarImagenMarca(ID);
    resenias(ID);
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
});

// Función para obtener y mostrar los datos del producto seleccionado.
function leerUnProducto(id) {
    // Se define un objeto con los datos del producto seleccionado.
    const data = new FormData();
    data.append('id_producto', id);
    // Petición para obtener los datos del producto solicitado.
    fetch(API_CATALOGO + 'leerUnProducto', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    if (response.dataset.porcentaje_descuento > 0) {
                        document.getElementById('precio_sin_descuento').classList.remove("hide");
                        let total_descuento = response.dataset.precio_producto * (response.dataset.porcentaje_descuento / 100);
                        document.getElementById('precio_sin_descuento').innerHTML += ('<s>' + '$' + response.dataset.precio_producto + '</s>');
                        document.getElementById('precio').textContent += ('$' + (response.dataset.precio_producto - total_descuento).toFixed(2));
                        document.getElementById('total_cantidad').textContent += (response.dataset.precio_producto - total_descuento).toFixed(2);
                        //Input Invisible
                        document.getElementById('precio_total').value = (response.dataset.precio_producto - total_descuento).toFixed(2);
                        document.getElementById('precio_actual').value = (response.dataset.precio_producto - total_descuento).toFixed(2);
                        //Se almacenan los valores del precio y existencias
                        existencias_productos = response.dataset.existencias;
                        precio_productos = (response.dataset.precio_producto - total_descuento).toFixed(2);
                    }
                    else {
                        document.getElementById('precio').textContent += ('$' + response.dataset.precio_producto);
                        document.getElementById('total_cantidad').textContent += response.dataset.precio_producto;
                        //Input Invisible
                        document.getElementById('precio_total').value = response.dataset.precio_producto;
                        document.getElementById('precio_actual').value = response.dataset.precio_producto;
                        //Se almacenan los valores del precio y existencias
                        existencias_productos = response.dataset.existencias;
                        precio_productos = response.dataset.precio_producto;
                    }
                    // Se colocan los datos en la tarjeta de acuerdo al producto seleccionado previamente.
                    document.getElementById('imagen_principal').setAttribute('src', SERVER + 'images/productos/' + response.dataset.imagen_principal);
                    document.getElementById('nombre').textContent = response.dataset.nombre_producto;
                    document.getElementById('descripcion').textContent = response.dataset.descripcion;
                    document.getElementById('color').textContent += response.dataset.color;
                    document.getElementById('marca').textContent += response.dataset.nombre_marca;
                    document.getElementById('tamanio').textContent += response.dataset.tamanio;
                    document.getElementById('material').textContent += response.dataset.material;
                    // Se asigna el valor del id del producto al campo oculto del formulario.
                    document.getElementById('idproducto').value = response.dataset.idproducto;
                    //Validamos si los valores de los array están vacios, serían en promedio y el total de reseñas
                    if (response.promedio_valoraciones != null || response.total_resenias != null) {
                        let promedio = Math.trunc(response.promedio_valoraciones);
                        let resenia = '';
                        //Mensaje de total de reseñas escritas por usuarios
                        resenia += `<img src="../../recursos/iconografia/reseña_mensaje.png" alt="reseña">(${response.total_resenias})<u>Reseñas</u>`;
                        document.getElementById('total_resenias').innerHTML = resenia;
                        let estrella = '';
                        //En un switch colocamos el promedio (1-5) y dependiendo de este esa será la cantidad a imprimir de estrellas
                        switch (promedio) {
                            //Caso 1, solo imprime una imagen y la cantidad de reseñas
                            case 1:
                                estrella += `
                                <div id="estrellas">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                </div>(${response.total_resenias})<u>Valoraciones</u>
                                `;
                                document.getElementById('total_valoraciones').innerHTML = estrella;
                                break;
                            //Caso 2, imprime dos imagenes y la cantidad de reseñas hechas
                            case 2:
                                estrella += `
                                <div id="estrellas">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                </div>(${response.total_resenias})<u>Valoraciones</u>
                                `;
                                document.getElementById('total_valoraciones').innerHTML = estrella;
                                break;
                            //Caso 3, imprime tres imagenes y la cantidad de reseñas hechas
                            case 3:
                                estrella += `
                                <div id="estrellas">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                </div>(${response.total_resenias})<u>Valoraciones</u>
                                `;
                                document.getElementById('total_valoraciones').innerHTML = estrella;
                                break;
                            //Caso 4, imprime cuatro imagenes y la cantidad de reseñas hechas
                            case 4:
                                estrella += `
                                <div id="estrellas">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                </div>(${response.total_resenias})<u>Valoraciones</u>
                                `;
                                document.getElementById('total_valoraciones').innerHTML = estrella;
                                break;
                            //Caso 3, imprime cinco imagenes y la cantidad de reseñas hechas
                            case 5:
                                estrella += `
                                <div id="estrellas">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                    <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                                </div>(${response.total_resenias})<u>Valoraciones</u>
                                `;
                                document.getElementById('total_valoraciones').innerHTML = estrella;
                                break;
                            default:
                                break;
                        }
                    }
                    generarImagenesSecundarias();
                } else {
                    // Se presenta un mensaje de error cuando no existen datos para mostrar.
                    document.getElementById('nombre').innerHTML = `<i class="material-icons small">cloud_off</i><span class="red-text">${response.exception}</span>`;
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function generarImagenesSecundarias(id) {
    // Se define un objeto con los datos del producto seleccionado
    const DATA = new FormData();
    DATA.append('id_producto', id);
    // Petición para consultar si existen imagenes secundarias
    fetch(API_CATALOGO + 'obtenerImagenesSecundarias', {
        method: 'post',
        body: DATA
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) { 
                    let imagen_secundaria = '';
                    response.dataset.map(function (row) {
                        imagen_secundaria += `<img class="imagen_muestra materialboxed" src="${SERVER}images/productos/${row.imagen_producto}">`;
                    });
                    document.getElementById("imagenes_secundarias").innerHTML = imagen_secundaria;
                    M.AutoInit();
                } else {
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function generarImagenMarca(id) {
    // Se define un objeto con los datos del producto seleccionado
    const DATA = new FormData();
    DATA.append('id_producto', id);
    // Petición para consultar si existen imagenes secundarias
    fetch(API_CATALOGO + 'obtenerImagenMarca', {
        method: 'post',
        body: DATA
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) { 
                    let imagen_secundaria = '';
                    imagen_secundaria += `<img class="imagen_muestra materialboxed" src="${SERVER}images/marca/${response.dataset.imagen_marca}">`;
                    document.getElementById("imagenes_secundarias").innerHTML += imagen_secundaria;
                    M.AutoInit();
                } else {
                    console.log(imagen_secundaria);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

//Funcion para cargar las reseñas hechas por usuarios
function resenias(id) {
    // Se define un objeto con los datos del producto seleccionado.
    const data = new FormData();
    data.append('id_producto', id);
    // Petición para consultar si existen reseñas.
    fetch(API_CATALOGO + 'resenias', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra en consola un mensaje con la excepción.
                if (response.status) {
                    let resenias_usuarios = '';
                    let estrellas_user = '';
                    //Cada usuario coloco una valoración del 1 - 5 y por cada valor se le representará por una estrella
                    response.dataset.map(function (row) {
                        //Evaluamos cuál caso cumple el usuario cliente, y se le anexa x cantidad de imagenes
                        if (row.valoraciones == 1) {
                            estrellas_user = `
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            `;
                        }
                        else if (row.valoraciones == 2) {
                            estrellas_user = `
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            `;
                        }
                        else if (row.valoraciones == 3) {
                            estrellas_user = `
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            `;
                        } else if (row.valoraciones == 4) {
                            estrellas_user = `
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            `;
                        } else if (row.valoraciones == 5) {
                            estrellas_user = `
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            <img src="../../recursos/iconografia/estrella.png" alt="estrella">
                            `;
                        }
                        //Luego le podemos anexar la cantidad de imagenes a la reseña
                        resenias_usuarios += `
                        <div class="usuario">
                            <div class="foto_usuario">
                                <img src="../../recursos/iconografia/usuario_redondo.png" alt="user">
                            </div>
                            <div class="nombre_usuario">
                                <h6>${row.nombre_cliente + " " + row.apellido_cliente}</h6>
                                <div id="estrellas_usuario">
                                    ${estrellas_user}
                                </div>
                                <p>${row.fecha_publicacion}</p>
                            </div>
                        </div>
                        <div class="comentario">
                            <p>${row.reseña}</p>
                        </div>
                        <hr>`;
                        document.getElementById('resenias_usuarios').innerHTML = resenias_usuarios;
                    });
                } else {
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de agregar un producto al carrito.
document.getElementById('detalle-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Petición para agregar un producto al pedido.
    fetch(API_CARRITO + 'crearDetalle', {
        method: 'post',
        body: new FormData(document.getElementById('detalle-form'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se constata si el cliente ha iniciado sesión.
                if (response.estado) {
                    sweetAlert(1, response.message, 'carrito.html');
                } else {
                    // Se verifica si el cliente ha iniciado sesión para mostrar la excepción, de lo contrario se direcciona para que se autentique. 
                    if (response.session) {
                        sweetAlert(2, response.exception, null);
                    } else {
                        sweetAlert(3, response.exception, 'index.html');
                    }
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
});

//Funciones para aumentar y reducir existencias
var existencias_productos;
var precio_productos;
var aumentar = 1;
function sumar() {
    if (aumentar < existencias_productos) {
        aumentar = aumentar + 1;
        cantidad_precio(aumentar);
        document.querySelector('.numero').innerHTML = aumentar;
        document.getElementById('cantidad').value = aumentar;
    } else {
    }
};

function resta() {
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