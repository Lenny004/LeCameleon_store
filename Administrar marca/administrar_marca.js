const API_ADMIN_MARCA_DASH = SERVER + 'sitio_privado/api_marca.php?action=';

//Evento que se ejecuta cuando se carga la página web
document.addEventListener('DOMContentLoaded', function () {
  //Var se crea para variables globales
  //let es variables locales

  //Instanciar el menú
  M.Sidenav.init(document.querySelectorAll('.sidenav'));

  //Instanciar Dropdown Menú
  var elems = document.querySelectorAll('.dropdown-trigger');
  M.Dropdown.init(elems, { coverTrigger: false, hover: true });

  //Instanciar Select
  M.FormSelect.init(document.querySelectorAll('select'));

  //Instanciar Slider del main;
  let options = { indicators: false, height: 500 };
  M.Slider.init(document.querySelectorAll('.slider'), options);

  //Instanciar ToolTips footer
  M.Tooltip.init(document.querySelectorAll('.tooltipped'));

  M.Datepicker.init(document.querySelectorAll('.datepicker'));

  //Instaciar el modal o pow up
  M.Modal.init(document.querySelectorAll('.modal'));

  M.Modal.init(document.querySelectorAll('.moda2'));

  M.Modal.init(document.querySelectorAll('.moda3'));

  readRows(API_ADMIN_MARCA_DASH);

});

document.addEventListener('DOMContentLoaded', function () {
  // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
  readRows(API_ADMIN_MARCA_DASH);
  // Se define una variable para establecer las opciones del componente Modal.
  let options = {
      dismissible: false,
      onOpenStart: function () {
          // Se restauran los elementos del formulario.
          document.getElementById('Agregar_forms').reset();
          document.getElementById('modificar_forms').reset();
          document.getElementById('eliminar_forms').reset();
      }
  }
  // Se inicializa el componente Modal para que funcionen las cajas de diálogo.
  M.Modal.init(document.querySelectorAll('.modal'), options);
  // Se inicializa el componente Modal para que funcionen las cajas de diálogo.
  M.Modal.init(document.querySelectorAll('.modal'), options);
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
  let content = '';
  // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
  dataset.map(function (row) {
      // Se crean y concatenan las filas de la tabla con los datos de cada registro.
      content += `
          <tr>
              <td>${row.id_marca}</td>
              <td>${row.nombre_marca}</td>
              <td><img src="${SERVER}images/marca/${row.imagen_marca}" class="materialboxed" height="100"></td>
              <td>
                  <a onclick="openUpdate(${row.id_marca})" class="btn green tooltipped" data-tooltip="Actualizar">
                  <i class="large material-icons">mode_edit</i>
                  </a>
                  <a onclick="openDelete(${row.id_marca })"class="btn  brown tooltipped" data-tooltip="Eliminar">
                  <i class="large material-icons">delete</i>
                  </a>
              </td>
          </tr>
      `;
  });
  // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
  document.getElementById('tabla_marca').innerHTML = content;
  // Se inicializa el componente Material Box para que funcione el efecto Lightbox.
  M.Materialbox.init(document.querySelectorAll('.materialboxed'));
  // Se inicializa el componente Tooltip para que funcionen las sugerencias textuales.
  M.Tooltip.init(document.querySelectorAll('.tooltipped'));
}

function openCreate() {
  // Se abre la caja de diálogo (modal) que contiene el formulario.
  M.Modal.getInstance(document.getElementById('modal_agregar_marca')).open();
  // Se establece el campo de archivo como obligatorio.
  document.getElementById('archivo').required = true;
}
document.getElementById('Agregar_forms').addEventListener('submit', function (event) {
  // Se evita recargar la página web después de enviar el formulario.
  event.preventDefault();
  let action = 'create';
  saveRow(API_ADMIN_MARCA_DASH, action, 'Agregar_forms', 'modal_agregar_marca');
});


// Función para preparar el formulario al momento de modificar un registro.
function openUpdate(id) {
  // Se abre la caja de diálogo (modal) que contiene el formulario.
  M.Modal.getInstance(document.getElementById('editar_modal_marca')).open();
  // Se asigna el título para la caja de diálogo (modal).
  document.getElementById('archivop').required = false;   
  // Se define un objeto con los datos del registro seleccionado.
  const data = new FormData();
  data.append('ide', id);
  // Petición para obtener los datos del registro solicitado.
  fetch(API_ADMIN_MARCA_DASH + 'readOne', {
      method: 'post',
      body: data
  }).then(function (request) {
      // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
      if (request.ok) {
          // Se obtiene la respuesta en formato JSON.
          request.json().then(function (response) {
              // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
              if (response.estado) {
                  // Se inicializan los campos del formulario con los datos del registro seleccionado.
                  document.getElementById('ide').value = response.dataset.id_marca;
                  document.getElementById('marca_modificar').value = response.dataset.nombre_marca;
                  document.getElementById('nombrearchivo').value = response.dataset.imagen_marca;
                  // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.
                  M.updateTextFields();
              } else {
                  sweetAlert(2, response.exception, null);
              }
          });
      } else {
          console.log(request.estado + ' ' + request.statusText);
      }
  });
}

document.getElementById('modificar_forms').addEventListener('submit', function (event) {
  // Se evita recargar la página web después de enviar el formulario.
  event.preventDefault();
  let action = 'update';
  saveRow(API_ADMIN_MARCA_DASH, action, 'modificar_forms', 'editar_modal_marca');
});

function openDelete(id) {
  // Se abre la caja de diálogo (modal) que contiene el formulario de eliminar registro.
  M.Modal.getInstance(document.getElementById('eliminar_modal_marca')).open();
  document.getElementById('idd').value = '';
  document.getElementById('idd').value = id;

  const data = new FormData();
  data.append('idd', id);
  
  fetch(API_ADMIN_MARCA_DASH + 'readOneE', {
      method: 'post',
      body: data
  }).then(function (request) {
      // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
      if (request.ok) {
          // Se obtiene la respuesta en formato JSON.
          request.json().then(function (response) {
              // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
              if (response.estado) {
                  // Se inicializan los campos del formulario con los datos del registro seleccionado.
                  document.getElementById('idd').value = response.dataset.idMarca;
                  document.getElementById('marca_eliminar').value = response.dataset.nombreMarca;
                  document.getElementById('namefile').value = response.dataset.imagenMarca;
                  // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.
                  M.updateTextFields();
              } else {
                  sweetAlert(2, response.exception, null);
              }
          });
      } else {
          console.log(request.estado + ' ' + request.statusText);
      }
  });
}
//Funcion para eliminar sin modal
function openDelete(id) {
  // Se define un objeto con los datos del registro seleccionado.
  const data = new FormData();
  data.append('idd', id);

  // Se llama a la función que elimina un registro. Se encuentra en el archivo components.js
  confirmDelete(API_ADMIN_MARCA_DASH, data);
}
document.getElementById('eliminar_forms').addEventListener('submit', function (event) {
  // Se evita recargar la página web después de enviar el formulario.
  event.preventDefault();
  var valor = document.getElementById('idd').value;

  const data = new FormData();  
  data.append('idd', valor);
  // Se llama a la función para guardar el registro.
  eliminateRow(API_ADMIN_MARCA_DASH, data);
});