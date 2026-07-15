<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/login_publico.php');
require_once('../modelo/carrito.php');
require_once('../modelo/registro_usuario_cliente.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedido = new Carrito;
    $registro = new RegistroUsuariosClientes;
    $usuario_cliente = new UsuarioCliente;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null, 'cliente' => null, 'nombre' => null, 'apellido' => null, 'telefono' => null, 'direccion' => null, 'dui' => null, 'correo' => null, 'contra' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idusuario_c'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando el administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'cerrarSesion':
                unset($_SESSION['idusuario_c']);
                $result['estado'] = 1;
                $result['message'] = 'Sesión eliminada correctamente';
                break;
            case 'obtenerUsuario':
                if (isset($_SESSION['usuario_c'])) {
                    $result['estado'] = 1;
                    $result['session'] = 1;
                    $result['username'] = $_SESSION['usuario_c'];
                    $result['contra'] = $_SESSION['contrasena_c'];
                    $result['nombre'] = $_SESSION['nombre_cliente'];
                    $result['apellido'] = $_SESSION['apellido_cliente'];
                    $result['telefono'] = $_SESSION['telefono_cliente'];
                    $result['direccion'] = $_SESSION['direccion_cliente'];
                    $result['dui'] = $_SESSION['dui_cliente'];
                    $result['correo'] = $_SESSION['correo_cliente'];
                    $result['cliente'] = $_SESSION['nombre_cliente'] . " " . $_SESSION['apellido_cliente'];
                    if ($result['dataset'] = $pedido->totalProductosDetalle()) {
                        $result['estado'] = 1;
                        $result['message'] = 'Datos del usuario obtenidos';
                    } elseif (Database::getException()) {
                        $result['exception'] = Database::getException();
                    } else {
                        $result['exception'] = 'Ocurrió un problema al obtener los datos del usuario';
                    }
                } else {
                    $result['exception'] = 'Nombre de usuario indefinido';
                }
                break;
            case 'actualizarPerfil':
                $_POST = $registro->validateForm($_POST);
                if (!$registro->setTelefono($_POST['telefono'])) {
                    $result['exception'] = 'El número de teléfono ingresado tiene un formato incorrecto. Recuerde el formato de teléfono "0000-0000"';
                } else if (!$registro->setDireccion($_POST['direccion_cliente'])) {
                    $result['exception'] = 'La dirección ingresada contiene carácteres no válidos.';
                } else if (!$registro->setCorreoCliente($_POST['correo_cliente'])) {
                    $result['exception'] = 'El correo ingresado no tiene un formato válido. Intentelo de nuevo';
                } elseif ($_POST['contra'] != $_POST['confirmar_contra']) {
                    $result['exception'] = 'Las contraseñas ingresadas son diferentes, compruebe que ha ingresado correctamente la contraseña en el apartado de confirmar contraseña';
                } else if (!$registro->setContraCliente($_POST['contra'])) {
                    $result['exception'] = $registro->getPasswordError();
                } else if ($registro->actualizarPerfil($_SESSION['idusuario_c'])) {
                    $_SESSION['telefono_cliente'] = $registro->getTelefono();
                    $_SESSION['direccion_cliente'] = $registro->getDireccion();
                    $_SESSION['correo_cliente'] = $registro->getCorreo();
                    $_SESSION['contrasena_c'] = $registro->getContra();
                    $result['estado'] = 1;
                    $result['message'] = 'Usuario modificado correctamente';
                } else if (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Nombre de usuario indefinido';
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
                break;
        }
    } else {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
                //Proceso para ingresar en el login
            case 'logIn':
                $_POST = $usuario_cliente->validateForm($_POST);
                //Si el usuario no es correcto manda una alerta
                if (!$usuario_cliente->validarUsuarioCliente($_POST['usuario_login'])) {
                    $result['exception'] = 'El usuario cliente ingresado no existe';
                    //Si el usuario y la contraseña es la correcta y el número de intentos es menor a 5 y un estado activo manda una alerta
                } else if ($usuario_cliente->validarContraUsuarioCliente($_POST['password_login']) && $usuario_cliente->getIntento() < 5 && $usuario_cliente->getEstadoU() == 1) {
                    $result['estado'] = 1;
                    $result['message'] = 'Autenticacion correcta';
                    $_SESSION['idusuario_c'] = $usuario_cliente->getIdUsuarioC();
                    $_SESSION['usuario_c'] = $usuario_cliente->getUsuario();
                    $_SESSION['contrasena_c'] = $usuario_cliente->getContra();
                    $_SESSION['nombre_cliente'] = $usuario_cliente->getNombreCliente();
                    $_SESSION['apellido_cliente'] = $usuario_cliente->getApellidoCliente();
                    $_SESSION['telefono_cliente'] = $usuario_cliente->getTelefono();
                    $_SESSION['direccion_cliente'] = $usuario_cliente->getDireccion();
                    $_SESSION['correo_cliente'] = $usuario_cliente->getCorreo();
                    $_SESSION['dui_cliente'] = $usuario_cliente->getDUICliente();
                }
                //Si el usuario es el correcto pero la contra es incorrecta
                else {
                    //Si el número de intentos es menor a 5
                    if ($usuario_cliente->getIntento() < 5) {
                        //Validamos si tiene intentos pero si posee un estado 2 (inactivo) es orque el empleado ha sido deshabilitado
                        if ($usuario_cliente->getEstadoU() == 2) {
                            $result['exception'] = 'El usuario esta inactivo';
                        }
                        //Se agrega un intento al usuario que está ingresando
                        else if ($usuario_cliente->intentosUsuarioCliente()) {
                            $result['exception'] = 'Contraseña incorrecta. Tienes ' . (5 - $usuario_cliente->getIntento()) . ' intentos restantes';
                        }
                        //Si ocurre un fallo al actualizar
                        else {
                            $result['exception'] = 'No se pudo actualizar los intentos';
                        }
                    }
                    //Si el número de intentos es igual a 5
                    else if ($usuario_cliente->getIntento() == 5) {
                        //Verificaremos si no existe una fecha de bloqueo para poder agregar una
                        if ($usuario_cliente->getHoraInactivacion() == null && $usuario_cliente->getHoraActivacion() == null) {
                            //Crearemos los valores de hora de bloqueo y desbloqueo
                            date_default_timezone_set('America/El_Salvador');
                            $hora_inactivacion = date('Y-m-d h:i:s', time());
                            $hora_actual = date('Y-m-d h:i:s', time());
                            //La hora de activación es la fecha y hora actual + 5 min que tendrá que esperar para volver a tener 5 intentos
                            $hora_activacion = date('Y-m-d h:i:s', strtotime($hora_actual) + 300);
                            //Mandamos la hora de bloqueo, la hora de desbloqueo y el 2 que es para poner estado inactiva a la cuenta
                            if ($usuario_cliente->registrarHoraIntento($hora_inactivacion, $hora_activacion, 2)) {
                                $result['exception'] = 'Tendrá 5 oportunidades dentro de 5 minutos, por favor espere';
                            } else {
                                $result['exception'] = 'No se pudo actualizar la hora de bloqueo';
                            }
                        }
                        //Verificaremos si existe una fecha de bloqueo para decirle al usuario el tiempo de espera
                        else {
                            date_default_timezone_set('America/El_Salvador');
                            //Creamos un variable que almacene la hora local
                            $hora_actual = date('Y-m-d h:i:s', time());
                            //Si la hora actual es mayor a la fecha de activación le decimos que debe esperar
                            if ($usuario_cliente->getHoraActivacion() > $hora_actual) {
                                $result['exception'] = 'Tendrá 5 oportunidades dentro de 5 minutos desde la hora de bloqueo, por favor espere';
                            }
                            //Si la hora actual es menor a la fecha de activación, se actualizaran los intentos a 0 y se eliminara las fechas de bloqueo y desbloqueo 
                            else if ($usuario_cliente->getHoraActivacion() < $hora_actual) {
                                //Actualizamos los intentos a 0 y su estado a 1 que es activo
                                if ($usuario_cliente->habilitarIntentos(0, 1)) {
                                    $result['exception'] = 'Estado Actualizado. Ha acabado el tiempo de espera, tiene 5 intentos más';
                                } else {
                                    $result['exception'] = 'Error de Actualización. Ha ocurrido un error en el momento de actualizar intentos';
                                }
                            }
                        }
                    }
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
                break;
        }
    }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
