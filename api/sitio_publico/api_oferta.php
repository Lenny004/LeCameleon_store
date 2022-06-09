<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/categorias.php');
require_once('../modelo/subcategoria.php');
require_once('../modelo/productos.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se instancian las clases correspondientes.
    $categoria = new Categorias;
    $producto = new Productos;
    $subcategorias = new Subcategoria;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'exception' => null);
    // Se compara la acción a realizar según la petición del controlador.
    switch ($_GET['action']) {
        case 'rangop':
            if ($result['dataset'] = $producto->Rangop()) {
                $result['status'] = 1;
                $result['message'] = 'Se han encontrado productos';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos con esos precios';
            }
            break; 

        case 'rangos':
            if ($result['dataset'] = $producto->Rangos()) {
                $result['status'] = 1;
                $result['message'] = 'Se han encontrado productos';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos con esos precios';
            }
            break; 
        
        case 'rangot':
            if ($result['dataset'] = $producto->Rangot()) {
                $result['status'] = 1;
                $result['message'] = 'Se han encontrado productos';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos con esos precios';
            }
            break;
            
         case 'rangoc':
            if ($result['dataset'] = $producto->Rangoc()) {
                $result['status'] = 1;
                $result['message'] = 'Se han encontrado productos';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos con esos precios';
            }
            break;
        default:
            $result['exception'] = 'Acción no disponible';
    }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
