<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Usuarios extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $usuario = null;
    private $contrasena = null;
    private $empleado = null;
    private $tipo = null;
    private $estado = null;
    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setId($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->id = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setUsuario($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->usuario = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setContrasena($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->contrasena = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEmpleado($value)
    {
        if ($this->validateBoolean($value)) {
            $this->empleado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTipo($value)
    {
        if ($this->validateBoolean($value)) {
            $this->tipo = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setEstado($value)
    {
        if ($this->validateBoolean($value)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }
    
    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getId()
    {
        return $this->id;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getContrasena()
    {
        return $this->contrasena;
    }

    public function getEmpleado()
    {
        return $this->empleado;
    }

    public function getTipo()
    {
        return $this->tipo;
    }
    public function getEstado()
    {
        return $this->estado;
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    public function searchRows($value)
    {
        $sql = 'SELECT idusuario_e, usuario_e, contrasena_e, idempleado, idtipo_usuario_e, idestado_usuario_e
                FROM tbusuario_empleado
                WHERE usuario_e ILIKE ?
                ORDER BY usuario_e';
        $params = array("%$value%");
        return Database::obtenerSentencias($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tbusuario_empleado(usuario_e, contrasena_e, idempleado, idtipo_usuario_e, idestado_usuario_e)
                VALUES (?, ?, ?, ?, ?)';
        $params = array($this->usuario, $this->contrasena, $this->empleado, $this->tipo,  $this->estado);
        return Database::ejecutarSentencia($sql, $params);
    }

 
    public function readAll()
    {
        $sql = 'SELECT tbusuario_empleado.idusuario_e, usuario_e, contrasena_e,nombre_empleado,tipo_usuario_e,estado_usuario_e
        FROM tbusuario_empleado
        INNER JOIN tbempleado
        ON tbusuario_empleado.idempleado = tbempleado.idempleado
        INNER JOIN tbtipo_usuario_e
        ON tbusuario_empleado.idtipo_usuario_e = tbtipo_usuario_e.idtipo_usuario_e
        INNER JOIN tbestado_usuario_e
        ON tbusuario_empleado.idestado_usuario_e = tbestado_usuario_e.idestado_usuario_e
        ORDER BY nombre_empleado';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT idusuario_e, usuario_e, contrasena_e, idempleado, idtipo_usuario_e, idestado_usuario_e
                FROM tbusuario_empleado
                WHERE idusuario_e = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }
    public function readOneE()
    {
        $sql = 'SELECT idusuario_e, usuario_e, contrasena_e, idempleado, idtipo_usuario_e, idestado_usuario_e
                FROM tbusuario_empleado
                WHERE idusuario_e = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tbusuario_empleado
	            SET usuario_e= ?, contrasena_e=?, idempleado=?, idtipo_usuario_e=?, idestado_usuario_e=?
	            WHERE idusuario_e" = ?';
        $params = array($this->usuario, $this->contrasena, $this->empleado, $this->tipo, $this->estado, $this->id);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tbusuario_empleado
                WHERE idusuario_e" = ?';
        $params = array($this->id);
        return Database::ejecutarSentencia($sql, $params);
    }
}