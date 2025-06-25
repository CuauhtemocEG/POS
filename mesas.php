<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mesas</title>
</head>
<body>
<h2>Crear Nueva Mesa</h2>
<form action="crearMesa.php" method="POST">
    <input type="text" name="nombreMesa" required>
    <button type="submit">Crear</button>
</form>
<h3>Mesas activas:</h3>
<ul>
<?php
foreach ($_SESSION['MESAS'] ?? [] as $id => $mesa) {
    echo "<li><a href='pos.php?mesa=$id'>Mesa $id</a></li>";
}
?>
</ul>
</body>
</html>