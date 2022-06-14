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
    //Var se crea para variables globales
    //let es variables locales
});


//Cambiar el color de la barra de anuncios
function ColorBarra(){
    barra = document.getElementById("barra_anuncio");
    barra.classList.add('servicio');
}