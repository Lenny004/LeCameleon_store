<?php
//Clase de existencias
class Existencias extends Validator 
{
    //Declarar atributos de Existencias
    private $id = null;
    private $nombrep = null;
    private $estado = null;
    private $marca = null;
    private $subcategoria = null;
    private $distribuidor = null;
    private $existencias = null;
    private $cantidad = null;
    private $precio = null;

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
            $this->nombrep = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstado($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setMarca($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->marca = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setSubcategoria($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->subcategoria = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setdistribuidor($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->distribuidor = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setExistencias($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->existencias = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCantidad($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->cantidad = $value;
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

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombrep;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function getSubcategoria()
    {
        return $this->subcategoria;
    }

    public function getdistribuidor()
    {
        return $this->distribuidor;
    }

    public function getExistencias()
    {
        return $this->existencias;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    /*
    *  Metodo para realizar las operaciones SCRUD que en este caso solo se ocuparan dos, que serán SEARCH y READ.
    */

    //Esta función sirve para mostrar todos los datos de la tb prodcutos a la página web
    public function readAll()
    {
        //se hace un inner join en este caso para poder unir tb para el funcionamiento de esta como la de producto, estado producto, marca, subcategoria, distribuidor, inventario y se ordenan por el nombre del producto
        $sql = 'SELECT tbproducto.idproducto, nombre_producto, estado_producto, nombre_marca, subcategoria_producto, nombre_distribuidor, existencias, cantidad, precio_producto
                FROM tbproducto 
                INNER JOIN tbestado_producto
                ON tbproducto.idestado_producto = tbestado_producto.idestado_producto
                INNER JOIN tbmarca
                ON tbproducto.id_marca = tbmarca.id_marca
                INNER JOIN tbsubcategoria_producto
                ON tbproducto.idsubcategoria_producto = tbsubcategoria_producto.idsubcategoria_producto
                INNER JOIN tbdistribuidor
                ON tbproducto.iddistribuidor = tbdistribuidor.iddistribuidor
                LEFT JOIN tbinventario
                ON tbproducto.idproducto = tbinventario.idproducto
                ORDER BY nombre_producto';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }
    //Esta función sirve para buscar datos de la tb hacia la página web
    public function searchRows($value)
    {
                //se hace un inner join en este caso para poder unir tb para el funcionamiento de esta como la de producto, estado producto, marca, subcategoria, distribuidor, inventario y mandas a buscar datos mediante el nombre
        $sql = 'SELECT tbproducto.idproducto, nombre_producto, estado_producto, nombre_marca, subcategoria_producto, nombre_distribuidor, existencias, cantidad, precio_producto
                FROM tbproducto 
                INNER JOIN tbestado_producto
                ON tbproducto.idestado_producto = tbestado_producto.idestado_producto
                INNER JOIN tbmarca
                ON tbproducto.id_marca = tbmarca.id_marca
                INNER JOIN tbsubcategoria_producto
                ON tbproducto.idsubcategoria_producto = tbsubcategoria_producto.idsubcategoria_producto
                INNER JOIN tbdistribuidor
                ON tbproducto.iddistribuidor = tbdistribuidor.iddistribuidor
                LEFT JOIN tbinventario
                ON tbproducto.idproducto = tbinventario.idproducto
                WHERE nombre_producto ILIKE ? OR nombre_distribuidor ILIKE ?
                ORDER BY nombre_producto';         
        $params = array("%$value%", "%$value%");
        return Database::obtenerSentencias($sql, $params);
    }
}