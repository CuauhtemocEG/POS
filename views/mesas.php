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

<div class="max-w-4xl mx-auto py-6 px-4">
  <h1 class="mb-4 text-center text-2xl md:text-3xl font-bold text-gray-800 dark:text-white">Kalli Jaguar Dorada</h1>
  <form method="post" action="controllers/crear_mesa.php" class="flex flex-col md:flex-row items-center gap-3 justify-center mt-6">
    <input type="text" name="nombre" class="w-full md:w-1/2 rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white" placeholder="Nombre nueva mesa" required>
    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg transition">Agregar Mesa</button>
  </form>
  <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <?php foreach ($mesas as $mesa):
      if ($mesa['orden_abierta'] > 0) {
        $estado = 'ocupada';
        $badgeClass = 'bg-red-500 text-white';
        $cardClass = 'border-red-400/60 hover:shadow-red-200 dark:border-red-400/80';
        $btnText = 'Ver POS';
      } else {
        $estado = 'libre';
        $badgeClass = 'bg-green-500 text-white';
        $cardClass = 'border-green-400/60 hover:shadow-green-200 dark:border-green-400/80';
        $btnText = 'Abrir POS';
      }
    ?>
      <div class="cursor-pointer transition hover:shadow-xl border-2 <?= $cardClass ?> rounded-xl p-5 flex flex-col justify-between bg-white dark:bg-gray-800"
        onclick="window.location='index.php?page=mesa&id=<?= $mesa['id'] ?>'">
        <div>
          <div class="text-xl font-semibold text-gray-700 dark:text-white mb-2">
            <?= htmlspecialchars($mesa['nombre']) ?>
          </div>
          <div class="mb-1">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass ?>">
              <?= ucfirst($estado) ?>
            </span>
          </div>
          <div class="text-gray-500 dark:text-gray-300 text-sm mb-3"><?= htmlspecialchars($mesa['descripcion'] ?? '') ?></div>
        </div>
        <a href="index.php?page=mesa&id=<?= $mesa['id'] ?>"
          class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium text-center mt-2 transition mesa-btn">
          <?= $btnText ?>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>