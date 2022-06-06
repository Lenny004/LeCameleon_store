<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Productos extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $nombre = null;
    private $descripcion = null;
    private $material = null;
    private $tamaño = null;
    private $existencias = null;
    private $descuento = null;
    private $precio = null;
    private $color = null;
    private $marca = null;
    private $distribuidor = null;
    private $estado = null;
    private $subc = null;
    private $imagen = null;
    private $link = '../images/productos/';

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

    public function setDescripcion($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->descripcion = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setMaterial($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->material = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTamaño($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->tamaño = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setExistencias($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->existencias = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDescuento($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->descuento = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setPrecio($value)
    {
        if ($this->validateMoney($value)) {
            $this->precio = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setColor($value)
    {
        if ($this->validateBoolean($value)) {
            $this->color = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setMarca($value)
    {
        if ($this->validateBoolean($value)) {
            $this->marca = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDistribuidor($value)
    {
        if ($this->validateBoolean($value)) {
            $this->distribuidor = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstado($value)
    {
        if ($this->validateBoolean($value)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setSubc($value)
    {
        if ($this->validateBoolean($value)) {
            $this->subc = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setImagen($file)
    {
        if ($this->validateImageFile($file, 2000, 2000)) {
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

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getMaterial()
    {
        return $this->material;
    }

    public function getTamaño()
    {
        return $this->tamaño;
    }

    public function getExistencias()
    {
        return $this->existencias;
    }

    public function getDescuento()
    {
        return $this->descuento;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function getDistribuidor()
    {
        return $this->distribuidor;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getSubc()
    {
        return $this->subc;
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

    public function createRow()
    {
        $sql = 'INSERT INTO public.tbproducto(
            nombre_producto, descripcion, material, "tamaño", existencias, porcentaje_descuento, precio_producto, idcolor, id_marca, iddistribuidor, idestado_producto, idsubcategoria_producto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->descripcion, $this->material, $this->tamaño,  $this->existencias,  $this->descuento,  $this->precio,  $this->color,  $this->marca,  $this->distribuidor,  $this->estado,  $this->subc);
        return Database::ejecutarSentencia($sql, $params);
    }


    public function readAll()
    {
        $sql = 'SELECT "idproducto", "nombre_producto", "descripcion", "material", "tamaño", "existencias", "porcentaje_descuento", "precio_producto", "tbcolor"."color", "tbmarca"."nombre_marca", "tbdistribuidor"."nombre_distribuidor", "tbestado_producto"."estado_producto", "tbsubcategoria_producto"."subcategoria_producto"
        FROM "tbproducto"
        INNER JOIN "tbcolor" ON "tbcolor"."idcolor" = "tbproducto"."idcolor"
        INNER JOIN "tbmarca" ON "tbmarca"."id_marca" = "tbproducto"."id_marca"
        INNER JOIN "tbdistribuidor" ON "tbdistribuidor"."iddistribuidor" = "tbproducto"."iddistribuidor"
        INNER JOIN "tbestado_producto" ON "tbestado_producto"."idestado_producto" = "tbproducto"."idestado_producto"
        INNER JOIN "tbsubcategoria_producto" ON "tbsubcategoria_producto"."idsubcategoria_producto" = "tbproducto"."idsubcategoria_producto"    
        ORDER BY "nombre_producto"';
        $params = null;
        return Database::obtenerSentencia($sql, $params);
    }

    public function obtenerColor()
    {
        $sql = 'SELECT idcolor, color
        FROM public.tbcolor';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function obtenerEstadoProducto()
    {
        $sql = 'SELECT idestado_producto, estado_producto
        FROM public.tbestado_producto';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT idproducto, nombre_producto, descripcion, material, "tamaño", existencias, porcentaje_descuento, precio_producto, idcolor, id_marca, iddistribuidor, idestado_producto, idsubcategoria_producto
        FROM public.tbproducto
        WHERE idproducto = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }
    public function readOneE()
    {
        
        $sql = 'SELECT idproducto, nombre_producto, descripcion, material, "tamaño", existencias, porcentaje_descuento, precio_producto, idcolor, id_marca, iddistribuidor, idestado_producto, idsubcategoria_producto
        FROM public.tbproducto
        WHERE idproducto = ?';
        $params = array($this->id);
        return Database::obtenerSentencia($sql, $params);
    }

    public function updateRow($current_image)
    {
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagen) ? $this->deleteFile($this->getLink(), $current_image) : $this->imagen = $current_image;

        $sql = 'UPDATE public.tbproducto
        SET nombre_producto=?, descripcion=?, material=?, "tamaño"=?, existencias=?, porcentaje_descuento=?, precio_producto=?, idcolor=?, id_marca=?, iddistribuidor=?, idestado_producto=?, idsubcategoria_producto=?
        WHERE idproducto = ?';
        $params = array($this->nombre, $this->descripcion, $this->material, $this->tamaño, $this->existencias, $this->descuento, $this->precio, $this->color, $this->marca, $this->distribuidor,$this->estado, $this->subc, $this->id);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM public.tbproducto
                WHERE idproducto = ?';
        $params = array($this->id);
        return Database::ejecutarSentencia($sql, $params);
    }
}
