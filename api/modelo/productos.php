<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Productos extends Validator
{
    //Datos a guardar-modificar del producto
    private $nombre_producto = null;
    private $descripcion = null;
    private $material = null;
    private $tamanio = null;
    private $existencias = null;
    private $descuento = null;
    private $precio = null;
    private $color = null;
    private $marca = null;
    private $distribuidor = null;
    private $estado = null;
    private $subcategoria = null;
    private $imagen = null;
    //private $imagenes = null;
    private $link = '../images/productos/';
    private $idvaloracion = null;
    private $estado_valoracion = null;
    // Declaración de atributos (propiedades).
    private $idproducto = null;
    private $idcategoria = null;
    private $idsubcategoria = null;
    private $promedio = null;
    private $total_resenia = null;
    private $min = 0;
    private $max = null;
    private $filtro = null;
    private $opcion = null;

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setIdProducto($value)
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

    public function setIdSubcategoria($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idsubcategoria = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setNombreP($value)
    {
        if ($this->validateAlphanumeric($value, 1, 75)) {
            $this->nombre_producto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDescripcion($value)
    {
        if ($this->validateString($value, 1, 1000)) {
            $this->descripcion = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setMaterial($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->material = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTamanio($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->tamanio = $value;
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

    public function setDescuento($value)
    {
        if ($this->validacionPorcentaje($value)) {
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

    public function setSubcategoria($value)
    {
        if ($this->validateBoolean($value)) {
            $this->subcategoria = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setBuscador($value)
    {
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

    public function setImagenes($file, $tamanio, $anchura, $altura, $tipo, $nombre_archivo)
    {
        if ($this->validarImagenes($file, $tamanio, $anchura, $altura, $tipo, $nombre_archivo)) {
            $this->imagenes = $this->getFileName();
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

    public function setMin($value)
    {
        $this->min = $value;
        return true;
    }

    public function setMax($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->max = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setIdValoracion($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->idvaloracion = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstadoValoracion($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->estado_valoracion = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setFiltro($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->filtro = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setOpcion($value)
    {
        if ($this->validacionNumeroNaturales($value)) {
            $this->opcion = $value;
            return true;
        } else {
            return false;
        }
    }

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getLink()
    {
        return $this->link;
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function getImagenes()
    {
        return $this->imagenes;
    }

    public function getPromedio()
    {
        return $this->promedio;
    }

    public function getTotalResenia()
    {
        return $this->total_resenia;
    }

    public function getFiltro()
    {
        return $this->filtro;
    }

    //Petición para traer los colores
    public function obtenerColor()
    {
        $sql = 'SELECT idcolor, color FROM tbcolor';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    //Petición para traer los colores
    public function obtenerEstadoProducto()
    {
        $sql = 'SELECT idestado_producto, estado_producto FROM tbestado_producto';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    //Petición para mostrar los datos del producto en la tabla
    public function obtenerDatosProductos()
    {
        $sql = 'SELECT tp.idproducto, tp.imagen_principal, tp.nombre_producto, tep.estado_producto, tsc.subcategoria_producto, tp.precio_producto
        FROM tbproducto tp
        INNER JOIN tbestado_producto tep
        ON tp.idestado_producto = tep.idestado_producto
        LEFT JOIN tbsubcategoria_producto tsc
        ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
        WHERE tp.idestado_producto != 3
        ORDER BY idproducto ASC';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    //Función para crear un producto
    public function crearProducto()
    {
        $sql = 'INSERT INTO tbproducto (nombre_producto, descripcion, material, tamanio, existencias, porcentaje_descuento, precio_producto, imagen_principal, idcolor, id_marca, iddistribuidor, idestado_producto, idsubcategoria_producto)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre_producto, $this->descripcion, $this->material, $this->tamanio,  $this->existencias,  $this->descuento,  $this->precio, $this->imagen, $this->color,  $this->marca,  $this->distribuidor,  $this->estado,  $this->subcategoria);
        return Database::ejecutarSentencia($sql, $params);
    }

    //Obtener el Id del último producto
    public function obtenerIdUltimoProducto()
    {
        $sql = 'SELECT idproducto FROM tbproducto ORDER BY idproducto DESC LIMIT 1';
        $params = null;
        if ($data = Database::obtenerSentencia($sql, $params)) {
            $this->idproducto = $data['idproducto'];
            return true;
        } else {
            return false;
        }
    }

    /*Función para crear imagenes extras */
    public function subirImagenesExtras()
    {
        $sql = 'INSERT INTO tbimagen_producto (imagen_producto, idproducto)
            VALUES (?, ?)';
        $params = array($this->imagenes, $this->idproducto);
        return Database::ejecutarSentencia($sql, $params);
    }

    //Petición para mostrar los datos del producto en la tabla
    public function buscarProducto($buscador)
    {
        $sql = 'SELECT tp.idproducto, tp.imagen_principal, tp.nombre_producto, tep.estado_producto, tsc.subcategoria_producto, tp.precio_producto
        FROM tbproducto tp
        INNER JOIN tbestado_producto tep
        ON tp.idestado_producto = tep.idestado_producto
        LEFT JOIN tbsubcategoria_producto tsc
        ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
        WHERE tp.idestado_producto != 3 AND tp.nombre_producto ILIKE ? OR tep.estado_producto ILIKE ? OR tsc.subcategoria_producto ILIKE ? OR CAST(tp.precio_producto AS VARCHAR) ILIKE ?
        ORDER BY idproducto ASC';
        $params = array("%$buscador%", "%$buscador%", "%$buscador%", "%$buscador%");
        return Database::obtenerSentencias($sql, $params);
    }

    //Obtener el Id del último producto
    public function obtenerUnProducto()
    {
        $sql = 'SELECT idproducto, nombre_producto, descripcion, material, tamanio, existencias, porcentaje_descuento, precio_producto, imagen_principal, idcolor, id_marca, iddistribuidor, idestado_producto, idsubcategoria_producto
        FROM tbproducto
        WHERE idproducto = ? AND idestado_producto != 3';
        $params = array($this->idproducto);
        return Database::obtenerSentencia($sql, $params);
    }

    //Actualizamos una Subcategoria
    public function actualizarProducto($imagen_producto)
    {
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagen) ? $this->deleteFile($this->getLink(), $imagen_producto) : $this->imagen = $imagen_producto;
        $sql = 'UPDATE tbproducto SET nombre_producto = ?, descripcion = ?, material = ?, tamanio = ?, existencias = ?, porcentaje_descuento = ?, precio_producto = ?, imagen_principal = ?, idcolor = ?, id_marca = ?, iddistribuidor = ?, idestado_producto = ?, idsubcategoria_producto = ?
        WHERE idproducto = ?';
        $params = array($this->nombre_producto, $this->descripcion, $this->material, $this->tamanio, $this->existencias, $this->descuento, $this->precio, $this->imagen, $this->color, $this->marca, $this->distribuidor, $this->estado, $this->subcategoria, $this->idproducto);
        return Database::ejecutarSentencia($sql, $params);
    }

    //Eliminamos el registro
    public function eliminarProducto()
    {
        $sql = 'UPDATE tbproducto SET idestado_producto = 3 WHERE idproducto = ?';
        $params = array($this->idproducto);
        return Database::ejecutarSentencia($sql, $params);
    }

    public function reseniasProductos()
    {
        $sql = 'SELECT tv.idvaloracion, tv.valoraciones, tv.reseña, tv.fecha_publicacion, tv.idestado_valoracion, tev.estado_valoracion, tuc.nombre_cliente, tuc.apellido_cliente
        FROM tbvaloraciones tv
        INNER JOIN tbusuario_cliente tuc
        ON tuc.idusuario_c = tv.idusuario_c
		INNER JOIN tbestado_valoracion tev
		ON tv.idestado_valoracion = tev.idestado_valoracion
        WHERE idproducto = ?';
        $params = array($this->idproducto);
        return Database::obtenerSentencias($sql, $params);
    }

    //Eliminamos el registro
    public function actualizarReseniasProductos()
    {
        $sql = 'UPDATE tbvaloraciones SET idestado_valoracion = ? WHERE idvaloracion = ?';
        $params = array($this->estado_valoracion, $this->idvaloracion);
        return Database::ejecutarSentencia($sql, $params);
    }

    /*--------------------------------------------------------PUBLICO--------------------------------------------------------*/

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

    //funcionamiento para cargar las cards de productos por categoria
    public function readProductosCategoria($id, $buscador)
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal, tc.idcategoria_producto
        FROM tbproducto tp
        LEFT JOIN tbsubcategoria_producto tsc
        ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
        LEFT JOIN tbcategoria tc
        ON tc.idcategoria_producto = tsc.idcategoria_producto
        WHERE tp.existencias > 0
        AND tp.idestado_producto = 1
        AND tc.idcategoria_producto = ?
        AND tp.nombre_producto ILIKE ?
        ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = array($id, "%$buscador%");
        return Database::obtenerSentencias($sql, $params);
    }

    //funcionamiento para cargar las cards de productos por subcategoria
    public function readProductossubCategoria($id, $buscador)
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto , tp.precio_producto, tp.imagen_principal
        FROM tbproducto tp
        LEFT JOIN tbsubcategoria_producto tsc
        ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
        WHERE tp.idestado_producto = 1
        AND tp.existencias >= 1
        AND tp.idsubcategoria_producto = ?
        AND tp.nombre_producto ILIKE ?
        ORDER BY tp.idproducto ASC LIMIT 6 OFFSET 0';
        $params = array($id, "%$buscador%");
        return Database::obtenerSentencias($sql, $params);
    }

    //Ver en detalle del producto
    public function leerUnProducto()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.imagen_principal, tp.descripcion, tp.material, tp.tamanio, tp.existencias, tp.porcentaje_descuento, tp.precio_producto, tc.color, tm.nombre_marca, idestado_producto
        FROM tbproducto tp
        INNER JOIN tbcolor tc
        ON tp.idcolor = tc.idcolor
        INNER JOIN tbmarca tm
        ON tp.id_marca = tm.id_marca
        WHERE tp.idproducto = ?';
        $params = array($this->idproducto);
        return Database::obtenerSentencia($sql, $params);
    }

    //Se obtiene el promedio de valoraciones para así mostrarlo por medio de una estrella
    public function promedioValoraciones()
    {
        $sql = 'SELECT AVG(valoraciones) as promedio FROM public.tbvaloraciones WHERE idproducto = ?';
        $params = array($this->idproducto);
        if ($data = Database::obtenerSentencia($sql, $params)) {
            $this->setPromedio($data['promedio']);
            return true;
        } else {
            return false;
        }
    }

    //Total de resenias para mostrarse en detalle del producto
    public function totalResenias()
    {
        $sql = 'SELECT COUNT(reseña) as total_resenia FROM public.tbvaloraciones WHERE idproducto = ?';
        $params = array($this->idproducto);
        if ($data = Database::obtenerSentencia($sql, $params)) {
            $this->setTotalResenias($data['total_resenia']);
            return true;
        } else {
            return false;
        }
    }

    //Mostrar las valoraciones que tiene cada producto, se mostrarán en detalle del producto
    public function valoracionesProducto()
    {
        $sql = 'SELECT tv.idvaloracion, tv.valoraciones, tv.reseña, tv.fecha_publicacion, tuc.nombre_cliente, tuc.apellido_cliente
        FROM tbvaloraciones tv
        INNER JOIN tbusuario_cliente tuc
        ON tuc.idusuario_c = tv.idusuario_c
        WHERE tv.idestado_valoracion = 1 AND idproducto = ?';
        $params = array($this->idproducto);
        return Database::obtenerSentencias($sql, $params);
    }

    //función para mostrar una pagina de descuento, donde mostrara todos los productos que se encuentran en descuento
    public function Descuento()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal
                FROM tbproducto tp
                WHERE tp.existencias > 0
                AND tp.idestado_producto = 1
                AND tp.porcentaje_descuento > 0
                ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }


    public function SearchOferta($buscador)
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal
                FROM tbproducto tp
                LEFT JOIN tbsubcategoria_producto tsc
                ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
                LEFT JOIN tbcategoria tc
                ON tc.idcategoria_producto = tsc.idcategoria_producto
                WHERE tp.existencias > 0
                AND tp.idestado_producto = 1
                AND porcentaje_descuento >= 1
                AND tp.nombre_producto ILIKE ?
                ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = array("%$buscador%");
        return Database::obtenerSentencias($sql, $params);
    }

    public function RangoProductoCategoria()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal
                FROM tbproducto tp
                LEFT JOIN tbsubcategoria_producto tsc
                ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
                LEFT JOIN tbcategoria tc
                ON tc.idcategoria_producto = tsc.idcategoria_producto
                WHERE tp.existencias > 0
				AND tp.idestado_producto = 1
                AND tc.idcategoria_producto = ?
                AND precio_producto BETWEEN ? AND ?
                ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = array($this->idcategoria, $this->min, $this->max);
        return Database::obtenerSentencias($sql, $params);
    }

    public function RangoMaxProductoCategoria()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal
                FROM tbproducto tp
                LEFT JOIN tbsubcategoria_producto tsc
                ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
                LEFT JOIN tbcategoria tc
                ON tc.idcategoria_producto = tsc.idcategoria_producto
                WHERE tp.existencias > 0
				AND tp.idestado_producto = 1
                AND tc.idcategoria_producto = ?
                AND precio_producto >= 100
                ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = array($this->idcategoria);
        return Database::obtenerSentencias($sql, $params);
    }

    public function RangoProductoSubcategoria()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal
                FROM tbproducto tp
                LEFT JOIN tbsubcategoria_producto tsc
                ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
                LEFT JOIN tbcategoria tc
                ON tc.idcategoria_producto = tsc.idcategoria_producto
                WHERE tp.existencias > 0
                AND tp.idestado_producto = 1
                AND tp.idsubcategoria_producto = ?
                AND precio_producto BETWEEN ? AND ?
                ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = array($this->idsubcategoria, $this->min, $this->max);
        return Database::obtenerSentencias($sql, $params);
    }

    public function RangoMaxProductoSubcategoria()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal
                FROM tbproducto tp
                LEFT JOIN tbsubcategoria_producto tsc
                ON tp.idsubcategoria_producto = tsc.idsubcategoria_producto
                LEFT JOIN tbcategoria tc
                ON tc.idcategoria_producto = tsc.idcategoria_producto
                WHERE tp.existencias > 0
                AND tp.idestado_producto = 1
                AND tp.idsubcategoria_producto = ?
                AND precio_producto >= 100
                ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = array($this->idsubcategoria);
        return Database::obtenerSentencias($sql, $params);
    }

    public function rangoOferta()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal
                FROM tbproducto tp
                WHERE tp.existencias > 0
                AND tp.idestado_producto = 1
                AND precio_producto BETWEEN ? AND ?
                AND tp.porcentaje_descuento > 0
                ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = array($this->min, $this->max);
        return Database::obtenerSentencias($sql, $params);
    }

    public function RangoMaxOfertas()
    {
        $sql = 'SELECT tp.idproducto, tp.nombre_producto, tp.precio_producto, tp.imagen_principal
                FROM tbproducto tp
                WHERE tp.existencias > 0
                AND tp.idestado_producto = 1
                AND precio_producto >= 100
                AND tp.porcentaje_descuento > 0
                ORDER BY idproducto ASC LIMIT 6 OFFSET 0';
        $params = null;
        return Database::obtenerSentencias($sql, $params);
    }

    public function filtroProductosVendidos()
    {   
        if ($this->filtro == 1) {
            $sql = 'SELECT tp.idproducto, tp.nombre_producto, SUM(tdf.cantidad_producto) as cantidad_vendida
            FROM tbproducto tp 
            INNER JOIN tbdetalle_factura tdf USING(idproducto)
            INNER JOIN tbfactura tf USING(idfactura)
            INNER JOIN tbsubcategoria_producto tsc USING(idsubcategoria_producto)
            INNER JOIN tbcategoria tc USING(idcategoria_producto)
            WHERE tf.idestado_factura = 1 AND tc.idcategoria_producto = ?
            GROUP BY tp.idproducto 
            ORDER BY cantidad_vendida DESC limit 15';
            $params = array($this->opcion);
            return Database::obtenerSentencias($sql, $params);
        } else {
            switch ($this->getFiltro()){
                case 2:
                    $sql = 'SELECT tp.idproducto, tp.nombre_producto, SUM(tdf.cantidad_producto) as cantidad_vendida
                    FROM tbproducto tp 
                    INNER JOIN tbdetalle_factura tdf USING(idproducto)
                    INNER JOIN tbfactura tf ON tf.idfactura = tdf.idfactura
                    WHERE tf.idestado_factura = 1 AND idsubcategoria_producto = ?
                    GROUP BY tp.idproducto LIMIT 20';
                    $params = array($this->opcion);
                    return Database::obtenerSentencias($sql, $params);
                break;
                case 3:
                    $sql = 'SELECT tp.idproducto, tp.nombre_producto, SUM(tdf.cantidad_producto) as cantidad_vendida
                    FROM tbproducto tp 
                    INNER JOIN tbdetalle_factura tdf USING(idproducto)
                    INNER JOIN tbfactura tf ON tf.idfactura = tdf.idfactura
                    WHERE tf.idestado_factura = 1 AND id_marca = ?
                    GROUP BY tp.idproducto LIMIT 20';
                    $params = array($this->opcion);
                    return Database::obtenerSentencias($sql, $params);
                break;
                case 4:
                    $sql = 'SELECT tp.idproducto, tp.nombre_producto, SUM(tdf.cantidad_producto) as cantidad_vendida
                    FROM tbproducto tp 
                    INNER JOIN tbdetalle_factura tdf USING(idproducto)
                    INNER JOIN tbfactura tf ON tf.idfactura = tdf.idfactura
                    WHERE tf.idestado_factura = 1 AND iddistribuidor = ?
                    GROUP BY tp.idproducto LIMIT 20';
                    $params = array($this->opcion);
                    return Database::obtenerSentencias($sql, $params);
                break;
            }
        }
    }
}
