/*
*   Controlador de uso general en las páginas web del sitio privado cuando se ha iniciado sesión.
*   Sirve para manejar las plantillas del encabezado y pie del documento.
*/

// Constante para establecer la ruta y parámetros de comunicación con la API.
const API = SERVER + 'sitio_privado/api_login.php?action=';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Petición para obtener en nombre del usuario que ha iniciado sesión.
    fetch(API + 'getUser', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se revisa si el usuario está autenticado, de lo contrario se envía a iniciar sesión.
                if (response.session) {
                    // Se comprueba si la respuesta es satisfactoria, de lo contrario se direcciona a la página web principal.
                    if (response.status) {
                        const header = `
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
                                    <p>${response.username}</p>
                                    <p>Administrador</p>
                                </div>
                                <div>
                                    <a href="perfil.html"><img src="../../recursos/iconografia/usuario.png" class="tooltipped" data-position="bottom" data-tooltip="Ver Perfil" alt="usuario"></a>
                                    <i class="dropdown-trigger material-icons right" data-target="dropdown_usuario">arrow_drop_down</i>
                                    <!-- Estructura del Dropdown de Usuario-->
                                    <ul id="dropdown_usuario" class="dropdown-content">
                                        <li><a class="teal-text text-darken-4">${response.username}</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#!" class="teal-text text-darken-4"><img src="../../recursos/iconografia/usuario.png" alt="perfil">Ver Perfil</a></li>
                                        <li><a href="#!" class="teal-text text-darken-4"><img src="../../recursos/iconografia/luna_estrellas.png" alt="luna">Modo Nocturno</a></li>
                                        <li class="divider"></li>
                                        <li><a href="index.html" class="teal-text text-darken-4"><img src="../../recursos/iconografia/logout.png" alt="logout">Cerrar Sesión</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        `;
                        document.querySelector('header').innerHTML = header;
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
                        M.Datepicker.init(document.querySelectorAll('.datepicker'));
                        
                        //Instanciar Modal
                        modal = document.querySelectorAll('.modal');
                        M.Modal.init(modal);

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
            console.log(request.status + ' ' + request.statusText);
        }
    });
});