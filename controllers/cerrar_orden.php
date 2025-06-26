<?php
require_once '../conexion.php';
$orden_id = $_POST['orden_id'];

$pdo = conexion();
$pdo->prepare("UPDATE ordenes SET estado='pagada' WHERE id=?")->execute([$orden_id]);
$mesa_id = $pdo->query("SELECT mesa_id FROM ordenes WHERE id=$orden_id")->fetchColumn();
$pdo->prepare("UPDATE mesas SET estado='cerrada' WHERE id=?")->execute([$mesa_id]);
header("Location: impresion_ticket.php?orden_id=$orden_id");
exit;
?>