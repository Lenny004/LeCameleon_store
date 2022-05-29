<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/subcategoria.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $subcategorias = new Subcategoria;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'message' => null, 'exception' => null, 'dataset' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idusuario_e'])) {    
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                    case 'readAll':
                        if ($result['dataset'] = $subcategorias->mostrarDatosTabla()) {
                            $result['estado'] = 1;
                        } elseif (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'No hay datos registrados';
                        }
                        break;
                    case 'crearSubcategoria':
                        $_POST = $subcategorias->validateForm($_POST);
                        if (!$subcategorias->setsubCategoriaProducto($_POST['subcategoria_agregar'])) {
                            $result['exception'] = 'Nombre incorrecto, el nombre de la subcategoria solo puede contener letras';
                        }elseif (!isset($_POST['select_categoria'])) {
                            $result['exception'] = 'Seleccione una categoria';
                        } elseif (!$subcategorias->setidCategoriaProducto($_POST['select_categoria'])) {
                            $result['exception'] = 'categoria incorrecto';
                        }elseif (!is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                            $result['exception'] = 'Seleccione una imagen';
                        } elseif (!$subcategorias->setimagenSubCategoria($_FILES['archivo'])) {
                            $result['exception'] = $subcategorias->getFileError();
                        } elseif ($subcategorias->crearSubcategoria()) {
                            $result['estado'] = 1;
                            if ($subcategorias->saveFile($_FILES['archivo'], $subcategorias->getrutaImagenS(), $subcategorias->getimagenSubCategoria())) {
                                $result['message'] = 'Subcategoría modificada correctamente';
                            } else {
                                $result['message'] = 'Subcategoría modificada pero no se guardó la imagen';
                            }
                        } else {
                            $result['exception'] = Database::getException();
                        }
                        break;
                    case 'obtener_categoria':
                        if ($result['dataset'] = $subcategorias->obtener_producto()) {
                            $result['estado'] = 1;
                        } elseif (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'No hay datos registrados';
                        }
                        break;
                    case 'readOne':
                        if (!$subcategorias->setidSubCategoriaProducto($_POST['ide'])) {
                            $result['exception'] = 'Subcategoria incorrecta';
                        } elseif ($result['dataset'] = $subcategorias->readOne()) {
                            $result['estado'] = 1;
                        } elseif (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'Subcategoria inexistente';
                        }
                        break;
                    case 'search':
                        $_POST = $subcategorias->validateForm($_POST);
                        if ($_POST['buscador_input'] == '') {
                            $result['exception'] = 'Ingrese el nombre de una marca para buscar';
                        } elseif ($result['dataset'] = $subcategorias->buscarSubcategorias($_POST['buscador_input'])) {
                            $result['estado'] = 1;
                            $result['message'] = 'Subcategoria encontrada';
                        } elseif (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'No hay coincidencias';
                        }
                        break;
                    case 'update':
                        $_POST = $subcategorias->validateForm($_POST);
                        if (!$subcategorias->setidSubCategoriaProducto($_POST['ide'])) {
                            $result['exception'] = 'Subcategoria incorrecta xdddd';
                        } elseif (!$data = $subcategorias->readOne()) {
                            $result['exception'] = 'Subcategoria inexistente';
                        } elseif (!$subcategorias->setsubCategoriaProducto($_POST['subcategoria_modificar'])) {
                            $result['exception'] = 'Nombre incorrecto';
                        } elseif (!$subcategorias->setidCategoriaProducto($_POST['select_subcategoria'])) {
                            $result['exception'] = 'Categoria incorrecto';
                        }elseif (!is_uploaded_file($_FILES['archivop']['tmp_name'])) {
                            if ($subcategorias->actualizarSubcategoria($data['imagen_subcategoria'])) {
                                $result['estado'] = 1;
                                $result['message'] = 'Categoría modificada correctamente';
                            } else {
                                $result['exception'] = Database::getException();
                            }
                        } elseif (!$subcategorias->setimagenSubCategoria($_FILES['archivop'])) {
                            $result['exception'] = $subcategorias->getFileError();
                        } elseif ($subcategorias->actualizarSubcategoria($data['imagen_subcategoria'])) {
                            $result['estado'] = 1;
                            if ($subcategorias->saveFile($_FILES['archivop'], $subcategorias->getrutaImagenS(), $subcategorias->getimagenSubCategoria())) {
                                $result['message'] = 'Categoría modificada correctamente';
                            } else {
                                $result['message'] = 'Categoría modificada pero no se guardó la imagen';
                            }
                        } else {
                            $result['exception'] = Database::getException();
                        }
                        break;
                    case 'delete':
                        if (!$subcategorias->setidSubCategoriaProducto($_POST['ide'])) {
                            $result['exception'] = 'Subcategoria incorrecta';
                        } elseif (!$data = $subcategorias->readOne()) {
                            $result['exception'] = 'Categoría inexistente';
                        } elseif ($subcategorias->eliminarSubcategoria()) {
                            $result['estado'] = 1;
                            if ($subcategorias->deleteFile($subcategorias->getrutaImagenS(), $data['imagen_subcategoria'])) {
                                $result['message'] = 'Categoría eliminada correctamente';
                            } else {
                                $result['message'] = 'Categoría eliminada pero no se borró la imagen';
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
    } else {
        print(json_encode('Acceso denegado'));
    }    
} else {
    print(json_encode('Recurso no disponible'));
}