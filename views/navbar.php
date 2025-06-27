  <style>
    body {
      background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
      min-height: 100vh;
    }

    .navbar-custom {
      background: linear-gradient(90deg, rgb(8, 13, 22) 0%, rgb(119, 68, 1) 100%);
      box-shadow: 0 4px 18px -2px #0001;
      border-radius: 0 0 1rem 1rem;
      padding-top: .4rem;
      padding-bottom: .4rem;
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.35rem;
      letter-spacing: 1px;
      display: flex;
      align-items: center;
      gap: 0.6rem;
    }

    .navbar-nav .nav-link {
      font-size: 1.13rem;
      border-radius: .45rem;
      margin-right: 0.4rem;
      transition: background .17s, color .17s;
      color: #f1f1f1 !important;
    }

    .navbar-nav .nav-link.active,
    .navbar-nav .nav-link:focus,
    .navbar-nav .nav-link:hover {
      background: rgba(255, 255, 255, 0.13);
      color: #fff !important;
    }

    .navbar-user {
      display: flex;
      align-items: center;
      gap: 0.7rem;
    }

    .navbar-user .avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #fff3;
    }

    .main-content-wrapper {
      max-width: 1200px;
      margin: 2rem auto 1rem auto;
      background: #fff;
      border-radius: 1.3rem;
      box-shadow: 0 8px 32px -7px #004b7080;
      padding: 2.5rem 2rem 1.5rem 2rem;
    }

    @media (max-width: 900px) {
      .main-content-wrapper {
        padding: 1.2rem 0.2rem;
        border-radius: 0.7rem;
      }

      .navbar-custom {
        border-radius: 0;
      }
    }
  </style>
  </head>
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">
        <i class="bi bi-cash-stack fs-3"></i> POS Kalli Jaguar
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.php?page=mesas"><i class="bi bi-grid-3x3-gap"></i> Mesas</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=ordenes"><i class="bi bi-list-check"></i> Ordenes</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=productos"><i class="bi bi-box-seam"></i> Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=cocina"><i class="bi bi-egg-fried"></i> Cocina</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page=bar"><i class="bi bi-cup-straw"></i> Bar</a></li>
        </ul>
        <div class="navbar-user ms-auto">
          <button id="fullscreen-btn" class="btn btn-outline-light border-0" type="button" title="Pantalla completa">
            <i class="bi bi-arrows-fullscreen fs-5"></i>
          </button>
        </div>
      </div>
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

      document.querySelectorAll('.nav-link').forEach(link => {
        if (location.href.includes(link.getAttribute('href'))) {
          link.classList.add('active');
        }
      });
    });
  </script>
  <div class="main-content-wrapper">