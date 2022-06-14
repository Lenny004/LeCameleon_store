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
    $result = array('status' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'idcategoria' => null, 'promedio_valoraciones' => null, 'total_resenias' => null);
    // Se compara la acción a realizar según la petición del controlador.
    switch ($_GET['action']) {
        case 'readAll':
            if ($result['dataset'] = $categoria->mostrarDatosTabla()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen categorías para mostrar';
            }
            break;
        case 'readAllS':
            if ($result['dataset'] = $subcategorias->mostrarDatosTabla()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen categorías para mostrar';
            }
            break;
        case 'readProductosCategoria':
            if (!$producto->setIdCategoria($_POST['idcategoria_producto'])) {
                $result['exception'] = 'Categoría incorrecta';
            } elseif ($result['dataset'] = $producto->readProductosCategoria($_POST['idcategoria_producto'], '')) {
                $result['status'] = 1;
                $result['idcategoria'] = $_POST['idcategoria_producto'];
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos para mostrar';
            }
            break;
        case 'readProductossubCategoria':
            if (!$producto->setIdSubcategoria($_POST['idsubcategoria_producto'])) {
                $result['exception'] = 'Categoría incorrecta';
            } elseif ($result['dataset'] = $producto->readProductossubCategoria($_POST['idsubcategoria_producto'], '')) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos para mostrar';
            }
            break;
            //Buscador pero por categoria
        case 'search':
            $_POST = $producto->validateForm($_POST);
            if ($_POST['search'] == '') {
                $result['exception'] = 'Ingrese un valor para buscar';
            } elseif ($result['dataset'] = $producto->readProductosCategoria($_POST['ide'], $_POST['search'])) {
                $result['status'] = 1;
                $result['message'] = 'Valor encontrado';
                echo ($result['idcategoria']);
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = $result['idcategoria'];
            }
            break;
        case 'searchS':
            $_POST = $producto->validateForm($_POST);
            if ($_POST['search'] == '') {
                $result['exception'] = 'Ingrese un valor para buscar';
            } elseif ($result['dataset'] = $producto->readProductossubCategoria($_POST['ide'], $_POST['search'])) {
                $result['status'] = 1;
                $result['message'] = 'Valor encontrado';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;
            //Leer los datos de un producto para visualizarlo
        case 'leerUnProducto':
            if (!$producto->setIdProducto($_POST['id_producto'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $producto->leerUnProducto()) {
                //Obtener el promedio de valoraciones entre clientes de un producto
                if ($producto->promedioValoraciones()) {
                    $result['promedio_valoraciones'] = $producto->getPromedio();
                    //Obtener el total de reseñas realizadas al producto
                    if ($producto->totalResenias()) {
                        $result['total_resenias'] = $producto->getTotalResenia();
                    }
                } else {
                    $result['exception'] = 'El producto no posee valoraciones por el momento';
                }
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Producto inexistente';
            }
            break;
            //Obtener las reseñas escritas por los usuarios y que están ligados al producto visto
        case 'resenias':
            if (!$producto->setIdProducto($_POST['id_producto'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $producto->valoracionesProducto()) {
                $result['status'] = 1;
            } else if (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Este producto no posee reseñas';
            }
            break;
        case 'Descuento':
            if ($result['dataset'] = $producto->Descuento()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos en descuento para mostrar';
            }
            break;
        case 'searchO':
            $_POST = $producto->validateForm($_POST);
            if ($_POST['search'] == '') {
                $result['exception'] = 'Ingrese un valor para buscar';
            } elseif ($result['dataset'] = $producto->SearchOferta($_POST['search'])) {
                $result['status'] = 1;
                $result['message'] = 'Valor encontrado';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;
        case 'rangoSubcategoria':
            if (!$producto->setIdSubcategoria($_POST['id'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$producto->setMin($_POST['min'])) {
                $result['exception'] = 'Ingrese un valor minimo valido para buscar';
            } elseif (!$producto->setMax($_POST['max'])) {
                $result['exception'] = 'Ingrese un valor máximo valido para buscar';
            } elseif ($result['dataset'] = $producto->RangoProductoSubcategoria()) {
                $result['status'] = 1;
                $result['message'] = 'Se han encontrado productos';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos con esos precios';
            }
            break;
        case 'rangoCategoria':
            if (!$producto->setIdCategoria($_POST['id'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$producto->setMin($_POST['min'])) {
                $result['exception'] = 'Ingrese un valor minimo valido para buscar';
            } elseif (!$producto->setMax($_POST['max'])) {
                $result['exception'] = 'Ingrese un valor máximo valido para buscar';
            } elseif ($result['dataset'] = $producto->RangoProductoCategoria()) {
                $result['status'] = 1;
                $result['message'] = 'Se han encontrado productos';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos con esos precios';
            }
            break;
        case 'rangoMaxCategoria':
            if (!$producto->setIdCategoria($_POST['id'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $producto->RangoMaxProductoCategoria()) {
                $result['status'] = 1;
                $result['message'] = 'Se han encontrado productos';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No existen productos con esos precios';
            }
            break;
        case 'rangoMaxProductoSubcategoria':
            if (!$producto->setIdSubcategoria($_POST['id'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $producto->RangoMaxProductoSubcategoria()) {
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
