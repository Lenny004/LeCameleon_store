<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Categorias extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $categoria = null;
    private $imagen = null;
    private $link = '../images/categorias/';

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

    public function setCategoria($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->categoria = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setImagen($file)
    {
        if ($this->validateImageFile($file, 500, 500)) {
            $this->imagen = $this->getFileName();
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

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function getLink()
    {
        return $this->link;
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */

    public function crearCategoria()
    {
        $sql = 'INSERT INTO tbcategoria(categoria_producto, imagen_categoria)
                VALUES (?, ?)';
        $params = array($this->categoria, $this->imagen);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function mostrarDatosTabla()
    {
        $sql = 'SELECT idcategoria_producto, categoria_producto, imagen_categoria
                FROM tbcategoria
                ORDER BY categoria_producto';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT idcategoria_producto, categoria_producto, imagen_categoria
                FROM tbcategoria
                WHERE idcategoria_producto = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }

    public function updateRow($current_image)
    {
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagen) ? $this->deleteFile($this->getLink(), $current_image) : $this->imagen = $current_image;
        $sql = 'UPDATE tbcategoria
                SET categoria_producto=?, imagen_categoria=?
                WHERE idcategoria_producto=?';
        $params = array($this->categoria, $this->imagen, $this->id);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tbcategoria
                WHERE idcategoria_producto = ?';
        $params = array($this->id);
        return Database::ejecutarSentencia($sql, $params);
    }
}