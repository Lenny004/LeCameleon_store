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
        if ($this->validateImages($file, 2000, 2000)) {
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

    public function crearMarca()
    {
        $sql = 'INSERT INTO public.tbmarca(
                nombre_marca, imagen_marca)
                VALUES (?, ?)';
        $params = array($this->nombreMarca,  $this->imagenMarca);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT id_marca, nombre_marca, imagen_marca
                FROM tbmarca
                WHERE id_marca = ?';
        $params = array($this->idMarca);
        return Database::obtenerSentencia($sql, $params);
    }

    public function actualizarMarca($current_image)
    {
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagenMarca) ? $this->deleteFile($this->getrutaImagen(), $current_image) : $this->imagenMarca = $current_image;
        $sql = 'UPDATE tbmarca
                SET nombre_marca=?, imagen_marca=?
                WHERE id_marca = ?';
        $params = array($this->nombreMarca, $this->imagenMarca, $this->idMarca);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function eliminarMarca()
    {
        $sql = 'DELETE FROM public.tbmarca
                WHERE id_marca = ?';
        $params = array($this->idMarca);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function mostrar_datos_tabla()
    {
        $sql = 'SELECT id_marca, nombre_marca, imagen_marca
                FROM tbmarca
                ORDER BY id_marca ASC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    /*
    *   Métodos para search
    */
    public function buscarMarcas($value)
    {
        $sql = 'SELECT id_marca, nombre_marca, imagen_marca
                FROM tbmarca
                WHERE nombre_marca ILIKE ?
                ORDER BY id_marca ASC';
        $params = array("%$value%");
        return Database::obtenerSentencias($sql, $params);
    }

    /*
    *   Método de busqueda de marcas alfabeticamente
    */
    public function buscarMarcasAlfabeticamente($value)
    {
        switch ($value) {
            case 1:
                $sql = 'SELECT id_marca, nombre_marca, imagen_marca FROM tbmarca ORDER BY nombre_marca ASC';
                $params = null;
                return Database::obtenerSentencias($sql, $params);
                break;
            case 2:
                $sql = 'SELECT id_marca, nombre_marca, imagen_marca FROM tbmarca ORDER BY nombre_marca DESC';
                $params = null;
                return Database::obtenerSentencias($sql, $params);
                break;
        }
    }
}
