<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Empleados extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $nombre = null;
    private $apellido = null;
    private $dui = null;
    private $nit = null;
    private $telefono = null;
    private $correo = null;
    private $fecha = null;
    private $tipo = null;
    private $estado = null;
    private $idusuario = null;

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

    public function setNombre($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->nombre = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setApellido($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->apellido = $value;
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
            $this->telefono = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCorreo($value)
    {
        if ($this->validateEmail($value)) {
            $this->correo = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setFecha($value)
    {
        if ($this->validateDate($value)) {
            $this->fecha = $value;
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

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getDUI()
    {
        return $this->dui;
    }

    public function getNIT()
    {
        return $this->nit;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getIdUsuario()
    {
        return $this->idusuario;
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    public function buscarEmpleado($value)
    {
        $sql = 'SELECT te.idempleado, te.nombre_empleado, te.apellido_empleado, te.duiempleado, te.nitempleado, te.telefono_empleado, te.correo_empleado, te.fecha_nacimiento_empleado, tte.tipo_empleado, tee.nombre_estado
        FROM tbempleado te
        INNER JOIN tbtipo_empleado tte
        ON te.idtipo_empleado = tte.idtipo_empleado
        INNER JOIN tbestado_empleado tee
        ON te.idestado_empleado = tee.idestado_empleado
        WHERE te.idestado_empleado != 2 AND (te.nombre_empleado ILIKE ? OR te.apellido_empleado ILIKE ? OR te.duiempleado ILIKE ? OR te.nitempleado ILIKE ? OR te.telefono_empleado ILIKE ? OR te.correo_empleado ILIKE ? OR CAST(te.fecha_nacimiento_empleado AS VARCHAR) ILIKE ? OR tte.tipo_empleado ILIKE ? OR tee.nombre_estado ILIKE ?)
        ORDER BY te.idempleado';
        $params = array("%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%", "%$value%");
        return Database::obtenerSentencias($sql, $params);
    }

    public function crearEmpleado()
    {
        $sql = 'INSERT INTO tbempleado(nombre_empleado, apellido_empleado, duiempleado, nitempleado, telefono_empleado, correo_empleado, fecha_nacimiento_empleado, idtipo_empleado, idestado_empleado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->dui, $this->nit, $this->telefono, $this->correo, $this->fecha, $this->tipo,  $this->estado);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT idempleado, nombre_empleado, apellido_empleado, duiempleado, nitempleado, telefono_empleado, correo_empleado, fecha_nacimiento_empleado, tipo_empleado,nombre_estado
        FROM tbempleado
        INNER JOIN tbtipo_empleado
        ON tbempleado.idtipo_empleado = tbtipo_empleado.idtipo_empleado
        INNER JOIN tbestado_empleado
        ON tbempleado.idestado_empleado = tbestado_empleado.idestado_empleado
        WHERE tbempleado.idestado_empleado != 2
        ORDER BY nombre_empleado';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function obtenerTipoEmpleados()
    {
        $sql = 'SELECT idtipo_empleado, tipo_empleado
        FROM tbtipo_empleado;';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function obtenerEstadoEmpleados()
    {
        $sql = 'SELECT idestado_empleado, nombre_estado
        FROM tbestado_empleado';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT idempleado, nombre_empleado, apellido_empleado, duiempleado, nitempleado, telefono_empleado, correo_empleado, fecha_nacimiento_empleado, idtipo_empleado, idestado_empleado
                FROM tbempleado
                WHERE idempleado = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }

    public function actualizarEmpleado()
    {
        $sql = 'UPDATE tbempleado
                SET nombre_empleado=?, apellido_empleado=?, duiempleado=?, nitempleado=?, telefono_empleado=?, correo_empleado=?, fecha_nacimiento_empleado=?, idtipo_empleado=?, idestado_empleado=?
                WHERE idempleado=?';
        $params = array($this->nombre, $this->apellido, $this->dui, $this->nit, $this->telefono, $this->correo, $this->fecha, $this->tipo, $this->estado, $this->id);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function eliminarEmpleado()
    {
        $sql = 'UPDATE tbempleado set idestado_empleado = 2
                WHERE idempleado = ?';
        $params = array($this->id);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function encontrarUsuarioEmpleado(){
        $sql = 'SELECT idusuario_e FROM tbusuario_empleado WHERE idempleado =?';
        $params = array($this->getId());
        if ($data = Database::obtenerSentencia($sql, $params)){
            $this->idusuario = $data['idusuario_e'];
            return true;
        }
        else{
            return false;
        }
    }

    public function actualizarUsuarioEmpleadoEliminado(){
        $sql = 'UPDATE tbusuario_empleado SET idestado_usuario_e = 2 WHERE idusuario_e = ?';
        $params = array($this->getIdUsuario());
        return Database::ejecutarSentencia($sql, $params);
    }
}