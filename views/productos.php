<?php
require_once 'conexion.php';
$pdo = conexion();

// Obtén todos los tipos y crea un array asociativo id => nombre
$tipos = $pdo->query("SELECT id, nombre FROM type")->fetchAll(PDO::FETCH_KEY_PAIR);

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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .productos-title {
    font-weight: 600;
    color:rgba(11, 76, 188, 0.65);
    margin-bottom: 2rem;
  }
  .table-productos th, .table-productos td {
    vertical-align: middle !important;
    text-align: center;
  }
  .table-productos img {
    border-radius: .5rem;
    box-shadow: 0 2px 10px #2a529822;
    max-width: 55px;
    max-height: 55px;
  }
  .modal-header {
    background: linear-gradient(90deg,rgb(178, 102, 35) 40%,rgb(217, 112, 13) 100%);
    color: #fff;
  }
  @media (max-width: 900px) {
    .productos-title { font-size: 1.35rem; }
  }
  @media (max-width: 600px) {
    .productos-title { font-size: 1.1rem; text-align: center; }
    .table-productos th, .table-productos td { font-size: .95rem; }
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="productos-title mb-0">Gestión de Productos</h2>
  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto"><i class="bi bi-plus-circle"></i> Agregar Producto</button>
</div>

<?php if ($editando): ?>
  <div class="alert alert-warning"><i class="bi bi-pencil-square"></i> Editando producto: <strong><?= htmlspecialchars($producto['nombre']) ?></strong></div>
<?php endif; ?>

<!-- Modal Agregar Producto -->
<div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form id="formProducto" enctype="multipart/form-data" class="productos-form">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAgregarProductoLabel"><i class="bi bi-plus-circle"></i> Agregar Producto</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Precio</label>
              <input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Descripción</label>
              <input type="text" name="descripcion" class="form-control" placeholder="Descripción">
            </div>
            <div class="col-md-4">
              <label class="form-label">Tipo</label>
              <select name="categoria" class="form-control" required>
                <option value="">Tipo</option>
                <option value="comidas">Comidas</option>
                <option value="bebidas">Bebidas</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Categoría</label>
              <select name="type" class="form-control" required>
                <option value="">Categoría</option>
                <?php foreach ($tipos as $idTipo => $nombreTipo): ?>
                  <option value="<?= $idTipo ?>"><?= htmlspecialchars($nombreTipo) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Imagen</label>
              <input type="file" name="imagen" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="crear" class="btn btn-success"><i class="bi bi-check-circle"></i> Guardar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Editar producto (solo cuando está en modo edición) -->
<?php if ($editando): ?>
<form id="formProductoEditar" enctype="multipart/form-data" class="productos-form mb-4">
  <input type="hidden" name="id" value="<?= $producto['id'] ?>">
  <div class="row g-2">
    <div class="col-md-2 col-6">
      <input type="text" name="nombre" class="form-control" placeholder="Nombre" required value="<?= htmlspecialchars($producto['nombre']) ?>">
    </div>
    <div class="col-md-2 col-6">
      <input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required value="<?= $producto['precio'] ?>">
    </div>
    <div class="col-md-2 col-6">
      <input type="text" name="descripcion" class="form-control" placeholder="Descripción" value="<?= htmlspecialchars($producto['descripcion']) ?>">
    </div>
    <div class="col-md-2 col-6">
      <select name="categoria" class="form-control" required>
        <option value="">Tipo</option>
        <option value="comidas" <?= $producto['categoria'] == 'comidas' ? 'selected' : '' ?>>Comidas</option>
        <option value="bebidas" <?= $producto['categoria'] == 'bebidas' ? 'selected' : '' ?>>Bebidas</option>
      </select>
    </div>
    <div class="col-md-2 col-6">
      <select name="type" class="form-control" required>
        <option value="">Categoría</option>
        <?php foreach ($tipos as $idTipo => $nombreTipo): ?>
          <option value="<?= $idTipo ?>" <?= $producto['type'] == $idTipo ? 'selected' : '' ?>>
            <?= htmlspecialchars($nombreTipo) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2 col-6">
      <input type="file" name="imagen" class="form-control">
      <?php if ($producto['imagen']) : ?>
        <img src="assets/img/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen" class="mt-1" width="50">
      <?php endif; ?>
    </div>
  </div>
  <div class="row g-2 mt-2">
    <div class="col-md-2 col-6">
      <button type="submit" name="editar" class="btn btn-warning w-100"><i class="bi bi-pencil-square"></i> Actualizar</button>
      <a href="index.php?page=productos" class="btn btn-secondary w-100 mt-1">Cancelar</a>
    </div>
  </div>
</form>
<?php endif; ?>

<div class="table-responsive">
  <table class="table table-striped table-bordered align-middle table-productos">
    <thead class="table-primary">
      <tr>
        <th>Imagen</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Descripción</th>
        <th>Categoría</th>
        <th>Tipo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody id="tbodyProductos">
      <?php foreach ($productos as $prod): ?>
        <tr>
          <td>
            <?php if ($prod['imagen']): ?>
              <img src="assets/img/<?= htmlspecialchars($prod['imagen']) ?>" alt="Imagen">
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($prod['nombre']) ?></td>
          <td>$<?= number_format($prod['precio'], 2) ?></td>
          <td><?= htmlspecialchars($prod['descripcion']) ?></td>
          <td><?= htmlspecialchars($prod['categoria']) ?></td>
          <td>
            <?= isset($tipos[$prod['type']]) ? htmlspecialchars($tipos[$prod['type']]) : '<span class="text-muted">Sin tipo</span>' ?>
          </td>
          <td>
            <a href="index.php?page=productos&editar=<?= $prod['id'] ?>" class="btn btn-sm btn-warning mb-1"><i class="bi bi-pencil"></i> Editar</a>
            <button class="btn btn-sm btn-danger btn-eliminar" data-id="<?= $prod['id'] ?>"><i class="bi bi-trash"></i> Eliminar</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Modal agregar producto
  document.getElementById('formProducto').onsubmit = function(e) {
    e.preventDefault();
    let form = this;
    let formData = new FormData(form);
    formData.append('crear', '1');
    fetch('controllers/productos_crud.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(resp => {
        if (resp.status === 'ok') {
          Swal.fire('¡Guardado!', resp.msg || 'Producto guardado correctamente', 'success')
            .then(() => location.href = 'index.php?page=productos');
        } else {
          Swal.fire('Error', resp.msg || 'No se pudo guardar', 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'No se pudo conectar con el servidor', 'error'));
  };

  // Editar producto
  <?php if ($editando): ?>
  document.getElementById('formProductoEditar').onsubmit = function(e) {
    e.preventDefault();
    let form = this;
    let formData = new FormData(form);
    formData.append('editar', '1');
    fetch('controllers/productos_crud.php', {
        method: 'POST',
        body: formData
      })
      .then(r => r.json())
      .then(resp => {
        if (resp.status === 'ok') {
          Swal.fire('¡Actualizado!', resp.msg || 'Producto actualizado correctamente', 'success')
            .then(() => location.href = 'index.php?page=productos');
        } else {
          Swal.fire('Error', resp.msg || 'No se pudo actualizar', 'error');
        }
      })
      .catch(() => Swal.fire('Error', 'No se pudo conectar con el servidor', 'error'));
  };
  <?php endif; ?>

  // Eliminar producto
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.onclick = function(e) {
      e.preventDefault();
      let pid = this.getAttribute('data-id');
      Swal.fire({
        title: '¿Eliminar producto?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then(result => {
        if (result.isConfirmed) {
          fetch('controllers/productos_crud.php?eliminar=' + pid)
            .then(r => r.json())
            .then(resp => {
              if (resp.status === 'ok') {
                Swal.fire('Eliminado', resp.msg || 'Producto eliminado', 'success')
                  .then(() => location.reload());
              } else {
                Swal.fire('Error', resp.msg || 'No se pudo eliminar', 'error');
              }
            })
            .catch(() => Swal.fire('Error', 'No se pudo conectar con el servidor', 'error'));
        }
      });
    }
  });
</script>