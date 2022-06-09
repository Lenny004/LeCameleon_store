<?php
/*
*   Clase para realizar las operaciones en la base de datos.
*/
class Database
{
    // Propiedades de la clase para manejar las acciones respectivas.
    private static $conexion = null;
    private static $estado = null;
    private static $error = null;

    /*
    *   Método para establecer la conexión con el servidor de base de datos.
    */
    private static function Conexion()
    {
        // Credenciales para establecer la conexión con la base de datos.
        $server = 'localhost';
        $database = 'dblecameleon';
        $username = 'postgres';
        $password = '1234';
        // Self hace referencia a la clase actual y se esta usando cuando instancia dicha clase, es decir se usan métodos estáticos. 
        // This hace referencia al objeto actual, es decir, cuando una clase si tiene instancia. No se puede hacer referencia a métodos estáticos usando this pero si a métodos públicos, privados y protegidos.
        // Se crea la conexión mediante la extensión PDO y el controlador para PostgreSQL.
        self::$conexion = new PDO('pgsql:host=' . $server . ';dbname=' . $database . ';port=5432', $username, $password);
    }

    /*
    *   Método para ejecutar las siguientes sentencias SQL: insert, update y delete.
    *   Parámetros: $query (sentencia SQL) y $values (arreglo de valores para la sentencia SQL).
    *   Retorno: booleano (true si la sentencia se ejecuta satisfactoriamente o false en caso contrario).
    */
    public static function ejecutarSentencia($query, $values)
    {
        try {
            self::Conexion();
            self::$estado = self::$conexion->prepare($query);
            $state = self::$estado->execute($values);
            // Se anula la conexión con el servidor de base de datos.
            self::$conexion = null;
            return $state;
        } catch (PDOException $error) {
            // Se obtiene el código y el mensaje de la excepción para establecer un error personalizado.
            self::setException($error->getCode(), $error->getMessage());
            return false;
        }
    }

    /*
    *   Método para obtener el valor de la llave primaria del último registro insertado.
    *   Parámetros: $query (sentencia SQL) y $values (arreglo de valores para la sentencia SQL).
    *   Retorno: numérico entero (último valor de la llave primaria si la sentencia se ejecuta satisfactoriamente o 0 en caso contrario).
    */
    public static function ultimaSentencia($query, $values)
    {
        try {
            self::Conexion();
            self::$estado = self::$conexion->prepare($query);
            if (self::$estado->execute($values)) {
                $id = self::$conexion->lastInsertId();
            } else {
                $id = 0;
            }
            // Se anula la conexión con el servidor de base de datos.
            self::$conexion = null;
            return $id;
        } catch (PDOException $error) {
            // Se obtiene el código y el mensaje de la excepción para establecer un error personalizado.
            self::setException($error->getCode(), $error->getMessage());
            return 0;
        }
    }

    /*
    *   Método para obtener un registro de una sentencia SQL tipo SELECT.
    *   Parámetros: $query (sentencia SQL) y $values (arreglo de valores para la sentencia SQL).
    *   Retorno: arreglo asociativo del registro si la sentencia SQL se ejecuta satisfactoriamente o false en caso contrario.
    */
    public static function obtenerSentencia($query, $values)
    {
        try {
            self::Conexion();
            self::$estado = self::$conexion->prepare($query);
            self::$estado->execute($values);
            // Se anula la conexión con el servidor de base de datos.
            self::$conexion = null;
            return self::$estado->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            // Se obtiene el código y el mensaje de la excepción para establecer un error personalizado.
            self::setException($error->getCode(), $error->getMessage());
            die(self::getException());
        }
    }

    /*
    *   Método para obtener todos los registros de una sentencia SQL tipo SELECT.
    *   Parámetros: $query (sentencia SQL) y $values (arreglo de valores para la sentencia SQL).
    *   Retorno: arreglo asociativo de los registros si la sentencia SQL se ejecuta satisfactoriamente o false en caso contrario.
    */
    public static function obtenerSentencias($query, $values)
    {
        try {
            self::Conexion();
            self::$estado = self::$conexion->prepare($query);
            self::$estado->execute($values);
            // Se anula la conexión con el servidor de base de datos.
            self::$conexion = null;
            return self::$estado->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            // Se obtiene el código y el mensaje de la excepción para establecer un error personalizado.
            self::setException($error->getCode(), $error->getMessage());
            die(self::getException());
        }
    }

    /*
    *   Método para establecer un mensaje de error personalizado al ocurrir una excepción.
    *   Parámetros: $code (código del error) y $message (mensaje original del error).
    *   Retorno: ninguno.
    */
    private static function setException($code, $mensaje)
    {
        // Se asigna el mensaje del error original por si se necesita.
        self::$error = utf8_encode($mensaje);
        // Se compara el código del error para establecer un error personalizado.
        switch ($code) {
            case '7':
                self::$error = utf8_encode($mensaje);
                break;
            case '42703':
                self::$error = 'Nombre de campo desconocido';
                break;
            case '23505':
                self::$error = 'Dato duplicado, no se puede guardar';
                break;
            case '42P01':
                self::$error = 'Nombre de tabla desconocido';
                break;
            case '23503':
                self::$error = 'Registro ocupado, es un dato padre y no se puede eliminar // Verifique que los datos padres requeridos existen';
                break;
            default:
                self::$error = 'Ocurrió un problema en la base de datos';
        }
    }

    /*
    *   Método para obtener un error personalizado cuando ocurre una excepción.
    *   Parámetros: ninguno.
    *   Retorno: error personalizado de la sentencia SQL o de la conexión con el servidor de base de datos.
    */
    public static function getException()
    {
        return self::$error;
    }
}
