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
        if ($this->validateDUI($value)) {
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


    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    public function searchRows($value)
    {
        $sql = 'SELECT idempleado, nombre_empleado, apellido_empleado, duiempleado, nitempleado, telefono_empleado, correo_empleado, fecha_nacimiento_empleado, idtipo_empleado, idestado_empleado
                FROM tbempleado
                WHERE nombre_empleado ILIKE ?
                ORDER BY nombre_empleado';
        $params = array("%$value%");
        return Database::obtenerSentencias($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tbempleado(nombre_empleado, apellido_empleado, duiempleado, nitempleado, telefono_empleado, correo_empleado, fecha_nacimiento_empleado, idtipo_empleado, idestado_empleado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->dui, $this->nit, $this->telefono, $this->correo, $this->fecha, $this->tipo,  $this->estado);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT nombre_empleado, apellido_empleado, duiempleado, nitempleado, telefono_empleado, correo_empleado, fecha_nacimiento_empleado, tipo_empleado,nombre_estado
        FROM tbempleado
        INNER JOIN tbtipo_empleado
        ON tbempleado.idestado_empleado = tbtipo_empleado.idtipo_empleado
        INNER JOIN tbestado_empleado
        ON tbempleado.idestado_empleado = tbestado_empleado.idestado_empleado
        ORDER BY nombre_empleado';
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
    
    public function readOneE()
    {
        $sql = 'SELECT idempleado, nombre_empleado, apellido_empleado, duiempleado, nitempleado, telefono_empleado, correo_empleado, fecha_nacimiento_empleado, idtipo_empleado, idestado_empleado
                FROM tbempleado
                WHERE idempleado = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tbempleado
                SET nombre_empleado=?, apellido_empleado=?, duiempleado=?, nitempleado=?, telefono_empleado=?, correo_empleado=?, fecha_nacimiento_empleado=?, idtipo_empleado=?, idestado_empleado=?
                WHERE idempleado=?';
        $params = array($this->nombre, $this->apellido, $this->dui, $this->nit, $this->telefono, $this->correo, $this->fecha, $this->tipo, $this->estado, $this->id);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tbempleado
                WHERE idempleado = ?';
        $params = array($this->id);
        return Database::ejecutarSentencia($sql, $params);
    }
}