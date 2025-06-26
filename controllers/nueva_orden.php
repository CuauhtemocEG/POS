<?php
require_once '../conexion.php';
$pdo = conexion();
$mesa_id = $_POST['mesa_id'];
if (!$mesa_id) {
    header("Location: ../index.php?page=error&msg=No se ha seleccionado una mesa");
    exit;
}
$codigo = 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
$stmt = $pdo->prepare("INSERT INTO ordenes (mesa_id, codigo) VALUES (?, ?)");
$stmt->execute([$mesa_id, $codigo]);
$orden_id = $pdo->lastInsertId();
$pdo->prepare("UPDATE mesas SET estado='abierta' WHERE id=?")->execute([$mesa_id]);
header("Location: ../index.php?page=mesa&id=$mesa_id");
exit;
?>