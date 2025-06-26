<?php
require_once 'conexion.php';
$pdo = conexion();
$ordenes = $pdo->query("
    SELECT o.*, m.nombre AS mesa_nombre
    FROM ordenes o
    JOIN mesas m ON m.id = o.mesa_id
    ORDER BY o.creada_en DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Órdenes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container py-3">
  <h2 class="mb-4 text-center">Listado de Órdenes</h2>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Código</th>
          <th>Mesa</th>
          <th>Estado</th>
          <th>Creada</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ordenes as $orden): ?>
        <tr>
          <td><?= htmlspecialchars($orden['codigo']) ?></td>
          <td><?= htmlspecialchars($orden['mesa_nombre']) ?></td>
          <td>
            <span class="badge bg-<?= $orden['estado'] === 'pagada' ? 'success' : ($orden['estado'] === 'cancelada' ? 'danger' : 'warning') ?>">
              <?= ucfirst($orden['estado']) ?>
            </span>
          </td>
          <td><?= htmlspecialchars($orden['creada_en']) ?></td>
          <td>
            <a href="index.php?page=detalleOrder&id=<?= $orden['id'] ?>" class="btn btn-primary btn-sm">Ver Detalle</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>