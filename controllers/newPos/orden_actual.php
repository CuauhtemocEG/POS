<?php
require_once '../../conexion.php';
$pdo = conexion();

$orden_id = intval($_GET['orden_id'] ?? 0);
if (!$orden_id) {
    echo json_encode(['items'=>[], 'subtotal'=>0, 'descuento'=>0, 'impuestos'=>0, 'total'=>0]);
    exit;
}

$stmt = $pdo->prepare("SELECT op.producto_id AS id, p.nombre, op.cantidad, p.precio 
    FROM orden_productos op
    JOIN productos p ON op.producto_id = p.id
    WHERE op.orden_id=?");
$stmt->execute([$orden_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($items as &$item) {
    $item['cantidad'] = intval($item['cantidad']);
    $item['precio'] = floatval($item['precio']);
    $subtotal += $item['precio'] * $item['cantidad'];
}
unset($item);

$descuento = 0;
$impuestos = 0;
$total = $subtotal - $descuento + $impuestos;

echo json_encode([
    'items' => $items,
    'subtotal' => $subtotal,
    'descuento' => $descuento,
    'impuestos' => $impuestos,
    'total' => $total
]);
?>