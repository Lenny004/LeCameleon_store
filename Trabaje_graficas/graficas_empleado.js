const API_PRODCUTO = SERVER + 'sitio_privado/api_producto.php?action=';
// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    let menu = document.getElementById("menu");
    graficoVendedoresvendieronmes();
    window.onscroll = function () {
        if (window.pageYOffset >= 100) {
            menu.classList.add("sticky");
        }
        else {
            menu.classList.remove("sticky");
        }
    }
    //Inicializar el componente del Tab
    M.AutoInit();
    // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
    M.Tooltip.init(document.querySelectorAll('.tooltipped'));

    //Instanciar Datepicker
	M.Datepicker.init(document.querySelectorAll('.datepicker'), {
		format: 'yyyy-mm-dd', i18n: {
			months: ['Enero', 'Febrero', 'Marzo', 'April', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			monthsShort: ['En', 'Febr', 'Mzo', 'Abr', 'My', 'Jun', 'Jul', 'Ag', 'Sept', 'Oct', 'Nov', 'Dic'],
			weekdaysShort: ['Dom', 'Lun', 'Mar', 'Miérc', 'Juev', 'Vier', 'Sáb'],
			weekdaysAbbrev: ['D', 'L', 'M', 'X', 'J', 'V', 'S']
	}});
});

// Función para mostrar el porcentaje de vendedores que mas vendieron por mes mediante grafico de polarArea
function graficoVendedoresvendieronmes() {
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODCUTO + 'porcentajeVendedoresxMes', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a gráficar.
                    let nombres = [];
                    let porcentajes = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        nombres.push(row.usuario_e);
                        porcentajes.push(row.porcentaje);
                    });
                    // Se llama a la función que genera y muestra un gráfico de pastel. Se encuentra en el archivo components.js
                    PolarArea('chart1', nombres, porcentajes, 'Vendendores que más vendieron en este mes');
                } else {
                    document.getElementById('chart1').remove();
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}
