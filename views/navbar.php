<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">POS Kalli Jaguar</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav me-auto">
      <li class="nav-item"><a class="nav-link" href="index.php?page=mesas">Mesas</a></li>
      <li class="nav-item"><a class="nav-link" href="index.php?page=ordenes">Ordenes</a></li>
      <li class="nav-item"><a class="nav-link" href="index.php?page=productos">Productos</a></li>
      <li class="nav-item"><a class="nav-link" href="index.php?page=cocina">Cocina</a></li>
      <li class="nav-item"><a class="nav-link" href="index.php?page=bar">Bar</a></li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item">
        <button id="fullscreen-btn" class="btn btn-outline-light" type="button" title="Pantalla completa">
          <i class="bi bi-arrows-fullscreen"></i>
        </button>
      </li>
    </ul>
  </div>
</nav>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fsBtn = document.getElementById('fullscreen-btn');
    if (fsBtn) {
      fsBtn.addEventListener('click', function() {
        if (!document.fullscreenElement) {
          document.documentElement.requestFullscreen();
        } else {
          document.exitFullscreen();
        }
      });
    }
  });
</script>