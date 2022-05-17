<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/login.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $usuario = new Usuarios;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'getUser':
                if (isset($_SESSION['nombre_empleado'.' '.'apellido_empleado'])) {
                    $result['estado'] = 1;
                    $result['username'] = $_SESSION['nombre_empleado'.' '.'apellido_empleado'];
                } else {
                    $result['exception'] = 'Nombre de usuario indefinido';
                }
                break;
            //Verificamos si existen usuarios
            case 'verificarPrimerUso':
                //Si existen usuarios manda un mensaje de que se encontraron
                if ($usuario->ValidarExistenciaPrimerUsuario()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Existe al menos un usuario registrado';
                } else {
                    $result['exception'] = 'No existe un usuario administrador registrado';
                }
                break;
            //Proceso para ingresar en el login
            case 'logIn':
                $_POST = $usuario->validateForm($_POST);
                //Si el usuario no es correcto manda una alerta
                if (!$usuario->ValidarUsuarioEmpleado($_POST['usuario_login'])) {
                    $result['exception'] = 'El usuario ingresado no existe';
                //Si el usuario y la contraseña es la correcta y el número de intentos es menor a 5 y un estado activo manda una alerta
                } else if ($usuario->ValidarContraUsuarioEmpleado($_POST['password_login']) && $usuario->getIntento() < 5 && $usuario->getEstadoU() == 1) {
                    $result['estado'] = 1;
                    $result['session'] = 1;
                    $result['message'] = 'Autenticacion correcta';
                    $_SESSION['idusuario_e'] = $usuario->getIdUsuarioE();
                    $_SESSION['usuario_e'] = $usuario->getUsuario();
                }
                //Si el usuario es el correcto pero la contra es incorrecta
                else {
                    //Si el número de intentos es menor a 5
                    if ($usuario->getIntento() < 5){
                        //Se agrega un intento al usuario que está ingresando
                        if ($usuario->IntentosUsuarioEmpleado()){
                            $result['exception'] = 'Contraseña incorrecta. Tienes ' . (5 - $usuario->getIntento()) . ' intentos restantes';
                        }
                        //Si ocurre un fallo al actualizar
                        else{
                            $result['exception'] = 'No se pudo actualizar los intentos';
                        }
                    }
                    //Si el número de intentos es igual a 5
                    else if($usuario->getIntento() == 5){
                        //Verificaremos si no existe una fecha de bloqueo para poder agregar una
                        if ($usuario->getHoraInactivacion() == null && $usuario->getHoraActivacion()== null){
                            //Crearemos los valores de hora de bloqueo y desbloqueo
                            date_default_timezone_set('America/El_Salvador');
                            $hora_inactivacion = date('Y-m-d h:i:s', time());
                            $hora_actual = date('Y-m-d h:i:s', time());
                            //La hora de activación es la fecha y hora actual + 5 min que tendrá que esperar para volver a tener 5 intentos
                            $hora_activacion = date('Y-m-d h:i:s',strtotime($hora_actual)+300);
                            //Mandamos la hora de bloqueo, la hora de desbloqueo y el 2 que es para poner estado inactiva a la cuenta
                            if($usuario->RegistrarHoraIntento($hora_inactivacion, $hora_activacion, 2)){
                                $result['exception'] = 'Tendrá 5 oportunidades dentro de 5 minutos, por favor espere';
                            }
                            else{
                                $result['exception'] = 'No se pudo actualizar la hora de bloqueo';
                            }
                        }
                        //Verificaremos si existe una fecha de bloqueo para decirle al usuario el tiempo de espera
                        else{
                            date_default_timezone_set('America/El_Salvador');
                            //Creamos un variable que almacene la hora local
                            $hora_actual = date('Y-m-d h:i:s', time());
                            //Si la hora actual es mayor a la fecha de activación le decimos que debe esperar
                            if($usuario->getHoraActivacion() > $hora_actual){
                                $result['exception'] = 'Tendrá 5 oportunidades dentro de 5 minutos desde la hora de bloqueo, por favor espere';
                            }
                            //Si la hora actual es menor a la fecha de activación, se actualizaran los intentos a 0 y se eliminara las fechas de bloqueo y desbloqueo 
                            else if($usuario->getHoraActivacion() < $hora_actual){
                                //Actualizamos los intentos a 0 y su estado a 1 que es activo
                                if($usuario->HabilitarIntentos(0, 1)){
                                    $result['exception'] = 'Estado Actualizado. Ha acabado el tiempo de espera, tiene 5 intentos más';
                                }
                                else{
                                    $result['exception'] = 'Error de Actualización. Ha ocurrido un error en el momento de actualizar intentos';
                                }
                            }
                        }
                    }
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
        }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
