<?php
require_once 'conexion.php';
$pdo = conexion();

$sql = "SELECT v.VentaID, v.MesaID, v.Fecha, d.ProductoID, p.Nombre, d.Cantidad
        FROM Ventas v
        INNER JOIN DetalleVenta d ON v.VentaID = d.VentaID
        INNER JOIN Productos p ON p.ProductoID = d.ProductoID
        WHERE DATE(v.Fecha) = CURDATE()
        ORDER BY v.Fecha DESC";

$data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$comandas = [];
foreach ($data as $row) {
    $comandas[$row['VentaID']]['mesa'] = $row['MesaID'];
    $comandas[$row['VentaID']]['fecha'] = $row['Fecha'];
    $comandas[$row['VentaID']]['productos'][] = [
        'nombre' => $row['Nombre'],
        'cantidad' => $row['Cantidad']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vista de Cocina</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Comandas del día</h3>
    <?php foreach ($comandas as $id => $comanda): ?>
        <div class="card mb-2 shadow-sm">
            <div class="card-header bg-dark text-white">
                Mesa <?= $comanda['mesa'] ?> | Venta #<?= $id ?> | <?= $comanda['fecha'] ?>
            </div>
            <div class="card-body">
                <ul>
                    <?php foreach ($comanda['productos'] as $prod): ?>
                        <li><strong><?= $prod['cantidad'] ?></strong> x <?= $prod['nombre'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
