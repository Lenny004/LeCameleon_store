<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos.
*   Es clase hija de Validator.
*/
class Usuarios extends Validator
{
    // Declaración de atributos (propiedades).
    private $idusuario_e = null;
    private $usuario_e = null;
    private $clave_usuario = null;
    private $hora_inactivacion = null;
    private $hora_activacion = null;
    private $intentos_e = null;
    private $idestadou_e = null;
    private $idtipo_usuario_e = null;
    private $tipo_usuario_e = null;
    private $nombre_empleado = null;
    private $apellido_empleado = null;
    private $dui_empleado = null;
    private $nit_empleado = null;
    private $telefono_empleado = null;
    private $correo_empleado = null;

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getIdUsuarioE()
    {
        return $this->idusuario_e;
    }

    public function getUsuario()
    {
        return $this->usuario_e;
    }

    public function getClaveUsuario()
    {
        return $this->clave_usuario;
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
        return $this->intentos_e;
    }

    public function getEstadoU()
    {
        return $this->idestadou_e;
    }

    public function getIdTipoU()
    {
        return $this->idtipo_usuario_e;
    }

    public function getTipoUsuario()
    {
        return $this->tipo_usuario_e;
    }

    public function getNombreEmpleado()
    {
        return $this->nombre_empleado;
    }

    public function getApellidoEmpleado()
    {
        return $this->apellido_empleado;
    }

    public function getDUIEmpleado()
    {
        return $this->dui_empleado;
    }

    public function getNITEmpleado()
    {
        return $this->nit_empleado;
    }

    public function getTelefono()
    {
        return $this->telefono_empleado;
    }

    public function getCorreoEmpleado()
    {
        return $this->correo_empleado;
    }

    public function setTelefono($value)
    {
        if ($this->validatePhone($value)) {
            $this->telefono_empleado = $value;
            return true;
        } else {
            return false;
        }
    }

    //Le asignamos el valor de correo
    public function setCorreoEmpleado($value)
    {
        if ($this->validateEmail($value)) {
            $this->correo_empleado = $value;
            return true;
        } else {
            return false;
        }
    }

    //Le asignamos el valor de empleado
    public function setClaveEmpleado($value)
    {
        if ($this->validatePassword($value)) {
            $this->clave_usuario = $value;
            return true;
        } else {
            return false;
        }
    }

    /* Traer los datos de un usuario si el usuario ingresado existe */
    public function ValidarExistenciaPrimerUsuario()
    {
        $sql = 'SELECT idusuario_e, usuario_e, contrasena_e, intentos_e, fecha_bloqueo_e, fecha_desbloqueo_e, idempleado, idtipo_usuario_e, idestado_usuario_e
                FROM tbusuario_empleado ORDER BY idusuario_e';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    /*
    *   Métodos para gestionar la cuenta del usuario.
    */
    public function ValidarUsuarioEmpleado($usuario)
    {
        $sql = 'SELECT tue.idusuario_e, tue.contrasena_e, tue.intentos_e, tue.fecha_bloqueo_e, tue.fecha_desbloqueo_e, tue.idestado_usuario_e, tue.idtipo_usuario_e, ttue.tipo_usuario_e, te.idempleado ,te.nombre_empleado, te.apellido_empleado, te.duiempleado, te.nitempleado, te.telefono_empleado, te.correo_empleado FROM tbusuario_empleado tue, tbempleado te, tbtipo_usuario_e ttue WHERE tue.idempleado = te.idempleado AND tue.idtipo_usuario_e = ttue.idtipo_usuario_e  AND usuario_e = ?';
        $params = array($usuario);
        if ($data = Database::obtenerSentencia($sql, $params)) {
            $_SESSION['idempleado'] = $data['idempleado'];
            $this->idusuario_e = $data['idusuario_e'];
            $this->usuario_e = $usuario;
            $this->clave_usuario = $data['contrasena_e'];
            $this->intentos_e = $data['intentos_e'];
            $this->hora_inactivacion = $data['fecha_bloqueo_e'];
            $this->hora_activacion = $data['fecha_desbloqueo_e'];
            $this->idestadou_e = $data['idestado_usuario_e'];
            $this->idtipo_usuario_e = $data['idtipo_usuario_e'];
            $this->tipo_usuario_e = $data['tipo_usuario_e'];
            $this->nombre_empleado = $data['nombre_empleado'];
            $this->apellido_empleado = $data['apellido_empleado'];
            $this->dui_empleado = $data['duiempleado'];
            $this->nit_empleado = $data['nitempleado'];
            $this->telefono_empleado = $data['telefono_empleado'];
            $this->correo_empleado = $data['correo_empleado'];
            return true;
        } else {
            return false;
        }
    }

    //Se actualizan los intentos del usuario empleado al equivocarse de contra
    public function IntentosUsuarioEmpleado()
    {
        $sql = 'UPDATE tbusuario_empleado SET intentos_e = ? WHERE usuario_e = ?';
        $params = array(($this->intentos_e += 1), $this->usuario_e);
        if ($data = Database::ejecutarSentencia($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    /* Traer los datos de un usuario si el usuario ingresado existe */
    public function ExistenciaHoraBlock()
    {
        $sql = 'SELECT fecha_bloqueo_e, fecha_desbloqueo_e FROM tbusuario_empleado WHERE usuario_e = ?';
        $params = array($this->usuario_e);
        return Database::obtenerSentencia($sql, $params);
    }

    /* Registrar la hora de bloqueo y la hora de desbloqueo del usuario empleado */
    public function RegistrarHoraIntento($hora_block, $hora_desblock, $idestadoU)
    {
        $sql = 'UPDATE tbusuario_empleado SET fecha_bloqueo_e = ? , fecha_desbloqueo_e = ?, idestado_usuario_e = ? WHERE usuario_e = ?';
        $params = array($hora_block, $hora_desblock, $idestadoU, $this->usuario_e);
        if ($data = Database::ejecutarSentencia($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    //Método para habilitar intentos
    public function HabilitarIntentos($intento, $idEstadoU)
    {
        $sql = "UPDATE tbusuario_empleado SET fecha_bloqueo_e = NULL, fecha_desbloqueo_e = NULL, intentos_e = ?, idestado_usuario_e = ? WHERE usuario_e = ?";
        $params = array($intento, $idEstadoU, $this->usuario_e);
        if ($data = Database::ejecutarSentencia($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    /*Validamos la contra del usuario empleado para acceder al sitio */
    public function ValidarContraUsuarioEmpleado($password)
    {
        $sql = 'SELECT contrasena_e FROM tbusuario_empleado WHERE idusuario_e = ?';
        $params = array($this->idusuario_e);
        $data = Database::obtenerSentencia($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        // Solo se ocupa cuando no está encriptada
        if ($password === $data['contrasena_e']) {
            return true;
        } else {
            return false;
        }
    }

    //Función para actualizar los datos del perfil del privado
    public function actualizarUsuario($id)
    {
        $sql = 'UPDATE tbusuario_empleado SET contrasena_e = ? WHERE idusuario_e = ?';
        $params = array($this->clave_usuario, $id);
        return Database::ejecutarSentencia($sql, $params);
    }

    //Función para actualizar los datos del perfil del privado
    public function actualizarEmpleado()
    {
        $sql = 'UPDATE tbempleado SET telefono_empleado = ?, correo_empleado = ? WHERE idempleado = ?';
        $params = array($this->telefono_empleado, $this->correo_empleado, $_SESSION['idempleado']);
        return Database::ejecutarSentencia($sql, $params);
    }
}