// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_LOGIN = SERVER + "sitio_publico/api_login.php?action=";

//Evento que se ejecuta cuando se carga la página web
document.addEventListener("DOMContentLoaded", function () {
    sweetAlert(6, "Bienvenido a Le Caméléon, nos ajustamos a tus gustos", null);
});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de iniciar sesión.
document.getElementById("inicio_sesion_form").addEventListener("submit", function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Petición para revisar si el administrador se encuentra registrado.
    fetch(API_LOGIN + "logIn", {
        method: "post",
        body: new FormData(document.getElementById("inicio_sesion_form"))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    //Si las credenciales son las correctas mostrará un mensaje de credenciales correctas y nos redirecciona al main
                    sweetAlert(1, response.message, "dashboard.html");
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