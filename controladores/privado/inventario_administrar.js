// Constantes para establecer las rutas y parámetros de comunicación con la API.
const API_PRODUCTOS = SERVER + 'sitio_privado/api_producto.php?action=';
// Estas constantes son para establecer conexión con lo SELECT
const ENDPOINT_COLOR = SERVER + 'sitio_privado/api_producto.php?action=obtenerColor';
const ENDPOINT_MARCA = SERVER + 'sitio_privado/api_marca.php?action=readAll';
const ENDPOINT_PROVEEDOR = SERVER + 'sitio_privado/api_proveedor.php?action=readAll';
const ENDPOINT_ESTADO = SERVER + 'sitio_privado/api_producto.php?action=obtenerEstadoProducto';
const ENDPOINT_SUBC = SERVER + 'sitio_privado/api_subcategoria.php?action=readAll';
const ENDPOINT_IMG = SERVER + 'sitop_privado/api_producto.php?action=obtenerImagenes';

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

    // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
    M.Materialbox.init(document.querySelectorAll('.materialboxed'));
});

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_PRODUCTOS);
    // Se define una variable para establecer las opciones del componente Modal.
    let options = {
        dismissible: false,
        onOpenStart: function () {
            // Se restauran los elementos del formulario.
            document.getElementById('agregar_forms').reset();
            document.getElementById('modificar_forms').reset();
            document.getElementById('eliminar_forms').reset();
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
            <td><img src="${SERVER}images/productos/${row.imagen_principal}"></td>
            <td>${row.nombre_producto}</td>
        	<td>${row.estado_producto}</td>
            <td>${row.subcategoria_producto}</td>
        	<td>${row.precio_producto}</td>
            <td>
				<div id="acciones">
                	<a onclick="openUpdate(${row.idproducto})" class="tooltipped" data-tooltip="Actualizar">
                	<img src="../../recursos/iconografia/editar.png" alt="editar">
                	</a>
                	<a onclick="openDelete(${row.idproducto})" class="tooltipped" data-tooltip="Eliminar">
                	<img src="../../recursos/iconografia/eliminar.png" alt="eliminar"></a>
                	</a>
                    <a onclick="openResenias(${row.idproducto})" class="tooltipped" data-tooltip="Reseñas">
                    <img src="../../recursos/iconografia/reseña.png" alt="reseña"></a>
				</div>
            </td>
        </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbproducto').innerHTML = content;
    //Instanciar ToolTips footer
    M.Tooltip.init(document.querySelectorAll(".tooltipped"));
}

// Función para preparar el formulario al momento de insertar un registro.
function openCreate() {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('agregar_modal_producto')).open();
    // Se establece el campo de archivo como obligatorio.
    document.getElementById('archivo').required = true;
    // Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js
    fillSelect(ENDPOINT_COLOR, 'un color', 'color', null);
    fillSelect(ENDPOINT_MARCA, 'una marca', 'marca', null);
    fillSelect(ENDPOINT_PROVEEDOR, 'un distribuidor', 'distribuidor', null);
    fillSelect(ENDPOINT_ESTADO, 'un estado', 'estado', null);
    fillSelect(ENDPOINT_SUBC, 'una subcategoría', 'subc', null);
    imagenesReseteadas();
}

//Función para validar la vista preliminar de una imagen
function preliminar(event) {
    let leer_img = new FileReader();
    let id_img = document.getElementById("imagen_principal");
    //Onload es un método que al cargar las imagenes seleccionadas procede a realizar una accion
    leer_img.onload = () => {
        if (leer_img.readyState == 2) {
            id_img.src = leer_img.result
        }
    }
    leer_img.readAsDataURL(event.target.files[0])
}

//Función que resetea las imagenes y coloca una por defecto
function imagenesReseteadas() {
    image1 = document.getElementById("imagen0");
    image2 = document.getElementById("imagen1");
    image3 = document.getElementById("imagen2");
    image4 = document.getElementById("imagen3");
    image5 = document.getElementById("imagen4");
    image1.src = "../../recursos/iconografia/sin_imagen.png";
    image2.src = "../../recursos/iconografia/sin_imagen.png";
    image3.src = "../../recursos/iconografia/sin_imagen.png";
    image4.src = "../../recursos/iconografia/sin_imagen.png";
    image5.src = "../../recursos/iconografia/sin_imagen.png";
}

var lista_img;

//Función para validar que la cantidad de archivos selecionados por el input files multiple no sea mayor a 5
function cantidad(e) {
    //Se formatean las imagenes si en caso vuelve a elegir otras imagenes y una cantidad inferior
    imagenesReseteadas();
    //Validamos la lingitud de los archivos seleccionados
    if (e.target.files.length > 4) {
        //Si es mayor a 5 salta una alerta
        sweetAlert(2, "Solo puedes subir 4 imagenes máximo", null);
        e.preventDefault();
        //Le damos un valor vacio si en caso la longitud es mayor a 4, de esta manera no cargaran los nombres de los archivos
        document.getElementById("archivos").value = "";
    }
    //Si es menor a 4
    else {
        //Objeto formulario
        lista_img = new FormData();
        //Por cada imagen selecionada se irá agregando a la vista preliminar en pequeño
        for (let i = 0; i < e.target.files.length; i++) {
            let leer_img = new FileReader();
            //Se trae el valor de cada apartado de imagen preliminar en la vista que irá incrementando por el "i"
            let id_img = document.getElementById(`imagen${i}`);
            //Onload es un método que al cargar las imagenes seleccionadas procede a realizar una accion
            leer_img.onload = () => {
                if (leer_img.readyState == 2) {
                    id_img.src = leer_img.result
                    //Guardamos los nombres de cada imagen en un array con la función append
                    lista_img.append("img_extra[]", e.target.files[i]);
                    console.log(toString(e.target.files[i]));
                }
            }
            leer_img.readAsDataURL(e.target.files[i]);
        }
    }
}

// Función para insertar el registro
document.getElementById('agregar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    let action = 'crearProducto';
    saveRow(API_PRODUCTOS, action, 'agregar_forms', 'agregar_modal_producto');
    //agregarImagenesExtras(lista_img);
});

function agregarImagenesExtras(data) {
    // Petición para obtener los datos del registro solicitado.
    fetch(API_PRODUCTOS + 'agregarImagenesExtras', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
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
function openUpdate(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('modificar_modal_producto')).open();
    // Se asigna el título para la caja de diálogo (modal).
    document.getElementById('archivop').required = false;
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('ide', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_PRODUCTOS + 'obtenerUnProducto', {
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
                    document.getElementById('ide').value = response.dataset.idproducto;
                    document.getElementById('nombreM').value = response.dataset.nombre_producto;
                    document.getElementById('descripcionM').value = response.dataset.descripcion;
                    document.getElementById('materialM').value = response.dataset.material;
                    document.getElementById('tamañoM').value = response.dataset.tamanio;
                    document.getElementById('existenciasM').value = response.dataset.existencias;
                    document.getElementById('descuentoM').value = response.dataset.porcentaje_descuento;
                    document.getElementById('precioM').value = response.dataset.precio_producto;
                    fillSelect(ENDPOINT_COLOR, 'un color', 'colorM', response.dataset.idcolor);
                    fillSelect(ENDPOINT_MARCA, 'una marca', 'marcaM', response.dataset.id_marca);
                    fillSelect(ENDPOINT_PROVEEDOR, 'un distribuidor', 'distribuidorM', response.dataset.iddistribuidor);
                    fillSelect(ENDPOINT_ESTADO, 'un estado', 'estadoM', response.dataset.idestado_producto);
                    fillSelect(ENDPOINT_SUBC, 'una subcategoría', 'subcM', response.dataset.idsubcategoria_producto);
                    document.getElementById('nombrearchivo').value = response.dataset.imagen_principal;
                    imagen_principal = document.getElementById("imagen_principal_modificar");
                    imagen_principal.src = `${SERVER}images/productos/${response.dataset.imagen_principal}`;
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

//Función para modificar un producto
document.getElementById('modificar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    let action = 'actualizarProducto';
    saveRow(API_PRODUCTOS, action, 'modificar_forms', 'modificar_modal_producto');
});

//Función para validar la vista preliminar de una imagen al modificar y eliminar
function preliminar2(event) {
    let leer_img = new FileReader();
    let id_img = document.getElementById("imagen_principal_modificar");
    //Onload es un método que al cargar las imagenes seleccionadas procede a realizar una accion
    leer_img.onload = () => {
        if (leer_img.readyState == 2) {
            id_img.src = leer_img.result
        }
    }
    leer_img.readAsDataURL(event.target.files[0])
}

//funcion que muestra los valores a eliminar de un producto
function openDelete(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario de eliminar registro.
    M.Modal.getInstance(document.getElementById('eliminar_modal_producto')).open();
    document.getElementById('ide').value = '';
    document.getElementById('ide').value = id;
    const data = new FormData();
    data.append('ide', id);
    fetch(API_PRODUCTOS + "obtenerUnProducto", {
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
                    document.getElementById('ide').value = response.dataset.idproducto;
                    document.getElementById('nombreD').value = response.dataset.nombre_producto;
                    document.getElementById('descripcionD').value = response.dataset.descripcion;
                    document.getElementById('materialD').value = response.dataset.material;
                    document.getElementById('tamañoD').value = response.dataset.tamanio;
                    document.getElementById('existenciasD').value = response.dataset.existencias;
                    document.getElementById('descuentoD').value = response.dataset.porcentaje_descuento;
                    document.getElementById('precioD').value = response.dataset.precio_producto;
                    fillSelect(ENDPOINT_COLOR, 'un color', 'colorD', response.dataset.idcolor);
                    fillSelect(ENDPOINT_MARCA, 'una marca', 'marcaD', response.dataset.id_marca);
                    fillSelect(ENDPOINT_PROVEEDOR, 'un distribuidor', 'distribuidorD', response.dataset.iddistribuidor);
                    fillSelect(ENDPOINT_ESTADO, 'un estado', 'estadoD', response.dataset.idestado_producto);
                    fillSelect(ENDPOINT_SUBC, 'una subcategoría', 'subcD', response.dataset.idsubcategoria_producto);
                    document.getElementById('nombrearchivoD').value = response.dataset.imagen_principal;
                    imagen_principal = document.getElementById("imagen_principal_eliminar");
                    imagen_principal.src = `${SERVER}images/productos/${response.dataset.imagen_principal}`;
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

//Función para eliminar el producto (Solo actualiza su estado, no lo elimina de forma permanente)
document.getElementById('eliminar_forms').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    var valor = document.getElementById('ide').value;
    const DATA = new FormData();
    DATA.append('ide', valor);
    // Se llama a la función para guardar el registro.
    confirmDelete(API_PRODUCTOS, DATA, 'eliminar_modal_producto');
});


// Función que muestra las reseñas del producto seleccionado.
function openResenias(id) {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById("reseniasModal")).open();
    document.getElementById('idproducto').value = '';
    document.getElementById('idproducto').value = id;
    const DATA = new FormData();
    DATA.append('idproducto', id);
    fetch(API_PRODUCTOS + "obtenerReseniasProducto", {
        method: 'post',
        body: DATA
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    let content = '';
                    let estrellas_user = '';
                    //Cada usuario coloco una valoración del 1 - 5 y por cada valor se le representará por una estrella
                    response.dataset.map(function (row) {
                        //Evaluamos el caso de la valoración (1-5), y se le anexa x cantidad de imagenesde pendiendo su valoración
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
                        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
                        content += `
                        <tr>
                            <td>${row.nombre_cliente + " " + row.apellido_cliente}</td>
                            <td>${row.reseña}</td>
                            <td>${row.fecha_publicacion}</td>
                            <td>${row.estado_valoracion}</td>
                            <td>
                                <div id="estrellas">
                                    ${estrellas_user}
                                </div>
                            </td>
                            <td>
                                <div id="estado_reseña">
                                    <a class='dropdown-trigger btn' href='#' data-target='estado_resenia'>Modificar Estado<img src="../../recursos/iconografia/editar.png" alt=""></a>
                                    <!-- Dropdown Estado Reseña -->
                                    <ul id='estado_resenia' class='dropdown-content'>
                                        <li><a onclick="actualizarResenia(${row.idvaloracion}, 1)">Visible <img src="../../recursos/iconografia/mostrar2.png" alt=""></a></li>
                                        <li><a onclick="actualizarResenia(${row.idvaloracion}, 2)">Oculto <img src="../../recursos/iconografia/ocultar2.png" alt=""></a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        `;
                    });
                    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
                    document.getElementById('tbresenias').innerHTML = content;
                    //Instanciar ToolTips footer
                    M.Tooltip.init(document.querySelectorAll(".tooltipped"));
                    //Instanciar Dropdown Menú
                    var elems = document.querySelectorAll('.dropdown-trigger');
                    M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

function actualizarResenia(idvaloracion, estado) {
    const DATA = new FormData();
    DATA.append('idvaloracion', idvaloracion);
    DATA.append('estado_valoracion', estado);
    fetch(API_PRODUCTOS + "actualizarValoracion", {
        method: 'post',
        body: DATA
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    sweetAlert(1, response.message, null);
                    fetch(API_PRODUCTOS + "obtenerReseniasProducto", {
                        method: 'post',
                        body: new FormData(document.getElementById('resenia_forms'))
                    }).then(function (request) {
                        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                        if (request.ok) {
                            // Se obtiene la respuesta en formato JSON.
                            request.json().then(function (response) {
                                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                                if (response.estado) {
                                    let content = '';
                                    let estrellas_user = '';
                                    //Cada usuario coloco una valoración del 1 - 5 y por cada valor se le representará por una estrella
                                    response.dataset.map(function (row) {
                                        //Evaluamos el caso de la valoración (1-5), y se le anexa x cantidad de imagenesde pendiendo su valoración
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
                                        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
                                        content += `
                                        <tr>
                                            <td>${row.nombre_cliente + " " + row.apellido_cliente}</td>
                                            <td>${row.reseña}</td>
                                            <td>${row.fecha_publicacion}</td>
                                            <td>${row.estado_valoracion}</td>
                                            <td>
                                                <div id="estrellas">
                                                    ${estrellas_user}
                                                </div>
                                            </td>
                                            <td>
                                                <div id="estado_reseña">
                                                    <a class='dropdown-trigger btn' href='#' data-target='estado_resenia'>Modificar Estado<img src="../../recursos/iconografia/editar.png" alt=""></a>
                                                    <!-- Dropdown Estado Reseña -->
                                                    <ul id='estado_resenia' class='dropdown-content'>
                                                        <li><a onclick="actualizarResenia(${row.idvaloracion}, 1)">Visible <img src="../../recursos/iconografia/mostrar2.png" alt=""></a></li>
                                                        <li><a onclick="actualizarResenia(${row.idvaloracion}, 2)">Invisible <img src="../../recursos/iconografia/ocultar2.png" alt=""></a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        `;
                                    });
                                    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
                                    document.getElementById('tbresenias').innerHTML = content;
                                    //Instanciar ToolTips footer
                                    M.Tooltip.init(document.querySelectorAll(".tooltipped"));
                                    //Instanciar Dropdown Menú
                                    var elems = document.querySelectorAll('.dropdown-trigger');
                                    M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                                } else {
                                    sweetAlert(2, response.exception, null);
                                }
                            });
                        } else {
                            console.log(request.estado + ' ' + request.statusText);
                        }
                    });
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar.
document.getElementById('thesearch').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
    fetch(API_PRODUCTOS + 'buscarProducto', {
        method: 'post',
        body: new FormData(document.getElementById('thesearch'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se envían los datos a la función del controlador para que llene la tabla en la vista y se muestra un mensaje de éxito.
                    sweetAlert(1, response.message, null);
                    let content = '';
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se crean y concatenan las tarjetas con los datos de cada producto.
                        content += `
                        <tr>
                            <td><img src="${SERVER}images/productos/${row.imagen_principal}"></td>
                            <td>${row.nombre_producto}</td>
                            <td>${row.estado_producto}</td>
                            <td>${row.subcategoria_producto}</td>
                            <td>${row.precio_producto}</td>
                            <td>
                                <div id="acciones">
                                    <a onclick="openUpdate(${row.idproducto})" class="tooltipped" data-tooltip="Actualizar">
                                    <img src="../../recursos/iconografia/editar.png" alt="editar">
                                    </a>
                                    <a onclick="openDelete(${row.idproducto})" class="tooltipped" data-tooltip="Eliminar">
                                    <img src="../../recursos/iconografia/eliminar.png" alt="eliminar"></a>
                                    </a>
                                    <a onclick="openResenias(${row.idproducto})" class="tooltipped" data-tooltip="Reseñas">
                                    <img src="../../recursos/iconografia/reseña.png" alt="reseña"></a>
                                </div>
                            </td>
                        </tr>
                        `;
                    });
                    // Se agregan las tarjetas a la etiqueta div mediante su id para mostrar los productos.
                    document.getElementById('tbproducto').innerHTML = content;
                    //Instanciar ToolTips footer
                    M.Tooltip.init(document.querySelectorAll(".tooltipped"));
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
});