<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/pedidos.php');
require_once('../modelo/usuarios_clientes.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $monitorear_pedidos = new Pedidos;
    $clientes = new UsuarioCliente;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idusuario_c'])) {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
                //Verificamos si existen pedidos realizados
            case 'pedidosRealizadosCliente':
                //Si existen pedidos manda un mensaje de que se encontraron
                $fecha_actual = date('Y-m-d');
                //Le enviamos la fecha actual
                if ($result['dataset'] = $monitorear_pedidos->pedidosCliente($_SESSION['idusuario_c'], $fecha_actual)) {
                    $result['estado'] = 1;
                    $result['message'] = 'Existen pedidos registrados';
                } 
                //Si no existen pedidos realizados se envia un exception
                else {
                    $result['exception'] = 'No existen pedidos registrados';
                }
                break;
                //Verificamos si existen pedidos realizados y la fecha de entrega es hoy
            case 'pedidosClienteEntregaHoy':
                //Si existen pedidos manda un mensaje de que se encontraron
                $fecha_actual = date('Y-m-d');
                //Si existen pedidos realizados con fecha de entrega hoy se envia resultado 1
                if ($result['dataset'] = $monitorear_pedidos->pedidosClienteEntregaHoy($_SESSION['idusuario_c'], $fecha_actual)) {
                    $result['estado'] = 1;
                    $result['message'] = 'Hoy recibiras un pedido';
                    //Si no existen pedidos realizados con fecha de entrega hoy se envia un exception
                } else {
                    $result['exception'] = 'No existen pedidos por entregar este día';
                }
                break;
                //Verificamos si existen pedidos que ya se hayan entregado
            case 'pedidosEntregadosCliente':
                //Si existen pedidos manda un mensaje de que se encontraron
                if ($result['dataset'] = $monitorear_pedidos->pedidosEntregadosCliente($_SESSION['idusuario_c'])) {
                    $result['estado'] = 1;
                    $result['message'] = 'Se te ha entregado pedidos';
                }
                //Si no existen pedidos entregados se envia un exception
                else {
                    $result['exception'] = 'No se te ha entregado un pedido';
                }
                break;
            case 'leerProducto':
                if (!$monitorear_pedidos->setIdProducto($_POST['ide'])) {
                    $result['exception'] = 'Producto incorrecto';
                } elseif ($result['dataset'] = $monitorear_pedidos->traerProducto()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Producto inexistente';
                }
                break;
            case 'agregarResenia':
                date_default_timezone_set('America/El_Salvador');
                $fecha_actual = date('Y-m-d h:i:s', time());
                $_POST = $monitorear_pedidos->validateForm($_POST);
                if (!$monitorear_pedidos->setIdProducto($_POST['idpedido'])) {
                    $result['exception'] = 'Producto incorrecto';
                } else if (!$monitorear_pedidos->setValoracion($_POST['valoracion'])) {
                    $result['exception'] = 'La valoración tiene un formato incorrecto';
                } else if (!$monitorear_pedidos->setIdUsuarioC($_SESSION['idusuario_c'])) {
                    $result['exception'] = 'El usuario cliente es incorrecto';
                } elseif (!$monitorear_pedidos->setResenia($_POST['resenia'])) {
                    $result['exception'] = 'La reseña cuenta con una longitud menor a 10 caracteres';
                } elseif ($monitorear_pedidos->agregarResenia($fecha_actual)) {
                    $result['estado'] = 1;
                    $result['message'] = '¡Muchas Gracias por tu reseña!';
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
