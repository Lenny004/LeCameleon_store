<?php
//Clase de existencias
class Existencias extends Validator 
{
    //Esta función sirve para mostrar todos los datos de la tb prodcutos a la página web
    public function readAll()
    {
        //se hace un inner join en este caso para poder unir tb para el funcionamiento de esta como la de producto, estado producto, marca, subcategoria, distribuidor, inventario y se ordenan por el nombre del producto
        //Se hace también Left join a la tabal inventario, porque a pesar que no exista nada en esta tabla, se debe mostrar lo demás
        //COALESCE Sirve para reemplazar en caso sea null y necesito que me lo reemplace por un valor por defecto (0) y le colocamos un as para identificar el valor de la columna
        $sql = 'SELECT tbproducto.idproducto, nombre_producto, estado_producto, nombre_marca, subcategoria_producto, nombre_distribuidor, existencias, COALESCE(cantidad,0) as cantidad, precio_producto
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