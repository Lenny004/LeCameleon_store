<?php
/*
*	Clase para manejar la tabla proveedor de la base de datos.
*   Es clase hija de Validator.
*/
class Proveedor extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $nombre = null;
    private $direccion = null;
    private $telefono = null;

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

    public function setDireccion($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->direccion = $value;
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

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */

    public function createRow()
    {
        $sql = 'INSERT INTO tbdistribuidor(nombre_distribuidor, direccion_distribuidor, telefono_distribuidor)
                VALUES (?, ?, ?)';
        $params = array($this->nombre, $this->direccion, $this->telefono);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT iddistribuidor, nombre_distribuidor, direccion_distribuidor, telefono_distribuidor
                FROM tbdistribuidor
                ORDER BY iddistribuidor';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT iddistribuidor, nombre_distribuidor, direccion_distribuidor, telefono_distribuidor
                FROM tbdistribuidor
                WHERE iddistribuidor = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }
    public function readOneE()
    {
        
        $sql = 'SELECT iddistribuidor, nombre_distribuidor, direccion_distribuidor, telefono_distribuidor
                FROM tbdistribuidor
                WHERE iddistribuidor = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tbdistribuidor
                SET nombre_distribuidor=?, direccion_distribuidor=?, telefono_distribuidor=?
                WHERE iddistribuidor=?';
        $params = array($this->nombre, $this->direccion, $this->telefono, $this->id);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tbdistribuidor
                WHERE iddistribuidor = ?';
        $params = array($this->id);
        return Database::ejecutarSentencia($sql, $params);
    }
}
