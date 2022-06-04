//Evento que se ejecuta cuando se carga la página web
document.addEventListener('DOMContentLoaded', function () {
    //Instanciar ToolTips footer
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));
});

var aumentar = 1;

function suma() {
    if (aumentar < 10) {
        aumentar = aumentar + 1;
        document.querySelector('.numero').innerHTML = aumentar;
    } else {

    }
};

function restar() {
    if (aumentar > 1) {
        aumentar = aumentar - 1;
        document.querySelector('.numero').innerHTML = aumentar;
    } else {

    }
};

function valoraciones(valor) {
    var image1;
    var image2;
    var image3;
    var image4;
    var image5;
    switch (valor) {
        case 1:
            image1 = document.getElementById("estrella1");
            image1.src = "../../recursos/iconografia/estrella.png";
            break;
        case 2:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella.png";
            break;
        case 3:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella.png";
            image3.src = "../../recursos/iconografia/estrella.png";
            break;
        case 4:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image4 = document.getElementById("estrella4");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella.png";
            image3.src = "../../recursos/iconografia/estrella.png";
            image4.src = "../../recursos/iconografia/estrella.png";
            break;
        case 5:
            image1 = document.getElementById("estrella1");
            image2 = document.getElementById("estrella2");
            image3 = document.getElementById("estrella3");
            image4 = document.getElementById("estrella4");
            image5 = document.getElementById("estrella5");
            image1.src = "../../recursos/iconografia/estrella.png";
            image2.src = "../../recursos/iconografia/estrella.png";
            image3.src = "../../recursos/iconografia/estrella.png";
            image4.src = "../../recursos/iconografia/estrella.png";
            image5.src = "../../recursos/iconografia/estrella.png";
            break;
    }
}
