<?php

//Se importan los archivos necesarios
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/inventario_entrega.php');

if (isset($_GET['action'])) {
    //Se crea o reiniciar una sesión
    session_start();
    //Se instancia la clase correspondiente en la variable
    $inventario = new inventario_entrega;
    //Se crea un vector con los datos para crear el mensaje (Se devuelve al controllador)
    $result = array('estado' => 0, 'message' => null, 'dataset' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idusuario_e'])) {
        //Se escoge el proceso que se ejecutará en el modelo
        switch ($_GET['action']) {
                //Cargar los datos de los productos registrados anteriormente en la tabla de inventario
            case 'readAll':
                if ($result['dataset'] = $inventario->cargarTabla()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay productos registrados en inventario';
                }
                break;
                //Función para cargar los productos en el select
            case 'cargarProductos':
                if ($result['dataset'] = $inventario->cargarProductos()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos';
                }
                break;
                //Función para cargar los proveedores en el select
            case 'cargarProveedores':
                if ($result['dataset'] = $inventario->cargarProveedores()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos';
                }
                break;
                //Función para cargar los productos en select según el buscador
            case 'leerProductosBuscador':
                $_POST = $inventario->validateForm($_POST);
                if ($_POST['buscador_registro'] == '') {
                    $result['exception'] = 'Ingrese un valor para buscar';
                } elseif ($result['dataset'] = $inventario->leerProductosBuscador($_POST['buscador_registro'])) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos';
                }
                break;
            case 'crear_entrega':
                $_POST = $inventario->validateForm($_POST);
                if (!$inventario->set_id_producto($_POST['id_producto'])) {
                    $result['exception'] = 'Hay problemas el producto';
                } elseif (!$inventario->set_cantidad($_POST['cantidad_formulario'])) {
                    $result['exception'] = 'Hay problemas al cargar la cantidad';
                } elseif (!$inventario->set_precio($_POST['precio'])) {
                    $result['exception'] = 'Hay problemas al cargar el precio';
                } elseif (!$inventario->set_fecha_entrega($_POST['fecha_entrega'])) {
                    $result['exception'] = 'Hay problemas al cargar la fecha de entrega';
                } elseif (!$inventario->set_fecha_inicio($_POST['fecha_inicio'])) {
                    $result['exception'] = 'Hay problemas al cargar la fecha de inicio';
                } elseif ($result['dataset'] = $inventario->crear_entrega()) {
                    $result['estado'] = 1;
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'seleccionar':
                $_POST = $inventario->validateForm($_POST);
                if ($result['dataset'] = $inventario->buscador_seleccionador($_POST['buscador'])) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos';
                }
                break;
            case 'actualizar_entrega':
                $_POST = $inventario->validateForm($_POST);
                if (!$inventario->set_id_producto($_POST['id_producto'])) {
                    $result['exception'] = 'Hay problemas el producto';
                } elseif (!$inventario->set_id_inventario($_POST['id_inventario'])) {
                    $result['exception'] = 'Hay problemas al cargar el identificador';
                } elseif (!$inventario->set_cantidad($_POST['cantidad_formulario'])) {
                    $result['exception'] = 'Hay problemas al cargar la cantidad';
                } elseif (!$inventario->set_precio($_POST['precio'])) {
                    $result['exception'] = 'Hay problemas al cargar el precio';
                } elseif (!$inventario->set_fecha_entrega($_POST['fecha_entrega'])) {
                    $result['exception'] = 'Hay problemas al cargar la fecha de entrega';
                } elseif (!$inventario->set_fecha_inicio($_POST['fecha_inicio'])) {
                    $result['exception'] = 'Hay problemas al cargar la fecha de inicio';
                } elseif ($result['dataset'] = $inventario->actualizar_entrega()) {
                    $result['estado'] = 1;
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible dentro de la sesión';
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
