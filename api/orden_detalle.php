<?php
header('Content-Type: application/json');
require_once '../conexion.php';
$pdo = conexion();

$id = intval($_GET['id'] ?? 0);

$orden = $pdo->prepare("SELECT o.id, o.codigo, o.estado, o.creada_en, m.nombre AS mesa FROM ordenes o JOIN mesas m ON m.id=o.mesa_id WHERE o.id=?");
$orden->execute([$id]);
$orden = $orden->fetch(PDO::FETCH_ASSOC);

if (!$orden) { http_response_code(404); echo json_encode(['error'=>'No encontrada']); exit; }

$productos = $pdo->prepare("SELECT p.nombre, op.cantidad, op.preparado, op.cancelado, p.precio FROM orden_productos op JOIN productos p ON op.producto_id = p.id WHERE op.orden_id = ?");
$productos->execute([$id]);
$orden['productos'] = $productos->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0; foreach ($orden['productos'] as $prod) { $subtotal += $prod['precio']*$prod['cantidad']; }
$orden['subtotal'] = $subtotal; $orden['descuento'] = 0; $orden['impuestos'] = 0; $orden['total'] = $subtotal;

echo json_encode($orden);