<?php
session_start();
include 'navbar.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Mesas</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container">
  <h3>Crear Nueva Mesa</h3>
  <form action="crearMesa.php" method="POST" class="form-inline mb-3">
    <input type="text" name="nombreMesa" class="form-control mr-2" required placeholder="Nombre de la mesa">
    <button type="submit" class="btn btn-success">Crear</button>
  </form>
  <h4>Mesas activas:</h4>
  <ul class="list-group">
  <?php
  foreach ($_SESSION['MESAS'] ?? [] as $id => $mesa) {
      echo "<li class='list-group-item'><a href='pos.php?mesa=$id'>Mesa $id</a></li>";
  }
  ?>
  </ul>
</div>
</body>
</html>