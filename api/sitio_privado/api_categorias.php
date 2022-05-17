<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/categorias.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $categoria = new Categorias;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'message' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $categoria->readAll()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;

            //se hace la operación create 
                case 'create':
                    $_POST = $categoria->validateForm($_POST);
                    if (!$categoria->setCategoria($_POST['categoria_agregar'])) {
                        $result['exception'] = 'Nombre incorrecto';
                    } elseif (!is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                        $result['exception'] = 'Seleccione una imagen';
                    } elseif (!$categoria->setImagen($_FILES['archivo'])) {
                        $result['exception'] = $categoria->getFileError();
                    } elseif ($categoria->createRow()) {
                        $result['estado'] = 1;
                        if ($categoria->saveFile($_FILES['archivo'], $categoria->getLink(), $categoria->getImagen())) {
                            $result['message'] = 'Categoría creada correctamente';
                        } else {
                            $result['message'] = 'Categoría creada pero no se guardó la imagen';
                        }
                    } else {
                        $result['exception'] = Database::getException();
                    }
                    break;
                case 'readOne':
                    if (!$categoria->setId($_POST['ide'])) {
                        $result['exception'] = 'Categoría incorrecta';
                    } elseif ($result['dataset'] = $categoria->readOne()) {
                        $result['estado'] = 1;
                    } elseif (Database::getException()) {
                        $result['exception'] = Database::getException();
                    } else {
                        $result['exception'] = 'Categoría inexistente';
                    }
                    break;
                case 'update':
                    $_POST = $categoria->validateForm($_POST);
                    if (!$categoria->setId($_POST['ide'])) {
                        $result['exception'] = 'Categoría incorrecta';
                    } elseif (!$data = $categoria->readOne()) {
                        $result['exception'] = 'Categoría inexistente';
                    } elseif (!$categoria->setCategoria($_POST['categoria_modificar'])) {
                        $result['exception'] = 'Nombre incorrecto';
                    } elseif (!is_uploaded_file($_FILES['archivop']['tmp_name'])) {
                        if ($categoria->updateRow($data['imagen_categoria'])) {
                            $result['estado'] = 1;
                            $result['message'] = 'Categoría modificada correctamente';
                        } else {
                            $result['exception'] = Database::getException();
                        }
                    } elseif (!$categoria->setImagen($_FILES['archivop'])) {
                        $result['exception'] = $categoria->getFileError();
                    } elseif ($categoria->updateRow($data['imagen_categoria'])) {
                        $result['estado'] = 1;
                        if ($categoria->saveFile($_FILES['archivop'], $categoria->getLink(), $categoria->getImagen())) {
                            $result['message'] = 'Categoría modificada correctamente';
                        } else {
                            $result['message'] = 'Categoría modificada pero no se guardó la imagen';
                        }
                    } else {
                        $result['exception'] = Database::getException();
                    }
                    break;
                case 'readOneE':
                        if (!$categoria->setId($_POST['idd'])) {
                            $result['exception'] = 'Categoría incorrecta';
                        } elseif ($result['dataset'] = $categoria->readOne()) {
                            $result['status'] = 1;
                        } elseif (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'Categoría inexistente';
                        }
                        break;
                case 'delete':
                        if (!$categoria->setId($_POST['idd'])) {
                            $result['exception'] = 'Categoría incorrecta';
                        } elseif (!$data = $categoria->readOneE()) {
                            $result['exception'] = 'Categoría inexistente';
                        } elseif ($categoria->deleteRow()) {
                            $result['estado'] = 1;
                            if ($categoria->deleteFile($categoria->getLink(), $data['imagen_categoria'])) {
                                $result['message'] = 'Categoría eliminada correctamente';
                            } else {
                                $result['message'] = 'Categoría eliminada pero no se borró la imagen';
                            }
                        } else {
                            $result['exception'] = Database::getException();
                        }
                    break;
                default:
                        break;
            $result['exception'] = 'Acción no disponible dentro de la sesión';
        }
        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('content-type: application/json; charset=utf-8');
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    }
