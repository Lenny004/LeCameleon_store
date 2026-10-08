<?php
require('../../conexion/dashboard_report.php');
require('../../modelo/inventario_entrega.php');

$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Productos Registrados en Inventario');
// Se instancia el módelo de Descuento para procesar los datos.
$inventario = new inventario_entrega;
// Se verifica si existen registros (productos) para mostrar, de lo contrario se imprime un mensaje.
if ($dataProductos = $inventario->obtenerInventarioReporte()) {
    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(140,196,63);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Helvetica', 'B', 9);
    // Se imprimen las celdas con los encabezados.
    $pdf->cell(70, 10, utf8_decode('Nombre Producto'), 1, 0, 'C', 1);
    $pdf->cell(20, 10, utf8_decode('Cantidad'), 1, 0, 'C', 1);
    $pdf->cell(25, 10, utf8_decode('Precio Unitario'), 1, 0, 'C', 1);
    $pdf->cell(35, 10, utf8_decode('Fecha Entrega'), 1, 0, 'C', 1);
    $pdf->cell(35, 10, utf8_decode('Fecha Inicio de Ventas'), 1, 1, 'C', 1);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Helvetica', '', 9);
    // Se establece un color de relleno para mostrar el nombre de la categoría.
    $pdf->setFillColor(255, 255, 255, 255);
    // Se recorren los registros ($dataProductos) fila por fila ($rowProducto).
    foreach ($dataProductos as $rowProducto) {
        $pdf->cell(70, 10, utf8_decode($rowProducto['nombre_producto']), 1, 0, '', 1);
        $pdf->cell(20, 10, $rowProducto['cantidad'], 1, 0, 'C', 1);
        $pdf->cell(25, 10, '$' . $rowProducto['precio_unitario'], 1, 0, 'C', 1);
        $pdf->cell(35, 10, $rowProducto['fecha_entrega'], 1, 0, '', 1);
        $pdf->cell(35, 10, $rowProducto['fecha_inicio_ventas'], 1, 1, '', 1);
    }
} else {
    $pdf->cell(0, 10, utf8_decode('No hay productos vendidos'), 1, 1);
}
// Se envía el documento al navegador y se llama al método footer()
$pdf->output('I', 'productos_vendidos.pdf');;
