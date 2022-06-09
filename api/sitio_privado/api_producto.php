<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/producto.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $producto = new Productos;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'message' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
    switch ($_GET['action']) {

        case 'readAll':
            if ($result['dataset'] = $producto->readAll()) {
                $result['estado'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay datos registrados';
            }
            break;
        case 'obtenerColor':
            if ($result['dataset'] = $producto->obtenerColor()) {
                $result['estado'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay datos registrados';
            }
            break;
        case 'obtenerEstadoProducto':
            if ($result['dataset'] = $producto->obtenerEstadoProducto()) {
                $result['estado'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay datos registrados';
            }
            break;
        case 'create':
            $_POST = $producto->validateForm($_POST);
            if (!$producto->setNombre($_POST['nombre'])) {
                $result['exception'] = 'Nombre incorrecto';
            } elseif (!$producto->setDescripcion($_POST['descripcion'])) {
                $result['exception'] = 'Descripción incorrecta';
            } elseif (!$producto->setMaterial($_POST['material'])) {
                $result['exception'] = 'Material incorrecto';
            } elseif (!$producto->setTamaño($_POST['tamaño'])) {
                $result['exception'] = 'Tamaño incorrecto';
            } elseif (!$producto->setExistencias($_POST['existencias'])) {
                $result['exception'] = 'Existencias incorrectas';
            } elseif (!$producto->setDescuento($_POST['descuento'])) {
                $result['exception'] = 'Descuento incorrecto';
            } elseif (!$producto->setPrecio($_POST['precio'])) {
                $result['exception'] = 'Precio incorrecto';
            } elseif (!isset($_POST['color'])) {
                $result['exception'] = 'Seleccione un color';
            } elseif (!$producto->setColor($_POST['color'])) {
                $result['exception'] = 'Color incorrecto';
            } elseif (!isset($_POST['marca'])) {
                $result['exception'] = 'Seleccione una marca';
            } elseif (!$producto->setMarca($_POST['marca'])) {
                $result['exception'] = 'Marca incorrecta';
            } elseif (!isset($_POST['distribuidor'])) {
                $result['exception'] = 'Seleccione un distribuidor';
            } elseif (!$producto->setDistribuidor($_POST['distribuidor'])) {
                $result['exception'] = 'Distribuidor incorrecto';
            } elseif (!isset($_POST['estado'])) {
                $result['exception'] = 'Seleccione un Estado';
            } elseif (!$producto->setEstado($_POST['estado'])) {
                $result['exception'] = 'estado incorrecto';
            } elseif (!isset($_POST['subc'])) {
                $result['exception'] = 'Seleccione una sub-categoría';
            } elseif (!$producto->setSubc($_POST['subc'])) {
                $result['exception'] = 'Sub-categoría incorrecta';
            } elseif (!is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                $result['exception'] = 'Seleccione una imagen';
            } elseif (!$producto->setImagen($_FILES['archivo'])) {
                $result['exception'] = $producto->getFileError();
            } elseif ($producto->createRow()) {
                $result['estado'] = 1;
                if ($producto->saveFile($_FILES['archivo'], $producto->getLink(), $producto->getImagen())) {
                    $result['message'] = 'Producto creado correctamente';
                } else {
                    $result['message'] = 'Producto creado pero no se guardó la imagen';
                }
            } else {
                $result['exception'] = Database::getException();
            }
            break;

        case 'readOne':
            if (!$producto->setId($_POST['ide'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $producto->readOne()) {
                $result['estado'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Producto inexistente';
            }
            break;

        case 'update':
            $_POST = $producto->validateForm($_POST);
            if (!$producto->setId($_POST['ide'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$data = $producto->readOne()) {
                $result['exception'] = 'Producto inexistente';
            } elseif (!$producto->setNombre($_POST['nombreM'])) {
                $result['exception'] = 'Nombre incorrecto';
            } elseif (!$producto->setDescripcion($_POST['descripcionM'])) {
                $result['exception'] = 'Descripción incorrecta';
            } elseif (!$producto->setMaterial($_POST['materialM'])) {
                $result['exception'] = 'Material incorrecto';
            } elseif (!$producto->setTamaño($_POST['tamañoM'])) {
                $result['exception'] = 'Tamaño incorrecto';
            } elseif (!$producto->setExistencias($_POST['existenciasM'])) {
                $result['exception'] = 'Existencias incorrectas';
            } elseif (!$producto->setDescuento($_POST['descuentoM'])) {
                $result['exception'] = 'Descuento incorrecto';
            } elseif (!$producto->setPrecio($_POST['precioM'])) {
                $result['exception'] = 'Precio incorrecto';
            } elseif (!$producto->setColor($_POST['colorM'])) {
                $result['exception'] = 'Color incorrecto';
            } elseif (!$producto->setMarca($_POST['marcaM'])) {
                $result['exception'] = 'Marca incorrecta';
            } elseif (!$producto->setDistribuidor($_POST['distribuidorM'])) {
                $result['exception'] = 'Distribuidor incorrecto';
            } elseif (!$producto->setEstado($_POST['estadoM'])) {
                $result['exception'] = 'Estado incorrecto';
            } elseif (!$producto->setSubc($_POST['subcM'])) {
                $result['exception'] = 'Sub-categoría incorrecta';
            } elseif (!is_uploaded_file($_FILES['archivop']['tmp_name'])) {
                if ($producto->updateRow($data['imagen'])) {
                    $result['estado'] = 1;
                    $result['message'] = 'Producto modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
            } elseif (!$producto->setImagen($_FILES['archivop'])) {
                $result['exception'] = $producto->getFileError();
            } elseif ($producto->updateRow($data['imagen'])) {
                $result['estado'] = 1;
                if ($producto->saveFile($_FILES['archivop'], $producto->getLink(), $producto->getImagen())) {
                    $result['message'] = 'Producto modificado correctamente';
                } else {
                    $result['message'] = 'Producto modificado pero no se guardó la imagen';
                }
            } else {
                $result['exception'] = Database::getException();
            }
            break;
        case 'delete':
            if (!$producto->setId($_POST['ide'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$data = $producto->readOne()) {
                $result['exception'] = 'Producto inexistente';
            } elseif ($producto->deleteRow()) {
                $result['estado'] = 1;
                if ($producto->deleteFile($producto->getLink(), $data['imagen'])) {
                    $result['message'] = 'Producto eliminado correctamente';
                } else {
                    $result['message'] = 'Producto eliminado pero no se borró la imagen';
                }
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
