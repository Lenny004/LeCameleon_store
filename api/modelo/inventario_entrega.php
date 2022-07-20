<?php
class inventario_entrega extends Validator
{

    /**
     *  Declaración de variables globales
     */

    private $id_inventario = null;
    private $id_producto = null;
    private $cantidad = null;
    private $precio = null;
    private $fecha_entrega = null;
    private $fecha_inicio = null;
    //atributo para el pagination
    private $contador = 0;

    /*
    * Métodos para guardar datos en las variables globales
    */

    public function set_id_inventario($valor)
    {
        if ($this->id_inventario = $valor) {
            return true;
        } else {
            return false;
        }
    }

    public function set_id_producto($valor)
    {
        if ($this->id_producto = $valor) {
            return true;
        } else {
            return false;
        }
    }
    public function set_cantidad($valor)
    {
        if ($this->cantidad = $valor) {
            return true;
        } else {
            return false;
        }
    }
    public function set_precio($valor)
    {
        if ($this->precio = $valor) {
            return true;
        } else {
            return false;
        }
    }
    public function set_fecha_entrega($valor)
    {
        if ($this->fecha_entrega = $valor) {
            return true;
        } else {
            return false;
        }
    }

    public function set_fecha_inicio($valor)
    {
        if ($this->fecha_inicio = $valor) {
            return true;
        } else {
            return false;
        }
    }

    //Funcion para cargar los datos existentes en el inventario
    public function cargarTabla()
    {
        $sql = 'SELECT ti.idinventario, tp.nombre_producto, ti.fecha_entrega, ti.fecha_inicio_ventas, ti.cantidad
        FROM tbinventario ti
        INNER JOIN tbproducto tp
        ON ti.idproducto = tp.idproducto
        ORDER BY idinventario DESC LIMIT 5 OFFSET ?';
        $params = array($this->contador);
        return Database::obtenerSentencias($sql, $params);
    }

    //Función que obtener los productos en el SELECT
    public function cargarProductos()
    {
        $sql = 'SELECT idproducto, nombre_producto FROM tbproducto ORDER BY nombre_producto';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    //Función que obtener los proveedores en el SELECT
    public function cargarProveedores()
    {
        $sql = 'SELECT iddistribuidor, nombre_distribuidor FROM public.tbdistribuidor ORDER BY nombre_distribuidor;';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    //Función para cargar los productos en SELECT
    public function leerProductosBuscador($buscador)
    {
        $sql = 'SELECT idproducto, nombre_producto FROM tbproducto WHERE nombre_producto ILIKE ? ORDER BY nombre_producto';
        $params = array("%$buscador%");
        return Database::obtenerSentencias($sql, $params);
    }

    public function crear_entrega()
    {
        $sql = "INSERT INTO tbinventario (cantidad, precio_unitario, fecha_entrega, fecha_inicio_ventas, idproducto)
        VALUES(?,?,?,?,?)";
        $params = array($this->cantidad, $this->precio, $this->fecha_entrega, $this->fecha_inicio, $this->id_producto);
        return Database::ejecutarSentencia($sql, $params);
    }



    //Función que buscar productos
    public function buscar($buscador)
    {
        $sql = 'SELECT idinventario, p.nombre_producto, i.fecha_entrega, i.fecha_inicio_ventas, i.cantidad
        FROM tbinventario i, tbproducto p
        WHERE i.idproducto = p.idproducto AND (nombre_producto ILIKE ?)';
        $params = array("%$buscador%");
        return Database::obtenerSentencias($sql, $params);
    }

    //Función de buscador para CRUD
    public function buscador_seleccionador($buscador)
    {
        $sql = 'SELECT i.idinventario, p.idproducto, p.precio_producto, i.fecha_entrega, i.fecha_inicio_ventas, i.cantidad
        FROM tbinventario i, tbproducto p
        WHERE i.idproducto = p.idproducto AND (nombre_producto ILIKE ?) LIMIT 1';
        $params = array("%$buscador%");
        return Database::obtenerSentencia($sql, $params);
    }

    //Función que actualiza el registro
    public function actualizar_entrega()
    {
        $sql = "UPDATE tbinventario SET cantidad = ?, precio_unitario = ?, fecha_entrega = ?, fecha_inicio_ventas =?, idproducto = ?
        WHERE idinventario = ?;";
        $params = array($this->cantidad, $this->precio, $this->fecha_entrega, $this->fecha_inicio, $this->id_producto, $this->id_inventario);
        //print_r ($params);
        return Database::ejecutarSentencia($sql, $params);
    }
}
