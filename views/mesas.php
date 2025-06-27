<?php
require_once 'conexion.php';
$pdo = conexion();
$mesas = $pdo->query("
    SELECT m.*, 
      (SELECT COUNT(*) FROM ordenes o WHERE o.mesa_id = m.id AND o.estado = 'abierta') as orden_abierta
    FROM mesas m
    ORDER BY m.nombre
")->fetchAll(PDO::FETCH_ASSOC);
?>

<head>
  <link rel="stylesheet" href="assets/css/mesas.css">
</head>

<div class="container py-3">
  <h1 class="mb-4 text-center">Kalli Jaguar Dorada</h1>
  <form method="post" action="controllers/crear_mesa.php" class="row g-2 mt-5 justify-content-center">
    <div class="col-md-6">
      <input type="text" name="nombre" class="form-control" placeholder="Nombre nueva mesa" required>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-success">Agregar Mesa</button>
    </div>
  </form>
  <div class="mesas-grid">
    <?php foreach ($mesas as $mesa):
      if ($mesa['orden_abierta'] > 0) {
        $estado = 'ocupada';
        $badgeClass = 'badge-ocupada';
        $btnText = 'Ver POS';
      } else {
        $estado = 'libre';
        $badgeClass = 'badge-libre';
        $btnText = 'Abrir POS';
      }
    ?>
      <div class="mesa-card mesa-<?= $estado ?>" onclick="window.location='index.php?page=mesa&id=<?= $mesa['id'] ?>'">
        <div>
          <div class="mesa-num"><?= htmlspecialchars($mesa['nombre']) ?></div>
          <div class="mesa-status">
            <span class="badge <?= $badgeClass ?>"><?= ucfirst($estado) ?></span>
          </div>
          <div class="mb-2"><?= htmlspecialchars($mesa['descripcion'] ?? '') ?></div>
        </div>
        <a href="index.php?page=mesa&id=<?= $mesa['id'] ?>" class="btn btn-primary mesa-btn"><?= $btnText ?></a>
      </div>
    <?php endforeach; ?>
  </div>
</div>