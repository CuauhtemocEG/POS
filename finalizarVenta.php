<?php
require_once 'conexion.php';
require_once 'fpdf/fpdf.php';

$input = json_decode(file_get_contents("php://input"), true);
$mesaID = $input['mesaID'];
$carrito = $input['carrito'];
$metodo = $input['metodoPago'];
$total = array_sum(array_column($carrito, 'subtotal'));

$pdo = conexion();
$pdo->beginTransaction();

$stmt = $pdo->prepare("INSERT INTO Ventas (MesaID, Total, MetodoPago) VALUES (?, ?, ?)");
$stmt->execute([$mesaID, $total, $metodo]);
$ventaID = $pdo->lastInsertId();

foreach ($carrito as $prod) {
    $stmt = $pdo->prepare("INSERT INTO DetalleVenta (VentaID, ProductoID, Cantidad, PrecioUnitario, Subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$ventaID, $prod['ProductoID'], $prod['cantidad'], $prod['PrecioUnitario'], $prod['subtotal']]);
    
    $pdo->prepare("UPDATE Productos SET Cantidad = Cantidad - ? WHERE ProductoID = ?")
        ->execute([$prod['cantidad'], $prod['ProductoID']]);
}

$pdo->commit();
session_start();
unset($_SESSION['MESAS'][$mesaID]); // Limpia la mesa

// Ticket PDF
$pdf = new FPDF('P','mm',array(80,200));
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,5,"Kalli Jaguar",0,1,'C');
$pdf->SetFont('Arial','',9);
$pdf->Cell(60,5,"Mesa: $mesaID",0,1,'C');
$pdf->Ln(2);

foreach ($carrito as $prod) {
    $line = "{$prod['Nombre']} x{$prod['cantidad']} = $" . number_format($prod['subtotal'], 2);
    $pdf->Cell(60,5,$line,0,1);
}

$pdf->Ln(2);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(60,5,"Total: $".number_format($total,2),0,1,'R');

// Enviar PDF como respuesta
$pdf->Output('ticket.pdf', 'I');
exit;
?>