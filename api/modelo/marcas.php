<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Admin_marca extends Validator
{
    // Declaración de atributos (propiedades).
    private $idMarca = null;
    private $nombreMarca = null;
    private $imagenMarca = null;
    private $rutaImagen = '../images/marca/';

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setidMarca($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idMarca = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setnombreMarca($value)
    {
        if ($this->validateAlphanumeric($value, 1, 35)) {
            $this->nombreMarca = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setimagenMarca($file)
    {
        if ($this->validateImageFile($file, 1000, 1000)) {
            $this->imagenMarca = $this->getFileName();
            return true;
        } else {
            return false;
        }
    }

    /*
    *-----------------------------------------------------Métodos-------------------------------------------------
    */
    public function getidMarca()
    {
        return $this->idMarca;
    }

    public function getnombreMarca()
    {
        return $this->nombreMarca;
    }

    public function getimagenMarca()
    {
        return $this->imagenMarca;
    }

    public function getrutaImagen()
    {
        return $this->rutaImagen;
    }

    /*
    *---------------------------------------------Metodos Query SQL------------------------------------------------
    */

    public function createRow()
    {
        $sql = 'INSERT INTO public.tbmarca(
                nombre_marca, imagen_marca)
                VALUES (?, ?)';
        $params = array($this->nombreMarca,  $this->imagenMarca);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_marca, nombre_marca, imagen_marca
                FROM public.tbmarca
                ORDER BY id_marca DESC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT id_marca, nombre_marca, imagen_marca
                FROM public.tbmarca
                WHERE id_marca = ?';
        $params = array($this->idMarca);
        return Database::obtenerSentencia($sql, $params);
    }

    public function readOneE()
    {

        $sql = 'SELECT id_marca, nombre_marca, imagen_marca
                FROM public.tbmarca
                WHERE id_marca = ?';
        $params = array($this->idMarca);
        return Database::obtenerSentencia($sql, $params);
    }

    public function updateRow($current_image)
    {
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagenMarca) ? $this->deleteFile($this->getrutaImagen(), $current_image) : $this->imagenMarca = $current_image;

        $sql = 'UPDATE public.tbmarca
                SET nombre_marca=?, imagen_marca=?
                WHERE id_marca = ?';
        $params = array($this->nombreMarca, $this->imagenMarca, $this->idMarca);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM public.tbmarca
                WHERE id_marca = ?';
        $params = array($this->idMarca);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function mostrar_datos_tabla(){
        $sql = 'SELECT id_marca, nombre_marca, imagen_marca
                FROM public.tbmarca
                ORDER BY id_marca ASC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }
}