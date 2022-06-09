<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/pedidos.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $monitorear_pedidos = new Pedidos;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idusuario_e'])) {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']){
            //Verificamos si existen usuarios
            case 'existenciaPedidos':
                //Si existen pedidos manda un mensaje de que se encontraron
                $fecha_actual = date('Y-m-d h:i:s', time());
                if ($result['dataset'] = $monitorear_pedidos->validarExistenciaPedidos($fecha_actual)) {
                    $result['estado'] = 1;
                    $result['message'] = 'Existen pedidos registrados';
                }
                else {
                    $result['exception'] = 'No existen pedidos registrados';
                }
                break;
            case 'existenciaPedidosHoy':
                //Si existen pedidos manda un mensaje de que se encontraron
                $fecha_actual = date('Y-m-d');
                if ($result['dataset'] = $monitorear_pedidos->existenciaPedidosHoy($fecha_actual)) {
                    $result['estado'] = 1;
                    $result['message'] = 'Existen pedidos por entregar este día';
                }
                else {
                    $result['exception'] = 'No existen pedidos por entregar este día';
                }
                break;
            //Se le asigna un empleado a la factura
            case 'asignarFacturaEmpleado':
                $_POST = $monitorear_pedidos->validateForm($_POST);
                    if (!$monitorear_pedidos->setIdEnvioPedido($_POST['id'])) {
                        $result['exception'] = 'Pedido incorrecto';
                    } elseif (!$data = $monitorear_pedidos->leerUnPedido()) {
                        $result['exception'] = 'Pedido inexistente';
                    } elseif (!$monitorear_pedidos->setIdUsuarioE($_SESSION['idusuario_e'])) {
                        $result['exception'] = 'Hubo un problema al asignar el empleado al pedido';
                    } elseif ($monitorear_pedidos->asignarFacturaEmpleado()) {
                        $result['estado'] = 1;
                        $result['message'] = 'Has tomado el pedido, ve el apartado de Pedidos Tomados';
                    } else {
                        $result['exception'] = Database::getException();
                    }
                    break;
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
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