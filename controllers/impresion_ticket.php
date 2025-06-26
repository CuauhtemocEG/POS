<?php
ini_set('memory_limit', '256M'); // o 512M si es necesario
ob_start();
require_once '../conexion.php';
require_once '../fpdf/fpdf.php';

$pdo = conexion();

$orden_id = $_GET['orden_id'] ?? 0;

// Datos de la orden
$stmt = $pdo->prepare(
    "SELECT m.nombre AS mesa, o.id, o.creada_en 
     FROM ordenes o JOIN mesas m ON o.mesa_id = m.id WHERE o.id=?"
);
$stmt->execute([$orden_id]);
$orden = $stmt->fetch();

// Productos (SOLO los preparados y cantidad > 0)
$detalles = $pdo->prepare(
    "SELECT p.nombre, op.preparado AS cantidad, p.precio 
     FROM orden_productos op JOIN productos p ON op.producto_id = p.id 
     WHERE op.orden_id=? AND op.preparado > 0"
);
$detalles->execute([$orden_id]);
$productos = $detalles->fetchAll();

// Ticket PDF
$pdf = new FPDF('P','mm',[80,150]);
$pdf->AddPage();

if (file_exists('../assets/img/LogoBlack.png')) {
    $pdf->Image('../assets/img/LogoBlack.png', 25, 5, 30);
    $pdf->Ln(10);
}

$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,7,"POS Restaurante",0,1,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,"Mesa: ".$orden['mesa'],0,1,'C');
$pdf->Cell(0,5,"Orden #: ".$orden['id'],0,1,'C');
$pdf->Cell(0,5,"Fecha: ".substr($orden['creada_en'],0,16),0,1,'C');
$pdf->Ln(3);

$pdf->Cell(0,0,'','T');
$pdf->Ln(2);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(30,6,'Producto',0);
$pdf->Cell(15,6,'Cant',0,0,'C');
$pdf->Cell(15,6,'Precio',0,0,'C');
$pdf->Cell(20,6,'Subt.',0,1,'R');
$pdf->SetFont('Arial','',10);
$total = 0;
foreach ($productos as $prod) {
    $subtotal = $prod['cantidad'] * $prod['precio'];
    $pdf->Cell(30,6,utf8_decode($prod['nombre']),0);
    $pdf->Cell(15,6,$prod['cantidad'],0,0,'C');
    $pdf->Cell(15,6,"$".number_format($prod['precio'],2),0,0,'C');
    $pdf->Cell(20,6,"$".number_format($subtotal,2),0,1,'R');
    $total += $subtotal;
}
$pdf->Ln(2);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(45,7,'Total',0);
$pdf->Cell(25,7,"$".number_format($total,2),0,1,'R');

$pdf->Ln(5);
$pdf->SetFont('Arial','I',8);
$pdf->Cell(0,4,utf8_decode('¡Gracias por su visita!'),0,1,'C');
ob_clean(); 
$pdf->Output('I', 'ticket_mesa_'.$orden['mesa'].'.pdf');
exit;
?>