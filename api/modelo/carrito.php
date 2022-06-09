<?php
/*
*	Clase para manejar las tablas pedidos y detalle_pedido de la base de datos.
*   Es clase hija de Validator.
*/
class Carrito extends Validator
{
    // Declaración de atributos (propiedades).
    private $idfactura = null;
    private $id_detalle = null;
    private $idcliente = null;
    private $producto = null;
    private $cantidad = null;
    private $total_producto = null;
    private $precio_a_producto = null;
    private $estado = null;
    private $direccion_entrega = null;
    private $monto_total = null;
    private $fecha_entrega = null;

    /*
    *   ESTADOS PARA UN PEDIDO
    *   1: Cancelado. Es cuando el pedido ya ha sido entregado al cliente
    *   2: Pendiente. Es cuando el pedido esta en espera de que sea la fecha de entrega.
    *   3: Retrasada. Es cuando la fecha de entrega ya ha pasado.
    *   4: Entregando. Es cuando el pedido esta en camino de ser entregado al cliente.
    *   5: Editando. Es cuando el cliente se encuentra en el proceso de compra y ha seleccionado el producto.
    */

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setIdFactura($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idfactura = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setIdDetalleF($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->id_detalle = $value;
            return true;
        } else {
            return false;
        }
    }

    //
    public function setCliente($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idcliente = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setProducto($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->producto = $value;
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

    public function setTotalProducto($value)
    {
        $this->total_producto = $value;
        return true;
    }

    public function setPrecioAProducto($value)
    {
        $this->precio_a_producto = $value;
        return true;
    }

    public function setEstado($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDireccion($value)
    {
        if ($this->validateString($value, 10, 500)) {
            $this->direccion_entrega = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setFechaEntrega($value)
    {
        if ($this->validateDate($value)) {
            $this->fecha_entrega = $value;
            return true;
        } else {
            return false;
        }
    }

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getIdFactura()
    {
        return $this->idfactura;
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    // Método para verificar si existe un pedido en proceso para seguir comprando, de lo contrario se crea uno.
    public function ordenIniciada()
    {
        date_default_timezone_set('America/El_Salvador');
        $fecha_actual = date('Y-m-d h:i:s', time());
        $this->estado = 5;
        $sql = 'SELECT idfactura FROM tbfactura WHERE idestado_factura = ? AND idusuario_c = ?';
        $params = array($this->estado, $_SESSION['idusuario_c']);
        if ($data = Database::obtenerSentencia($sql, $params)) {
            $this->idfactura = $data['idfactura'];
            return true;
        } else {
            $sql = 'INSERT INTO tbfactura(fecha_factura,idestado_factura, idusuario_c) VALUES(?, ?, ?)';
            $params = array($fecha_actual, $this->estado, $_SESSION['idusuario_c']);
            // Se obtiene el ultimo valor insertado en la llave primaria de la tabla pedidos.
            if ($this->idfactura = Database::ultimaSentencia($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }

    // Método para agregar un producto al carrito de compras.
    public function crearDetalleFactura()
    {
        $sql = 'INSERT INTO tbdetalle_factura(total_producto, precio_actual, cantidad_descuento, cantidad_producto, idfactura, idproducto)
        VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->total_producto, $this->precio_a_producto, 0 , $this->cantidad, $this->idfactura, $this->producto);
        return Database::ejecutarSentencia($sql, $params);
    }

    // Método para obtener los datos del producto y se muestren en el detalle
    public function leerDetallePedido()
    {
        $sql = 'SELECT tdf.iddetalle_factura, tp.nombre_producto, tp.imagen_principal tp.existencias, tp.precio_producto , tep.estado_producto, tdf.total_producto, tdf.cantidad_producto 
        FROM tbdetalle_factura tdf
        INNER JOIN tbfactura tf
        ON tf.idfactura = tdf.idfactura
        INNER JOIN tbproducto tp
        ON tp.idproducto = tdf.idproducto
        INNER JOIN tbestado_producto tep
        ON tp.idestado_producto = tep.idestado_producto
        WHERE tdf.idfactura = ?';
        $params = array($this->idfactura);
        return Database::obtenerSentencias($sql, $params);
    }

    // Método para finalizar un pedido por parte del cliente.
    public function finishOrder()
    {
        // Se establece la zona horaria local para obtener la fecha del servidor.
        date_default_timezone_set('America/El_Salvador');
        $date = date('Y-m-d');
        $this->estado = 1;
        $sql = 'UPDATE pedidos
                SET estado_pedido = ?, fecha_pedido = ?
                WHERE id_pedido = ?';
        $params = array($this->estado, $date, $_SESSION['id_pedido']);
        return Database::ejecutarSentencia($sql, $params);
    }

    // Método para eliminar un producto que se encuentra en el carrito de compras.
    public function eliminarDetalle()
    {
        $sql = 'DELETE FROM tbdetalle_factura
                WHERE iddetalle_factura = ? AND idfactura = ?';
        $params = array($this->id_detalle, $_SESSION['idfactura']);
        return Database::ejecutarSentencia($sql, $params);
    }

    // Método para eliminar un producto que se encuentra en el carrito de compras.
    public function eliminarDetalles()
    {
        $sql = 'DELETE FROM tbdetalle_factura
                WHERE idfactura = ?';
        $params = array($_SESSION['idfactura']);
        return Database::ejecutarSentencia($sql, $params);
    }

    // Método para los datos del usuario cliente, y se muestren en el formulario de dirección de envio
    public function traerDatosUsuarioCliente()
    {
        $sql = 'SELECT nombre_cliente, apellido_cliente, telefono_cliente FROM tbusuario_cliente WHERE idusuario_c = ?';
        $params = array($_SESSION['idusuario_c']);
        return Database::obtenerSentencia($sql, $params);
    }

    //Método que actualiza los campos faltantes de la factura
    public function actualizarFactura()
    {
        $sql = 'UPDATE tbfactura SET monto_total = ?, idestado_factura = ? WHERE idfactura = ?';
        $params = array($this->total_producto, 2, $_SESSION['idfactura']);
        return Database::ejecutarSentencia($sql, $params);
    }

    //Crear el pedido una vez se finalice el proceso de pedido
    public function crearPedidoCliente()
    {
        $sql = 'INSERT INTO tbenvio_pedido(direccion_entrega_pedido, fecha_entrega_pedido, idfactura)
        VALUES (?, ?, ?)';
        $params = array($this->direccion_entrega, $this->fecha_entrega, $_SESSION['idfactura']);
        return Database::ejecutarSentencia($sql, $params);
    }
}