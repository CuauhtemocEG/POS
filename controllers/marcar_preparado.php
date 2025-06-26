<?php
require_once '../conexion.php';

$pdo = conexion();
$op_id = $_POST['op_id'];
$marcar = isset($_POST['marcar']) ? intval($_POST['marcar']) : 1;

// Consulta la cantidad y preparado/cancelado actuales
$stmt = $pdo->prepare("SELECT cantidad, preparado, cancelado FROM orden_productos WHERE id=?");
$stmt->execute([$op_id]);
$row = $stmt->fetch();

if ($row) {
    $pendientes = $row['cantidad'] - $row['preparado'] - $row['cancelado'];
    $a_preparar = min($pendientes, max(1, $marcar));
    $nuevo_preparado = $row['preparado'] + $a_preparar;
    $pdo->prepare("UPDATE orden_productos SET preparado=? WHERE id=?")
        ->execute([$nuevo_preparado, $op_id]);
    echo json_encode(["status"=>"ok", "msg"=>"Se marcaron $a_preparar como preparados"]);
} else {
    echo json_encode(["status"=>"error", "msg"=>"No se encontró el producto"]);
}
exit;
?>