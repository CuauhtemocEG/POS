<?php
require_once 'conexion.php';
$pdo = conexion();

$date_filter = $_GET['date_filter'] ?? '30days';
$search = trim($_GET['search'] ?? '');

$where = [];
$params = [];

switch ($date_filter) {
  case '1day':
    $where[] = "o.creada_en >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
    break;
  case '7days':
    $where[] = "o.creada_en >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    break;
  case '30days':
    $where[] = "o.creada_en >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    break;
  case 'month':
    $where[] = "YEAR(o.creada_en) = YEAR(NOW()) AND MONTH(o.creada_en) = MONTH(NOW())";
    break;
  case 'year':
    $where[] = "YEAR(o.creada_en) = YEAR(NOW())";
    break;
}

if ($search !== '') {
  $where[] = "(o.codigo LIKE :search OR m.nombre LIKE :search OR o.estado LIKE :search)";
  $params[':search'] = "%$search%";
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT o.*, m.nombre AS mesa_nombre
    FROM ordenes o
    JOIN mesas m ON m.id = o.mesa_id
    $where_sql
    ORDER BY o.creada_en DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="max-w-5xl mx-auto py-6 px-3">
  <h2 class="mb-6 text-center text-3xl font-bold text-gray-800 dark:text-white">Listado de Órdenes</h2>
  <div id="ordenes-filtros" class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4 px-4 pt-4">
    <form id="filtros-form" class="flex items-center space-x-2 w-full sm:w-auto">
      <select name="date_filter"
          class="text-gray-700 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-2 focus:ring-blue-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
        <option value="1day">Último día</option>
        <option value="7days">Últimos 7 días</option>
        <option value="30days" selected>Últimos 30 días</option>
        <option value="month">Este mes</option>
        <option value="year">Este año</option>
      </select>
      <input type="text" name="search" autocomplete="off" placeholder="Buscar órdenes"
        class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-44 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
    </form>
  </div>
  <div id="tabla-ordenes" class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white dark:bg-gray-900">
    <div class="p-6 text-center text-gray-600 dark:text-gray-300">Cargando órdenes...</div>
  </div>
</div>
<script>
  function cargarOrdenes(page = 1) {
    const form = document.getElementById('filtros-form');
    const data = new FormData(form);
    data.append('page', page);

    fetch('controllers/orders/ordenes_list.php', {
        method: 'POST',
        body: data
      })
      .then(resp => resp.text())
      .then(html => {
        document.getElementById('tabla-ordenes').innerHTML = html;
        document.querySelectorAll('.paginacion-ordenes').forEach(el => {
          el.onclick = e => {
            e.preventDefault();
            cargarOrdenes(el.dataset.page);
          };
        });
      });
  }

  document.addEventListener('DOMContentLoaded', function() {
    cargarOrdenes();

    document.getElementById('filtros-form').addEventListener('input', () => cargarOrdenes());
    document.getElementById('filtros-form').addEventListener('change', () => cargarOrdenes());
  });
</script>