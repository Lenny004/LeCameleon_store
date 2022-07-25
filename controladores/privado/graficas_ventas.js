const ENDPOINT_CATEGORIA = SERVER + "sitio_privado/api_categorias.php?action=readAll";
const ENDPOINT_SUBC = SERVER + 'sitio_privado/api_subcategoria.php?action=readAll';
const ENDPOINT_MARCA = SERVER + 'sitio_privado/api_marca.php?action=readAll';
const ENDPOINT_PROVEEDOR = SERVER + 'sitio_privado/api_proveedor.php?action=readAll';
const API_PRODUCTOS = SERVER + 'sitio_privado/api_producto.php?action=';

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
        }
    });

    //para cargar la gráfica de los productos con más descuento
    graficoDescuentoProducto();
    graficoInventarioProducto();
    graficoVentaProducto();
});

//Variable para saber que filtro eligio
var tipo;
var tipo_frase;
//Variable para saber que opción ha elegido
var opcion;

//Funcion para seleccionar un filtro para cargar en el select de opciones
function seleccionFiltro(opcion) {
    switch (opcion) {
        case "1":
            //Traemos todas las categorias existentes
            fillSelect(ENDPOINT_CATEGORIA, 'una categoría', 'opciones', null);
            tipo = 1;
            tipo_frase = "una categoria";
            break;
        case "2":
            //Traemos todas las subcategorias existentes
            fillSelect(ENDPOINT_SUBC, 'una subcategoría', 'opciones', null);
            tipo = 2;
            tipo_frase = "una subcategoría";
            break;
        case "3":
            //Traemos todas las marcas existentes
            fillSelect(ENDPOINT_MARCA, 'una marca', 'opciones', null);
            tipo = 3;
            tipo_frase = "una marca";
            break;
        case "4":
            //Traemos todos los proveedores existentes
            fillSelect(ENDPOINT_PROVEEDOR, 'un proveedor', 'opciones', null);
            tipo = 4;
            tipo_frase = "un proveedor";
            break;
        default:
            console.log("no entre a ningún caso");
            break;
    }
}

function seleccionOpcion(opcion_elegida) {
    opcion = opcion_elegida;
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de generar gráfica
document.getElementById('form_personalizado').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('tipo', tipo);
    data.append('opcion', opcion);
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + 'productosVendidosFiltro', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.estado) {
                    // Se declaran los arreglos para guardar los datos a graficar.
                    let cantidades = [];
                    let nombre_producto = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        cantidades.push(row.cantidad_vendida);
                        nombre_producto.push(row.nombre_producto);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    barGraph('grafica5', nombre_producto, cantidades, 'Cantidad de productos', ('Nombres de productos por ' + tipo_frase));
                    // Se llama a la función que genera y muestra un gráfico de pastel. Se encuentra en el archivo components.js
                    donutGraph('grafica6', nombre_producto, cantidades, ('Nombres de productos por ' + tipo_frase));
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
});


// Función para preparar el formulario al momento de modificar un registro.
function openGraficaVenta(event) {
    event.preventDefault();
    // Petición para obtener los datos del registro solicitado.
    fetch(API_PRODUCTOS + 'graficoInventarioRango', {
        method: 'post',
        body: new FormData(document.getElementById('MandarFechas'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    let fecha = [];
                    let cantidades = [];
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        fecha.push(row.fecha_entrega);
                        cantidades.push(row.cantidad);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    barGraph('grafica7', fecha, cantidades, 'Cantidad ingresada en la fecha', 'Cantidad de productos ingresados en inventario por rango de fechas');
                    lineGraph('grafica8', fecha, cantidades, 'Cantidad ingresada en la fecha', 'Cantidad de productos ingresados en inventario por rango de fechas');
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}


// Función para mostrar el porcentaje de productos por categoría en un gráfico de pastel.
function graficoDescuentoProducto() {
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + 'graficaProductoxdesctop', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a gráficar.
                    let producto = [];
                    let descuento = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        producto.push(row.nombre_producto);
                        descuento.push(row.porcentaje_descuento);
                    });
                    // Se llama a la función que genera y muestra un gráfico de pastel. Se encuentra en el archivo components.js
                    polarGraph('grafica1', producto, descuento, 'Productos con más porcentaje de descuento');
                    lineGraph('grafica2', producto, descuento, 'Porcentaje de descuento', 'Productos con más porcentaje de descuento');
                } else {
                    document.getElementById('grafica1').remove();
                    document.getElementById('grafica2').remove();
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}


// Función para mostrar el grafico por la cantidad de productos en inventario
function graficoInventarioProducto() {
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + 'graficaProductoInventariod', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a gráficar.
                    let producto = [];
                    let cantidad = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        producto.push(row.nombre_producto);
                        cantidad.push(row.cantidad);
                    });
                    // Se llama a la función que genera y muestra un gráfico de pastel. Se encuentra en el archivo components.js
                    donutGraph('grafica3', producto, cantidad, 'Top 5 de productos con más cantidad en inventario');
                    //barGraph('grafica4', producto, cantidad, 'Porcentaje de producto');
                } else {
                    document.getElementById('grafica3').remove();
                    //document.getElementById('grafica4').remove();
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

// Función para mostrar el grafico de los 5 productos mas vendidos
function graficoVentaProducto() {
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + 'graficaProductosVendidsos', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a gráficar.
                    let producto = [];
                    let cantidadd = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        producto.push(row.nombre_producto);
                        cantidadd.push(row.cantidad_vendida);
                    });
                    // Se llama a la función que genera y muestra un gráfico de pastel. Se encuentra en el archivo components.js
                    barGraph('grafica4', producto, cantidadd,'Cantidad de productos vendidos', 'top 5 productos vendidos');

                } else {
                    document.getElementById('grafica4').remove();

                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}
