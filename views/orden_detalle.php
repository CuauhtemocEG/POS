<?php
require_once 'conexion.php';
$pdo = conexion();
$orden_id = intval($_GET['id'] ?? 0);
$orden = $pdo->prepare("
    SELECT o.*, m.nombre AS mesa_nombre
    FROM ordenes o
    JOIN mesas m ON m.id = o.mesa_id
    WHERE o.id = ?
");
$orden->execute([$orden_id]);
$orden = $orden->fetch(PDO::FETCH_ASSOC);

if (!$orden) {
  echo "<div class='alert alert-danger'>Orden no encontrada</div>";
  exit;
}

// Productos
$productos = $pdo->prepare("
    SELECT p.nombre, op.cantidad, op.preparado, op.cancelado, p.precio
    FROM orden_productos op
    JOIN productos p ON op.producto_id = p.id
    WHERE op.orden_id = ?
");
$productos->execute([$orden_id]);
$productos = $productos->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($productos as $prod) {
  $subtotal += $prod['precio'] * $prod['cantidad'];
}
$descuento = 0;
$impuestos = 0;
$total = $subtotal - $descuento + $impuestos;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Detalle Orden <?= htmlspecialchars($orden['codigo']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="container py-3">
    <h2 class="mb-4">Detalle de Orden</h2>
    <div class="mb-3">
      <strong>Código:</strong> <?= htmlspecialchars($orden['codigo']) ?><br>
      <strong>Mesa:</strong> <?= htmlspecialchars($orden['mesa_nombre']) ?><br>
      <strong>Estado:</strong>
      <span class="badge bg-<?= $orden['estado'] === 'pagada' ? 'success' : ($orden['estado'] === 'cancelada' ? 'danger' : 'warning') ?>">
        <?= ucfirst($orden['estado']) ?>
      </span><br>
      <strong>Fecha:</strong> <?= htmlspecialchars($orden['creada_en']) ?>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Preparado</th>
            <th>Cancelado</th>
            <th>Precio</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($productos as $prod): ?>
            <tr>
              <td><?= htmlspecialchars($prod['nombre']) ?></td>
              <td><?= $prod['cantidad'] ?></td>
              <td><?= $prod['preparado'] ?></td>
              <td><?= $prod['cancelado'] ?></td>
              <td>$<?= number_format($prod['precio'], 2) ?></td>
              <td>$<?= number_format($prod['precio'] * $prod['cantidad'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="5" class="text-end">Subtotal</th>
            <th>$<?= number_format($subtotal, 2) ?></th>
          </tr>
          <tr>
            <th colspan="5" class="text-end">Descuento</th>
            <th>$<?= number_format($descuento, 2) ?></th>
          </tr>
          <tr>
            <th colspan="5" class="text-end">Impuestos</th>
            <th>$<?= number_format($impuestos, 2) ?></th>
          </tr>
          <tr class="table-success">
            <th colspan="5" class="text-end">Total</th>
            <th>$<?= number_format($total, 2) ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
    <a href="exportar_orden_pdf.php?id=<?= $orden['id'] ?>" target="_blank" class="btn btn-outline-danger mb-3">
      <i class="bi bi-file-pdf"></i> Exportar PDF
    </a>
    <a href="index.php?page=ordenes" class="btn btn-secondary mb-3">Volver a listado</a>
  </div>
</body>

</html>