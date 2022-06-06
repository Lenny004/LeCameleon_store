<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Subcategoria extends Validator
{
    //Declaración de atributos (propiedades).
    private $idSubCategoriaProducto = null;
    private $subCategoriaProducto = null;
    private $imagenSubCategoria = null;
    private $idCategoriaProducto = null;
    private $rutaImagenS = '../images/subcategoria/';


    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setidSubCategoriaProducto($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idSubCategoriaProducto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setsubCategoriaProducto($value)
    {
        if ($this->validateAlphabetic($value, 1, 60)) {
            $this->subCategoriaProducto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setimagenSubCategoria($file)
    {
        if ($this->validateImageFile($file, 2000, 2000)) {
            $this->imagenSubCategoria = $this->getFileName();
            return true;
        } else {
            return false;
        }
    }

    public function setidCategoriaProducto($value)
    {
        if ($this->validateBoolean($value)) {
            $this->idCategoriaProducto = $value;
            return true;
        } else {
            return false;
        }
    }

    /*
    *-----------------------------------------------------Métodos-------------------------------------------------
    */
    public function getidSubCategoriaProducto()
    {
        return $this->idSubCategoriaProducto;
    }

    public function getsubCategoriaProducto()
    {
        return $this->subCategoriaProducto;
    }

    public function getimagenSubCategoria()
    {
        return $this->imagenSubCategoria;
    }

    public function getidCategoriaProducto()
    {
        return $this->idCategoriaProducto;
    }

    public function getrutaImagenS()
    {
        return $this->rutaImagenS;
    }

    /*
    *---------------------------------------------Metodos Query SQL------------------------------------------------
    */
    public function mostrarDatosTabla()
    {
        $sql = 'SELECT idsubcategoria_producto, subcategoria_producto, imagen_subcategoria, categoria_producto
        FROM tbsubcategoria_producto
        INNER JOIN tbcategoria ON tbcategoria.idcategoria_producto = tbsubcategoria_producto.idcategoria_producto
        ORDER BY categoria_producto ASC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT idsubcategoria_producto, subcategoria_producto, imagen_subcategoria, idcategoria_producto
                FROM public.tbsubcategoria_producto
                WHERE idsubcategoria_producto = ?';
        $params = array($this->idSubCategoriaProducto);
        return Database::obtenerSentencia($sql, $params);
    }

    public function crearSubcategoria()
    {
        $sql = 'INSERT INTO tbsubcategoria_producto(
                subcategoria_producto, imagen_subcategoria, idcategoria_producto)
                VALUES ( ?, ?, ?)';
        $params = array($this->subCategoriaProducto,  $this->imagenSubCategoria, $this->idCategoriaProducto);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function actualizarSubcategoria($current_image)
    {
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagenSubCategoria) ? $this->deleteFile($this->getrutaImagenS(), $current_image) : $this->imagenSubCategoria = $current_image;
        $sql = 'UPDATE public.tbsubcategoria_producto
                SET subcategoria_producto=?, imagen_subcategoria=?, idcategoria_producto=?
                WHERE idsubcategoria_producto=?';
        $params = array($this->subCategoriaProducto, $this->imagenSubCategoria, $this->idCategoriaProducto, $this->idSubCategoriaProducto);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function eliminarSubcategoria()
    {
        $sql = 'DELETE FROM tbsubcategoria_producto
        WHERE idsubcategoria_producto = ?';
        $params = array($this->idSubCategoriaProducto);
        return Database::ejecutarSentencia($sql, $params);
    }
    
    public function obtener_producto() {
        $sql = 'SELECT idcategoria_producto, categoria_producto, imagen_categoria
                FROM tbcategoria
                ORDER BY idcategoria_producto DESC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    /*
    *   Métodos para search
    */
    public function buscarSubcategorias($value)
    {
        $sql = 'SELECT idsubcategoria_producto, subcategoria_producto, imagen_subcategoria, categoria_producto
        FROM tbsubcategoria_producto
        INNER JOIN tbcategoria ON tbcategoria.idcategoria_producto = tbsubcategoria_producto.idcategoria_producto
        WHERE subcategoria_producto ILIKE ? OR categoria_producto ILIKE ?
        ORDER BY categoria_producto ASC';
        $params = array("%$value%","%$value%");
        return Database::obtenerSentencias($sql, $params);
    }
}