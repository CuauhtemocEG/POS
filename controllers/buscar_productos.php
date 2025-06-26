<?php
require_once '../conexion.php';

$pdo = conexion();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$mesa_id = isset($_GET['mesa_id']) ? intval($_GET['mesa_id']) : 0;

if ($q === '') {
    $stmt = $pdo->query("SELECT * FROM productos LIMIT 20");
} else {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE nombre LIKE ? OR descripcion LIKE ? LIMIT 20");
    $stmt->execute(["%$q%", "%$q%"]);
}
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($productos);
?>