// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + "sitio_privado/api_login.php?action=";

//Evento que se ejecuta cuando se carga la página web
document.addEventListener("DOMContentLoaded", function () {
    // Petición para consultar si existen usuarios registrados.
    fetch(API_USUARIOS + "verificarPrimerUso", {
        method: "get",
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si existe una sesión, de lo contrario se revisa si la respuesta es satisfactoria.
                if (response.session) {
                    location.href = "http://localhost/LeCameleon/vistas/privado/dashboard.html";
                } else if (response.estado) {
                    sweetAlert(4, "Debe autenticarse para ingresar", null);
                } else {
                    sweetAlert(
                        3, response.exception, "http://localhost/LeCameleon/vistas/privado/registro_usuario.html"
                    );
                }
            });
        } else {
            console.log(request.estado + " " + request.statusText);
        }
    });

    //Var se crea para variables globales
    //let es variables locales
});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de iniciar sesión.
document.getElementById("inicio_sesion_form").addEventListener("submit", function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Petición para revisar si el administrador se encuentra registrado.
    fetch(API_USUARIOS + "logIn", {
        method: "post",
        body: new FormData(document.getElementById("inicio_sesion_form"))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    //Si las credenciales son las correctas mostrará un mensaje de credenciales correctas y nos redirecciona al main
                    sweetAlert(1, response.message, "http://localhost/LeCameleon/vistas/privado/dashboard.html");
                } else {
                    //Si alguna de las credenciales es incorrecta mostrará un mensaje de error diciendo que credencial es la mala
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + " " + request.estadoText);
        }
    });
});

//Método para mostrar y ocultar la contraseña del login
function mostrarContrasena(){
    var image = document.getElementById("ocultar");
    var tipo = document.getElementById("password_login");
    if(tipo.type == "password"){
        tipo.type = "text";
        image.src = "../../recursos/iconografia/mostrar.png";
    }else{
        tipo.type = "password";
        image.src = "../../recursos/iconografia/ocultar.png";
    }
}
