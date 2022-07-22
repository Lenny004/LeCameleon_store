const ENDPOINT_CATEGORIA = SERVER + "sitio_privado/api_categorias.php?action=readAll";
const ENDPOINT_SUBC = SERVER + 'sitio_privado/api_subcategoria.php?action=readAll';
const ENDPOINT_MARCA = SERVER + 'sitio_privado/api_marca.php?action=readAll';
const ENDPOINT_PROVEEDOR = SERVER + 'sitio_privado/api_proveedor.php?action=readAll';
const ENDPOINT_EMPLEADOS = SERVER + 'sitio_privado/api_empleados.php?action=readAll';

//Evento que se ejecuta cuando se carga la página web
document.addEventListener('DOMContentLoaded', function () {
    let menu = document.getElementById("menu");
    window.onscroll = function () {
        if (window.pageYOffset >= 80) {
            menu.classList.add("sticky");
        }
        else {
            menu.classList.remove("sticky");
        }
    }
    //Instanciar Select
    M.FormSelect.init(document.querySelectorAll('select'));

    let options = {
        dismissible: false,
        onOpenStart: function () {
            // Se restauran los elementos del formulario.
            document.getElementById('form_personalizado1').reset();
        }
    }
    // Se inicializa el componente Modal para que funcionen las cajas de diálogo.
    M.Modal.init(document.querySelectorAll('.modal'), options);

    //Instanciar Datepicker
    M.Datepicker.init(document.querySelectorAll('.datepicker'), {
        format: 'yyyy-mm-dd', i18n: {
            months: ['Enero', 'Febrero', 'Marzo', 'April', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthsShort: ['En', 'Febr', 'Mzo', 'Abr', 'My', 'Jun', 'Jul', 'Ag', 'Sept', 'Oct', 'Nov', 'Dic'],
            weekdaysShort: ['Dom', 'Lun', 'Mar', 'Miérc', 'Juev', 'Vier', 'Sáb'],
            weekdaysAbbrev: ['D', 'L', 'M', 'X', 'J', 'V', 'S']
        }
    });
});

// Función para preparar el formulario al momento de generar un reporte
function openGenerar() {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('modal_personalizado1')).open();
}

// Función para preparar el formulario al momento de generar un reporte
function openGenerar2() {
    //Se vacian los campos
    document.getElementById('fecha_inicio').value = " ";
    document.getElementById('fecha_fin').value = " ";
    M.updateTextFields();
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('modal_personalizado2')).open();
}

// Función para preparar el formulario al momento de generar un reporte
function openGenerar3() {
    // Se abre la caja de diálogo (modal) que contiene el formulario.
    M.Modal.getInstance(document.getElementById('modal_personalizado3')).open();
    // Se asigna el título para la caja de diálogo (modal).
}

function seleccionFiltro(opcion) {
    switch (opcion) {
        case "1":
            //Traemos todas las categorias existentes
            fillSelect(ENDPOINT_CATEGORIA, 'una categoría', 'opciones', null);
            break;
        case "2":
            //Traemos todas las subcategorias existentes
            fillSelect(ENDPOINT_SUBC, 'una subcategoría', 'opciones', null);
            break;
        case "3":
            //Traemos todas las marcas existentes
            fillSelect(ENDPOINT_MARCA, 'una marca', 'opciones', null);
            break;
        case "4":
            //Traemos todos los proveedores existentes
            fillSelect(ENDPOINT_PROVEEDOR, 'un proveedor', 'opciones', null);
            break;
        case "5":
            //Traemos todos los empleados existentes
            fillSelect(ENDPOINT_EMPLEADOS, 'un empleado', 'opciones', null);
        default:
            console.log("no entre a ningún caso");
            break;
    }
}