<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/empleados.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $empleado = new Empleados;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'message' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
    switch ($_GET['action']) {
        case 'readAll':
            if ($result['dataset'] = $empleado->readAll()) {
                $result['estado'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay datos registrados';
            }
            break;
        case 'search':
            $_POST = $empleado->validateForm($_POST);
            if ($_POST['search'] == '') {
                $result['exception'] = 'Ingrese un valor para buscar';
            } elseif ($result['dataset'] = $empleado->searchRows($_POST['search'])) {
                $result['estado'] = 1;
                $result['message'] = 'Valor encontrado';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;
        case 'create':
            $_POST = $empleado->validateForm($_POST);
            if (!$empleado->setNombre($_POST['nombre'])) {
                $result['exception'] = 'Nombre incorrecto';
            } elseif (!$empleado->setApellido($_POST['apellido'])) {
                $result['exception'] = 'apellido incorrecta';
            } elseif (!$empleado->setDUI($_POST['DUI'])) {
                $result['exception'] = 'DUI incorrecta';
            } elseif (!$empleado->setNIT($_POST['NIT'])) {
                $result['exception'] = 'NIT incorrecta';
            } elseif (!$empleado->setTelefono($_POST['telefono'])) {
                $result['exception'] = 'telefono incorrecta';
            } elseif (!$empleado->setCorreo($_POST['correo'])) {
                $result['exception'] = 'correo incorrecta';
            } elseif (!$empleado->setFecha($_POST['fecha'])) {
                $result['exception'] = 'fecha incorrecta';
            } elseif (!isset($_POST['tipo'])) {
                $result['exception'] = 'Seleccione un Tipo';
            } elseif (!$empleado->setTipo($_POST['tipo'])) {
                $result['exception'] = 'Tipo incorrecto';
            } elseif (!isset($_POST['estado'])) {
                $result['exception'] = 'Seleccione un Estado';
            } elseif (!$empleado->setEstado($_POST['estado'])) {
                $result['exception'] = 'estado incorrecto';
            } elseif ($empleado->createRow()) {
                $result['estado'] = 1;
                $result['message'] = 'Empleado creado correctamente';
            } else {
                $result['exception'] = Database::getException();
            }
            break;
        case 'readOne':
            if (!$empleado->setId($_POST['ide'])) {
                $result['exception'] = 'empleado incorrecto';
            } elseif ($result['dataset'] = $empleado->readOne()) {
                $result['estado'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'empleado inexistente';
            }
            break;

        case 'readOneE':
            if (!$empleado->setId($_POST['idd'])) {
                $result['exception'] = 'empleado incorrecto';
            } elseif ($result['dataset'] = $empleado->readOne()) {
                $result['estado'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'empleado inexistente';
            }
            break;

        case 'update':
            $_POST = $empleado->validateForm($_POST);
            if (!$empleado->setId($_POST['ide'])) {
                $result['exception'] = 'empleado incorrecto';
            } elseif (!$data = $empleado->readOne()) {
                $result['exception'] = 'empleado inexistente';
            } elseif (!$empleado->setNombre($_POST['nombre'])) {
                $result['exception'] = 'Nombre incorrecto';
            } elseif (!$empleado->setApellido($_POST['apellido'])) {
                $result['exception'] = 'Apellido incorrecto';
            } elseif (!$empleado->setDUI($_POST['DUI'])) {
                $result['exception'] = 'DUI incorrecto';
            } elseif (!$empleado->setNIT($_POST['NIT'])) {
                $result['exception'] = 'nit incorrecto';
            } elseif (!$empleado->setTelefono($_POST['telefono'])) {
                $result['exception'] = 'telefono incorrecto';
            } elseif (!$empleado->setCorreo($_POST['correo'])) {
                $result['exception'] = 'correo incorrecto';
            } elseif (!$empleado->setFecha($_POST['fecha'])) {
                $result['exception'] = 'fecha incorrecta';
            } elseif (!$empleado->setTipo($_POST['tipo'])) {
                $result['exception'] = 'Seleccione un tipo';
            } elseif (!$empleado->setEstado($_POST['estado'])) {
                $result['exception'] = 'Seleccione un estado';
            } elseif ($empleado->updateRow()) {
                $result['estado'] = 1;
                $result['message'] = 'empleado modificado correctamente';
            } else {
                $result['exception'] = Database::getException();
            }
            break;

        case 'delete':
            if (!$empleado->setId($_POST['idd'])) {
                $result['exception'] = 'empleado incorrecto';
            } elseif (!$data = $empleado->readOneD()) {
                $result['exception'] = 'empleado inexistente';
            } elseif ($empleado->deleteRow()) {
                $result['estado'] = 1;
                $result['message'] = 'empleado eliminado correctamente';
            } else {
                $result['exception'] = Database::getException();
            }
            break;
        default:
            $result['exception'] = 'Acción no disponible dentro de la sesión';
    }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
}
