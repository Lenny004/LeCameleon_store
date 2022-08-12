// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_LOGIN = SERVER + "sitio_publico/api_login.php?action=";

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
    obtenerDatosUsuario();
});

function obtenerDatosUsuario(){
    fetch(API_LOGIN + 'obtenerUsuario', {
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
                        //Cargamos los datos en los input de perfil
                        document.getElementById('nombre_completo_perfil').innerHTML = response.cliente;
                        document.getElementById('nombre').value = response.nombre;
                        document.getElementById('apellido').value = response.apellido;
                        document.getElementById('telefono').value = response.telefono;
                        document.getElementById('correo_cliente').value = response.correo;
                        document.getElementById('usuario').value = response.username;
                        document.getElementById('contra').value = response.contra;
                        document.getElementById('confirmar_contra').value = response.contra;
                        document.getElementById('direccion_cliente').value = response.direccion;
                        document.getElementById('dui').value = response.dui;
                        M.updateTextFields();
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
}

document.getElementById('perfil_form').addEventListener('submit', function (event) {
    event.preventDefault();
    fetch(API_LOGIN + 'actualizarPerfil', {
        method: 'post',
        body: new FormData(document.getElementById('perfil_form'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    deshabilitarEdicion();
                    sweetAlert(1, response.message, null);
                    obtenerDatosUsuario();
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
});

//Si no se ha presionado el boton de ediación
var valor = 0;

//Quitar la propiedad readonly al input de telefono para poder modificarse
function habilitarEdicion() {
    //Caso que si, se deshabilitan
    if (valor == 1){
        deshabilitarEdicion();
        valor = 0;
    }
    //Sino se activan 
    else {
        document.getElementById("imagen_edit").src = "../../recursos/gif/edit.gif";
        document.getElementById("telefono").removeAttribute("readonly");
        document.getElementById("lbtelefono").innerHTML = "<b><u>Teléfono:</u> *</b>";
        document.getElementById("lbtelefono").style.color = "#36465d";
        document.getElementById("direccion_cliente").removeAttribute("readonly");
        document.getElementById("lbdireccion").innerHTML = "<b><u>Dirección:</u> *</b>";
        document.getElementById("lbdireccion").style.color = "#36465d";
        document.getElementById("contra").removeAttribute("readonly");
        document.getElementById("contra").type = "text";
        document.getElementById("lbcontra").innerHTML = "<b><u>Contraseña:</u> *</b>";
        document.getElementById("lbcontra").style.color = "#36465d";
        document.getElementById("confirmar_contra").removeAttribute("readonly");
        document.getElementById("confirmar_contra").style.color = "#b51d26";
        document.getElementById("lbconfirmar_contra").innerHTML = "<b><u>Confirmar Contraseña:</u> *</b>";
        document.getElementById("lbconfirmar_contra").style.color = "#36465d";
        let boton = document.getElementById("boton_modificar");
        boton.removeAttribute('disabled');
        valor = 1;
    }
}

function deshabilitarEdicion(){
    document.getElementById("imagen_edit").src = "../../recursos/iconografia/editar3.png";
    document.getElementById("telefono").setAttribute("readonly", true);
    document.getElementById("lbtelefono").innerHTML = "Teléfono:";
    document.getElementById("lbtelefono").style.color = "#9E9E9E";
    document.getElementById("direccion_cliente").setAttribute("readonly", true);
    document.getElementById("lbdireccion").innerHTML = "Dirección:";
    document.getElementById("lbdireccion").style.color = "#9E9E9E";
    document.getElementById("contra").setAttribute("readonly", true);
    document.getElementById("contra").type = "password";
    document.getElementById("lbcontra").innerHTML = "Contraseña:";
    document.getElementById("lbcontra").style.color = "#9E9E9E";
    document.getElementById("confirmar_contra").setAttribute("readonly", true);
    document.getElementById("confirmar_contra").style.color = "#000000";
    document.getElementById("lbconfirmar_contra").innerHTML = "Confirmar Contraseña:";
    document.getElementById("lbconfirmar_contra").style.color = "#9E9E9E";
    let boton = document.getElementById("boton_modificar");
    boton.setAttribute('disabled', true);
}