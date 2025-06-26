<?php
$page = $_GET['page'] ?? 'mesas';
include 'views/header.php';
switch ($page) {
    case 'mesas':
        include 'views/mesas.php';
        break;
    case 'mesa':
        include 'views/mesa.php';
        break;
    case 'productos':
        include 'views/productos.php';
        break;
    case 'cocina':
        include 'views/cocina.php';
        break;
    case 'bar':
        include 'views/bar.php';
        break;
    default:
        include 'views/mesas.php';
}
?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>