<?php
require_once 'conexion.php';
require_once 'fpdf/fpdf.php';
session_start();

$mesaID = $_POST['mesaID'];
$carrito = $_SESSION['MESAS'][$mesaID]['productos'];
$metodo = $_POST['metodoPago'];
$total = array_sum(array_column($carrito, 'subtotal'));

$pdo = conexion();
$pdo->beginTransaction();
$stmt = $pdo->prepare("INSERT INTO Ventas (MesaID, Total, MetodoPago) VALUES (?, ?, ?)");
$stmt->execute([$mesaID, $total, $metodo]);
$ventaID = $pdo->lastInsertId();

foreach ($carrito as $idProd => $prod) {
    $stmt = $pdo->prepare("INSERT INTO DetalleVenta (VentaID, ProductoID, Cantidad, PrecioUnitario, Subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$ventaID, $idProd, $prod['cantidad'], $prod['precio'], $prod['subtotal']]);
    $pdo->prepare("UPDATE Productos SET Cantidad = Cantidad - ? WHERE ProductoID = ?")
        ->execute([$prod['cantidad'], $idProd]);
}

$pdo->commit();
unset($_SESSION['MESAS'][$mesaID]);

$pdf = new FPDF('P','mm',array(80,200));
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
$pdf->Cell(60,5,"Kalli Jaguar",0,1,'C');
$pdf->Ln(2);
$pdf->SetFont('Arial','',8);
$pdf->Cell(60,5,"Mesa: $mesaID",0,1,'C');
$pdf->Ln(2);
foreach ($carrito as $prod) {
    $pdf->Cell(60,5, "{$prod['nombre']} x{$prod['cantidad']} - $".number_format($prod['subtotal'],2), 0, 1);
}
$pdf->Ln(2);
$pdf->Cell(60,5,"Total: $".number_format($total,2),0,1,'R');
$pdf->Output();
?>