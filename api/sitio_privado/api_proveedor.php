<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/proveedor.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $proveedor = new Proveedor;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'message' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $proveedor->readAll()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'search':
                $_POST = $proveedor->validateForm($_POST);
                if ($_POST['search'] == '') {
                    $result['exception'] = 'Ingrese un valor para buscar';
                } elseif ($result['dataset'] = $proveedor->searchRows($_POST['search'])) {
                    $result['estado'] = 1;
                    $result['message'] = 'Valor encontrado';
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;
            case 'create':
                $_POST = $proveedor->validateForm($_POST);
                if (!$proveedor->setNombre($_POST['nombre'])) {
                    $result['exception'] = 'Nombre incorrecto';
                } elseif (!$proveedor->setDireccion($_POST['direccion'])) {
                    $result['exception'] = 'Direccion incorrecta';
                } elseif (!$proveedor->setTelefono($_POST['telefono'])) {
                    $result['exception'] = 'Telefono incorrecto';
                } elseif ($proveedor->createRow()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Distribuidor creado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

            case 'readOne':
                if (!$proveedor->setId($_POST['ide'])) {
                    $result['exception'] = 'Distribuidor incorrecto';
                } elseif ($result['dataset'] = $proveedor->readOne()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Distribuidor inexistente';
                }
                break;
            case 'update':
                $_POST = $proveedor->validateForm($_POST);
                if (!$proveedor->setId($_POST['ide'])) {
                    $result['exception'] = 'Distribuidor incorrecto';
                } elseif (!$data = $proveedor->readOne()) {
                    $result['exception'] = 'Distribuidor inexistente';
                } elseif (!$proveedor->setNombre($_POST['nombreM'])) {
                    $result['exception'] = 'Nombre incorrecto';
                } elseif (!$proveedor->setDireccion($_POST['direccionM'])) {
                    $result['exception'] = 'Direccion incorrecta';
                } elseif (!$proveedor->setTelefono($_POST['telefonoM'])) {
                    $result['exception'] = 'Telefono incorrecto';
                } elseif ($proveedor->updateRow()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Proveedor modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'readOneE':
                if (!$proveedor->setId($_POST['idd'])) {
                    $result['exception'] = 'Distribuidor incorrecta';
                } elseif ($result['dataset'] = $proveedor->readOne()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Distribuidor inexistente';
                    }
                break;

            case 'delete':
                if (!$proveedor->setId($_POST['idd'])) {
                    $result['exception'] = 'Distribuidor incorrecto';
                } elseif (!$data = $proveedor->readOneE()) {
                    $result['exception'] = 'Distribuidor inexistente';
                } elseif ($proveedor->deleteRow()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Distribuidor eliminado correctamente';
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