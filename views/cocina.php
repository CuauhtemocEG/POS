<div class="container mx-auto py-6">
  <div class="kitchen-header mb-6">
    <h2 class="text-center text-3xl font-bold mb-0 flex items-center justify-center gap-2 text-gray-800 dark:text-white">
      <i class="bi bi-egg-fried text-yellow-500 text-4xl"></i> Vista Cocina <span class="hidden sm:inline">(Comidas)</span>
    </h2>
  </div>
  <div id="cocina-content"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function cargarCocina() {
    fetch('controllers/cocina_ajax.php')
      .then(res => res.json())
      .then(data => {
        // Agrupar por mesa
        let mesas = {};
        data.forEach(item => {
          if (!mesas[item.mesa]) mesas[item.mesa] = [];
          mesas[item.mesa].push(item);
        });

        let html = '';
        for (const nombreMesa in mesas) {
          html += `<div class="mesa-section mb-8 p-6 rounded-xl shadow-lg bg-white border border-gray-200">
            <div class="mesa-title flex items-center gap-2 mb-4 text-lg font-semibold text-blue-700">
              <i class='bi bi-table text-2xl'></i> Mesa: <span class="text-blue-900">${nombreMesa}</span>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-50">
                  <tr>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-700">Producto</th>
                    <th class="px-2 py-2 text-center text-xs font-bold text-gray-700">Cant.</th>
                    <th class="px-2 py-2 text-center text-xs font-bold text-green-700">Preparado</th>
                    <th class="px-2 py-2 text-center text-xs font-bold text-red-700">Cancelado</th>
                    <th class="px-2 py-2 text-center text-xs font-bold text-yellow-700">Faltan</th>
                    <th class="px-2 py-2 text-center text-xs font-bold text-gray-700">Acción</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">`;
          mesas[nombreMesa].forEach(item => {
            html += `<tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-gray-900">${item.producto}</td>
                <td class="px-2 py-2 text-center font-semibold">${item.cantidad}</td>
                <td class="px-2 py-2 text-center text-green-600 font-bold">${item.preparado}</td>
                <td class="px-2 py-2 text-center text-red-600 font-bold">${item.cancelado}</td>
                <td class="px-2 py-2 text-center text-yellow-600 font-bold">${item.faltan}</td>
                <td class="px-2 py-2 text-center">
                  <form class="marcar-preparado-form-cocina flex items-center gap-2 justify-center" data-op="${item.op_id}">
                    <input type="number" name="marcar" value="1" min="1" max="${item.faltan}" class="w-16 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-center" style="width:60px;">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded shadow text-sm font-semibold transition">Preparado</button>
                  </form>
                </td>
              </tr>`;
          });
          html += `</tbody></table></div></div>`;
        }
        document.getElementById('cocina-content').innerHTML = html;

        document.querySelectorAll('.marcar-preparado-form-cocina').forEach(form => {
          form.onsubmit = function(e) {
            e.preventDefault();
            let op_id = form.getAttribute('data-op');
            let marcar = form.querySelector('input[name="marcar"]').value;
            fetch('controllers/marcar_preparado.php', {
                method: 'POST',
                body: new URLSearchParams({
                  op_id,
                  marcar
                }),
                headers: {
                  'X-Requested-With': 'XMLHttpRequest'
                }
              })
              .then(r => r.json())
              .then(resp => {
                if (resp.status === "ok") {
                  Swal.fire('¡Listo!', resp.msg, 'success');
                  cargarCocina();
                } else {
                  Swal.fire('Error', resp.msg, 'error');
                }
              });
          }
        });
      });
  }
  setInterval(cargarCocina, 3000);
  cargarCocina();
</script>