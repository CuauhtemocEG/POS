<?php
require_once '../conexion.php';

$pdo = conexion();

function guardarImagen($archivo) {
    if ($archivo['error'] === UPLOAD_ERR_OK) {
        $nombreTmp = $archivo['tmp_name'];
        $nombreOriginal = basename($archivo['name']);
        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $permitidas)) return null;
        $nuevoNombre = uniqid().".".$ext;
        move_uploaded_file($nombreTmp, "../assets/img/" . $nuevoNombre);
        return $nuevoNombre;
    }
    return null;
}

// Alta
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $imagen = guardarImagen($_FILES['imagen']);
    $pdo->prepare("INSERT INTO productos (nombre, precio, descripcion, categoria, imagen) VALUES (?, ?, ?, ?, ?)")
        ->execute([$nombre, $precio, $descripcion, $categoria, $imagen]);
    header("Location: ../index.php?page=productos");
    exit;
}

// Edición
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $imagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen = guardarImagen($_FILES['imagen']);
    }
    if ($imagen) {
        $pdo->prepare("UPDATE productos SET nombre=?, precio=?, descripcion=?, categoria=?, imagen=? WHERE id=?")
            ->execute([$nombre, $precio, $descripcion, $categoria, $imagen, $id]);
    } else {
        $pdo->prepare("UPDATE productos SET nombre=?, precio=?, descripcion=?, categoria=? WHERE id=?")
            ->execute([$nombre, $precio, $descripcion, $categoria, $id]);
    }
    header("Location: ../index.php?page=productos");
    exit;
}

// Eliminación
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    // Borra la imagen del producto
    $stmt = $pdo->prepare("SELECT imagen FROM productos WHERE id=?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists("../assets/img/$img")) {
        unlink("../assets/img/$img");
    }
    $pdo->prepare("DELETE FROM productos WHERE id=?")->execute([$id]);
    header("Location: ../index.php?page=productos");
    exit;
}
?>