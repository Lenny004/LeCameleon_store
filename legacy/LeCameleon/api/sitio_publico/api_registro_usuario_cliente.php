<?php
require_once('../conexion/database.php');
require_once('../conexion/validaciones.php');
require_once('../modelo/registro_usuario_cliente.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $registroUsuarioC = new RegistroUsuariosClientes;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('estado' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.   
    // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
    switch ($_GET['action']) {
        case 'registroUsuario':
            date_default_timezone_set('America/El_Salvador');
            $fecha_creacion = date('Y-m-d h:i:s', time());
            $_POST = $registroUsuarioC->validateForm($_POST);
            if (!$registroUsuarioC->setNombres($_POST['nombre'])) {
                $result['exception'] = 'Nombres incorrectos';
            } elseif (!$registroUsuarioC->setApellidos($_POST['apellidos'])) {
                $result['exception'] = 'Apellidos incorrectos';
            } elseif (!$registroUsuarioC->setDUI($_POST['dui'])) {
                $result['exception'] = 'Formato de DUI incorrecto';
            } elseif (!$registroUsuarioC->setTelefono($_POST['telefono'])) {
                $result['exception'] = 'El formato de teléfono incorrecto';
            } elseif (!$registroUsuarioC->setCorreoCliente($_POST['email'])) {
                $result['exception'] = 'El formato de correo eléctroncio es inválido';
            } elseif (!$registroUsuarioC->setFechaCreacion($fecha_creacion)) {
                $result['exception'] = 'Formato de fecha de creación es incorrecto';
            } elseif ($_POST['password'] != $_POST['confirmar_contra']) {
                $result['exception'] = 'Las contraseñas ingresadas son diferentes';
            } elseif (!$registroUsuarioC->setContraCliente($_POST['password'])) {
                $result['exception'] = $registroUsuarioC->getPasswordError();
            } elseif ($registroUsuarioC->registrarUsuarioCliente()) {
                $result['estado'] = 1;
                $result['message'] = 'Tu usuario ha sido registrado correctamente';
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Usuario no pudo ser registrado';
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
