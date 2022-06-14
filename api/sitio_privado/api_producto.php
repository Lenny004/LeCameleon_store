<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/productos.php');

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
            if ($result['dataset'] = $producto->obtenerDatosProductos()) {
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
        case 'crearProducto':
            
            $_POST = $producto->validateForm($_POST);
            if (!$producto->setNombreP($_POST['nombre'])) {
                $result['exception'] = 'El nombre del producto posee caracteres no válidos';
            } elseif (!$producto->setDescripcion($_POST['descripcion'])) {
                $result['exception'] = 'Descripción contiene caracteres no válidos';
            } elseif (!$producto->setMaterial($_POST['material'])) {
                $result['exception'] = 'Los datos ingresados en materiales no son válidos';
            } elseif (!$producto->setTamanio($_POST['tamaño'])) {
                $result['exception'] = 'Los datos ingresados en tamaño contienen caracteres no válidos';
            } elseif (!$producto->setExistencias($_POST['existencias'])) {
                $result['exception'] = 'Ingrese un valor en existencias';
            } elseif (!$producto->setDescuento($_POST['descuento'])) {
                $result['exception'] = 'Ingrese un descuento de 0% a 100%';
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
            } elseif (!$producto->setSubcategoria($_POST['subc'])) {
                $result['exception'] = 'Sub-categoría incorrecta';
            } elseif (!is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                $result['exception'] = 'Seleccione una imagen';
            } elseif (!$producto->setImagen($_FILES['archivo'])) {
                $result['exception'] = $producto->getFileError();
            } elseif ($producto->crearProducto()) {
                $result['estado'] = 1;
                //Si se pudo crear el producto se guarda la imagen principal
                if ($producto->saveFile($_FILES['archivo'], $producto->getLink(), $producto->getImagen())) {
                    //Mandamos a traer el último ID
                    if ($producto->obtenerIdUltimoProducto()) {
                        $result['message'] = 'Producto creado correctamente';
                        //print_r($_FILES['archivos']);
                        if (is_uploaded_file($_FILES['archivos']['tmp_name'][0])) {
                            foreach ($_FILES['archivos'] as $filas) {
                                print_r($filas);
                            }
                        }
                    } else {
                        $result['exception'] = 'Producto creado correctamente. Pero no se pudo obtener el identificador del último producto ingresado';
                    }
                } else {
                    $result['message'] = 'Producto creado pero no se guardó la imagen';
                }
            } else {
                $result['exception'] = Database::getException();
            }
            break;
        case 'agregarImagenesExtras':
            $cantidad = count($_FILES["img_extra"]["tmp_name"]);
            printf($cantidad);
            //Por cada imagen se realizará un insert en la tabla de tbimagen_producto
            for ($i = 0; $i < $cantidad; $i++) {
                //Se guarda la imagen
                if (!$producto->setImagenes($_FILES['img_extra'][$i])) {
                    $result['exception'] = $producto->getFileError();
                } else {
                    //Agrega las imagenes extras ligadas al último producto
                    if ($producto->subirImagenesExtras) {
                        $result['estado'] = 1;
                        $result['message'] = 'Producto con imagenes extras creado correctamente';
                    } else {
                        $result['exception'] = 'Las imagenes extras no pudieron ser ingresadas';
                    }
                }
            }
            break;
        case 'obtenerUnProducto':
            if (!$producto->setIdProducto($_POST['ide'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $producto->obtenerUnProducto()) {
                $result['estado'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Producto inexistente';
            }
            break;
        case 'buscarProducto':
            $_POST = $producto->validateForm($_POST);
            if ($_POST['search'] == '') {
                $result['exception'] = 'Ingrese un valor para buscar';
            } elseif ($result['dataset'] = $producto->buscarProducto($_POST['search'])) {
                $result['estado'] = 1;
                $result['message'] = 'Valor encontrado';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;
        case 'actualizarProducto':
            $_POST = $producto->validateForm($_POST);
            if (!$producto->setIdProducto($_POST['ide'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$data = $producto->leerUnProducto()) {
                $result['exception'] = 'Producto inexistente';
            } elseif (!$producto->setNombreP($_POST['nombreM'])) {
                $result['exception'] = 'Nombre incorrecto';
            } elseif (!$producto->setDescripcion($_POST['descripcionM'])) {
                $result['exception'] = 'Descripción incorrecta';
            } elseif (!$producto->setMaterial($_POST['materialM'])) {
                $result['exception'] = 'Material incorrecto';
            } elseif (!$producto->setTamanio($_POST['tamañoM'])) {
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
            } elseif (!$producto->setSubcategoria($_POST['subcM'])) {
                $result['exception'] = 'Sub-categoría incorrecta';
            } elseif (!is_uploaded_file($_FILES['archivop']['tmp_name'])) {
                if ($producto->actualizarProducto($data['imagen_principal'])) {
                    $result['estado'] = 1;
                    $result['message'] = 'Producto modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
            } elseif (!$producto->setImagen($_FILES['archivop'])) {
                $result['exception'] = $producto->getFileError();
            } elseif ($producto->actualizarProducto($data['imagen_principal'])) {
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
            if (!$producto->setIdProducto($_POST['ide'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$data = $producto->leerUnProducto()) {
                $result['exception'] = 'Producto inexistente';
            } elseif ($producto->eliminarProducto()) {
                $result['estado'] = 1;
                if ($producto->deleteFile($producto->getLink(), $data['imagen_principal'])) {
                    $result['message'] = 'Producto eliminado correctamente';
                } else {
                    $result['message'] = 'Producto eliminado pero no se borró la imagen';
                }
            } else {
                $result['exception'] = Database::getException();
            }
            break;
        case 'obtenerReseniasProducto':
            if (!$producto->setIdProducto($_POST['idproducto'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $producto->reseniasProductos()) {
                $result['estado'] = 1;
            } else if (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Este producto no posee reseñas';
            }
            break;
        case 'actualizarValoracion':
            if (!$producto->setIdValoracion($_POST['idvaloracion'])) {
                $result['exception'] = 'Identificador de la valoración incorrecta';
            } elseif (!$producto->setEstadoValoracion($_POST['estado_valoracion'])) {
                $result['exception'] = 'Estado de la valoración incorrecta';
            } elseif ($result['dataset'] = $producto->actualizarReseniasProductos()) {
                $result['estado'] = 1;
                $result['message'] = 'Reseña actualizada correctamente';
            } else if (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Este producto no posee reseñas';
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
