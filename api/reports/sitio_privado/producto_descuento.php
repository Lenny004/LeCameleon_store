<?php
require('../../conexion/dashboard_report.php');
require('../../modelo/detalle_factura.php');
require('../../modelo/productos.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Productos más vendidos con descuentos');

// Se instancia el módelo Categorías para obtener los datos.
$descuento = new Descuento;
if ($dataDescuento = $descuento->ProductoDescuento()) {
    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(175);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Times', 'B', 11);
    // Se imprimen las celdas con los encabezados.
    $pdf->cell(30, 10, utf8_decode('Id Producto'), 1, 0, 'C', 1);
    $pdf->cell(70, 10, utf8_decode('Nombre Producto'), 1, 0, 'C', 1);
    $pdf->cell(30, 10, utf8_decode('Precio Unitario'), 1, 0, 'C', 1);
    $pdf->cell(30, 10, utf8_decode('Descuento'), 1, 0, 'C', 1);
    $pdf->cell(30, 10, utf8_decode('Total'), 1, 1, 'C', 1);

    // Se establece un color de relleno para mostrar el nombre de la categoría.
    $pdf->setFillColor(225);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Times', '', 11);

    // Se recorren los registros ($dataDescuento) fila por fila ($rowDescuento).
    foreach ($dataDescuento as $rowDescuento) {
        // Se instancia el módelo Productos para procesar los datos.
        $producto = new Productos;
        // Se establece la categoría para obtener sus productos, de lo contrario se imprime un mensaje de error.
        if ($producto->setIdproducto($rowDescuento['idproducto'])) {
            // Se verifica si existen registros (productos) para mostrar, de lo contrario se imprime un mensaje.
            if ($dataProductos = $producto->ProductoDescuento()) {
                // Se recorren los registros ($dataProductos) fila por fila ($rowProducto).
                foreach ($dataProductos as $rowProducto) {
                    // Se imprimen las celdas con los datos de los productos.
                    $pdf->cell(30, 10, $rowProducto['idproducto'], 1, 0);
                    $pdf->cell(70, 10, utf8_decode($rowProducto['nombre_producto']), 1, 0);
                    $pdf->cell(30, 10, $rowProducto['precio_unitario'], 1, 0);
                    $pdf->cell(30, 10, $rowProducto['cantidad_descuento'], 1, 0);
                    $pdf->cell(30, 10, $rowProducto['total'], 1, 1);
                }
            } else {
                $pdf->cell(0, 10, utf8_decode('No hay productos en descuentos vendidos'), 1, 1);
            }
        } else {
            $pdf->cell(0, 10, utf8_decode('Productos incorrecto o inexistente'), 1, 1);
        }
    }
} else {
    $pdf->cell(0, 10, utf8_decode('No hay productos con descuentos para mostrar'), 1, 1);
}

// Se envía el documento al navegador y se llama al método footer()
$pdf->output('I', 'productos.pdf');
