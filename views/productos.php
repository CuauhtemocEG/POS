
<?php
require_once 'conexion.php';

$pdo = conexion();

$editando = false;
if (isset($_GET['editar'])) {
    $editando = true;
    $pid = $_GET['editar'];
    $prodedit = $pdo->prepare("SELECT * FROM productos WHERE id=?");
    $prodedit->execute([$pid]);
    $producto = $prodedit->fetch();
}

$productos = $pdo->query("SELECT * FROM productos")->fetchAll();
?>

<h2>Productos</h2>

<form method="post" action="controllers/productos_crud.php" enctype="multipart/form-data" class="mb-4">
  <input type="hidden" name="id" value="<?= $editando ? $producto['id'] : '' ?>">
  <div class="row g-2">
    <div class="col-md-2">
      <input type="text" name="nombre" class="form-control" placeholder="Nombre" required value="<?= $editando ? htmlspecialchars($producto['nombre']) : '' ?>">
    </div>
    <div class="col-md-2">
      <input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required value="<?= $editando ? $producto['precio'] : '' ?>">
    </div>
    <div class="col-md-2">
      <input type="text" name="descripcion" class="form-control" placeholder="Descripción" value="<?= $editando ? htmlspecialchars($producto['descripcion']) : '' ?>">
    </div>
    <div class="col-md-2">
      <select name="categoria" class="form-control" required>
        <option value="">Tipo</option>
        <option value="comidas" <?= $editando && $producto['categoria'] == 'comidas' ? 'selected' : '' ?>>Comidas</option>
        <option value="bebidas" <?= $editando && $producto['categoria'] == 'bebidas' ? 'selected' : '' ?>>Bebidas</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="type" class="form-control" required>
        <option value="">Categoría</option>
        <option value="1" <?= $editando && $producto['type'] == '1' ? 'selected' : '' ?>>Desayunos</option>
        <option value="2" <?= $editando && $producto['type'] == '2' ? 'selected' : '' ?>>Antojitos</option>
        <option value="7" <?= $editando && $producto['type'] == '7' ? 'selected' : '' ?>>Extras</option>
        <option value="8" <?= $editando && $producto['type'] == '8' ? 'selected' : '' ?>>Bebidas Frías</option>
        <option value="9" <?= $editando && $producto['type'] == '9' ? 'selected' : '' ?>>Bebidas Calientes</option>
      </select>
    </div>
    <div class="col-md-2">
      <input type="file" name="imagen" class="form-control" <?= $editando ? "" : "required" ?>>
      <?php if ($editando && $producto['imagen']) : ?>
        <img src="assets/img/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen" width="50">
      <?php endif; ?>
    </div>
  </div>
  <div class="row g-2 mt-2">
    <div class="col-md-2">
      <?php if ($editando): ?>
        <button type="submit" name="editar" class="btn btn-warning w-100">Actualizar</button>
        <a href="index.php?page=productos" class="btn btn-secondary w-100 mt-1">Cancelar</a>
      <?php else: ?>
        <button type="submit" name="crear" class="btn btn-success w-100">Agregar</button>
      <?php endif; ?>
    </div>
  </div>
</form>

<table class="table table-bordered">
  <thead>
    <tr><th>Imagen</th><th>Nombre</th><th>Precio</th><th>Descripción</th><th>Categoría</th><th>Acciones</th></tr>
  </thead>
  <tbody>
    <?php foreach ($productos as $prod): ?>
      <tr>
        <td>
          <?php if ($prod['imagen']): ?>
            <img src="assets/img/<?= htmlspecialchars($prod['imagen']) ?>" alt="Imagen" width="50">
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($prod['nombre']) ?></td>
        <td>$<?= number_format($prod['precio'],2) ?></td>
        <td><?= htmlspecialchars($prod['descripcion']) ?></td>
        <td><?= htmlspecialchars($prod['categoria']) ?></td>
        <td>
          <a href="index.php?page=productos&editar=<?= $prod['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
          <a href="controllers/productos_crud.php?eliminar=<?= $prod['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>