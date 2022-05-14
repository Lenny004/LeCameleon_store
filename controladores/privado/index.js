// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + 'dashboard/usuarios.php?action=';


//Evento que se ejecuta cuando se carga la página web
document.addEventListener('DOMContentLoaded', function() {
    let menu = document.getElementById("menu");
    window.onscroll = function () {
        if (window.pageYOffset >= 100) {
            menu.classList.add("sticky");
        }
        else {
            menu.classList.remove("sticky");
        }
    }

    validarExistenciaPrimerUsuario(API_USUARIOS, 'verificarPrimerUso');
    //Var se crea para variables globales
    //let es variables locales
});
