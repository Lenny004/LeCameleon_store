<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos.
*   Es clase hija de Validator.
*/
class RegistroUsuariosClientes extends Validator
{
    // Declaración de atributos (propiedades).
    private $nombre_cliente = null;
    private $apellido_cliente = null;
    private $dui = null;
    private $telefono_cliente = null;
    private $correo_cliente = null;
    private $idestado_cliente = 1;
    private $contrasena = null;

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */

    /*Validar que el nombre tenga como longitud máxima 50 */
    public function setNombres($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->nombre_cliente = $value;
            return true;
        } else {
            return false;
        }
    }

    //Le asignamos los apellidos del empleado
    public function setApellidos($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->apellido_cliente = $value;
            return true;
        } else {
            return false;
        }
    }

    //Le asignamos el valor de NIT
    public function setDUI($value)
    {
        if ($this->validateDUI($value)) {
            $this->dui = $value;
            return true;
        } else {
            return false;
        }
    }

    //Le asignamos el valor del telefono de empleado
    public function setTelefono($value)
    {
        if ($this->validatePhone($value)) {
            $this->telefono_cliente = $value;
            return true;
        } else {
            return false;
        }
    }

    //Le asignamos el valor de correo
    public function setCorreoEmpleado($value)
    {
        if ($this->validateEmail($value)) {
            $this->correo_cliente = $value;
            return true;
        } else {
            return false;
        }
    }

    //Le asignamos el valor de empleado
    public function setContraEmpleado($value)
    {
        if ($this->validatePassword($value)) {
            $this->contrasena = $value;
            return true;
        } else {
            return false;
        }
    }

    //Función para registrar un usuario cliente
    public function registrarUsuarioCliente()
    {
        $sql = 'INSERT INTO tbusuario_cliente(usuario_c, contrasena_c, nombre_cliente, apellido_cliente, correo_cliente, telefono_cliente, idestado_usuario_c)
        VALUES (?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->correo_cliente, $this->contrasena, $this->nombre_cliente, $this->apellido_cliente, $this->correo_cliente, $this->telefono_cliente, $this->idestado_cliente);
        return Database::ejecutarSentencia($sql, $params);
    }
}