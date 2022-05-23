<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/usuarios.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $usuario = new Usuarios;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'message' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $usuario->readAll()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;

                
            case 'search':
                $_POST = $usuario->validateForm($_POST);
                if ($_POST['search'] == '') {
                    $result['exception'] = 'Ingrese un valor para buscar';
                } elseif ($result['dataset'] = $usuario->searchRows($_POST['search'])) {
                    $result['estado'] = 1;
                    $result['message'] = 'Valor encontrado';
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;
            case 'create':
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->setUsuario($_POST['usuario'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif (!$usuario->setContrasena($_POST['contrasena'])) {
                    $result['exception'] = 'Contraseña incorrecta';
                } elseif (!isset($_POST['empleado'])) {
                    $result['exception'] = 'Seleccione un empleado';
                } elseif (!$usuario->setEmpleado($_POST['empleado'])) {
                    $result['exception'] = 'Empleado incorrecto';                 
                } elseif (!isset($_POST['tipo'])) {
                    $result['exception'] = 'Seleccione un Tipo';
                } elseif (!$usuario->setTipo($_POST['tipo'])) {
                    $result['exception'] = 'Tipo incorrecto';
                } elseif (!isset($_POST['estado'])) {
                    $result['exception'] = 'Seleccione un Estado';
                } elseif (!$usuario->setEstado($_POST['estado'])) {
                    $result['exception'] = 'Estado incorrecto';
                } elseif ($usuario->createRow()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Usuario creado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

                

            case 'readOne':
                if (!$usuario->setId($_POST['ide'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif ($result['dataset'] = $usuario->readOne()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Usuario inexistente';
                }
                break;

            case 'update':
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->setId($_POST['ide'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif (!$data = $usuario->readOne()) {
                    $result['exception'] = 'Usuario inexistente';
                } elseif (!$usuario->setUsuario($_POST['usuarioeM'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif (!$usuario->setContrasena($_POST['contrasenaM'])) {
                    $result['exception'] = 'Direccion incorrecta';
                } elseif (!$usuario->setEmpleado($_POST['empleadoM'])) {
                    $result['exception'] = 'Seleccion un empleado';
                } elseif (!$usuario->setTipo($_POST['tipoM'])) {
                    $result['exception'] = 'Seleccion un tipo';
                } elseif (!$usuario->setEstado($_POST['estadoM'])) {
                    $result['exception'] = 'Seleccion un estado';
                } elseif ($usuario->updateRow()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Usuario modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

            case 'readOneE':
                if (!$usuario->setId($_POST['idd'])) {
                    $result['exception'] = 'Usuario incorrecta';
                } elseif ($result['dataset'] = $usuario->readOne()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Usuario inexistente';
                    }
                break;

            case 'delete':
                if (!$usuario->setId($_POST['idd'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif (!$data = $usuario->readOneE()) {
                    $result['exception'] = 'Usuario inexistente';
                } elseif ($usuario->deleteRow()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Usuario eliminado correctamente';
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