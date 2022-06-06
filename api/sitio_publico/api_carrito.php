<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/carrito.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedido = new Carrito;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null, 'cliente' => null);
    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    if (isset($_SESSION['idusuario_c'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
            case 'crearDetalle':
                $_POST = $pedido->validateForm($_POST);
                if (!$pedido->ordenIniciada()) {
                    $result['exception'] = 'Ocurrió un problema al obtener el pedido';
                } elseif (!$pedido->setProducto($_POST['idproducto'])) {
                    $result['exception'] = 'Producto incorrecto';
                } elseif (!$pedido->setTotalProducto($_POST['precio_total'])) {
                    $result['exception'] = 'El precio total del producto es incorrecto';
                } elseif (!$pedido->setPrecioAProducto($_POST['precio_actual'])) {
                    $result['exception'] = 'El precio unitario del producto es incorrecto';
                } elseif (!$pedido->setCantidad($_POST['cantidad'])) {
                    $result['exception'] = 'La cantidad de productos seleccionados es incorrecta';
                } else if ($pedido->crearDetalleFactura()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Producto agregado correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al agregar el producto al carrito';
                }
                break;
            case 'leerDetallePedido':
                if (!$pedido->ordenIniciada()) {
                    $result['exception'] = 'Debe agregar un producto al carrito';
                } elseif ($result['dataset'] = $pedido->leerDetallePedido()) {
                    $result['estado'] = 1;
                    $_SESSION['idfactura'] = $pedido->getIdFactura();
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No tiene productos en el carrito';
                }
                break;
            case 'updateDetail':
                $_POST = $pedido->validateForm($_POST);
                if (!$pedido->setIdDetalle($_POST['id_detalle'])) {
                    $result['exception'] = 'Detalle incorrecto';
                } elseif (!$pedido->setCantidad($_POST['cantidad'])) {
                    $result['exception'] = 'Cantidad incorrecta';
                } elseif ($pedido->updateDetail()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Cantidad modificada correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al modificar la cantidad';
                }
                break;
            case 'eliminarDetalle':
                if (!$pedido->setIdDetalleF($_POST['id_detalle'])) {
                    $result['exception'] = 'Detalle incorrecto';
                } elseif ($pedido->eliminarDetalle()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Producto removido correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al remover el producto';
                }
                break;
            case 'eliminarDetalles':
                if ($pedido->eliminarDetalles()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Productos removidos correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al remover los productos';
                }
                break;
            case 'finishOrder':
                if ($pedido->finishOrder()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Pedido finalizado correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al finalizar el pedido';
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando un cliente no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'crearDetalle':
                $result['exception'] = 'Debe iniciar sesión para agregar el producto al carrito';
                break;
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
        }
    }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
