<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos.
*   Es clase hija de Validator.
*/
class UsuarioCliente extends Validator
{
    // Declaración de atributos (propiedades).
    private $idusuario_c = null;
    private $usuario_c = null;
    private $contra_c = null;
    private $telefono = null;
    private $direccion = null;
    private $correo = null;
    private $dui = null;
    private $clave_usuario = null;
    private $hora_inactivacion = null;
    private $hora_activacion = null;
    private $intentos_c = null; 
    private $idestadou_c = null;
    private $nombre_cliente = null;
    private $apellido_cliente = null;

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getIdUsuarioC()
    {
        return $this->idusuario_c;
    }

    public function getUsuario()
    {
        return $this->usuario_c;
    }

    public function getContra()
    {
        return $this->contra_c;
    }

    public function getClaveUsuario()
    {
        return $this->clave_usuario;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function getDUICliente()
    {
        return $this->dui;
    }


    public function getHoraInactivacion()
    {
        return $this->hora_inactivacion;
    }

    public function getHoraActivacion()
    {
        return $this->hora_activacion;
    }

    public function getIntento()
    {
        return $this->intentos_c;
    }

    public function getEstadoU()
    {
        return $this->idestadou_c;
    }

    public function getNombreCliente()
    {
        return $this->nombre_cliente;
    }

    public function getApellidoCliente()
    {
        return $this->apellido_cliente;
    }

    /*
    *   Métodos para gestionar la cuenta del usuario.
    */
    //Función que obtiene los datos del usuario si escribe un usuario existente en la base
    public function validarUsuarioCliente($usuario)
    {
        $sql = 'SELECT tuc.idusuario_c, tuc.contrasena_c, tuc.intentos_c, tuc.telefono_cliente, tuc.direccion_cliente, tuc.correo_cliente, tuc.dui_cliente, tuc.fecha_bloqueo_c, tuc.fecha_desbloqueo_c, teuc.idestado_usuario_c, tuc.nombre_cliente, tuc.apellido_cliente 
        FROM tbusuario_cliente tuc, tbestado_usuario_c teuc
        WHERE tuc.idestado_usuario_c = teuc.idestado_usuario_c AND usuario_c = ?';
        $params = array($usuario);
        if ($data = Database::obtenerSentencia($sql, $params)) {
            $this->idusuario_c = $data['idusuario_c'];
            $this->usuario_c = $usuario;
            $this->contra_c = $data['contrasena_c'];
            $this->telefono = $data['telefono_cliente'];
            $this->direccion = $data['direccion_cliente'];
            $this->correo = $data['correo_cliente'];
            $this->dui = $data['dui_cliente'];
            $this->intentos_c = $data['intentos_c'];
            $this->hora_inactivacion = $data['fecha_bloqueo_c'];
            $this->hora_activacion = $data['fecha_desbloqueo_c'];
            $this->idestadou_c = $data['idestado_usuario_c'];
            $this->nombre_cliente = $data['nombre_cliente'];
            $this->apellido_cliente = $data['apellido_cliente'];
            return true;
        } else {
            return false;
        }
    }

    //Actualizamos los intentos del usuario cliente cuando se equivoque
    public function intentosUsuarioCliente(){
        $sql = 'UPDATE tbusuario_cliente SET intentos_c = ? WHERE usuario_c = ?';
        $params = array(($this->intentos_c += 1), $this->usuario_c);
        if($data = Database::ejecutarSentencia($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    //Registramos la hora del bloqueo y la hora de desbloqueo
    public function registrarHoraIntento($hora_block, $hora_desblock, $idestadoU){
        $sql = 'UPDATE tbusuario_cliente SET fecha_bloqueo_c = ? , fecha_desbloqueo_c = ?, idestado_usuario_c = ? WHERE usuario_c = ?';
        $params = array($hora_block, $hora_desblock, $idestadoU, $this->usuario_c);
        if($data = Database::ejecutarSentencia($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    //Método para habilitar intentos
    public function habilitarIntentos($intento, $idEstadoU){
        $sql = "UPDATE tbusuario_cliente SET fecha_bloqueo_c = NULL, fecha_desbloqueo_c = NULL, intentos_c = ?, idestado_usuario_c = ? WHERE usuario_c = ?";
        $params = array($intento, $idEstadoU, $this->usuario_c);
        if($data = Database::ejecutarSentencia($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    //Función para entrar al sistema, evalua si las credenciales son correctas
    public function validarContraUsuarioCliente($password)
    {
        $sql = 'SELECT contrasena_c FROM tbusuario_cliente WHERE idusuario_c = ?';
        $params = array($this->idusuario_c);
        $data = Database::obtenerSentencia($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        // Solo se ocupa cuando no está encriptada
        if ($password === $data['contrasena_c']) {
            return true;
        } else {
            return false;
        }
    }
}