<?php
require_once '../../conexion.php';
$pdo = conexion();

$producto_id = intval($_POST['producto_id'] ?? 0);
$cantidad = intval($_POST['cantidad'] ?? 1);
$orden_id = intval($_POST['orden_id'] ?? 0);

if ($cantidad < 1 || !$producto_id || !$orden_id) {
    echo json_encode(['status'=>'error', 'msg'=>'Datos incompletos']);
    exit;
}

// Verifica si el producto existe
$stmt = $pdo->prepare("SELECT id, nombre, precio FROM productos WHERE id=?");
$stmt->execute([$producto_id]);
$prod = $stmt->fetch();

if (!$prod) {
    echo json_encode(['status'=>'error', 'msg'=>'Producto no encontrado']);
    exit;
}

// Busca si ya existe en la orden
$stmt = $pdo->prepare("SELECT id, cantidad FROM orden_productos WHERE orden_id=? AND producto_id=?");
$stmt->execute([$orden_id, $producto_id]);
$item = $stmt->fetch();

if ($item) {
    $nuevo = $item['cantidad'] + $cantidad;
    $pdo->prepare("UPDATE orden_productos SET cantidad=? WHERE id=?")
        ->execute([$nuevo, $item['id']]);
} else {
    $pdo->prepare("INSERT INTO orden_productos (orden_id, producto_id, cantidad, preparado, cancelado) VALUES (?, ?, ?, 0, 0)")
        ->execute([$orden_id, $producto_id, $cantidad]);
}

echo json_encode(['status'=>'ok']);
?>