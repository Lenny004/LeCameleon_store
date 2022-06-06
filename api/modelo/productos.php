<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Productos extends Validator
{
    // Declaración de atributos (propiedades).
    private $idproducto = null;
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
    private $idcategoria = null;
    private $link = '../images/productos/';
    private $promedio = null;
    private $total_resenia = null;

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setId($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idproducto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setIdCategoria($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idcategoria = $value;
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

    public function setBuscador($value){
        if ($this->validateString($value, 1, 35)) {
            $this->buscador = $value;
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
    
    public function setPromedio($value)
    {
        $this->promedio = $value;
    }

    public function setTotalResenias($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->total_resenia = $value;
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
        return $this->idproducto;
    }

    public function getIdCategoria()
    {
        return $this->idcategoria;
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

    public function getPromedio()
    {
        return $this->promedio;
    }

    public function getTotalResenia()
    {
        return $this->total_resenia;
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    //funcionamiento search para hacer busquedas de los productos que querramos
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
    //funcionamiento para cargar las cards de productos

    
    public function readProductosCategoria($id, $buscador)
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tip.imagen_producto, tc.idcategoria_producto
            FROM tbproducto tp
            LEFT JOIN tbimagen_producto tip
            ON tp.idproducto = tip.idproducto
            LEFT JOIN tbsubcategoria_producto tsc
            ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
            LEFT JOIN tbcategoria tc
            ON tc.idcategoria_producto = tsc.idcategoria_producto
            WHERE tp.idproducto not in (SELECT idproducto FROM tbproducto LIMIT 0)
            AND tp.existencias > 0
            AND tp.idestado_producto = 1
            AND tc.idcategoria_producto = ?
            AND tp.nombre_producto ILIKE ?
            ORDER BY idproducto ASC LIMIT 6';
        $params = array($id, "%$buscador%");
        return Database::obtenerSentencias($sql, $params);
    }
    
    public function readProductossubCategoria($id, $buscador)
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto , tp.precio_producto, tip.imagen_producto
                FROM tbproducto tp
                LEFT JOIN tbimagen_producto tip
                ON tp.idproducto = tip.idproducto
                LEFT JOIN tbinventario ti
                ON tp.idproducto = ti.idproducto
                LEFT JOIN tbsubcategoria_producto tsc
                ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
                WHERE tp.idproducto not in (SELECT idproducto FROM tbproducto LIMIT 0)
                AND idestado_producto = 1
                AND cantidad >= 1
                AND tp.idsubcategoria_producto = ?
                AND tp.nombre_producto ILIKE ?
                ORDER BY idproducto ASC LIMIT 6';
        $params = array($id, "%$buscador%");
        return Database::obtenerSentencias($sql, $params);
    }

    public function leerUnProducto()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.descripcion, tp.material, tp.tamaño, tp.existencias, tp.porcentaje_descuento, tp.precio_producto, tc.color, tm.nombre_marca, idestado_producto
        FROM tbproducto tp
        INNER JOIN tbcolor tc
        ON tp.idcolor = tc.idcolor
        INNER JOIN tbmarca tm
        ON tp.id_marca = tm.id_marca
        WHERE idproducto = ?';
        $params = array($this->idproducto);
        return Database::obtenerSentencia($sql, $params);
    }

    public function promedioValoraciones()
    {
        $sql = 'SELECT AVG(valoraciones) as promedio FROM public.tbvaloraciones WHERE idproducto = ?';
        $params = array($this->idproducto);
        if($data = Database::obtenerSentencia($sql, $params)){
            $this->setPromedio($data['promedio']);
            return true;
        }else{
            return false;
        }
    }

    public function totalResenias()
    {
        $sql = 'SELECT COUNT(reseña) as total_resenia FROM public.tbvaloraciones WHERE idproducto = ?';
        $params = array($this->idproducto);
        if($data = Database::obtenerSentencia($sql, $params)){
            $this->setTotalResenias($data['total_resenia']);
            return true;
        }else{
            return false;
        }
    }

    public function valoracionesProducto(){
        $sql = 'SELECT tv.idvaloracion, tv.valoraciones, tv.reseña, tv.fecha_publicacion, tuc.nombre_cliente, tuc.apellido_cliente
        FROM tbvaloraciones tv
        INNER JOIN tbusuario_cliente tuc
        ON tuc.idusuario_c = tv.idusuario_c
        WHERE tv.idestado_valoracion = 1 AND idproducto = ?';
        $params = array($this->getId());
        return Database::obtenerSentencias($sql, $params);
    }
}