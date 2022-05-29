<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos.
*   Es clase hija de Validator.
*/
class Pedidos extends Validator
{
    // Declaración de atributos (propiedades).
    private $direccionPedido = null;
    private $nombreCliente = null;
    private $apellidoCliente = null;
    private $fechaEntrega = null;
    private $idestado_pedido = null;
    private $idenvio_pedido = null;
    private $idusuario_e = null;

    public function setIdEnvioPedido($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idenvio_pedido = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setIdUsuarioE($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idusuario_e = $value;
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
        WHERE tep.idfactura = tf.idfactura AND tf.idestado_factura = tef.idestado_factura AND tf.idusuario_c = tuc.idusuario_c AND (tep.fecha_entrega_pedido != ? AND tef.idestado_factura != 1) ORDER BY tep.fecha_entrega_pedido ASC';
        $params = array($fecha_actual);
        return Database::obtenerSentencias($sql, $params);
    }

    /* Traer los datos de un usuario si el usuario ingresado existe */
    public function existenciaPedidosHoy($fecha_actual)
    {
        $sql = 'SELECT tep.idenvio_pedido, tep.direccion_entrega_pedido, tep.fecha_entrega_pedido, tuc.nombre_cliente, tuc.apellido_cliente, tf.idestado_factura
        FROM tbenvio_pedido tep, tbfactura tf, tbestado_factura  tef, tbusuario_cliente tuc 
        WHERE tep.idfactura = tf.idfactura AND tf.idestado_factura = tef.idestado_factura AND tf.idusuario_c = tuc.idusuario_c AND (tep.fecha_entrega_pedido = ? AND tef.idestado_factura != 1 AND tef.idestado_factura != 4) ORDER BY tep.fecha_entrega_pedido ASC';
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
}