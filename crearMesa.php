<?php
require_once 'conexion.php';
session_start();
$nombreMesa = $_POST['nombreMesa'];
$pdo = conexion();
$stmt = $pdo->prepare("INSERT INTO Mesas (NombreMesa) VALUES (:nombre)");
$stmt->execute(['nombre' => $nombreMesa]);
$mesaID = $pdo->lastInsertId();
$_SESSION['MESAS'][$mesaID] = ['productos' => [], 'activa' => true];
header("Location: pos.php?mesa=$mesaID");