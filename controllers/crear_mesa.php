<?php
require_once '../conexion.php';
$pdo = conexion();
$nombre = $_POST['nombre'];
$pdo->prepare("INSERT INTO mesas (nombre) VALUES (?)")->execute([$nombre]);
$mesa_id = $pdo->lastInsertId();
$pdo->prepare("INSERT INTO ordenes (mesa_id) VALUES (?)")->execute([$mesa_id]);
header("Location: ../index.php?page=mesa&id=$mesa_id");
exit;
?>