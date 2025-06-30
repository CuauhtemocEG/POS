<?php
require_once '../conexion.php';
require_once '../fpdf/fpdf.php'; // ruta a FPDF

$pdo = conexion();

$orden_id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT o.*, m.nombre AS mesa_nombre FROM ordenes o JOIN mesas m ON m.id=o.mesa_id WHERE o.id=?");
$stmt->execute([$orden_id]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orden) { die('Orden no encontrada'); }

$productos = $pdo->prepare("SELECT p.nombre, op.cantidad, op.preparado, op.cancelado, p.precio FROM orden_productos op JOIN productos p ON op.producto_id = p.id WHERE op.orden_id = ?");
$productos->execute([$orden_id]);
$productos = $productos->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($productos as $prod) $subtotal += $prod['precio'] * $prod['cantidad'];
$descuento = 0; $impuestos = 0; $total = $subtotal - $descuento + $impuestos;

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0,10,'DETALLE DE ORDEN',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,7,"Codigo: ".$orden['codigo'],0,1);
$pdf->Cell(0,7,"Mesa: ".$orden['mesa_nombre'],0,1);
$pdf->Cell(0,7,"Estado: ".ucfirst($orden['estado']),0,1);
$pdf->Cell(0,7,"Fecha: ".$orden['creada_en'],0,1);
$pdf->Ln(3);

$pdf->SetFont('Arial','B',11);
$pdf->Cell(50,7,'Producto',1);
$pdf->Cell(20,7,'Cant.',1);
$pdf->Cell(25,7,'Preparado',1);
$pdf->Cell(25,7,'Cancelado',1);
$pdf->Cell(25,7,'Precio',1);
$pdf->Cell(35,7,'Subtotal',1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
foreach($productos as $prod){
  $pdf->Cell(50,7,utf8_decode($prod['nombre']),1);
  $pdf->Cell(20,7,$prod['cantidad'],1);
  $pdf->Cell(25,7,$prod['preparado'],1);
  $pdf->Cell(25,7,$prod['cancelado'],1);
  $pdf->Cell(25,7,"$".number_format($prod['precio'],2),1);
  $pdf->Cell(35,7,"$".number_format($prod['precio']*$prod['cantidad'],2),1);
  $pdf->Ln();
}
$pdf->SetFont('Arial','B',11);
$pdf->Cell(145,7,'Subtotal',1,0,'R'); $pdf->Cell(35,7,"$".number_format($subtotal,2),1,1,'R');
$pdf->Cell(145,7,'Descuento',1,0,'R'); $pdf->Cell(35,7,"$".number_format($descuento,2),1,1,'R');
$pdf->Cell(145,7,'Impuestos',1,0,'R'); $pdf->Cell(35,7,"$".number_format($impuestos,2),1,1,'R');
$pdf->Cell(145,7,'Total',1,0,'R'); $pdf->Cell(35,7,"$".number_format($total,2),1,1,'R');

// Limpia el buffer antes de imprimir el PDF
if (ob_get_length()) ob_clean();
$pdf->Output("I",$orden['codigo'].".pdf");
exit;