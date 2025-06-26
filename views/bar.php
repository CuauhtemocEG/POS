<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Vista Bar (Bebidas)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/bar.css">
</head>

<body>
    <div class="container py-3">
        <div class="bar-header mb-3">
            <h2 class="text-center mb-0"><i class="bi bi-cup-straw"></i> Vista Bar (Bebidas)</h2>
        </div>
        <div id="bar-content"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function cargarBar() {
            fetch('controllers/bar_ajax.php')
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
                        html += `<div class="mesa-section mb-4">
            <div class="mesa-title"><i class="bi bi-table"></i> Mesa: <span>${nombreMesa}</span></div>
            <div class="table-responsive">
              <table class="table align-middle table-sm">
                <thead class="table-light">
                  <tr>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th class="preparado-col">Preparado</th>
                    <th class="cancelado-col">Cancelado</th>
                    <th class="faltan-col">Faltan</th>
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>`;
                        mesas[nombreMesa].forEach(item => {
                            html += `<tr>
                <td>${item.producto}</td>
                <td>${item.cantidad}</td>
                <td class="preparado-col">${item.preparado}</td>
                <td class="cancelado-col">${item.cancelado}</td>
                <td class="faltan-col">${item.faltan}</td>
                <td>
                  <form class="marcar-preparado-form-bar" data-op="${item.op_id}">
                    <input type="number" name="marcar" value="1" min="1" max="${item.faltan}" class="form-control form-control-sm d-inline" style="width:60px;">
                    <button type="submit" class="btn btn-success btn-sm">Preparado</button>
                  </form>
                </td>
              </tr>`;
                        });
                        html += `</tbody></table></div></div>`;
                    }
                    document.getElementById('bar-content').innerHTML = html;

                    document.querySelectorAll('.marcar-preparado-form-bar').forEach(form => {
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
                                        cargarBar();
                                    } else {
                                        Swal.fire('Error', resp.msg, 'error');
                                    }
                                });
                        }
                    });
                });
        }
        setInterval(cargarBar, 3000);
        cargarBar();
    </script>
</body>

</html>