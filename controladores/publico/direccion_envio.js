const API_CARRITO = SERVER + 'sitio_publico/api_carrito.php?action=';

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
    //Instanciar Datepicker
	M.Datepicker.init(document.querySelectorAll('.datepicker'), {
		format: 'yyyy-mm-dd', i18n: {
			months: ['Enero', 'Febrero', 'Marzo', 'April', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
			monthsShort: ['En', 'Febr', 'Mzo', 'Abr', 'My', 'Jun', 'Jul', 'Ag', 'Sept', 'Oct', 'Nov', 'Dic'],
			weekdaysShort: ['Dom', 'Lun', 'Mar', 'Miérc', 'Juev', 'Vier', 'Sáb'],
			weekdaysAbbrev: ['D', 'L', 'M', 'X', 'J', 'V', 'S']
	}});
    traerDatosEmpleado();
});

function traerDatosEmpleado() {
    // Petición para consultar si existen pedidos registrados.
    fetch(API_CARRITO + 'traerDatosCliente', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    document.getElementById('nombre').value = response.dataset.nombre_cliente;
                    document.getElementById('apellidos').value = response.dataset.apellido_cliente;
                    document.getElementById('telefono').value = response.dataset.telefono_cliente;
                    // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.
					M.updateTextFields();
                } else {
                    //Si no se obtuvieron los datos del empleado
                    sweetAlert(4, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

function guardarDatosEnvio(event){
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Petición para guardar los datos ingresados
    fetch(API_CARRITO + 'guardarDatos', {
        method: 'post',
        body: new FormData(document.getElementById('register-form'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se establece la ruta del reporte en el servidor.
                    let url = SERVER + 'reports/sitio_publico/recibo_factura.php';
                    // Se abre el reporte en una nueva pestaña del navegador web.
                    window.open(url);
                    //Si no se obtuvieron los datos del empleado
                    sweetAlert(4, response.message, 'dashboard.html');
                } else {
                    //Si no se obtuvieron los datos del empleado
                    sweetAlert(4, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}


function guardarDatosEnvio2(event){
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Petición para guardar los datos ingresados
    fetch(API_CARRITO + 'guardarDatosCentroComercial', {
        method: 'post',
        body: new FormData(document.getElementById('register-form'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria para obtener los datos, de lo contrario se muestra un mensaje con la excepción.
                if (response.estado) {
                    // Se establece la ruta del reporte en el servidor.
                    let url = SERVER + 'reports/sitio_publico/recibo_factura.php';
                    // Se abre el reporte en una nueva pestaña del navegador web.
                    window.open(url);
                    //Si no se obtuvieron los datos del empleado
                    sweetAlert(4, response.message, 'dashboard.html');
                } else {
                    //Si no se obtuvieron los datos del empleado
                    sweetAlert(4, response.exception, null);
                }
            });
        } else {
            console.log(request.estado + ' ' + request.statusText);
        }
    });
}

function centroComercial(){
    let direccion1 = document.getElementById('centro_comercial');
    let direccion2 = document.getElementById('servicio_domicilio');
    let boton2 = document.getElementById('boton_naranja_envio2');
    let boton1 = document.getElementById('boton_naranja_envio');
    direccion1.classList.remove('hide');
    direccion2.classList.add('hide');
    boton2.classList.remove('hide');
    boton1.classList.add('hide');
}

function domicilio(){
    let direccion1 = document.getElementById('centro_comercial');
    let direccion2 = document.getElementById('servicio_domicilio');
    let boton2 = document.getElementById('boton_naranja_envio2');
    let boton1 = document.getElementById('boton_naranja_envio');
    direccion1.classList.add('hide');
    direccion2.classList.remove('hide');
    boton2.classList.add('hide');
    boton1.classList.remove('hide');
}