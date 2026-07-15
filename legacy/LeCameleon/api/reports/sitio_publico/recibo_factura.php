<?php
require('../../conexion/dashboard_report2.php');
require('../../modelo/carrito.php');

$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Recibo de Compra');
// Se instancia el módelo de Descuento para procesar los datos.
$carrito = new Carrito;
// Se verifica si existen registros (productos) para mostrar, de lo contrario se imprime un mensaje.
if ($dataProducto = $carrito->reciboFinalizarCompra()) {
    // Se establece un color de relleno para mostrar el nombre de la categoría.
    $pdf->setFillColor(255, 255, 255, 255);
    $pdf->SetTextColor(33, 25, 10);
    $pdf->setFont('Helvetica', 'B', 14);
    $pdf->cell(120, 10, utf8_decode('Datos del pedido:'), 0, 0, '', 1);
    $pdf->cell(50, 10, utf8_decode('Confirmación de Pedido:'), 0, 1, 'C', 1);
    $pdf->setFont('Helvetica', 'B', 11);
    $pdf->cell(120, 10, utf8_decode('Dirección de entrega:'), 0, 0, '', 1);
    $pdf->SetTextColor(240, 163, 19);
    $pdf->setFont('Helvetica', 'B', 14);
    $pdf->cell(50, 10, utf8_decode('Descuento: $' . $dataProducto['cantidad_descuento']), 0, 1, 'C', 1);
    $pdf->SetTextColor(33, 25, 10);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Helvetica', '', 11);
    $pdf->cell(120, 10, utf8_decode($dataProducto['direccion_cliente']), 0, 0, '', 1);
    $pdf->SetTextColor(240, 163, 19);
    $pdf->setFont('Helvetica', 'B', 14);
    $pdf->cell(50, 10, utf8_decode('Importe Total: $' . $dataProducto['total_producto']), 0, 1, 'C', 1);
    $pdf->SetTextColor(33, 25, 10);
    $pdf->setFont('Helvetica', 'B', 11);
    $pdf->cell(70, 10, utf8_decode('Nombres completos:'), 0, 1, '', 1);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Helvetica', '', 11);
    $pdf->cell(70, 10, utf8_decode($dataProducto['nombre_cliente']), 0, 1, '', 1);
    $pdf->setFont('Helvetica', 'B', 11);
    $pdf->cell(70, 10, utf8_decode('Apellidos completos:'), 0, 1, '', 1);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Helvetica', '', 11);
    $pdf->cell(70, 10, utf8_decode($dataProducto['apellido_cliente']), 0, 1, '', 1);
    $pdf->setFont('Helvetica', 'B', 11);
    $pdf->cell(70, 10, utf8_decode('Teléfono de contacto:'), 0, 1, '', 1);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Helvetica', '', 11);
    $pdf->cell(70, 10, $dataProducto['telefono_cliente'], 0, 1, '', 1);
    $pdf->setFont('Helvetica', 'B', 11);
    $pdf->cell(70, 10, utf8_decode('Fecha y Hora de Entrega:'), 0, 1, '', 1);
    // Se establece la fuente para los datos de los productos.
    $pdf->setFont('Helvetica', '', 11);
    $pdf->cell(70, 10, utf8_decode($dataProducto['fecha_factura']), 0, 1, '', 1);
    // Se agrega un salto de línea para mostrar el contenido principal del documento.
    $pdf->ln(50);
    $pdf->setFont('Helvetica', 'B', 16);
    $pdf->SetTextColor(240, 163, 19);
    $pdf->cell(70, 10, utf8_decode('Muchas Gracias por su compra :D'), 0, 1, '', 1);
} else {
    $pdf->cell(0, 10, utf8_decode('No hay productos vendidos'), 1, 1);
}
// Se envía el documento al navegador y se llama al método footer()
$pdf->output('I', 'productos_vendidos.pdf');;
