<?php
require_once 'conexion.php';
$pdo = conexion();

$mesa_id = intval($_GET['id']);
$mesa = $pdo->query("SELECT * FROM mesas WHERE id=$mesa_id")->fetch();
$orden = $pdo->query("SELECT * FROM ordenes WHERE mesa_id=$mesa_id AND estado='abierta'")->fetch();
$orden_id = $orden ? $orden['id'] : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mesa <?= htmlspecialchars($mesa['nombre']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/mesa.css">
</head>
<body>
<div class="container py-3">
    <div class="mesa-header mb-4">
        <h2 class="mb-0"><i class="bi bi-table"></i> Mesa: <?= htmlspecialchars($mesa['nombre']) ?></h2>
        <a href="index.php?page=mesas" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver a Mesas</a>
    </div>

    <?php if ($orden): ?>
    <div class="mb-3">
        <button class="btn btn-info w-100 d-md-none toggle-productos-btn" type="button" id="toggleProductosBtn">
            <i class="bi bi-bag-plus"></i> Catálogo de Productos
        </button>
    </div>
    <div class="pos-container">
        <!-- Panel Izquierdo: Lista de la orden -->
        <div class="pos-left d-flex flex-column shadow-sm">
            <div class="pos-sale-list" id="orden-lista"></div>
            <div class="pos-summary border-top pt-2 mb-2" id="orden-totales"></div>
            <div class="pos-actions mt-auto">
                <button class="btn btn-danger" id="cancelar_orden"><i class="bi bi-x-circle"></i> Cancelar Orden</button>
                <a href="controllers/impresion_ticket.php?orden_id=<?= $orden_id ?>" target="_blank" class="btn btn-info"><i class="bi bi-printer"></i> Imprimir Ticket</a>
                <form method="post" action="controllers/cerrar_orden.php" class="d-inline">
                    <input type="hidden" name="orden_id" value="<?= $orden['id'] ?>">
                    <button type="submit" class="btn btn-success"><i class="bi bi-cash-coin"></i> Cerrar y pagar</button>
                </form>
            </div>
        </div>
        <!-- Panel Derecho: Catálogo -->
        <div class="pos-right shadow-sm" id="divProductos">
            <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">
                <input type="text" id="buscador" class="form-control" style="max-width:220px;" placeholder="Buscar producto...">
                <div id="categorias" class="d-flex flex-wrap"></div>
            </div>
            <div class="pos-products" id="productos"></div>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-info">No hay orden abierta en esta mesa.</div>
        <form method="post" action="controllers/nueva_orden.php">
            <input type="hidden" name="mesa_id" value="<?= $mesa_id ?>">
            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Abrir nueva orden</button>
        </form>
    <?php endif; ?>
</div>

<script>
const mesaId = <?= $mesa_id ?>;
const ordenId = <?= $orden_id ?>;

// --- Responsive Catalog Toggle ---
let productosVisible = false;
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleProductosBtn');
    const divProductos = document.getElementById('divProductos');
    if(toggleBtn && divProductos){
        toggleBtn.addEventListener('click', function(){
            productosVisible = !productosVisible;
            divProductos.classList.toggle('show', productosVisible);
            toggleBtn.innerHTML = productosVisible
                ? '<i class="bi bi-x-circle"></i> Ocultar Catálogo'
                : '<i class="bi bi-bag-plus"></i> Catálogo de Productos';
            if(productosVisible) window.scrollTo({top: divProductos.offsetTop - 60, behavior: 'smooth'});
        });
    }
});

// Cargar categorías
function cargarCategorias() {
    fetch('controllers/categorias.php')
    .then(r => r.json())
    .then(data => {
        let html = '';
        data.forEach((cat, i) => {
            html += `<button class='pos-category-btn${i===0 ? " active" : ""}' data-cat="${cat.id}">${cat.nombre}</button>`;
        });
        document.getElementById('categorias').innerHTML = html;
        document.querySelectorAll('.pos-category-btn').forEach(btn => {
            btn.onclick = function() {
                document.querySelectorAll('.pos-category-btn').forEach(b=>b.classList.remove('active'));
                this.classList.add('active');
                cargarProductos(this.getAttribute('data-cat'), document.getElementById('buscador').value);
            }
        });
    });
}

// Cargar productos AJAX
function cargarProductos(cat_id='', q='') {
    fetch(`controllers/buscar_productos.php?cat_id=${encodeURIComponent(cat_id)}&q=${encodeURIComponent(q)}`)
    .then(r => r.json())
    .then(data => {
        let html = '';
        data.forEach(prod => {
            html += `
            <div class="product-card" data-id="${prod.id}">
                <img src="assets/img/${prod.imagen || 'noimg.png'}" alt="${prod.nombre}">
                <div class="product-name">${prod.nombre}</div>
                <div class="product-price">$${Number(prod.precio).toFixed(2)}</div>
            </div>`;
        });
        document.getElementById('productos').innerHTML = html || "<div class='text-center w-100 alert alert-warning'>Sin productos</div>";
        document.querySelectorAll('.product-card').forEach(card=>{
            card.onclick = () => agregarProductoMesa(card.getAttribute('data-id'));
        });
    });
}

// Agregar producto a la orden de la mesa
function agregarProductoMesa(producto_id) {
    fetch('controllers/newPos/agregar_producto_orden.php', {
        method: 'POST',
        body: new URLSearchParams({producto_id, cantidad: 1, orden_id: ordenId}),
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r=>r.json())
    .then(resp=>{
        if(resp.status === "ok") {
            cargarOrden();
            Swal.fire('Agregado', 'Producto agregado a la orden.', 'success');
        } else {
            Swal.fire('Error', resp.msg || 'No se pudo agregar', 'error');
        }
    });
}

// Cargar lista de orden actual
function cargarOrden() {
    fetch('controllers/newPos/orden_actual.php?orden_id=' + ordenId)
    .then(r=>r.json())
    .then(data=>{
        let html = `<table class="table table-hover table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cant</th>
                    <th>Subt.</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>`;
        let subtotal = 0;
        data.items.forEach(item => {
            let sub = item.precio * item.cantidad;
            subtotal += sub;
            html += `<tr>
                <td>${item.nombre}</td>
                <td>$${Number(item.precio).toFixed(2)}</td>
                <td>
                    <input type="number" min="1" value="${item.cantidad}" class="sale-item-qty form-control form-control-sm d-inline" data-id="${item.id}">
                </td>
                <td>$${sub.toFixed(2)}</td>
                <td>
                    <button class="sale-item-remove btn btn-link p-0" data-id="${item.id}" title="Eliminar"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('orden-lista').innerHTML = html;

        // Totales
        let resumen = `
            <div>Subtotal: <span class="fw-bold text-primary">$${Number(data.subtotal).toFixed(2)}</span></div>
            <div>Descuento: <span class="fw-bold text-success">$${Number(data.descuento||0).toFixed(2)}</span></div>
            <div>Impuestos: <span class="fw-bold text-warning">$${Number(data.impuestos||0).toFixed(2)}</span></div>
            <div class="fw-bold fs-5">Total: $${Number(data.total).toFixed(2)}</div>
        `;
        document.getElementById('orden-totales').innerHTML = resumen;

        // Cambiar cantidad
        document.querySelectorAll('.sale-item-qty').forEach(input=>{
            input.onchange = function() {
                let val = Math.max(1, parseInt(this.value));
                fetch('controllers/newPos/actualizar_producto_orden.php', {
                    method: 'POST',
                    body: new URLSearchParams({producto_id:this.getAttribute('data-id'), cantidad:val, orden_id: ordenId}),
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                }).then(()=>cargarOrden());
            }
        });

        // Eliminar producto
        document.querySelectorAll('.sale-item-remove').forEach(btn=>{
            btn.onclick = function() {
                fetch('controllers/newPos/actualizar_producto_orden.php', {
                    method: 'POST',
                    body: new URLSearchParams({producto_id:this.getAttribute('data-id'), cantidad:0, orden_id: ordenId}),
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                }).then(()=>cargarOrden());
            }
        });
    });
}

let ordenInterval = null;

// Función para arrancar o detener el polling según el estado de la ventana
function handleVisibility() {
  if (document.visibilityState === 'visible') {
    if (!ordenInterval) {
      cargarOrden(); // refresca al volver
      ordenInterval = setInterval(cargarOrden, 2000); // cada 2 segundos
    }
  } else {
    if (ordenInterval) {
      clearInterval(ordenInterval);
      ordenInterval = null;
    }
  }
}

// Escucha cambios de visibilidad
document.addEventListener('visibilitychange', handleVisibility);

// Arranca al cargar
handleVisibility();

document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('cancelar_orden')){
        document.getElementById('cancelar_orden').onclick = function() {
            Swal.fire({
                title: '¿Cancelar toda la orden?',
                text: "Se eliminarán todos los productos de la orden.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('controllers/newPos/cancelar_orden.php', {
                        method: 'POST',
                        body: new URLSearchParams({orden_id: ordenId}),
                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                    }).then(()=>location.reload());
                }
            });
        };
    }
    // Buscador
    if(document.getElementById('buscador')){
        document.getElementById('buscador').addEventListener('input', function() {
            let cat = document.querySelector('.pos-category-btn.active')?.getAttribute('data-cat') || '';
            cargarProductos(cat, this.value);
        });
    }
});

// Inicialización
cargarCategorias();
setTimeout(()=> {
    let cat = document.querySelector('.pos-category-btn.active')?.getAttribute('data-cat') || '';
    cargarProductos(cat, '');
}, 350);
cargarOrden();
</script>
</body>
</html>