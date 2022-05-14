<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos.
*   Es clase hija de Validator.
*/
class Usuarios extends Validator
{
    // Declaración de atributos (propiedades).
    private $idusuario_e = null
    private $usuario = null;
    private $intento = null; 
    private $idestadou_e = null;
    private $clave_usuario = null;
    private $hora_inactivacion = null;
    private $hora_activacion = null;
    private $hora_block = null;
    private $hora_desblock = null;

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setIdUsuarioE($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idusuario_e = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setUsuario($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->nombres = $value;
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

    public function setAlias($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->alias = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setClave($value)
    {
        if ($this->validatePassword($value)) {
            $this->clave = $value;
            /*$this->clave = password_hash($value, PASSWORD_DEFAULT);*/
            return true;
        } else {
            return false;
        }
    }

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getIdUsuarioE()
    {
        return $this->idusuario_e;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getIntento()
    {
        return $this->intento;
    }

    public function getEstadoU()
    {
        return $this->idestadou_e;
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

    public function getHoraBlock()
    {
        return $this->hora_block;
    }

    public function getHoraDesblock()
    {
        return $this->hora_desblock;
    }

    public function validarExistenciaPrimerUsuario(){
        $sql = 'SELECT * FROM tbusuario_empleado';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    /*
    *   Métodos para gestionar la cuenta del usuario.
    */
    public function checkUser($alias)
    {
        $sql = 'SELECT id_usuario FROM usuarios WHERE alias_usuario = ?';
        $params = array($alias);
        if ($data = Database::obtenerSentencia($sql, $params)) {
            $this->id = $data['id_usuario'];
            $this->alias = $alias;
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT clave_usuario FROM usuarios WHERE id_usuario = ?';
        $params = array($this->id);
        $data = Database::obtenerSentencia($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($password, $data['clave_usuario'])) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE usuarios SET clave_usuario = ? WHERE id_usuario = ?';
        $params = array($this->clave, $_SESSION['id_usuario']);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_usuario, nombres_usuario, apellidos_usuario, correo_usuario, alias_usuario
                FROM usuarios
                WHERE id_usuario = ?';
        $params = array($_SESSION['id_usuario']);
        return Database::obtenerSentencia($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE usuarios
                SET nombres_usuario = ?, apellidos_usuario = ?, correo_usuario = ?
                WHERE id_usuario = ?';
        $params = array($this->nombres, $this->apellidos, $this->correo, $_SESSION['id_usuario']);
        return Database::ejecutarSentencia($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    public function searchRows($value)
    {
        $sql = 'SELECT id_usuario, nombres_usuario, apellidos_usuario, correo_usuario, alias_usuario
                FROM usuarios
                WHERE apellidos_usuario ILIKE ? OR nombres_usuario ILIKE ?
                ORDER BY apellidos_usuario';
        $params = array("%$value%", "%$value%");
        return Database::obtenerSentencias($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO usuarios(nombres_usuario, apellidos_usuario, correo_usuario, alias_usuario, clave_usuario)
                VALUES(?, ?, ?, ?, ?)';
        $params = array($this->nombres, $this->apellidos, $this->correo, $this->alias, $this->clave);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_usuario, nombres_usuario, apellidos_usuario, correo_usuario, alias_usuario
                FROM usuarios
                ORDER BY apellidos_usuario';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT id_usuario, nombres_usuario, apellidos_usuario, correo_usuario, alias_usuario
                FROM usuarios
                WHERE id_usuario = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE usuarios 
                SET nombres_usuario = ?, apellidos_usuario = ?, correo_usuario = ?
                WHERE id_usuario = ?';
        $params = array($this->nombres, $this->apellidos, $this->correo, $this->id);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM usuarios
                WHERE id_usuario = ?';
        $params = array($this->id);
        return Database::ejecutarSentencia($sql, $params);
    }
}
