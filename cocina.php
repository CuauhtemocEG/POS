<?php
require_once 'conexion.php';
include 'navbar.php';
$pdo = conexion();

$sql = "SELECT v.VentaID, v.MesaID, v.Fecha, d.ProductoID, p.Nombre, d.Cantidad, p.TipoCocina
        FROM Ventas v
        INNER JOIN DetalleVenta d ON v.VentaID = d.VentaID
        INNER JOIN Productos p ON p.ProductoID = d.ProductoID
        WHERE DATE(v.Fecha) = CURDATE()
        ORDER BY v.Fecha DESC";

$data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$comandas = [];
foreach ($data as $row) {
    $ventaID = $row['VentaID'];
    $tipo = $row['TipoCocina'];
    $comandas[$ventaID]['mesa'] = $row['MesaID'];
    $comandas[$ventaID]['fecha'] = $row['Fecha'];
    $comandas[$ventaID]['items'][$tipo][] = [
        'ProductoID' => $row['ProductoID'],
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
<div class="container">
  <h3>Comandas del Día</h3>
  <?php foreach ($comandas as $id => $comanda): ?>
    <div class="card mb-3 shadow-sm">
      <div class="card-header bg-info text-white">
        Mesa <?= $comanda['mesa'] ?> | Venta #<?= $id ?> | <?= $comanda['fecha'] ?>
      </div>
      <div class="card-body">
        <?php foreach (['Comida', 'Bebida'] as $categoria): ?>
          <h5><?= $categoria === 'Comida' ? '🍽️ Comida' : '🥤 Bebidas' ?></h5>
          <ul>
            <?php if (!empty($comanda['items'][$categoria])): ?>
              <?php foreach ($comanda['items'][$categoria] as $prod): ?>
                <li>
                  <?= $prod['cantidad'] ?> × <?= $prod['nombre'] ?>
                  <input type="checkbox" onclick="alert('Marca de preparado solo visual')">
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li><em>Sin <?= strtolower($categoria) ?></em></li>
            <?php endif; ?>
          </ul>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
</body>
</html>