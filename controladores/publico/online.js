// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_LOGIN_CLIENTE = SERVER + "sitio_publico/api_login.php?action=";

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Petición para obtener en nombre del usuario que ha iniciado sesión.
    fetch(API_LOGIN_CLIENTE + 'obtenerUsuario', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                //Se crea la variable de Header que será llamada en varias páginas
                let Header = '';
                mostrarCategorias();
                // Se revisa si el usuario está autenticado, de lo contrario se envía a iniciar sesión.
                if (response.session) {
                    //Si el usuario tiene productos en el carrito, le contara cuantos productos diferentes tiene (No cuenta la cantidad de productos de cada uno)
                    Header += `
                    <!--Anuncios-->
                    <div id="barra_anuncio" class="z-depth-1">
                        <a href="#" class="mensaje_arriba">
                            <img src="../../recursos/iconografia/regalo100px.png" alt="regalo" height="22">
                            <p>Esta semana Descuento en la Decoracion de oficina</p>
                        </a>
                    </div>
                    <!--Logo, buscador, idioma, login y carrito de compras-->
                    <div id="apartado_buscador">
                    <!--Logo-->
                    <div id="logo_imagen">
                        <a href="dashboard.html"><a href="dashboard.html"><img src="../../recursos/img/logo2.png" alt="logo"></a></a>
                    </div>
                    <!--Categoria de busqueda-->
                    <div id="boton_categoria" class="hide-on-med-and-down">
                        <div class="input-field grey lighten-2 z-depth-2">
                            <select>
                                <option value="" disabled selected>Todo</option>
                                <option value="1">Decoración</option>
                                <option value="2">Accesorios</option>
                                <option value="3">Artesanales</option>
                                <option value="4">Marcas</option>
                                <option value="5">Color</option>
                            </select>
                        </div>
                    </div>
                    <!-- Buscador -->
                    <div id="buscador" class="hide-on-med-and-down">
                        <nav class="grey lighten-2">
                            <div class="nav-wrapper">
                                <form method="post" id="thesearch">
                                    <div class="input-field">
                                        <input id="search" name="search" type="search" required>
                                        <input id="ide" name="ide" class="hide" type="number">
                                    </div>
                                    <button href="" type="submit" onclick="buscar(event)" ><img src="../../recursos/iconografia/lupa.png" alt="lupa" height="22"></button>
                                </form>
                            </div>
                        </nav>
                    </div>
                    <!--Idioma-->
                    <div id="boton_idioma">
                        <!-- Dropdown Trigger -->
                        <a class='dropdown-trigger btn grey lighten-2' href='#' data-target='dropdown_idioma'>
                            <p>Español</p>
                            <img src="../../recursos/iconografia/el_salvador.png" alt="español" width="20" height="20">
                        </a>
                        <!-- Dropdown Structure -->
                        <ul id='dropdown_idioma' class='dropdown-content'>
                            <li class="divider" tabindex="-1"></li>
                            <li><a href="#!">English<img src="../../recursos/iconografia/inglaterra.png" alt="english" width="20" height="20"></a></li>
                            <li class="divider" tabindex="-1"></li>
                            <li><a href="#!">Français<img src="../../recursos/iconografia/francia.png" alt="frances" width="20" height="20"></a></li>
                        </ul>
                    </div>
                    <!--Login-->
                    <div id="boton_login" class="hide-on-med-and-down">
                        <a href="index.html" class="icono_perfil"><img class="tooltipped" data-position="bottom" data-tooltip="Ver Perfil" src="../../recursos/iconografia/usuario.png" alt="login"></a>
                        <i class="dropdown-trigger material-icons right" data-target="dropdown_usuario">arrow_drop_down</i>
                        <!-- Estructura del Dropdown de Usuario-->
                        <ul id="dropdown_usuario" class="dropdown-content">
                            <li><a href="perfil.html" class="teal-text text-darken-4 ver_perfil"><img src="../../recursos/iconografia/usuario.png" alt="perfil">Ver Perfil</a></li>
                            <li><a href="pedidos_cliente.html" class="teal-text text-darken-4 pedidos_icon"><img src="../../recursos/iconografia/pedidos.png" alt="perfil">Tus Pedidos</a></li>
                            <li><a href="#!" class="teal-text text-darken-4 modo_oscuro" onclick="ModoNocturno()"><img src="../../recursos/iconografia/luna_estrellas.png" alt="luna">Modo</a></li>
                            <li class="divider"></li>
                            <li><a class="teal-text text-darken-4 cerrar_sesion" onclick="cerrarSesion()"><img src="../../recursos/iconografia/logout.png" alt="logout">Cerrar Sesión</a></li>
                        </ul>
                    </div>            
                    <!--Carrito de Compras-->
                    <div id="boton_carrito">
                        <a href="carrito.html" class="carrito_compras"><img src="../../recursos/iconografia/carrito.png" class="tooltipped" data-position="bottom" data-tooltip="Carrito de Compras" alt="login" height="35"><span>${response.dataset.cantidad_producto}</span></a>
                    </div>`;
                } else {
                    Header += `
                    <!--Anuncios-->
                    <div id="barra_anuncio" class="z-depth-1">
                        <a href="#" class="mensaje_arriba">
                            <img src="../../recursos/iconografia/regalo100px.png" alt="regalo" height="22">                                    <p>Esta semana Descuento en la Decoracion de oficina</p>
                        </a>
                    </div>
                    <!--Logo, buscador, idioma, login y carrito de compras-->
                    <div id="apartado_buscador">
                        <!--Logo-->
                        <div id="logo_imagen">
                            <a href="dashboard.html"><a href="dashboard.html"><img src="../../recursos/img/logo2.png" alt="logo"></a></a>
                        </div>
                        <!--Categoria de busqueda-->
                        <div id="boton_categoria" class="hide-on-med-and-down">
                            <div class="input-field grey lighten-2 z-depth-2">
                                <select>
                                    <option value="" disabled selected>Todo</option>
                                    <option value="1">Decoración</option>
                                    <option value="2">Accesorios</option>
                                    <option value="3">Artesanales</option>
                                    <option value="4">Marcas</option>
                                    <option value="5">Color</option>
                                </select>
                            </div>
                        </div>
                        <!-- Buscador -->
                    <div id="buscador" class="hide-on-med-and-down">
                        <nav class="grey lighten-2">
                            <div class="nav-wrapper">
                                <form method="post" id="thesearch">
                                    <div class="input-field">
                                        <input id="search" name="search" type="search" required>
                                        <input id="ide" name="ide" class="hide" type="number">
                                    </div>
                                    <button href="" onclick="buscar(event)" type="submit"><img src="../../recursos/iconografia/lupa.png" alt="lupa" height="22"></button>
                                </form>
                            </div>
                        </nav>
                    </div>
                        <!--Idioma-->
                        <div id="boton_idioma">
                            <!-- Dropdown Trigger -->
                            <a class='dropdown-trigger btn grey lighten-2' href='#' data-target='dropdown_idioma'>
                                <p>Español</p>
                                <img src="../../recursos/iconografia/el_salvador.png" alt="español" width="20" height="20">
                            </a>
                            <!-- Dropdown Structure -->
                            <ul id='dropdown_idioma' class='dropdown-content'>
                                <li class="divider" tabindex="-1"></li>
                                <li><a href="#!">English<img src="../../recursos/iconografia/inglaterra.png" alt="english" width="20" height="20"></a></li>
                                <li class="divider" tabindex="-1"></li>
                                <li><a href="#!">Français<img src="../../recursos/iconografia/francia.png" alt="frances" width="20" height="20"></a></li>
                            </ul>
                        </div>
                        <!--Login-->
                        <div id="boton_login" class="hide-on-med-and-down">
                            <a href="index.html"><img class="tooltipped" data-position="bottom" data-tooltip="Iniciar Sesión" src="../../recursos/iconografia/usuario.png" alt="login"></a>
                        </div>
                        <!--Carrito de Compras-->
                        <div id="boton_carrito">
                            <a href="index.html"><img src="../../recursos/iconografia/carrito.png" class="tooltipped" data-position="bottom" data-tooltip="Login Necesario para el Carrito de Compras" alt="login" height="35"><span>0</span></a>
                        </div>
                    </div>`;
                }
                //La constante y las variables las mandamos a llamar con un Id para que se concatenen
                document.querySelector('Header').innerHTML = Header;
                //Instanciar el menú
                M.Sidenav.init(document.querySelectorAll('.sidenav'));
                //Instanciar Select
                M.FormSelect.init(document.querySelectorAll('select'));
                //Instanciar Slider del main;
                let options = { indicators: false, height: 500 };
                M.Slider.init(document.querySelectorAll('.slider'), options);
                //Instanciar ToolTips footer
                M.Tooltip.init(document.querySelectorAll('.tooltipped'));
                //Instanciar Dropdown Menú
                var elems = document.querySelectorAll('.dropdown-trigger');
                M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                M.Collapsible.init(document.querySelectorAll('.collapsible'));
                //Instanciar MaterialBox
                M.Materialbox.init(document.querySelectorAll('.materialboxed'));
                //Al cargar cambia el color de la barra de anuncios
                ColorBarra();
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
});

const ENDPOINT_CATEGORIA = SERVER + "sitio_publico/api_categorias.php?action=readAll";
const ENDPOINT_CATEGORIA2 = SERVER + "sitio_publico/api_categorias.php?action=sumaCategorias";
const ENDPOINT_CATEGORIA3 = SERVER + "sitio_publico/api_categorias.php?action=obtenerSubcategorias";

function mostrarCategorias() {
    fetch(ENDPOINT_CATEGORIA, {
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
                    // Se envían los datos a la función para llenar el menú de opciones
                    agregarCategoria(data);
                } else {
                    sweetAlert(4, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

function agregarCategoria(dataset) {
    var menu = `<li><a href="dashboard.html">Inicio</a></li>`;
    var menu_movil = `<li><a class="subheader">Opciones Principales</a></li>
    <li><a href="dashboard.html" class="waves-effect waves-red"><img src="../../recursos/iconografia/inicio.png"
                alt="inicio">Inicio</a></li>`;
    document.getElementById("opciones").innerHTML = menu;
    document.getElementById("opciones_movil").innerHTML = menu_movil;
    //Variable que almacenará el boton de categoria
    let dropdown = '';
    let dropdown_movil = '';
    //Variable para crear drowpdown desde 1
    let i = 1;
    //Por cada categoria 
    dataset.forEach(row => {
        menu = `<!-- Dropdown Trigger ${i} -->
        <li id="dropdown${i}"></li>
        <!-- Estructura del Dropdown ${i}-->
        <ul id="dropdown_${i}" class="dropdown-content">
        </ul>`;
        menu_movil = `<!-- Dropdown Trigger Mobile ${i}-->
        <li id="dropdown_mobile${i}">
        </li>
        <!-- Estructura del Dropdown mobile desplegable ${i}-->
        <ul id="dropdown_desplegable_movil${i}" class="dropdown-content">
        </ul>`;
        document.getElementById("opciones").innerHTML += menu;
        document.getElementById("opciones_movil").innerHTML += menu_movil;
        // Se define una dirección con los datos de cada categoría para mostrar sus productos en otra página web.
        url = `productosc.html?id=${row.idcategoria_producto}&nombre=${row.categoria_producto}`;
        //Se crea el dropdown
        dropdown = `<a class="dropdown-trigger" href="${url}" data-target="dropdown_${i}">${row.categoria_producto}<i class="material-icons right">arrow_drop_down</i></a>`;
        dropdown_movil = `<a class="dropdown-trigger" href="${url}" data-target="dropdown_desplegable_movil${i}">${row.categoria_producto}<i class="material-icons right">arrow_drop_down</i></a>`;
        document.getElementById(`dropdown${i}`).innerHTML = dropdown;
        document.getElementById(`dropdown_mobile${i}`).innerHTML = dropdown_movil;
        //Instanciar Dropdown Menú
        var elems = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(elems, { coverTrigger: false, hover: false });
        //Contador para crear dropdowns
        i = 1 + i;
        //Función para cargar las subcategorias en los dropdowns de las categorias
        subcategoria(row.idcategoria_producto)
    });
    menu = `<li><a href="marcas.html">Marcas</a></li>
    <li><a href="ofertas.html">Ofertas</a></li>
    <!-- Dropdown Trigger Servicio al cliente -->
    <li><a class="dropdown-trigger" href="#" data-target="dropdown_servicio">Servicio al Cliente
            <i class="material-icons right">arrow_drop_down</i></a>
    </li>
    <!-- Estructura del Dropdown del Servicio al cliente-->
    <ul id="dropdown_servicio" class="dropdown-content">
        <li><a href="contactanos.html">Contáctanos</a></li>
        <li class="divider"></li>
        <li><a href="condiciones_compra.html">Condiciones de Compra</a></li>
        <li class="divider"></li>
        <li><a href="devoluciones.html">Devoluciones y Reembolsos</a></li>
        <li class="divider"></li>
        <li><a href="politica_envio.html">Políticas y Zonas de envío</a></li>
        <li class="divider"></li>
        <li><a href="reportar_problema.html">Reportar un problema</a></li>
        <li class="divider"></li>
    </ul>`;
    menu_movil = `<li><a href="marcas.html" class="waves-effect waves-red"><img src="../../recursos/iconografia/marcas.png"
    alt="marca">Marcas</a></li>
    <li><a href="otros.html" class="waves-effect waves-red"><img src="../../recursos/iconografia/oferta.png" alt="ofertas">Ofertas</a></li>
    <!-- Dropdown Trigger Servicio al cliente -->
    <li><a class="dropdown-trigger" href="#!" data-target="dropdown_servicio_movil"><img
        src="../../recursos/iconografia/servicio.png" alt="servicio">Servicio al Cliente
    <i class="material-icons right">arrow_drop_down</i></a>
    </li>
    <!-- Estructura del Dropdown del Servicio al cliente-->
    <ul id="dropdown_servicio_movil" class="dropdown-content">
    <li><a href="contactanos.html" class="waves-effect waves-red">Contáctanos</a></li>
    <li class="divider"></li>
    <li><a href="condiciones_compra.html" class="waves-effect waves-red">Condiciones de Compra</a></li>
    <li class="divider"></li>
    <li><a href="devoluciones.html" class="waves-effect waves-red">Devoluciones y Reembolsos</a></li>
    <li class="divider"></li>
    <li><a href="politica_envio.html" class="waves-effect waves-red">Políticas y Zonas de envío</a>
    </li>
    <li class="divider"></li>
    <li><a href="reportar_problema.html" class="waves-effect waves-red">Reportar un problema</a></li>
    <li class="divider"></li>
    </ul>
    <li>
    <div class="divider"></div>
    </li>
    <li><a class="subheader">Configuración</a></li>
    <li><a href="perfil.html"><img src="../../recursos/iconografia/usuario.png" alt="perfil">Perfil</a></li>
    <li><a href="#"><img src="../../recursos/iconografia/luna_estrellas.png" alt="luna">Modo Nocturno</a></li>
    <li><a href="index.html"><img src="../../recursos/iconografia/logout.png" alt="cerrar">Cerrar Sesión</a></li>`;
    document.getElementById("opciones").innerHTML += menu;
    document.getElementById("opciones_movil").innerHTML += menu_movil;
    //Instanciar Dropdown Menú
    var elems = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(elems, { coverTrigger: false, hover: false });
}

//Variable para crear drowpdown desde 1 para subcategorias
var dropdownsub = 1;

function subcategoria(id) {
    // Se define un objeto con los datos del registro seleccionado.
    let data = new FormData();
    data.append('idcategoria_menu', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(ENDPOINT_CATEGORIA3, {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    //Por cada subcategoria
                    response.dataset2.forEach(row => {
                        // Se define una dirección con los datos de cada categoría para mostrar sus productos en otra página web.
                        url = `productoss.html?id=${row.idsubcategoria_producto}&nombre=${row.subcategoria_producto}`;
                        submenu =
                            `<li><a href="${url}">${row.subcategoria_producto}</a></li>
                        <li class="divider"></li>`
                        //Se agrega al dropdown de la subcategoria
                        document.getElementById(`dropdown_${dropdownsub}`).innerHTML += submenu;
                        document.getElementById(`dropdown_desplegable_movil${dropdownsub}`).innerHTML += submenu;
                        //Instanciar Dropdown Menú
                        var elems = document.querySelectorAll('.dropdown-trigger');
                        M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                    });
                    //Contador para crear dropdowns de las subcategorias
                    dropdownsub = 1 + dropdownsub;
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}