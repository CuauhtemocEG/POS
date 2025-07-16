<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
<style>
  body, .font-montserrat {
    font-family: 'Montserrat', sans-serif !important;
  }
  .navbar-link {
    text-decoration: none !important;
  }
</style>

<nav class="border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700 font-montserrat">
  <div class="max-w-5xl flex flex-wrap items-center justify-between mx-auto p-2">
    <a href="#" class="flex items-center space-x-2 navbar-link">
      <img src="https://flowbite.com/docs/images/logo.svg" class="h-7" alt="Logo" />
      <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">POS Kalli Jaguar</span>
    </a>
    <div class="flex items-center space-x-1">
      <button id="navbar-toggle" type="button"
        class="inline-flex items-center p-2 w-9 h-9 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
        aria-controls="navbar-menu" aria-expanded="false"
      >
        <span class="sr-only">Abrir menú principal</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
      </button>
    </div>
    <div class="hidden w-full md:block md:w-auto" id="navbar-menu">
      <ul class="flex flex-col font-medium mt-2 rounded-lg bg-gray-50 md:space-x-4 md:flex-row md:mt-0 md:border-0 md:bg-transparent dark:bg-gray-800 md:dark:bg-transparent dark:border-gray-700 sm:text-sm text-base">
        <li>
          <a href="index.php?page=mesas" class="navbar-link block py-2 px-3 md:p-0 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:dark:text-blue-500 dark:bg-blue-600 md:dark:bg-transparent" aria-current="page">Mesas</a>
        </li>
        <li>
          <a href="index.php?page=ordenes" class="navbar-link block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Ordenes</a>
        </li>
        <li>
          <a href="index.php?page=productos" class="navbar-link block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Catalogo</a>
        </li>
        <li>
          <a href="index.php?page=cocina" class="navbar-link block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Cocina</a>
        </li>
        <li>
          <a href="index.php?page=bar" class="navbar-link block py-2 px-3 md:p-0 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Bebidas</a>
        </li>
      </ul>
    </div>
    <button onclick="toggleFullScreen()" type="button"
        class="inline-flex items-center p-2 w-9 h-9 justify-center text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
        title="Pantalla completa"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M8 3H5a2 2 0 0 0-2 2v3m0 8v3a2 2 0 0 0 2 2h3m8-16h3a2 2 0 0 1 2 2v3m0 8v3a2 2 0 0 1-2 2h-3">
          </path>
        </svg>
        <span class="sr-only">Pantalla completa</span>
      </button>
  </div>
</nav>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggle = document.getElementById('navbar-toggle');
  const menu = document.getElementById('navbar-menu');
  toggle.addEventListener('click', function() {
    const expanded = this.getAttribute('aria-expanded') === 'true';
    this.setAttribute('aria-expanded', !expanded);
    menu.classList.toggle('hidden');
    menu.classList.toggle('block');
  });
});

function toggleFullScreen() {
  if (
    !document.fullscreenElement &&
    !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement
  ) {
    var d = document.documentElement;
    if (d.requestFullscreen) d.requestFullscreen();
    else if (d.mozRequestFullScreen) d.mozRequestFullScreen();
    else if (d.webkitRequestFullscreen) d.webkitRequestFullscreen();
    else if (d.msRequestFullscreen) d.msRequestFullscreen();
  } else {
    if (document.exitFullscreen) document.exitFullscreen();
    else if (document.mozCancelFullScreen) document.mozCancelFullScreen();
    else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
    else if (document.msExitFullscreen) document.msExitFullscreen();
  }
}
</script>