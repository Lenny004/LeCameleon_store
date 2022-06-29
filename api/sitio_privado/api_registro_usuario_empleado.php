<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/registro_usuario_empleado.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $registroUsuario = new RegistroUsuarios;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']){
            //Verificamos si existen usuarios
            case 'verificarPrimerUso':
                //Si existen usuarios manda un mensaje de que se encontraron
                if ($registroUsuario->ValidarExistenciaPrimerUsuario()) {
                    $result['estado'] = 1;
                    $result['message'] = 'Existe al menos un usuario registrado';
                }
                else {
                    $result['exception'] = 'No existe un usuario administrador registrado';
                }
                break;
            case 'registroUsuario':
                $_POST = $registroUsuario->validateForm($_POST);
                $fecha_actual = date('Y');
                $fecha = explode('-', $_POST['fechaM']);
                if (!$registroUsuario->setNombres($_POST['nombre'])) {
                    $result['exception'] = 'Nombres incorrectos';
                }   elseif (!$registroUsuario->setApellidos($_POST['apellidos'])) {
                    $result['exception'] = 'Apellidos incorrectos';
                }   elseif (!$registroUsuario->setDUI($_POST['dui'])) {
                    $result['exception'] = 'Formato de DUI incorrecto';
                }   elseif (!$registroUsuario->setNIT($_POST['nit'])) {
                    $result['exception'] = 'Formato de NIT incorrecto';
                }   elseif (!$registroUsuario->setTelefono($_POST['telefono'])) {
                    $result['exception'] = 'El formato de teléfono incorrecto';
                }   elseif (!$registroUsuario->setFechaNEmpleado($_POST['fechaM'])) {
                    $result['exception'] = 'Formato de fecha ingresado es incorrecto';
                }   elseif (intval($fecha[0]) > intval($fecha_actual-18)) {
                    $result['exception'] = 'El empleado debe ser mayor de edad';
                }   elseif (!$registroUsuario->setCorreoEmpleado($_POST['email'])) {
                    $result['exception'] = 'El formato de correo eléctroncio es inválido';
                }   elseif ($_POST['password'] != $_POST['confirmar_contra']) {
                    $result['exception'] = 'Las contraseñas ingresadas son diferentes';
                }   elseif (!$registroUsuario->setContraEmpleado($_POST['password'])) {
                    $result['exception'] = $registroUsuario->getPasswordError();
                }   elseif ($registroUsuario->RegistrarEmpleado()) {
                    if ($registroUsuario->ObtenerEmpleadoRegistrado()){
                        if ($registroUsuario->RegistrarUsuarioEmpleado()){
                            $result['estado'] = 1;
                            $result['message'] = 'Usuario administrador registrado correctamente';
                        }
                        else{
                            $result['exception'] = 'Usuario administrador no pudo ser registrado';
                        }
                    }
                    else{
                        $result['exception'] = 'Error al obtener credenciales';
                    }
                } else {
                    $result['exception'] = Database::getException();
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