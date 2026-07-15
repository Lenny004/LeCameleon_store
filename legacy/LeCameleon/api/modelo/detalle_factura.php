<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Descuento extends Validator 
{
    private $iddetalle_factura = null;
    private $total_producto = null;
    private $precio_actual = null;
    private $cantidad_descuento = null;
    private $cantidad_producto = null;
    private $idfactura = null;
    private $idproducto = null;

    //Metodos de validaciones

    public function setIddetalle_factura($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->iddetalle_factura = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTotal_producto($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->total_producto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setPrecio_actual($value)
    {
        if ($this->validateMoney($value)) {
            $this->precio_actual = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCantidad_descuento($value)
    {
        if ($this->validacionPorcentaje($value)) {
            $this->cantidad_descuento = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCantidad_producto($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->cantidad_producto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setIdfactura($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idfactura = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setIdproducto($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idproducto = $value;
            return true;
        } else {
            return false;
        }
    }

    //Metodos Get

    public function getiddetalle_factura()
    {
        return $this->iddetalle_factura;
    }

    public function gettotal_producto()
    {
        return $this->total_producto;
    }

    public function getprecio_actual()
    {
        return $this->precio_actual;
    }

    public function getcantidad_descuento()
    {
        return $this->cantidad_descuento;
    }

    public function getcantidad_producto()
    {
        return $this->cantidad_producto;
    }

    public function getidfactura()
    {
        return $this->idfactura;
    }

    public function getidproducto()
    {
        return $this->idproducto;
    }

    //Metodo Read all
    public function readAll()
    {
        $sql = 'SELECT iddetalle_factura, total_producto, precio_actual, cantidad_descuento, cantidad_producto, idfactura, idproducto
        FROM tbdetalle_factura
        WHERE cantidad_descuento != 0.00';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    //Metodos para reporte de producto mas vendido con descuento
    public function ProductoDescuento()
    {
        $sql = 'SELECT idproducto, nombre_producto, precio_producto, porcentaje_descuento
        FROM tbproducto
        WHERE porcentaje_descuento > 0
        ORDER BY idproducto DESC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    //Metodos para reporte de producto mas vendido con descuento
    public function ProductoVendido()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, SUM(tdf.cantidad_producto) as cantidad_vendida
        FROM tbproducto tp
        INNER JOIN tbdetalle_factura tdf using(idproducto)
        GROUP BY tp.idproducto
        ORDER BY idproducto ASC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }
}