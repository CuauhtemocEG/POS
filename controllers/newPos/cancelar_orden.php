<?php
require_once '../../conexion.php';
$pdo = conexion();

$orden_id = intval($_POST['orden_id'] ?? 0);
if (!$orden_id) {
    echo json_encode(['status'=>'error', 'msg'=>'Orden no válida']);
    exit;
}

// Elimina productos de la orden
$pdo->prepare("DELETE FROM orden_productos WHERE orden_id=?")->execute([$orden_id]);
// Opcional: Puedes marcar la orden como cancelada
$pdo->prepare("UPDATE ordenes SET estado='cancelada' WHERE id=?")->execute([$orden_id]);

echo json_encode(['status'=>'ok']);
?>ca