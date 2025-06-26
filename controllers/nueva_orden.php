<?php
require_once '../conexion.php';

$pdo = conexion();
$mesa_id = $_POST['mesa_id'];
$pdo->prepare("INSERT INTO ordenes (mesa_id) VALUES (?)")->execute([$mesa_id]);
$pdo->prepare("UPDATE mesas SET estado='abierta' WHERE id=?")->execute([$mesa_id]);
header("Location: ../index.php?page=mesa&id=$mesa_id");
exit;
?>