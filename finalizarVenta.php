<?php
require_once 'conexion.php';
require_once 'fpdf/fpdf.php';

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');

$input = json_decode(file_get_contents("php://input"), true);

$mesaID = $input['mesaID'] ?? null;
$carrito = $input['carrito'] ?? [];
$metodo = $input['metodoPago'] ?? 'Efectivo';

if (!$mesaID || empty($carrito)) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$total = array_sum(array_column($carrito, 'subtotal'));

$pdo = conexion();
$pdo->beginTransaction();

$stmt = $pdo->prepare("INSERT INTO Ventas (MesaID, Total, MetodoPago) VALUES (?, ?, ?)");
$stmt->execute([$mesaID, $total, $metodo]);
$ventaID = $pdo->lastInsertId();

foreach ($carrito as $prod) {
    $stmt = $pdo->prepare("INSERT INTO DetalleVenta (VentaID, ProductoID, Cantidad, PrecioUnitario, Subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $ventaID,
        $prod['ProductoID'],
        $prod['cantidad'],
        $prod['PrecioUnitario'],
        $prod['subtotal']
    ]);
    $pdo->prepare("UPDATE Productos SET Cantidad = Cantidad - ? WHERE ProductoID = ?")
        ->execute([$prod['cantidad'], $prod['ProductoID']]);
}

$pdo->commit();

$pdf = new FPDF('P','mm',array(80,200));
$pdf->AddPage();
$pdf->Image('assets/LogoBlack.png', 30, 5, 20);
$pdf->Ln(20);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,6,"KALLI JAGUAR",0,1,'C');
$pdf->SetFont('Arial','',9);
$pdf->Cell(60,5,"Mesa: $mesaID",0,1,'C');
$pdf->Cell(60,5,"Venta #: $ventaID",0,1,'C');
$pdf->Cell(60,5,"Pago: $metodo",0,1,'C');
$pdf->Cell(60,5,strftime("%A %d de %B %Y, %H:%M"),0,1,'C');
$pdf->Ln(3);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(40,5,"Producto",0,0);
$pdf->Cell(10,5,"Cant",0,0);
$pdf->Cell(20,5,"Total",0,1);
$pdf->SetFont('Arial','',9);

foreach ($carrito as $prod) {
    $nombre = substr($prod['Nombre'], 0, 20);
    $pdf->Cell(40,5,$nombre,0,0);
    $pdf->Cell(10,5,$prod['cantidad'],0,0);
    $pdf->Cell(20,5,"$".number_format($prod['subtotal'],2),0,1);
}

$pdf->Ln(2);
$pdf->SetFont('Arial','B',11);
$pdf->Cell(60,6,"TOTAL: $".number_format($total,2),0,1,'R');
$pdf->Ln(3);
$pdf->SetFont('Arial','I',8);
$pdf->Cell(60,5,"Gracias por su compra",0,1,'C');

$pdf->Output("I", "ticket_mesa_$mesaID.pdf");
exit;