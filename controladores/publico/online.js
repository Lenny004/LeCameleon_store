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
                                <option value="4">Marcas</option>                                    <option value="5">Color</option>
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
                                    </div>
                                    <button href=""><img src="../../recursos/iconografia/lupa.png" alt="lupa" height="22"></button>
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
                        <a href="carrito.html" class="carrito_compras"><img src="../../recursos/iconografia/carrito.png" class="tooltipped" data-position="bottom" data-tooltip="Carrito de Compras" alt="login" height="35"><span>1</span></a>
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
                                    <form>
                                        <div class="input-field">
                                            <input id="search" type="search" required>
                                        </div>
                                    </form>
                                </div>
                            </nav>
                            <a href=""><img src="../../recursos/iconografia/lupa.png" alt="lupa" height="22"></a>
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
                            <a href=""><img src="../../recursos/iconografia/carrito.png" class="tooltipped" data-position="bottom" data-tooltip="Login Necesario para el Carrito de Compras" alt="login" height="35"><span>1</span></a>
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
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
});

const ENDPOINT_CATEGORIA = SERVER + "sitio_publico/api_categorias.php?action=readAll";
const ENDPOINT_CATEGORIA2 = SERVER + "sitio_publico/api_categorias.php?action=sumaCategorias";

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
                    // Se envían los datos a la función del controlador para llenar la tabla en la vista.
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
    fetch(ENDPOINT_CATEGORIA2, {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let total = '';
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    total = response.suma_categorias;
                    console.log(total);
                    dropdown1 = '';
                    dropdown2 = '';
                    dropdown3 = '';
                    suma = 1;
                    if (total > 0) {
                        dataset.map(function (row) {
                            if (suma == 1 && total == 1) {
                                dropdown1 +=
                                    `<a class="dropdown-trigger" href="#" data-target="dropdown_1">${row.categoria_producto}<i class="material-icons right">arrow_drop_down</i></a>`;
                                document.getElementById('dropdown1').innerHTML = dropdown1;
                                //Instanciar Dropdown Menú
                                var elems = document.querySelectorAll('.dropdown-trigger');
                                M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                            }
                            else if (suma == 1 && total == 2) {
                                dropdown1 +=
                                    `<a class="dropdown-trigger" href="#" data-target="dropdown_1">${row.categoria_producto}<i class="material-icons right">arrow_drop_down</i></a>`;
                                document.getElementById('dropdown1').innerHTML = dropdown1;
                                //Instanciar Dropdown Menú
                                var elems = document.querySelectorAll('.dropdown-trigger');
                                M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                                suma = suma + 1;
                            }
                            else if (suma == 1 && total == 3) {
                                dropdown1 +=
                                    `<a class="dropdown-trigger" href="#" data-target="dropdown_1">${row.categoria_producto} <i class="material-icons right">arrow_drop_down</i></a>`;
                                document.getElementById('dropdown1').innerHTML = dropdown1;
                                //Instanciar Dropdown Menú
                                var elems = document.querySelectorAll('.dropdown-trigger');
                                M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                                suma = suma + 1;
                            }
                            else if (suma == 2 && total == 2) {
                                dropdown2 +=
                                    `<a class="dropdown-trigger" href="#" data-target="dropdown_2">${row.categoria_producto} <i class="material-icons right">arrow_drop_down</i></a>`;
                                document.getElementById('dropdown2').innerHTML = dropdown2;
                                //Instanciar Dropdown Menú
                                var elems = document.querySelectorAll('.dropdown-trigger');
                                M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                            }
                            else if (suma == 2 && total == 3) {
                                dropdown2 +=
                                    `<a class="dropdown-trigger" href="#" data-target="dropdown_2">${row.categoria_producto} <i class="material-icons right">arrow_drop_down</i></a>`;
                                document.getElementById('dropdown2').innerHTML = dropdown2;
                                //Instanciar Dropdown Menú
                                var elems = document.querySelectorAll('.dropdown-trigger');
                                M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                                suma = suma + 1;
                            }
                            else if (suma == 3 && total == 3) {
                                dropdown3 +=
                                    `<a class="dropdown-trigger" href="#" data-target="dropdown_3">${row.categoria_producto}<i class="material-icons right">arrow_drop_down</i></a>`;
                                document.getElementById('dropdown3').innerHTML = dropdown3;
                                //Instanciar Dropdown Menú
                                var elems = document.querySelectorAll('.dropdown-trigger');
                                M.Dropdown.init(elems, { coverTrigger: false, hover: false });
                            }
                            else {
                                sweetAlert(4, 'Necesitas crear otro dropdown', null);
                            }
                        });
                    }
                    else {
                    }
                } else {
                    sweetAlert(4, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

function subcategoria() {
    // Petición para consultar si existen pedidos con fecha de entrega actual.
    fetch(API_PEDIDOS + 'existenciaPedidosHoy', {
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
                CrearPedidoHoy(data);
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}