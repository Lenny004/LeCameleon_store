// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_REGISTRAR = SERVER + 'sitio_privado/api_registro_usuario_empleado.php?action=';

//Evento que se ejecuta cuando se carga la página web
document.addEventListener("DOMContentLoaded", function () {
    // Petición para consultar si existen usuarios registrados.
    fetch(API_REGISTRAR + 'verificarPrimerUso', {
        method: "get",
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si existe una sesión, de lo contrario se revisa si la respuesta es satisfactoria.
                if (response.session) {
                    location.href = 'http://localhost/LeCameleon/vistas/privado/dashboard.html';
                } else if (response.estado) {
                    sweetAlert(3, response.message, 'http://localhost/LeCameleon/vistas/privado/index.html');
                } else {
                    sweetAlert(4, 'Debe crear un usuario para comenzar', null);
                }
            });
        } else {
            console.log(request.estado + " " + request.statusText);
        }
    });
    //Var se crea para variables globales
    //let es variables locales

    //Instanciar Datepicker
    M.Datepicker.init(document.querySelectorAll('.datepicker'), {format: 'yyyy-mm-dd' , i18n:{ 
        months: ['Enero','Febrero','Marzo','April','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthsShort: ['En','Febr','Mzo','Abr','My','Jun','Jul','Ag','Sept','Oct','Nov','Dic'],
        weekdaysShort: ['Dom','Lun','Mar','Miérc','Juev','Vier','Sáb'],
        weekdaysAbbrev: ['D','L','M','X','J','V','S']
    }});
});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de registrar.
document.getElementById('register-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Petición para registrar el primer usuario del sitio privado.
    fetch(API_REGISTRAR + 'registroUsuario', {
        method: 'post',
        body: new FormData(document.getElementById('register-form'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    sweetAlert(1, response.message, 'http://localhost/LeCameleon/vistas/privado/index.html');
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.estadoText);
        }
    });
});

//Método para mostrar y ocultar la contraseña del login
function mostrarContrasena(){
    var image = document.getElementById("ocultar");
    var tipo = document.getElementById("password");
    if(tipo.type == "password"){
        tipo.type = "text";
        image.src = "../../recursos/iconografia/mostrar.png";
    }else{
        tipo.type = "password";
        image.src = "../../recursos/iconografia/ocultar.png";
    }
}

//Método para mostrar y ocultar la contraseña del login
function mostrarContrasena2(){
    var image = document.getElementById("ocultar2");
    var tipo = document.getElementById("confirmar_contra");
    if(tipo.type == "password"){
        tipo.type = "text";
        image.src = "../../recursos/iconografia/mostrar.png";
    }else{
        tipo.type = "password";
        image.src = "../../recursos/iconografia/ocultar.png";
    }
}