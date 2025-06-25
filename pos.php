<?php
session_start();
$mesaID = $_GET['mesa'] ?? null;
if (!$mesaID || !isset($_SESSION['MESAS'][$mesaID])) {
    header("Location: mesas.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Punto de Venta - Mesa <?php echo $mesaID; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a2d9b0f91e.js"></script>
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-4 text-center">POS - Mesa <?php echo $mesaID; ?></h2>
        <input type="hidden" id="mesaActual" value="<?php echo $mesaID; ?>">
        <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar producto...">
        <div id="resultadoProductos" class="row mt-3"></div>
        <div class="mt-4">
            <h4>Carrito</h4>
            <table class="table table-bordered" id="tablaCarrito">
                <thead class="thead-light">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <h4 class="text-right">Total: $<span id="totalVenta">0.00</span></h4>
            <form id="formVenta">
                <input type="hidden" name="mesaID" value="<?php echo $mesaID; ?>">
                <select name="metodoPago" id="metodoPago" class="form-control mb-2">
                    <option value="Efectivo">Efectivo</option>
                    <option value="Tarjeta">Tarjeta</option>
                    <option value="Mixto">Mixto</option>
                </select>
                <button type="submit" class="btn btn-success btn-block">Finalizar Venta</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/pos.js"></script>
</body>

</html>