// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + "sitio_privado/api_login.php?action=";

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Petición para obtener en nombre del usuario que ha iniciado sesión.
    fetch(API_USUARIOS + 'obtenerUsuario', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se revisa si el usuario está autenticado, de lo contrario se envía a iniciar sesión.
                if (response.session) {
                    // Se comprueba si la respuesta es satisfactoria, de lo contrario se direcciona a la página web principal.
                    if (response.estado) {
                        //Se crea la variable de Header que será llamada en varias páginas
                        let Header = '';
                        //creamos una variable que tendra el menú
                        let menu_usuario = '';
                        //creamos una variable que tendra el menú movil
                        let menu_usuario_movil = '';
                        switch (response.nivel_usuario) {
                            case 1:
                                Header = `
                                <!--Anuncios-->
                                <div id="barra_anuncio" class="z-depth-1">
                                    <a href="#" class="mensaje_arriba">
                                    </a>
                                </div>
                                <!--Logo, buscador, idioma, login y carrito de compras-->
                                <div id="apartado_header">
                                    <!--Logo-->
                                    <div id="logo_imagen">
                                        <a href="dashboard.html"><a href="dashboard.html"><img src="../../recursos/img/logo2.png" alt="logo"></a></a>
                                    </div>
                                    <!--Usuario y sus datos-->
                                    <!--Ocultamos estos elementos para moviles y tablets-->
                                    <div class="hide-on-med-and-down" id="usuario">
                                        <div>
                                            <p>${response.empleado}</p>
                                            <p>${response.tipo_usuario}</p>
                                        </div>
                                        <div>
                                            <a href="perfil.html" class="icono_perfil"><img src="../../recursos/iconografia/usuario.png" class="tooltipped" data-position="bottom" data-tooltip="Ver Perfil" alt="usuario"></a>
                                            <i class="dropdown-trigger material-icons right" data-target="dropdown_usuario">arrow_drop_down</i>
                                            <!-- Estructura del Dropdown de Usuario-->
                                            <ul id="dropdown_usuario" class="dropdown-content">
                                                <li><a class="teal-text text-darken-4">${response.empleado}</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#!" class="teal-text text-darken-4 ver_perfil"><img src="../../recursos/iconografia/usuario.png" alt="perfil">Ver Perfil</a></li>
                                                <li><a href="#!" class="teal-text text-darken-4 modo_oscuro" onclick="ModoNocturno()"><img src="../../recursos/iconografia/luna_estrellas.png" alt="luna">Modo</a></li>
                                                <li class="divider"></li>
                                                <li><a class="teal-text text-darken-4 cerrar_sesion" onclick="logOut()"><img src="../../recursos/iconografia/logout.png" alt="logout">Cerrar Sesión</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                `;
                                menu_usuario += `
                                <nav class="nav-extended white">
                                    <div class="nav-wrapper">
                                        <a href="#" data-target="mobile" class="sidenav-trigger" id="menu_desplegable"><i
                                                class="material-icons">menu</i></a>
                                        <!--Menú con opciones que será invisible cuando este en tamaños de móviles y tablets-->
                                        <ul class="left hide-on-med-and-down">
                                            <li><a href="dashboard.html">Inicio</a></li>
                                            <!-- Dropdown Trigger Estadísticas -->
                                            <li><a class="dropdown-trigger" href="estadisticas.html" data-target="dropdown_graficas">Estadísticas
                                                    <i class="material-icons right">arrow_drop_down</i></a>
                                            </li>
                                            <!-- Estructura del Dropdown de Estadísticas-->
                                            <ul id="dropdown_graficas" class="dropdown-content">
                                                <li><a href="graficas_ventas.html">Gráficas de Ventas</a></li>
                                                <li class="divider"></li>
                                                <li><a href="graficas_empleados.html">Gráficas de Empleados</a></li>
                                                <li class="divider"></li>
                                                <li><a href="reportes.html">Reportes</a></li>
                                                <li class="divider"></li>
                                            </ul>
                                            <!-- Dropdown Trigger Inventario -->
                                            <li><a class="dropdown-trigger" href="inventario.html" data-target="dropdown_inventarios">Inventario
                                                    <i class="material-icons right">arrow_drop_down</i></a>
                                            </li>
                                            <!-- Estructura del Dropdown del Inventario-->
                                            <ul id="dropdown_inventarios" class="dropdown-content">
                                                <li><a href="inventario_existencias.html">Visualizar Existencias</a></li>
                                                <li class="divider"></li>
                                                <li><a href="inventario_administrar.html">Administrar Productos</a></li>
                                                <li class="divider"></li>
                                                <li><a href="inventario_entregas.html">Registrar Entrega</a></li>
                                                <li class="divider"></li>
                                            </ul>
                                            <!-- Dropdown Trigger Categorias -->
                                            <li><a class="dropdown-trigger" href="categoria_privado.html" data-target="dropdown_categorias">Categorias
                                                    <i class="material-icons right">arrow_drop_down</i></a>
                                            </li>
                                            <!-- Estructura del Dropdown del Categorias-->
                                            <ul id="dropdown_categorias" class="dropdown-content">
                                                <li><a href="categorias_principales.html">Categorias</a></li>
                                                <li class="divider"></li>
                                                <li><a href="subcategorias.html">Sub-Categorias</a></li>
                                                <li class="divider"></li>
                                            </ul>
                                            <!-- Dropdown Trigger Empleados y Usuarios -->
                                            <li><a class="dropdown-trigger" href="empleado_usuario.html" data-target="dropdown_empleados">Empleados y Usuarios
                                                    <i class="material-icons right">arrow_drop_down</i></a>
                                            </li>
                                            <!-- Estructura del Dropdown de Empleados y Usuarios-->
                                            <ul id="dropdown_empleados" class="dropdown-content">
                                                <li><a href="administrar_empleados.html">Administrar Empleados</a></li>
                                                <li class="divider"></li>
                                                <li><a href="administrar_usuarios.html">Administrar Usuarios Empleados</a></li>
                                                <li class="divider"></li>
                                                <li><a href="administrar_usuarios_clientes.html">Administrar Usuarios Clientes</a></li>
                                                <li class="divider"></li>
                                            </ul>
                                            <li><a href="administrar_marca.html">Marcas</a></li>
                                            <li><a href="proveedores.html">Proveedores</a></li>
                                            <li><a href="monitorear_pedidos.html">Monitorear Pedidos</a></li>
                                        </ul>
                                    </div>
                                </nav>
                                `;
                                menu_usuario_movil += `
                                <li>
                                    <div class="user-view">
                                        <div class="background">
                                            <img src="../../recursos/img/main_privado/nav2.png">
                                        </div>
                                        <a class="ver_perfil"><img class="circle" src="../../recursos/iconografia/usuario.png">${response.empleado}<br>${response.tipo_usuario}</a>
                                    </div>
                                </li>
                                <div id="opciones_movil">
                                    <li><a class="subheader">Opciones Principales</a></li>
                                    <li><a href="dashboard.html" class="inicio_icon"><img src="../../recursos/iconografia/inicio.png" alt="inicio">Inicio</a></li>
                                    <!-- Dropdown Trigger Estadísticas -->
                                    <li><a class="dropdown-trigger graficas_icon" href="estadisticas.html" data-target="dropdown_decoracion"><img
                                                src="../../recursos/iconografia/graficas.png" alt="graficas">Estadísticas
                                            <i class="material-icons right">arrow_drop_down</i></a>
                                    </li>
                                    <!-- Estructura del Dropdown de Estadísticas-->
                                    <ul id="dropdown_decoracion" class="dropdown-content">
                                        <li><a href="graficas_ventas.html">Gráficas de Ventas</a></li>
                                        <li class="divider"></li>
                                        <li><a href="graficas_empleados.html">Gráficas de Empleados</a></li>
                                        <li class="divider"></li>
                                        <li><a href="reportes.html">Reportes</a></li>
                                        <li class="divider"></li>
                                    </ul>
                                    <!-- Dropdown Trigger Inventario -->
                                    <li><a class="dropdown-trigger inventario_icon" href="inventario.html" data-target="dropdown_inventario"><img
                                                src="../../recursos/iconografia/inventario.png" alt="inventario">Inventario
                                            <i class="material-icons right">arrow_drop_down</i></a>
                                    </li>
                                    <!-- Estructura del Dropdown del Inventario-->
                                    <ul id="dropdown_inventario" class="dropdown-content">
                                        <li><a href="inventario_existencias.html">Visualizar Existencias</a></li>
                                        <li class="divider"></li>
                                        <li><a href="inventario_administrar.html">Administrar Productos</a></li>
                                        <li class="divider"></li>
                                        <li><a href="inventario_entregas.html">Registrar Entrega</a></li>
                                        <li class="divider"></li>
                                    </ul>
                                    <!-- Dropdown Trigger Categoria -->
                                    <li><a class="dropdown-trigger categoria_icon" href="#!" data-target="dropdown_accesorios"><img
                                                src="../../recursos/iconografia/categorias.png" alt="categorias">Categorias
                                            <i class="material-icons right">arrow_drop_down</i></a>
                                    </li>
                                    <!-- Estructura del Dropdown de Categoria-->
                                    <ul id="dropdown_accesorios" class="dropdown-content">
                                        <li><a href="#!">Categorias</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#!">Sub-Categorias</a></li>
                                        <li class="divider"></li>
                                    </ul>
                                    <li><a class="dropdown-trigger empleado_icon" href="empleado_usuario.html" data-target="dropdown_artesanales"><img
                                                src="../../recursos/iconografia/empleados_usuarios.png" alt="empleados">Empleados y
                                            Usuarios <i class="material-icons right">arrow_drop_down</i></a>
                                    </li>
                                    <!-- Estructura del Dropdown de Empleados y Usuarios-->
                                    <ul id="dropdown_artesanales" class="dropdown-content">
                                        <li><a href="administrar_empleados.html">Administrar Empleados</a></li>
                                        <li class="divider"></li>
                                        <li><a href="administrar_usuarios.html">Administrar Usuarios</a></li>
                                        <li class="divider"></li>
                                    </ul>
                                    <li><a href="administrar_marca.html" class="marca_icon"><img src="../../recursos/iconografia/marcas.png" alt="marca">Marcas</a></li>
                                    <li><a href="proveedores.html" class="distribuidor_icon"><img src="../../recursos/iconografia/distribuidor.png"
                                                alt="distribuidor">Proveedores</a></li>
                                    <li><a href="monitorear_pedidos.html" class="pedidos_icon"><img src="../../recursos/iconografia/auto_pedidos.png"
                                                alt="pedidos">Monitorear Pedidos</a></li>
                                    <li>
                                        <div class="divider"></div>
                                    </li>
                                    <li><a class="subheader">Configuración</a></li>
                                    <li><a href="perfil.html" class="ver_perfil"><img src="../../recursos/iconografia/usuario.png" alt="perfil">Perfil</a></li>
                                    <li><a href="#" onclick="ModoNocturno()" class="modo_oscuro"><img src="../../recursos/iconografia/luna_estrellas.png" alt="luna">Modo</a></li>
                                    <li><a href="#" onclick="logOut()" class="cerrar_sesion"><img src="../../recursos/iconografia/logout.png" alt="cerrar">Cerrar
                                            Sesión</a></li>
                                </div>
                                `;
                                break;
                            case 2:
                                Header = `
                                <!--Anuncios-->
                                <div id="barra_anuncio" class="z-depth-1">
                                    <a href="#" class="mensaje_arriba">
                                    </a>
                                </div>
                                <!--Logo, buscador, idioma, login y carrito de compras-->
                                <div id="apartado_header">
                                    <!--Logo-->
                                    <div id="logo_imagen">
                                        <a href="dashboard.html"><a href="dashboard.html"><img src="../../recursos/img/logo2.png" alt="logo"></a></a>
                                    </div>
                                    <!--Usuario y sus datos-->
                                    <!--Ocultamos estos elementos para moviles y tablets-->
                                    <div class="hide-on-med-and-down" id="usuario">
                                        <div>
                                            <p>${response.empleado}</p>
                                            <p>${response.tipo_usuario}</p>
                                        </div>
                                        <div>
                                            <a href="perfil.html" class="icono_perfil"><img src="../../recursos/iconografia/usuario.png" class="tooltipped" data-position="bottom" data-tooltip="Ver Perfil" alt="usuario"></a>
                                            <i class="dropdown-trigger material-icons right" data-target="dropdown_usuario">arrow_drop_down</i>
                                            <!-- Estructura del Dropdown de Usuario-->
                                            <ul id="dropdown_usuario" class="dropdown-content">
                                                <li><a class="teal-text text-darken-4">${response.empleado}</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#!" class="teal-text text-darken-4 ver_perfil"><img src="../../recursos/iconografia/usuario.png" alt="perfil">Ver Perfil</a></li>
                                                <li><a href="#!" class="teal-text text-darken-4 modo_oscuro" onclick="ModoNocturno()"><img src="../../recursos/iconografia/luna_estrellas.png" alt="luna">Modo</a></li>
                                                <li class="divider"></li>
                                                <li><a class="teal-text text-darken-4 cerrar_sesion" onclick="logOut()"><img src="../../recursos/iconografia/logout.png" alt="logout">Cerrar Sesión</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                `;
                                menu_usuario += `
                                <nav class="nav-extended white">
                                    <div class="nav-wrapper">
                                        <a href="#" data-target="mobile" class="sidenav-trigger" id="menu_desplegable"><i
                                                class="material-icons">menu</i></a>
                                        <!--Menú con opciones que será invisible cuando este en tamaños de móviles y tablets-->
                                        <ul class="left hide-on-med-and-down">
                                            <li><a href="dashboard.html">Inicio</a></li>
                                            <li><a href="monitorear_pedidos.html">Monitorear Pedidos</a></li>
                                        </ul>
                                    </div>
                                </nav>
                                `;
                                menu_usuario_movil += `
                                <li>
                                    <div class="user-view">
                                        <div class="background">
                                            <img src="../../recursos/img/main_privado/nav2.png">
                                        </div>
                                        <a class="ver_perfil"><img class="circle" src="../../recursos/iconografia/usuario.png">${response.empleado}<br>${response.tipo_usuario}</a>
                                    </div>
                                </li>
                                <div id="opciones_movil">
                                    <li><a class="subheader">Opciones Principales</a></li>
                                    <li><a href="dashboard.html" class="inicio_icon"><img src="../../recursos/iconografia/inicio.png" alt="inicio">Inicio</a></li>
                                    <li><a href="monitorear_pedidos.html" class="pedidos_icon"><img src="../../recursos/iconografia/auto_pedidos.png"
                                                alt="pedidos">Monitorear Pedidos</a></li>
                                    <li>
                                        <div class="divider"></div>
                                    </li>
                                    <li><a class="subheader">Configuración</a></li>
                                    <li><a href="perfil.html" class="ver_perfil"><img src="../../recursos/iconografia/usuario.png" alt="perfil">Perfil</a></li>
                                    <li><a href="#" onclick="ModoNocturno()" class="modo_oscuro"><img src="../../recursos/iconografia/luna_estrellas.png" alt="luna">Modo</a></li>
                                    <li><a href="#" onclick="logOut()" class="cerrar_sesion"><img src="../../recursos/iconografia/logout.png" alt="cerrar">Cerrar
                                            Sesión</a></li>
                                </div>
                                `;
                                break;
                            default:
                                break;
                        }
                        //La constante y las variables las mandamos a llamar con un Id para que se concatenen
                        document.querySelector('Header').innerHTML = Header;
                        document.getElementById('menu').innerHTML = menu_usuario;
                        document.getElementById('mobile').innerHTML = menu_usuario_movil;
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
                        //Instanciar Datepicker
                        //Instanciar Select 'Combobox'
                        M.FormSelect.init(document.querySelectorAll('select'));
                    } else {
                        sweetAlert(3, response.exception, 'index.html');
                    }
                } else {
                    location.href = 'index.html';
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
});