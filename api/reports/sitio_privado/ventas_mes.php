<?php
require('../../conexion/dashboard_report.php');
require('../../modelo/productos.php');
// Se instancia la clase para crear el reporte.
$pdf = new Report;
//Establecemos la zona horaria de El Salvador
date_default_timezone_set('America/El_Salvador');
//Se guarda en una variable la fecha del mes actual
$meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$mes_actual = date('m');
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Reporte de ventas de productos del Mes de ' . $meses[$mes_actual - 1]);
// Se instancia el módelo de productos para obtener los datos.
$producto = new Productos;
// Se verifica si existen registros (categorías) para mostrar, de lo contrario se imprime un mensaje.
if ($dataProductos = $producto->ventasMesxProducto()) {
    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(140,196,63,255);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Helvetica', 'B', 10);
    // Se imprimen las celdas con los encabezados.
    $pdf->cell(84, 10, utf8_decode('Nombre Producto'), 1, 0, 'C', 1);
    $pdf->cell(38, 10, utf8_decode('Fecha Factura'), 1, 0, 'C', 1);
    $pdf->cell(32, 10, utf8_decode('Nombre usuario'), 1, 0, 'C', 1);
    $pdf->cell(32, 10, utf8_decode('Monto total(US$)'), 1, 1, 'C', 1);
    // Se establece un color de relleno para mostrar el nombre de los productos.
    $pdf->setFillColor(255, 255, 255, 255);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Helvetica', '', 9);
    // Se recorren los registros ($dataCategorias) fila por fila ($rowCategoria).
            if ($dataProductos = $producto->ventasMesxProducto()) {
                // Se recorren los registros ($dataProductos) fila por fila ($rowProducto).
                foreach ($dataProductos as $rowProducto) {
                    // Se imprimen las celdas con los datos de los productos.
                    $pdf->cell(84, 10, utf8_decode($rowProducto['nombre_producto']), 1, 0, '', 1);
                    $pdf->cell(38, 10, $rowProducto['fecha_factura'], 1, 0, '', 1);
                    $pdf->cell(32, 10, $rowProducto['usuario_c'], 1, 0,'', 1);
                    $pdf->cell(32, 10, '$' . $rowProducto['monto_total'], 1, 1,'', 1);
                }
            } else {
                $pdf->cell(0, 10, utf8_decode('No hay productos'), 1, 1);
            }
} else {
    $pdf->cell(0, 10, utf8_decode('No hay ventas de este producto en este mes'), 1, 1);
}
// Se envía el documento al navegador y se llama al método footer()
$pdf->output('I', 'ventas_año_producto_específico.pdf');
