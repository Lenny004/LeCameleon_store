/*
*   CONTROLADOR DE USO GENERAL EN TODAS LAS PÁGINAS WEB.
*/

/*Constante para establecer la ruta del servidor.*/
const SERVER = "http://localhost/LeCameleon/api/";

/*
*   Función para obtener todos los registros disponibles en los mantenimientos de tablas (operación read).
*   Parámetros: api (ruta del servidor para obtener los datos).
*   Retorno: ninguno.
*/
function readRows(api) {
    fetch(api + 'readAll', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let data = [];
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    data = response.dataset;
                } else {
                    sweetAlert(4, response.exception, null);
                }
                // Se envían los datos a la función del controlador para llenar la tabla en la vista.
                fillTable(data);
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

/*
*   Función para obtener los resultados de una búsqueda en los mantenimientos de tablas (operación search).
*   Parámetros: api (ruta del servidor para obtener los datos) y form (identificador del formulario de búsqueda).
*   Retorno: ninguno.
*/
function searchRows(api, form) {
    fetch(api + 'search', {
        method: 'post',
        body: new FormData(document.getElementById(form))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se envían los datos a la función del controlador para que llene la tabla en la vista y se muestra un mensaje de éxito.
                    fillTable(response.dataset);
                    sweetAlert(1, response.message, null);
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

/*
*   Función para crear o actualizar un registro en los mantenimientos de tablas (operación create y update).
*   Parámetros: api (ruta del servidor para enviar los datos), form (identificador del formulario) y modal (identificador de la caja de dialogo).
*   Retorno: ninguno.
*/
function saveRow(api, action, form, modal) {
    fetch(api + action, {
        method: 'post',
        body: new FormData(document.getElementById(form))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se cierra la caja de dialogo (modal) del formulario.
                    M.Modal.getInstance(document.getElementById(modal)).close();
                    // Se cargan nuevamente las filas en la tabla de la vista después de guardar un registro y se muestra un mensaje de éxito.
                    readRows(api);
                    sweetAlert(1, response.message, null);
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

/*
*   Función para eliminar un registro seleccionado en los mantenimientos de tablas (operación delete). Requiere el archivo sweetalert.min.js para funcionar.
*   Parámetros: api (ruta del servidor para enviar los datos) y data (objeto con los datos del registro a eliminar)
*   Retorno: ninguno.
*/
function confirmDelete(api, data, modal) {
    swal({
        title: 'Advertencia',
        text: '¿Desea eliminar el registro?',
        icon: 'warning',
        buttons: ['No', 'Sí'],
        closeOnClickOutside: false,
        closeOnEsc: false
    }).then(function (value) {
        // Se comprueba si fue cliqueado el botón Sí para hacer la petición de borrado, de lo contrario no se hace nada.
        if (value) {
            fetch(api + 'delete', {
                method: 'post',
                body: data
            }).then(function (request) {
                // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                if (request.ok) {
                    // Se obtiene la respuesta en formato JSON.
                    request.json().then(function (response) {
                        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                        if (response.estado) {
                            modal.hide();
                            // Se cargan nuevamente las filas en la tabla de la vista después de borrar un registro y se muestra un mensaje de éxito.
                            readRows(api);
                            sweetAlert(1, response.message, null);
                        } else {
                            sweetAlert(2, response.exception, null);
                        }
                    });
                } else {
                    console.log(request.estado + ' ' + request.estadoText);
                }
            });
        }
    });
}

/*
*   Función para manejar los mensajes de notificación al usuario. Requiere el archivo sweetalert.min.js para funcionar.
*   Parámetros: type (tipo de mensaje), text (texto a mostrar) y url (ubicación para enviar al cerrar el mensaje).
*   Retorno: ninguno.
*/
function sweetAlert(type, text, url) {
    // Se compara el tipo de mensaje a mostrar.
    switch (type) {
        case 1:
            title = 'Éxito';
            icon = 'success';
            break;
        case 2:
            title = 'Error';
            icon = 'error';
            break;
        case 3:
            title = 'Advertencia';
            icon = 'warning';
            break;
        case 4:
            title = 'Aviso';
            icon = 'info';
            break;
        case 5:
            title = 'Campos Vacios';
            icon = 'warning';
            break;
        case 6:
            title = 'Bienvenido';
            icon = 'info';
            break;
    }
    // Si existe una ruta definida, se muestra el mensaje y se direcciona a dicha ubicación, de lo contrario solo se muestra el mensaje.
    if (url) {
        swal({
            title: title,
            text: text,
            icon: icon,
            button: 'Aceptar',
            closeOnClickOutside: false,
            closeOnEsc: false
        }).then(function () {
            location.href = url
        });
    } else {
        swal({
            title: title,
            text: text,
            icon: icon,
            button: 'Aceptar',
            closeOnClickOutside: false,
            closeOnEsc: false
        });
    }
}

/*
*   Función para cargar las opciones en un select de formulario.
*   Parámetros: endpoint (ruta específica del servidor para obtener los datos), select (identificador del select en el formulario) y selected (valor seleccionado).
*   Retorno: ninguno.
*/
function fillSelect(endpoint, opcion, select, selected) {
    fetch(endpoint, {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                let content = '';
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Si no existe un valor para seleccionar, se muestra una opción para indicarlo.
                    if (!selected) {
                        content += `<option disabled selected>Seleccione ${opcion}</option>`;
                    }
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se obtiene el dato del primer campo de la sentencia SQL (valor para cada opción).
                        value = Object.values(row)[0];
                        // Se obtiene el dato del segundo campo de la sentencia SQL (texto para cada opción).
                        text = Object.values(row)[1];
                        // Se verifica si el valor de la API es diferente al valor seleccionado para enlistar una opción, de lo contrario se establece la opción como seleccionada.
                        if (value != selected) {
                            content += `<option value="${value}">${text}</option>`;
                        } else {
                            content += `<option value="${value}" selected>${text}</option>`;
                        }
                    });
                } else {
                    content += '<option>No hay opciones disponibles</option>';
                }
                // Se agregan las opciones a la etiqueta select mediante su id.
                document.getElementById(select).innerHTML = content;
                // Se inicializa el componente Select del formulario para que muestre las opciones.
                M.FormSelect.init(document.querySelectorAll('select'));
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

// Función para mostrar un mensaje de confirmación al momento de cerrar sesión.
function logOut() {
    swal({
        title: 'Cerrar Sesión',
        text: '¿Está seguro de cerrar la sesión?',
        icon: 'warning',
        buttons: ['No', 'Sí'],
        closeOnClickOutside: false,
        closeOnEsc: false
    }).then(function (value) {
        // Se verifica si fue cliqueado el botón Sí para hacer la petición de cerrar sesión, de lo contrario se muestra un mensaje.
        if (value) {
            fetch(API_USUARIOS + 'cerrarSesion', {
                method: 'get'
            }).then(function (request) {
                // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                if (request.ok) {
                    // Se obtiene la respuesta en formato JSON.
                    request.json().then(function (response) {
                        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                        if (response.estado) {
                            sweetAlert(1, response.message, 'index.html');
                        } else {
                            sweetAlert(2, response.exception, null);
                        }
                    });
                } else {
                    console.log(request.estado + ' ' + request.estadoText);
                }
            });
        } else {
            sweetAlert(4, 'Puede continuar con la sesión', null);
        }
    });
}


// Función para mostrar un mensaje de confirmación al momento de cerrar sesión en el sitio público.
function cerrarSesion() {
    swal({
        title: 'Cerrar Sesión',
        text: '¿Está seguro de cerrar la sesión?',
        icon: 'warning',
        buttons: ['No', 'Sí'],
        closeOnClickOutside: false,
        closeOnEsc: false
    }).then(function (value) {
        // Se verifica si fue cliqueado el botón Sí para hacer la petición de cerrar sesión, de lo contrario se muestra un mensaje.
        if (value) {
            fetch(API_LOGIN_CLIENTE + 'cerrarSesion', {
                method: 'get'
            }).then(function (request) {
                // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                if (request.ok) {
                    // Se obtiene la respuesta en formato JSON.
                    request.json().then(function (response) {
                        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                        if (response.estado) {
                            sweetAlert(1, response.message, 'index.html');
                        } else {
                            sweetAlert(2, response.exception, null);
                        }
                    });
                } else {
                    console.log(request.estado + ' ' + request.estadoText);
                }
            });
        } else {
            sweetAlert(4, 'Puede continuar con la sesión', null);
        }
    });
}


/*
*   Función para generar un gráfico de barras verticales. Requiere el archivo chart.js. Para más información https://www.chartjs.org/
*   Parámetros: canvas (identificador de la etiqueta canvas), xAxis (datos para el eje X), yAxis (datos para el eje Y), legend (etiqueta para los datos) y title (título del gráfico).
*   Retorno: ninguno.
*/
function barGraph(canvas, xAxis, yAxis, legend, titulo) {
    // Se declara un arreglo para guardar códigos de colores en formato rgb temporalmente.
    let rgb_desordenado = [];
    // Se declara un arreglo para guardar códigos de colores en formato rgb.
    let colors = [];
    // Se declara un arreglo para guardar códigos de colores en formato rgb para los bordes.
    let colors2 = [];
    // Se generan códigos hexadecimales de 6 cifras de acuerdo con el número de datos a mostrar y se agregan al arreglo.
    for (i = 0; i < xAxis.length; i++) {
        //Generamos un valor hasta 255 que es el máximo en rgb para luego anexarlo
        let valor1 = Math.floor(Math.random() * 255);
        let valor2 = Math.floor(Math.random() * 255);
        //Tendrá un valor establecido para que sea una paleta de colores igual (pastel)
        let valor3 = 150;
        // Se declara un arreglo para guardar los códigos RGB para luego desordenarlos
        let rgb = [valor1, valor2, valor3];
        //Se desordenan los valores obtenidos
        rgb.sort(() => Math.random() - 0.5);
        //Se ingresan en el array que los guardará temporalmente
        rgb_desordenado.push(rgb[0]);
        rgb_desordenado.push(rgb[1]);
        rgb_desordenado.push(rgb[2]);
        colors.push("rgb"+ "(" + rgb_desordenado[0] + "," + rgb_desordenado[1] + "," + rgb_desordenado[2] + ",0.4" + ")");
        colors2.push("rgb"+ "(" + rgb_desordenado[0] + "," + rgb_desordenado[1] + "," + rgb_desordenado[2] + ")");
        //Volvemos a vaciar el array para que guarde nuevos valores
        rgb_desordenado = [];
    }
    // Se establece el contexto donde se mostrará el gráfico, es decir, se define la etiqueta canvas a utilizar.
    let context = document.getElementById(canvas).getContext('2d');
    if (window.barras) {
        window.barras.clear();
        window.barras.destroy();
    }
    // Se crea una instancia para generar el gráfico con los datos recibidos.
    window.barras = new Chart(context, {
        type: 'bar',
        data: {
            labels: xAxis,
            datasets: [{
                label: legend,
                data: yAxis,
                borderColor: ['rgb(140, 196, 63, 0.9)',
                'rgb(47, 105, 80, 0.8)'],
                color: '#242221',
                borderWidth: 1,
                backgroundColor: ['rgb(140, 196, 63, 0.9)',
                'rgb(47, 105, 80, 0.8)'],
                barPercentage: 1
            }]
        },
        options: {
            aspectRatio: 1,
            plugins: {
                title: {
                    display: true,
                    text: titulo
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        }
    });
}


/*
*   Función para generar un gráfico de linea.
*   Parámetros: canvas (identificador de la etiqueta canvas), xAxis (datos para el eje X), yAxis (datos para el eje Y), legend (etiqueta para los datos) y title (título del gráfico).
*   Retorno: ninguno.
*/
function lineGraph(canvas, xAxis, yAxis, legend, titulo) {
    // Se declara un arreglo para guardar códigos de colores en formato rgb temporalmente.
    let rgb_desordenado = [];
    // Se declara un arreglo para guardar códigos de colores en formato rgb.
    let colors = [];
    // Se generan códigos hexadecimales de 6 cifras de acuerdo con el número de datos a mostrar y se agregan al arreglo.
    for (i = 0; i < xAxis.length; i++) {
        //Generamos un valor hasta 255 que es el máximo en rgb para luego anexarlo
        let valor1 = Math.floor(Math.random() * 255);
        let valor2 = Math.floor(Math.random() * 255);
        //Tendrá un valor establecido para que sea una paleta de colores igual (pastel)
        let valor3 = 150;
        // Se declara un arreglo para guardar los códigos RGB para luego desordenarlos
        let rgb = [valor1, valor2, valor3];
        //Se desordenan los valores obtenidos
        rgb.sort(() => Math.random() - 0.5);
        //Se ingresan en el array que los guardará temporalmente
        rgb_desordenado.push(rgb[0]);
        rgb_desordenado.push(rgb[1]);
        rgb_desordenado.push(rgb[2]);
        colors.push("rgb"+ "(" + rgb_desordenado[0] + "," + rgb_desordenado[1] + "," + rgb_desordenado[2] + ",0.5" + ")");
        //Volvemos a vaciar el array para que guarde nuevos valores
        rgb_desordenado = [];
    }
    // Se establece el contexto donde se mostrará el gráfico, es decir, se define la etiqueta canvas a utilizar.
    let context = document.getElementById(canvas).getContext('2d');
    let chart = new Chart(context);
    chart.destroy();
    // Se crea una instancia para generar el gráfico con los datos recibidos.
    chart = new Chart(context, {
        type: 'line',
        data: {
            labels: xAxis,
            datasets: [{
                fill: true,
                label: legend,
                data: yAxis,
                borderWidth: 1,
                backgroundColor: colors,
                barPercentage: 1,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            aspectRatio: 1,
            plugins: {
                title: {
                    display: true,
                    text: titulo
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        }
    });
}

/*
*   Función para generar un gráfico de pastel. Requiere el archivo chart.js. Para más información https://www.chartjs.org/
*   Parámetros: canvas (identificador de la etiqueta canvas), legends (valores para las etiquetas), values (valores de los datos) y title (título del gráfico).
*   Retorno: ninguno.
*/
function donutGraph(canvas, legends, values, titulo) {
    // Se declara un arreglo para guardar códigos de colores en formato rgb temporalmente.
    let rgb_desordenado = [];
    // Se declara un arreglo para guardar códigos de colores en formato rgb.
    let colors = [];
    // Se declara un arreglo para guardar códigos de colores en formato rgb para los bordes.
    let colors2 = [];
    // Se generan códigos hexadecimales de 6 cifras de acuerdo con el número de datos a mostrar y se agregan al arreglo.
    for (i = 0; i < values.length; i++) {
        //Generamos un valor hasta 255 que es el máximo en rgb para luego anexarlo
        let valor1 = Math.floor(Math.random() * 255);
        let valor2 = Math.floor(Math.random() * 255);
        //Tendrá un valor establecido para que sea una paleta de colores igual (pastel)
        let valor3 = 0;
        // Se declara un arreglo para guardar los códigos RGB para luego desordenarlos
        let rgb = [valor1, valor2, valor3];
        //Se desordenan los valores obtenidos
        rgb.sort(() => Math.random() - 0.5);
        //Se ingresan en el array que los guardará temporalmente
        rgb_desordenado.push(rgb[0]);
        rgb_desordenado.push(rgb[1]);
        rgb_desordenado.push(rgb[2]);
        colors.push("rgb"+ "(" + rgb_desordenado[0] + "," + rgb_desordenado[1] + "," + rgb_desordenado[2] + ",0.4" + ")");
        colors2.push("rgb"+ "(" + rgb_desordenado[0] + "," + rgb_desordenado[1] + "," + rgb_desordenado[2] + ")");
        //Volvemos a vaciar el array para que guarde nuevos valores
        rgb_desordenado = [];
    }
    // Se establece el contexto donde se mostrará el gráfico, es decir, se define la etiqueta canvas a utilizar.
    let context = document.getElementById(canvas).getContext('2d');
    if (window.donut) {
        window.donut.clear();
        window.donut.destroy();
    }
    // Se crea una instancia para generar el gráfico con los datos recibidos.
    window.donut = new Chart(context, {
        type: 'doughnut',
        data: {
            labels: legends,
            datasets: [{
                data: values,
                borderColor: colors2,
                backgroundColor: colors
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: titulo
                }
            }
        }
    });
}

/*
*   Función para generar un gráfico de pastel. Requiere el archivo chart.js. Para más información https://www.chartjs.org/
*   Parámetros: canvas (identificador de la etiqueta canvas), legends (valores para las etiquetas), values (valores de los datos) y title (título del gráfico).
*   Retorno: ninguno.
*/
function pieGraph(canvas, legends, values, titulo) {
    // Se declara un arreglo para guardar códigos de colores en formato rgb temporalmente.
    let rgb_desordenado = [];
    // Se declara un arreglo para guardar códigos de colores en formato rgb.
    let colors = [];
    // Se generan códigos hexadecimales de 6 cifras de acuerdo con el número de datos a mostrar y se agregan al arreglo.
    for (i = 0; i < values.length; i++) {
        //Generamos un valor hasta 255 que es el máximo en rgb para luego anexarlo
        let valor1 = Math.floor(Math.random() * 255);
        let valor2 = Math.floor(Math.random() * 255);
        //Tendrá un valor establecido para que sea una paleta de colores igual (pastel)
        let valor3 = 150;
        // Se declara un arreglo para guardar los códigos RGB para luego desordenarlos
        let rgb = [valor1, valor2, valor3];
        //Se desordenan los valores obtenidos
        rgb.sort(() => Math.random() - 0.5);
        //Se ingresan en el array que los guardará temporalmente
        rgb_desordenado.push(rgb[0]);
        rgb_desordenado.push(rgb[1]);
        rgb_desordenado.push(rgb[2]);
        colors.push("rgb"+ "(" + rgb_desordenado[0] + "," + rgb_desordenado[1] + "," + rgb_desordenado[2] + ",0.8" + ")");
        //Volvemos a vaciar el array para que guarde nuevos valores
        rgb_desordenado = [];
    }
    // Se establece el contexto donde se mostrará el gráfico, es decir, se define la etiqueta canvas a utilizar.
    const context = document.getElementById(canvas).getContext('2d');
    // Se crea una instancia para generar el gráfico con los datos recibidos.
    const chart = new Chart(context, {
        type: 'pie',
        data: {
            labels: legends,
            datasets: [{
                data: values,
                backgroundColor: colors
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: titulo
                }
            }
        }
    });
}

function ModoNocturno(){
    document.body.classList.toggle('dark');
    //Guardamos el modo nocturno
    if(document.body.classList.contains('dark')){
        localStorage.setItem('dark-mode', 'true');
    }
    else{
        localStorage.setItem('dark-mode', 'false');
    }
}

if(localStorage.getItem('dark-mode') === 'true'){
    document.body.classList.add('dark');
}
else{
    document.body.classList.remove('dark');
}