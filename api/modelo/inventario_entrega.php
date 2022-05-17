<?php
class inventario_entrega extends Validator{

    /**
    *  Declaración de variables globales
    */

    private $id_inventario = null;
    private $id_producto = null;
    private $cantidad = null;
    private $precio = null;
    private $fecha_entrega = null;
    private $fecha_inicio = null;


    /*
    * Métodos para guardar datos en las variables globales
    */

    public function set_id_inventario($valor)
    {
        if($this->id_inventario = $valor)
        {
            return true;
        }else{
            return false;
        }
    }

    public function set_id_producto($valor)
    {
        if($this->id_producto = $valor)
        {
            return true;
        }else{
            return false;
        }
    }
    public function set_cantidad($valor)
    {
        if($this->cantidad = $valor)
        {
            return true;
        }else{
            return false;
        }
    }
    public function set_precio($valor)
    {
        if($this->precio = $valor)
        {
            return true;
        }else{
            return false;
        }
    
    }
    public function set_fecha_entrega($valor)
    {
        if($this->fecha_entrega = $valor)
        {
            return true;
        }else{
            return false;
        }
    
    }

    public function set_fecha_inicio($valor)
    {
        if($this->fecha_inicio = $valor)
        {
            return true;
        }else{
            return false;
        }
    
    }

    //Funciones con query's

    public function cargar_tabla(){
        $sql = 'SELECT idinventario, p.nombre_producto, i.fecha_entrega, i.fecha_inicio_ventas, i.cantidad
        FROM tbinventario i, tbproducto p
        WHERE i.idproducto = p.idproducto';
        $parametros = null;
        return Database::obtenerSentencias($sql, $parametros);

    }
        
    public function crear_entrega(){
        $sql = "INSERT INTO tbinventario (cantidad, precio_unitario, fecha_entrega, fecha_inicio_ventas, idproducto)
        VALUES(?,?,?,?,?)";
        $parametros = array($this->cantidad,$this->precio, $this->fecha_entrega, $this->fecha_inicio, $this->id_producto);
        //print_r ($parametros);
        return Database::ejecutarSentencia($sql, $parametros);
    }

    //Función que obtener los productos en el SELECT
    public function productos()
    {
        $sql = 'SELECT idproducto, nombre_producto FROM tbproducto';
        $parametros = null;
        return Database::obtenerSentencias($sql, $parametros);
    }

    //Función que buscar productos
    public function buscar($buscador)
    {
        $sql = 'SELECT idinventario, p.nombre_producto, i.fecha_entrega, i.fecha_inicio_ventas, i.cantidad
        FROM tbinventario i, tbproducto p
        WHERE i.idproducto = p.idproducto AND (nombre_producto ILIKE ?)';
        $parametros = array("%$buscador%");
        return Database::obtenerSentencias($sql, $parametros);
    }

    //Función de buscador para CRUD
    public function buscador_seleccionador($buscador)
    {
        $sql = 'SELECT i.idinventario, p.idproducto, p.precio_producto, i.fecha_entrega, i.fecha_inicio_ventas, i.cantidad
        FROM tbinventario i, tbproducto p
        WHERE i.idproducto = p.idproducto AND (nombre_producto ILIKE ?) LIMIT 1';
        $parametros = array("%$buscador%");
        return Database::obtenerSentencia($sql, $parametros);
    }

    //Función que actualiza el registro
    public function actualizar_entrega(){
        $sql = "UPDATE tbinventario SET cantidad = ?, precio_unitario = ?, fecha_entrega = ?, fecha_inicio_ventas =?, idproducto = ?
        WHERE idinventario = ?;";
        $parametros = array($this->cantidad,$this->precio, $this->fecha_entrega, $this->fecha_inicio, $this->id_producto, $this->id_inventario);
        //print_r ($parametros);
        return Database::ejecutarSentencia($sql, $parametros);
    }
}

?>