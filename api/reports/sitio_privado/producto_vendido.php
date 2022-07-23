<?php
require('../../conexion/dashboard_report.php');
require('../../modelo/detalle_factura.php');
require('../../modelo/productos.php');

$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Productos de la categoría');
// Se instancia el módelo Productos para procesar los datos.
$vendida = new Descuento;
    // Se verifica si existen registros (productos) para mostrar, de lo contrario se imprime un mensaje.
    if ($dataProductos = $vendida->ProductoVendido()) {
        // Se establece un color de relleno para los encabezados.
        $pdf->setFillColor(225);
        // Se establece la fuente para los encabezados.
        $pdf->setFont('Times', 'B', 11);
        // Se imprimen las celdas con los encabezados.
        $pdf->cell(30, 10, utf8_decode('Id Producto'), 1, 0, 'C', 1);
        $pdf->cell(70, 10, utf8_decode('Nombre Producto'), 1, 0, 'C', 1);
        $pdf->cell(30, 10, utf8_decode('Precio Unitario'), 1, 0, 'C', 1);
        $pdf->cell(30, 10, utf8_decode('Total'), 1, 1, 'C', 1);
        // Se establece la fuente para los datos de los productos.
        $pdf->setFont('Times', '', 11);
        // Se recorren los registros ($dataProductos) fila por fila ($rowProducto).
        foreach ($dataProductos as $rowProducto) {
            $pdf->cell(30, 10, $rowProducto['idproducto'], 1, 0);
            $pdf->cell(70, 10, utf8_decode($rowProducto['nombre_producto']), 1, 0);
            $pdf->cell(30, 10, $rowProducto['precio_unitario'], 1, 0);
            $pdf->cell(30, 10, $rowProducto['total'], 1, 1);
        }
    } else {
        $pdf->cell(0, 10, utf8_decode('No hay productos para esta categoría'), 1, 1);
    }
    // Se envía el documento al navegador y se llama al método footer()
    $pdf->output('I', 'categoria.pdf');;
