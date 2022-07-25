const API_PRODUCTO = SERVER + 'sitio_privado/api_producto.php?action=';

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
    //Se llama a la función para cargar las gráficas
    graficoVendedoresVendieronMes();
});


// Función para mostrar el porcentaje de vendedores que mas vendieron por mes mediante grafico de polarArea
function graficoVendedoresVendieronMes() {
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTO + 'porcentajeVendedoresxMes', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a gráficar.
                    let nombres = [];
                    let cantidades = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        nombres.push(row.usuario_e);
                        cantidades.push(row.cantidad);
                    });
                    // Se llama a la función que genera y muestra un gráfico polar. Se encuentra en el archivo components.js
                    polarGraph('grafica1', nombres, cantidades, 'Top 5 Vendedores que más vendieron en este mes');
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    barGraph('grafica2', nombres, cantidades, 'Cantidad de productos vendidos', 'Top 5 Vendedores que más vendieron en este mes');
                } else {
                    document.getElementById('grafica1').remove();
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}