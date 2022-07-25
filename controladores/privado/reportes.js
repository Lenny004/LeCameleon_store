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


//funcion onchange para seleccionar el tiempo o extraer el tiempo de los select
function seleccionTiempo(event) {
    event.preventDefault();
    //Se crea una variable para guardar el el valor del select
    var filtro = document.getElementById("filtro").value;
    switch (filtro) {
        //Caso para dia
        case "1":
            console.log('Escogiste dia');
            // Se establece la ruta del reporte en el servidor.
            var url = SERVER + 'reports/sitio_privado/ventas_dias.php';
            // Se abre el reporte en una nueva pestaña del navegador web.
            window.open(url);
            break;
        //Caso para mes
        case "2":
            console.log('Escogiste mes');
            // Se establece la ruta del reporte en el servidor.
            var url = SERVER + 'reports/sitio_privado/ventas_mes.php';
            // Se abre el reporte en una nueva pestaña del navegador web.
            window.open(url);
            break;
        //Caso para año
        case "3":
            console.log('Escogiste año');
            // Se establece la ruta del reporte en el servidor.
            var url = SERVER + 'reports/sitio_privado/ventas_anio.php';
            // Se abre el reporte en una nueva pestaña del navegador web.
            window.open(url);
            break;
    }
}

// Función para abrir el reporte de productos con descuentos.
function openReportDescuento() {
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/sitio_privado/producto_descuento.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    window.open(url);
}

// Función para abrir el reporte de productos con descuentos.
function openReportDescuentoPrueba() {
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/sitio_privado/prueba.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    window.open(url);
}

// Función para abrir el reporte de productos mas vendidos.
function openReportVendidos() {
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/sitio_privado/producto_vendido.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    window.open(url);
}