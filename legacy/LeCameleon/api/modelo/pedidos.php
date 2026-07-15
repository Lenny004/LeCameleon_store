<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos.
*   Es clase hija de Validator.
*/
class Pedidos extends Validator
{
    // Declaración de atributos (propiedades).
    private $idenvio_pedido = null;
    private $idusuario_e = null;
    private $idusuario_c = null;
    private $id_producto = null;
    private $valoracion = null;
    private $resenia = null;

    public function setIdEnvioPedido($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idenvio_pedido = $value;
            return true;
        } else {
            return false;
        }
    }

    //Asignamos un entero para el id del producto
    public function setIdProducto($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->id_producto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setValoracion($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->valoracion = $value;
            return true;
        } else {
            return false;
        }
    }

    //Asignamos un entero para el id del usuario empleado
    public function setIdUsuarioE($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idusuario_e = $value;
            return true;
        } else {
            return false;
        }
    }

    //Asignamos un entero para el id del usuario empleado
    public function setIdUsuarioC($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idusuario_c = $value;
            return true;
        } else {
            return false;
        }
    }

    //Validamos que pueda o no escribir en reseña
    public function setResenia($value)
    {
        if ($this->validateString($value, 0, 500)) {
            $this->resenia = $value;
            return true;
        } else {
            return false;
        }
    }

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getIdUsuarioE()
    {
        return $this->idusuario_e;
    }

    public function getIdEnvioPedido()
    {
        return $this->idenvio_pedido;
    }

    /* Traer los datos de un usuario si el usuario ingresado existe */
    public function validarExistenciaPedidos($fecha_actual)
    {
        $sql = 'SELECT tep.idenvio_pedido, tep.direccion_entrega_pedido, tep.fecha_entrega_pedido, tuc.nombre_cliente, tuc.apellido_cliente, tf.idestado_factura
        FROM tbenvio_pedido tep, tbfactura tf, tbestado_factura  tef, tbusuario_cliente tuc 
        WHERE tep.idfactura = tf.idfactura AND tf.idestado_factura = tef.idestado_factura AND tf.idusuario_c = tuc.idusuario_c AND (tep.fecha_entrega_pedido != ? AND tef.idestado_factura != 1 AND tef.idestado_factura != 5) ORDER BY tep.fecha_entrega_pedido ASC';
        $params = array($fecha_actual);
        return Database::obtenerSentencias($sql, $params);
    }

    /* Traer los datos de un usuario si el usuario ingresado existe */
    public function existenciaPedidosHoy($fecha_actual)
    {
        $sql = 'SELECT tep.idenvio_pedido, tep.direccion_entrega_pedido, tep.fecha_entrega_pedido, tuc.nombre_cliente, tuc.apellido_cliente, tf.idestado_factura
        FROM tbenvio_pedido tep, tbfactura tf, tbestado_factura  tef, tbusuario_cliente tuc 
        WHERE tep.idfactura = tf.idfactura AND tf.idestado_factura = tef.idestado_factura AND tf.idusuario_c = tuc.idusuario_c AND (tep.fecha_entrega_pedido = ? AND tef.idestado_factura != 1 AND tef.idestado_factura != 4 AND tef.idestado_factura != 5) ORDER BY tep.fecha_entrega_pedido ASC';
        $params = array($fecha_actual);
        return Database::obtenerSentencias($sql, $params);
    }

    public function asignarFacturaEmpleado(){
        $sql = 'UPDATE tbfactura SET idusuario_e = ?, idestado_factura = 4 
        FROM tbenvio_pedido 
        WHERE tbenvio_pedido.idfactura = tbfactura.idfactura AND tbenvio_pedido.idenvio_pedido = ?';
        $params = array($this->getIdUsuarioE(), $this->getIdEnvioPedido());
        return Database::ejecutarSentencia($sql, $params);
    }

    
    public function leerUnPedido()
    {
        $sql = 'SELECT tep.idenvio_pedido, tep.direccion_entrega_pedido, tep.fecha_entrega_pedido, tuc.nombre_cliente, tuc.apellido_cliente, tf.idestado_factura
        FROM tbenvio_pedido tep, tbfactura tf, tbestado_factura  tef, tbusuario_cliente tuc 
        WHERE tep.idfactura = tf.idfactura AND tep.idenvio_pedido = ? AND tf.idestado_factura = tef.idestado_factura AND tf.idusuario_c = tuc.idusuario_c';
        $params = array($this->getIdEnvioPedido());
        return Database::obtenerSentencia($sql, $params);
    }

    /*-------------------------------------------Pedidos Cliente-------------------------------------------*/
    //Metodo para cargar los pedidos realizados
    public function pedidosCliente($idusuario_cliente, $fecha_actual){
        $sql = 'SELECT distinct tep.idenvio_pedido , tep.direccion_entrega_pedido, tep.fecha_entrega_pedido, tf.idestado_factura, tdf.cantidad_producto, tdf.total_producto, tp.idproducto, tp.nombre_producto, tef.idestado_factura FROM tbenvio_pedido tep
        INNER JOIN tbfactura tf
        ON tep.idfactura = tf.idfactura
        INNER JOIN tbdetalle_factura tdf
        ON tf.idfactura = tdf.idfactura
        INNER JOIN tbproducto tp
        ON tdf.idproducto = tp.idproducto
        INNER JOIN tbestado_factura tef
		ON tef.idestado_factura = tf.idestado_factura
        WHERE tf.idusuario_c = ? AND tef.idestado_factura !=1 AND tep.fecha_entrega_pedido != ?';
        $params = array($idusuario_cliente, $fecha_actual);
        return Database::obtenerSentencias($sql, $params);
    }

    //Método para cargar pedidos con fecha de entrega actual
    public function pedidosClienteEntregaHoy($idusuario_cliente, $fecha_actual){
        $sql = 'SELECT distinct tep.idenvio_pedido , tep.direccion_entrega_pedido, tep.fecha_entrega_pedido, tf.idestado_factura, tdf.cantidad_producto, tdf.total_producto, tp.idproducto, tp.nombre_producto, tef.idestado_factura FROM tbenvio_pedido tep
        INNER JOIN tbfactura tf
        ON tep.idfactura = tf.idfactura
        INNER JOIN tbdetalle_factura tdf
        ON tf.idfactura = tdf.idfactura
        INNER JOIN tbproducto tp
        ON tdf.idproducto = tp.idproducto
        INNER JOIN tbestado_factura tef
		ON tef.idestado_factura = tf.idestado_factura
        WHERE tf.idusuario_c = ? AND tef.idestado_factura !=1 AND tep.fecha_entrega_pedido = ?';
        $params = array($idusuario_cliente, $fecha_actual);
        return Database::obtenerSentencias($sql, $params);
    }

    //Método para traer los pedidos entregados con anterioridad
    public function pedidosEntregadosCliente($idusuario_cliente){
        $sql = 'SELECT distinct tep.idenvio_pedido, tep.fecha_entrega_pedido, tf.idestado_factura, tdf.cantidad_producto, tf.idfactura, tdf.total_producto, tp.idproducto, tp.nombre_producto, tv.idusuario_c FROM tbenvio_pedido tep
        INNER JOIN tbfactura tf
        ON tep.idfactura = tf.idfactura
        INNER JOIN tbdetalle_factura tdf
        ON tf.idfactura = tdf.idfactura
        INNER JOIN tbproducto tp
        ON tdf.idproducto = tp.idproducto
        INNER JOIN tbestado_factura tef
		ON tef.idestado_factura = tf.idestado_factura
		LEFT JOIN tbvaloraciones tv
		ON tv.idusuario_c = tf.idusuario_c
        WHERE tf.idusuario_c = ? AND tef.idestado_factura = 1
        ORDER BY tv.idusuario_c';
        $params = array($idusuario_cliente);
        return Database::obtenerSentencias($sql, $params);
    }

    //Traer el nombre para saber el dato a actualizar
    public function traerProducto()
    {
        $sql = 'SELECT idproducto, nombre_producto FROM tbproducto WHERE idproducto = ?';
        $params = array($this->id_producto);
        return Database::obtenerSentencia($sql, $params);
    }

    //Proceso para agregar reseñas del cliente a un producto
    public function agregarResenia($fecha_actual){
        $sql = 'INSERT INTO tbvaloraciones(
            valoraciones, "reseña", fecha_publicacion, idproducto, idestado_valoracion, idusuario_c)
            VALUES (?, ?, ?, ?, ?, ?)';
        $params = array($this->valoracion, $this->resenia, $fecha_actual, $this->id_producto, 1, $this->idusuario_c);
        return Database::ejecutarSentencia($sql, $params);
    }
}