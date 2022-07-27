<?php
require('../../conexion/dashboard_report.php');
require('../../modelo/detalle_factura.php');
require('../../modelo/productos.php');

$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Productos más vendidos');
// Se instancia el módelo de Descuento para procesar los datos.
$vendida = new Descuento;
// Se verifica si existen registros (productos) para mostrar, de lo contrario se imprime un mensaje.
if ($dataProductos = $vendida->ProductoVendido()) {
    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(140, 196, 63);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Helvetica', 'B', 10);
    // Se imprimen las celdas con los encabezados.
    $pdf->cell(25, 10, utf8_decode('Id Producto'), 1, 0, 'C', 1);
    $pdf->cell(90, 10, utf8_decode('Nombre Producto'), 1, 0, 'C', 1);
    $pdf->cell(35, 10, utf8_decode('Precio Unitario'), 1, 0, 'C', 1);
    $pdf->cell(30, 10, utf8_decode('Total Vendido'), 1, 1, 'C', 1);
    // Se establece un color de relleno para mostrar el nombre de la categoría.
    $pdf->setFillColor(255, 255, 255, 255);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Helvetica', '', 9);
    // Se recorren los registros ($dataProductos) fila por fila ($rowProducto).
    foreach ($dataProductos as $rowProducto) {
        $pdf->cell(25, 10, $rowProducto['idproducto'], 1, 0, 'C', 1);
        $pdf->cell(90, 10, utf8_decode($rowProducto['nombre_producto']), 1, 0, '', 1);
        $pdf->cell(35, 10, '$' . $rowProducto['precio_producto'], 1, 0, '', 1);
        $pdf->cell(30, 10, '$' . round($rowProducto['precio_producto'] * $rowProducto['cantidad_vendida'], 2), 1, 1, '', 1);
    }
} else {
    $pdf->cell(0, 10, utf8_decode('No hay productos vendidos'), 1, 1);
}
// Se envía el documento al navegador y se llama al método footer()
$pdf->output('I', 'productos_vendidos.pdf');;
