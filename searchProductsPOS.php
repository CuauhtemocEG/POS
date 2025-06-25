<?php
require_once 'conexion.php';
$query = $_GET['query'] ?? '';
$pdo = conexion();
$sql = "SELECT ProductoID, Nombre, PrecioUnitario, Cantidad FROM Productos 
        WHERE Nombre LIKE :query OR UPC LIKE :query LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute(['query' => "%$query%"]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));