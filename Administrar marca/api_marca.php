<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/admin_marca.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $admin_marca = new Admin_marca;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'message' => null, 'exception' => null, 'dataset' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $admin_marca->mostrar_datos_tabla()) {
                    $result['estado'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            
                case 'create':
                    $_POST = $admin_marca->validateForm($_POST);
                    if (!$admin_marca->setnombreMarca($_POST['marca_agregar'])) {
                        $result['exception'] = 'Nombre incorrecto';
                    } elseif (!is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                        $result['exception'] = 'Seleccione una imagen';
                    } elseif (!$admin_marca->setimagenMarca($_FILES['archivo'])) {
                        $result['exception'] = $admin_marca->getFileError();
                    } elseif ($admin_marca->createRow()) {
                        $result['estado'] = 1;
                        if ($admin_marca->saveFile($_FILES['archivo'], $admin_marca->getrutaImagen(), $admin_marca->getimagenMarca())) {
                            $result['message'] = 'Categoría creada correctamente';
                        } else {
                            $result['message'] = 'Categoría creada pero no se guardó la imagen';
                        }
                    } else {
                        $result['exception'] = Database::getException();
                    }

                    break;
                case 'readOne':
                    if (!$admin_marca->setidMarca($_POST['ide'])) {
                        $result['exception'] = 'Categoría incorrecta';
                    } elseif ($result['dataset'] = $admin_marca->readOne()) {
                        $result['estado'] = 1;
                    } elseif (Database::getException()) {
                        $result['exception'] = Database::getException();
                    } else {
                        $result['exception'] = 'Categoría inexistente';
                    }
                    break;
                
                case 'update':
                    $_POST = $admin_marca->validateForm($_POST);
                    if (!$admin_marca->setidMarca($_POST['ide'])) {
                        $result['exception'] = 'Marca incorrecta';
                    } elseif (!$data = $admin_marca->readOne()) {
                        $result['exception'] = 'Marca inexistente';
                    } elseif (!$admin_marca->setnombreMarca($_POST['marca_modificar'])) {
                        $result['exception'] = 'Nombre incorrecto';
                    } elseif (!is_uploaded_file($_FILES['archivop']['tmp_name'])) {
                        if ($admin_marca->updateRow($data['imagen_marca'])) {
                            $result['estado'] = 1;
                            $result['message'] = 'Marca modificada correctamente';
                        } else {
                            $result['exception'] = Database::getException();
                        }
                    } elseif (!$admin_marca->setimagenMarca($_FILES['archivop'])) {
                        $result['exception'] = $admin_marca->getFileError();
                    } elseif ($admin_marca->updateRow($data['imagen_marca'])) {
                        $result['estado'] = 1;
                        if ($admin_marca->saveFile($_FILES['archivop'], $admin_marca->getrutaImagen(), $admin_marca->getimagenMarca())) {
                            $result['message'] = 'Categoría modificada correctamente';
                        } else {
                            $result['message'] = 'Categoría modificada pero no se guardó la imagen';
                        }
                    } else {
                        $result['exception'] = Database::getException();
                    }
                    break;

                    case 'readOneE':
                        if (!$admin_marca->setidMarca($_POST['idd'])) {
                            $result['exception'] = 'Categoría incorrecta';
                        } elseif ($result['dataset'] = $admin_marca->readOne()) {
                            $result['estado'] = 1;
                        } elseif (Database::getException()) {
                            $result['exception'] = Database::getException();
                        } else {
                            $result['exception'] = 'Categoría inexistente';
                        }
                        break;

                case 'delete':
                    if (!$admin_marca->setidMarca($_POST['idd'])) {
                        $result['exception'] = 'Categoría incorrecta';
                    } elseif (!$data = $admin_marca->readOne()) {
                        $result['exception'] = 'Categoría inexistente';
                    } elseif ($admin_marca->deleteRow()) {
                        $result['estado'] = 1;
                        if ($admin_marca->deleteFile($admin_marca->getLink(), $data['imagen_marca'])) {
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
} 
