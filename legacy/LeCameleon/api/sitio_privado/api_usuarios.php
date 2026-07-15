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
    if (isset($_SESSION['idusuario_e'])) {    
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $usuario->obtenerUsuarios()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'obtenerEstadoU':
                if ($result['dataset'] = $usuario->obtenerEstadoUsuarioE()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'obtenerTipoU':
                if ($result['dataset'] = $usuario->obtenerTipoUsuarioE()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'readOne':
                if (!$usuario->setId($_POST['ide'])) {
                    $result['exception'] = 'Usuario empleado incorrecto';
                } elseif ($result['dataset'] = $usuario->LeerUnUsuario()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Usuario inexistente';
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
            case 'crearUsuarioE':
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->setUsuario($_POST['usuario_e'])) {
                    $result['exception'] = 'El usuario empleado es incorrecto';
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
            case 'actualizarUsuarioE':
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->setId($_POST['ide'])) {
                    $result['exception'] = 'Usuario empleado incorrecto';
                } elseif (!$data = $usuario->LeerUnUsuario()) {
                    $result['exception'] = 'Usuario inexistente';
                } elseif (!$usuario->setUsuario($_POST['usuarioeM'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif (!$usuario->setContrasena($_POST['contrasenaM'])) {
                    $result['exception'] = 'Direccion incorrecta';
                } elseif (!$usuario->setEmpleado($_POST['empleadoM'])) {
                    $result['exception'] = 'Seleccion un empleado';
                } elseif (!$usuario->setTipo($_POST['tipoM'])) {
                    $result['exception'] = 'Seleccion un tipo de usuario';
                } elseif (!$usuario->setEstado($_POST['estadoM'])) {
                    $result['exception'] = 'Seleccion un estado del usuario';
                } elseif ($usuario->actualizarUsuarioEmpleado()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Usuario empleado modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'delete':
                if (!$usuario->setId($_POST['ide'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif (!$data = $usuario->LeerUnUsuario()) {
                    $result['exception'] = 'Usuario inexistente';
                } elseif ($usuario->eliminarUsuarioEmpleado()) {
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
    } else {
        print(json_encode('Acceso denegado'));
    }    
} else {
    print(json_encode('Recurso no disponible'));
}