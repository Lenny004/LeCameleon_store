<?php
/*
*    Clase para manejar la tabla estado_ropa de la base de datos.
*   Es clase hija de Validator.
*/
class TipoU extends Validator{

    // Declaración de atributos (propiedades).
    private $id = null;
    private $tipo = null;

    /* Métodos para validar y asignar valores de los atributos. */

    public function setId($value)
    {
        if ($this->validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTipo($value)
    {
        if ($this->validateAlphanumeric($value, 1, 25)) {
            $this->tipo = $value;
            return true;
        } else {
            return false;
        }
    }

    /* Métodos para obtener valores de los atributos. */

    public function getId()
    {
        return $this->id;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    /* Métodos para realizar readAll */

    public function readAll()
    {
        $sql = 'SELECT idtipo_usuario_e, tipo_usuario_e
        FROM tbTipoUsuarioE";';
        $params = null;
        return Database::getRows($sql, $params);
    }

}