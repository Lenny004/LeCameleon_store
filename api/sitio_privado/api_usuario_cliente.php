<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/usuarios_clientes.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $usuario_cliente = new UsuarioCliente;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idusuario_e'])) {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $usuario_cliente->obtenerUsuariosClientes()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'leerUno':
                if (!$usuario_cliente->setId($_POST['ide'])) {
                    $result['exception'] = 'Usuario cliente incorrecto';
                } elseif ($result['dataset'] = $usuario_cliente->leerUnCliente()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Usuario cliente inexistente';
                }
                break;
            case 'obtenerEstadoUsuarioCliente':
                if ($result['dataset'] = $usuario_cliente->obtenerEstadoUsuarioCliente()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'search':
                $_POST = $usuario_cliente->validateForm($_POST);
                if ($_POST['search'] == '') {
                    $result['exception'] = 'Ingrese un valor para buscar';
                } elseif ($result['dataset'] = $usuario_cliente->buscarUsuariosClientes($_POST['search'])) {
                    $result['estado'] = 1;
                    $result['message'] = 'Valor encontrado';
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;
            case 'actualizarEmpleado':
                $_POST = $usuario_cliente->validateForm($_POST);
                if (!$usuario_cliente->setId($_POST['ide'])) {
                    $result['exception'] = 'Usuario cliente incorrecto';
                } elseif (!$data = $usuario_cliente->leerUnCliente()) {
                    $result['exception'] = 'Usuario cliente inexistente';
                } elseif (!$usuario_cliente->setUsuarioC($_POST['usuarioC'])) {
                    $result['exception'] = 'Usuario cliente incorrecto';
                } elseif (!$usuario_cliente->setEstadoUsuarioC($_POST['estadoUsuarioC'])) {
                    $result['exception'] = 'Seleccione un estado';
                } elseif ($usuario_cliente->actualizarEmpleado()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Estado del usuario cliente modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
                break;
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