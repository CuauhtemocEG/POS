<?php
require_once '../../conexion.php';
$pdo = conexion();

$producto_id = intval($_POST['producto_id'] ?? 0);
$cantidad = intval($_POST['cantidad'] ?? 1);
$orden_id = intval($_POST['orden_id'] ?? 0);

if (!$producto_id || !$orden_id) {
    echo json_encode(['status'=>'error', 'msg'=>'Datos incompletos']);
    exit;
}

if ($cantidad <= 0) {
    // Eliminar producto de la orden
    $stmt = $pdo->prepare("DELETE FROM orden_productos WHERE orden_id=? AND producto_id=?");
    $stmt->execute([$orden_id, $producto_id]);
} else {
    // Actualizar cantidad
    $stmt = $pdo->prepare("UPDATE orden_productos SET cantidad=? WHERE orden_id=? AND producto_id=?");
    $stmt->execute([$cantidad, $orden_id, $producto_id]);
}

echo json_encode(['status'=>'ok']);
?>