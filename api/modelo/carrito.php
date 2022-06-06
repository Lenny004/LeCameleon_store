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
    private $cliente = null;
    private $producto = null;
    private $cantidad = null;
    private $total_producto = null;
    private $precio_a_producto = null;
    private $estado = null;
    /*
    *   ESTADOS PARA UN PEDIDO
    *   1: Pendiente. Es cuando el pedido esta en proceso por parte del cliente y se puede modificar el detalle.
    *   2: Finalizado. Es cuando el cliente finaliza el pedido y ya no es posible modificar el detalle.
    *   3: Entregado. Es cuando la tienda ha entregado el pedido al cliente.
    *   4: Anulado. Es cuando el cliente se arrepiente de haber realizado el pedido.
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

    public function setCliente($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->cliente = $value;
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
        // Se realiza una subconsulta para obtener el precio del producto.
        $sql = 'INSERT INTO tbdetalle_factura(total_producto, precio_actual, cantidad_descuento, cantidad_producto, idfactura, idproducto)
        VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->total_producto, $this->precio_a_producto, 0 , $this->cantidad, $this->idfactura, $this->producto);
        return Database::ejecutarSentencia($sql, $params);
    }

    // Método para obtener los productos que se encuentran en el carrito de compras.
    public function leerDetallePedido()
    {
        $sql = 'SELECT tdf.iddetalle_factura, tip.imagen_producto, tp.nombre_producto, tp.existencias, tep.estado_producto, tdf.total_producto, tdf.cantidad_producto 
        FROM tbdetalle_factura tdf
        INNER JOIN tbfactura tf
        ON tf.idfactura = tdf.idfactura
        INNER JOIN tbproducto tp
        ON tp.idproducto = tdf.idproducto
        INNER JOIN tbestado_producto tep
        ON tp.idestado_producto = tep.idestado_producto
        LEFT JOIN tbimagen_producto tip
        ON tip.idproducto = tp.idproducto
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

    // Método para actualizar la cantidad de un producto agregado al carrito de compras.
    public function updateDetail()
    {
        $sql = 'UPDATE detalle_pedido
                SET cantidad_producto = ?
                WHERE id_detalle = ? AND id_pedido = ?';
        $params = array($this->cantidad, $this->id_detalle, $_SESSION['id_pedido']);
        return Database::obtenerSentencia($sql, $params);
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
}