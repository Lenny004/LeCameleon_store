<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos.
*   Es clase hija de Validator.
*/
class RegistroUsuarios extends Validator
{
    // Declaración de atributos (propiedades).
    private $idempleado = null;
    private $nombre_empleado = null;
    private $apellido_empleado = null;
    private $dui = null;
    private $nit = null;
    private $telefono_empleado = null;
    private $correo_empleado = null;
    private $fecha_nacimiento_empleado = null;
    private $idtipo_empleado = 1;
    private $idestado_empleado = 1;
    private $contrasena = null;

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setIdEmpleado($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idempleado = $value;
            return true;
        } else {
            return false;
        }
    }

    /*Validar que el nombre tenga como longitud máxima 50 */
    public function setNombres($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->nombre_empleado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setApellidos($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->apellido_empleado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDUI($value)
    {
        if ($this->validateDUI($value)) {
            $this->dui = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setNIT($value)
    {
        if ($this->validarNIT($value)) {
            $this->nit = $value;
            return true;
        } else {
            return false;
        }
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

    public function setCorreoEmpleado($value)
    {
        if ($this->validateEmail($value)) {
            $this->correo_empleado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setFechaNEmpleado($value)
    {
        if ($this->validateDate($value)) {
            $this->fecha_nacimiento_empleado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setContraEmpleado($value)
    {
        if ($this->validatePassword($value)) {
            $this->contrasena = $value;
            return true;
        } else {
            return false;
        }
    }

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getIdempleado()
    {
        return $this->idempleado;
    }

    public function getNombres()
    {
        return $this->nombres;
    }

    public function getApellidos()
    {
        return $this->apellidos;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function getDui()
    {
        return $this->dui;
    }

    public function getClave()
    {
        return $this->clave;
    }

    /* Traer los datos de un usuario si el usuario ingresado existe */
    public function ValidarExistenciaPrimerUsuario()
    {
        $sql = 'SELECT idusuario_e, usuario_e, contrasena_e, intentos_e, fecha_bloqueo_e, fecha_desbloqueo_e, idempleado, idtipo_usuario_e, idestado_usuario_e
                FROM tbusuario_empleado ORDER BY idusuario_e;';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function RegistrarEmpleado()
    {
        $sql = 'INSERT INTO tbempleado(nombre_empleado, apellido_empleado, duiempleado, nitempleado, telefono_empleado, correo_empleado, fecha_nacimiento_empleado, idtipo_empleado, idestado_empleado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);';
        $params = array($this->nombre_empleado, $this->apellido_empleado, $this->dui, $this->nit, $this->telefono_empleado, $this->correo_empleado, $this->fecha_nacimiento_empleado, $this->idtipo_empleado, $this->idestado_empleado);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function ObtenerEmpleadoRegistrado()
    {
        $sql = 'SELECT idempleado FROM tbempleado WHERE duiempleado = ?';
        $params = array($this->dui);
        if ($data = Database::obtenerSentencia($sql, $params)){
            $this->idempleado = $data['idempleado'];
            return true;
        } else{
            return false;
        }
    }

    public function RegistrarUsuarioEmpleado()
    {
        $sql = 'INSERT INTO tbusuario_empleado(usuario_e, contrasena_e, idempleado, idtipo_usuario_e, idestado_usuario_e)
        VALUES (?, ?, ?, ?, ?)';
        $params = array($this->correo_empleado, $this->contrasena, $this->idempleado, $this->idtipo_empleado, $this->idestado_empleado);
        return Database::ejecutarSentencia($sql, $params);
    }
}