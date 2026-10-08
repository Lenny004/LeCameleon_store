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
    private $idestado_usuario_c = null;

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setId($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idusuario_c = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setUsuarioC($value)
    {
        if ($this->validateEmail($value)) {
            $this->usuario_c = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstadoUsuarioC($value)
    {
        if ($this->validateBoolean($value)) {
            $this->idestado_usuario_c = $value;
            return true;
        } else {
            return false;
        }
    }

    public function getIdUsuarioC()
    {
        return $this->idusuario_c;
    }

    /* Traer los datos de un usuario si el usuario ingresado existe */
    public function obtenerUsuariosClientes()
    {
        $sql = 'SELECT tuc.idusuario_c, tuc.usuario_c, tuc.idestado_usuario_c, teuc.estado_usuario_c 
        FROM tbusuario_cliente tuc 
        INNER JOIN tbestado_usuario_c teuc 
        ON tuc.idestado_usuario_c = teuc.idestado_usuario_c 
        ORDER BY tuc.idusuario_c ASC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function leerUnCliente()
    {
        $sql = 'SELECT idusuario_c, usuario_c, idestado_usuario_c
                FROM tbusuario_cliente
                WHERE idusuario_c = ?';
        $params = array($this->idusuario_c);
        return Database::obtenerSentencia($sql, $params);
    }

    public function obtenerEstadoUsuarioCliente()
    {
        $sql = 'SELECT idestado_usuario_c, estado_usuario_c
        FROM tbestado_usuario_c';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function actualizarEmpleado()
    {
        $sql = 'UPDATE tbusuario_cliente
                SET usuario_c = ?, idestado_usuario_c = ?
                WHERE idusuario_c=?';
        $params = array($this->usuario_c, $this->idestado_usuario_c, $this->idusuario_c);
        return Database::ejecutarSentencia($sql, $params);
    }

    /* buscador */
    public function buscarUsuariosClientes($value)
    {
        $sql = 'SELECT tuc.idusuario_c, tuc.usuario_c, tuc.idestado_usuario_c, teuc.estado_usuario_c 
        FROM tbusuario_cliente tuc 
        INNER JOIN tbestado_usuario_c teuc 
        ON tuc.idestado_usuario_c = teuc.idestado_usuario_c 
        WHERE tuc.usuario_c ILIKE ?
        ORDER BY tuc.idusuario_c ASC';
        $params = array("%$value%");
        return Database::obtenerSentencias($sql, $params);
    }
}
