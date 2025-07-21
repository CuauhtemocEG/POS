<?php
/**
 * API Principal del Sistema POS
 * Maneja todas las rutas de la API REST
 */

require_once 'config.php';

// Configurar headers
header('Content-Type: application/json; charset=utf-8');
setCorsHeaders();
handleOptionsRequest();

class ApiController {
    private $pdo;
    private $method;
    private $endpoint;
    private $params;
    
    public function __construct() {
        try {
            $this->pdo = getDbConnection();
            $this->method = $_SERVER['REQUEST_METHOD'];
            
            // Obtener la ruta solicitada
            $request = $_SERVER['REQUEST_URI'];
            $path = parse_url($request, PHP_URL_PATH);
            $path = str_replace('/api/', '', $path);
            $path = str_replace('/api/index.php/', '', $path);
            $path = trim($path, '/');
            
            $pathParts = explode('/', $path);
            $this->endpoint = isset($pathParts[0]) ? $pathParts[0] : '';
            $this->params = array_slice($pathParts, 1);
        } catch (Exception $e) {
            logApiError('Constructor error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
    
    public function processRequest() {
        try {
            switch ($this->endpoint) {
                case '':
                case 'info':
                    return $this->getApiInfo();
                case 'mesas':
                    return $this->handleMesas();
                case 'productos':
                    return $this->handleProductos();
                case 'ordenes':
                    return $this->handleOrdenes();
                case 'categorias':
                    return $this->handleCategorias();
                case 'estadisticas':
                    return $this->handleEstadisticas();
                default:
                    return ApiResponse::error('Endpoint no encontrado', 404);
            }
        } catch (InvalidArgumentException $e) {
            logApiError('Validation error', ['error' => $e->getMessage(), 'endpoint' => $this->endpoint]);
            return ApiResponse::error($e->getMessage(), 400);
        } catch (Exception $e) {
            logApiError('Internal server error', ['error' => $e->getMessage(), 'endpoint' => $this->endpoint]);
            return ApiResponse::error('Error interno del servidor', 500);
        }
    }
    
    private function getApiInfo() {
        return ApiResponse::success([
            'name' => API_NAME,
            'version' => API_VERSION,
            'description' => 'API REST para Sistema de Punto de Venta',
            'timestamp' => date('c'),
            'endpoints' => [
                'GET /api/' => 'Información de la API',
                'GET /api/mesas' => 'Listar todas las mesas',
                'GET /api/mesas/{id}' => 'Obtener mesa específica',
                'POST /api/mesas' => 'Crear nueva mesa',
                'PUT /api/mesas/{id}' => 'Actualizar mesa',
                'DELETE /api/mesas/{id}' => 'Eliminar mesa',
                'GET /api/productos' => 'Listar productos (con paginación y filtros)',
                'GET /api/productos/{id}' => 'Obtener producto específico',
                'POST /api/productos' => 'Crear producto',
                'PUT /api/productos/{id}' => 'Actualizar producto',
                'DELETE /api/productos/{id}' => 'Eliminar producto',
                'GET /api/ordenes' => 'Listar órdenes (con filtros)',
                'GET /api/ordenes/{id}' => 'Obtener orden específica',
                'GET /api/ordenes/{codigo}' => 'Obtener orden por código',
                'POST /api/ordenes' => 'Crear nueva orden',
                'PUT /api/ordenes/{id}' => 'Actualizar orden',
                'GET /api/categorias' => 'Listar categorías',
                'GET /api/estadisticas' => 'Obtener estadísticas del sistema'
            ],
            'query_parameters' => [
                'page' => 'Número de página (para paginación)',
                'limit' => 'Elementos por página (máx. ' . MAX_PAGE_SIZE . ')',
                'search' => 'Búsqueda por texto',
                'categoria' => 'Filtrar por categoría',
                'estado' => 'Filtrar por estado'
            ]
        ]);
    }
    
    private function handleMesas() {
        switch ($this->method) {
            case 'GET':
                if (isset($this->params[0]) && is_numeric($this->params[0])) {
                    return $this->getMesa($this->params[0]);
                }
                return $this->getMesas();
            case 'POST':
                return $this->createMesa();
            case 'PUT':
                if (isset($this->params[0]) && is_numeric($this->params[0])) {
                    return $this->updateMesa($this->params[0]);
                }
                return ApiResponse::error('ID de mesa requerido', 400);
            case 'DELETE':
                if (isset($this->params[0]) && is_numeric($this->params[0])) {
                    return $this->deleteMesa($this->params[0]);
                }
                return ApiResponse::error('ID de mesa requerido', 400);
            default:
                return ApiResponse::error('Método no permitido', 405);
        }
    }
    
    private function handleProductos() {
        switch ($this->method) {
            case 'GET':
                if (isset($this->params[0]) && is_numeric($this->params[0])) {
                    return $this->getProducto($this->params[0]);
                }
                return $this->getProductos();
            case 'POST':
                return $this->createProducto();
            case 'PUT':
                if (isset($this->params[0]) && is_numeric($this->params[0])) {
                    return $this->updateProducto($this->params[0]);
                }
                return ApiResponse::error('ID de producto requerido', 400);
            case 'DELETE':
                if (isset($this->params[0]) && is_numeric($this->params[0])) {
                    return $this->deleteProducto($this->params[0]);
                }
                return ApiResponse::error('ID de producto requerido', 400);
            default:
                return ApiResponse::error('Método no permitido', 405);
        }
    }
    
    private function handleOrdenes() {
        switch ($this->method) {
            case 'GET':
                if (isset($this->params[0])) {
                    if (is_numeric($this->params[0])) {
                        return $this->getOrden($this->params[0]);
                    } else {
                        return $this->getOrdenByCodigo($this->params[0]);
                    }
                }
                return $this->getOrdenes();
            case 'POST':
                return $this->createOrden();
            case 'PUT':
                if (isset($this->params[0]) && is_numeric($this->params[0])) {
                    return $this->updateOrden($this->params[0]);
                }
                return ApiResponse::error('ID de orden requerido', 400);
            default:
                return ApiResponse::error('Método no permitido', 405);
        }
    }
    
    private function handleCategorias() {
        if ($this->method === 'GET') {
            return $this->getCategorias();
        }
        return ApiResponse::error('Método no permitido', 405);
    }
    
    private function handleEstadisticas() {
        if ($this->method === 'GET') {
            return $this->getEstadisticas();
        }
        return ApiResponse::error('Método no permitido', 405);
    }
    
    // Métodos para Mesas
    private function getMesas() {
        $stmt = $this->pdo->query("SELECT * FROM mesas ORDER BY nombre");
        $mesas = $stmt->fetchAll();
        return ApiResponse::success($mesas);
    }
    
    private function getMesa($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM mesas WHERE id = ?");
        $stmt->execute([$id]);
        $mesa = $stmt->fetch();
        
        if (!$mesa) {
            return ApiResponse::error('Mesa no encontrada', 404);
        }
        
        return ApiResponse::success($mesa);
    }
    
    private function createMesa() {
        $data = ApiUtils::getJsonInput();
        
        $nombre = Validator::required($data['nombre'] ?? '', 'nombre');
        $nombre = Validator::string($nombre, 'nombre', 100);
        
        $stmt = $this->pdo->prepare("INSERT INTO mesas (nombre, estado) VALUES (?, 'libre')");
        $success = $stmt->execute([$nombre]);
        
        if ($success) {
            $id = $this->pdo->lastInsertId();
            return ApiResponse::success(['id' => $id], 'Mesa creada exitosamente', 201);
        }
        
        return ApiResponse::error('Error al crear la mesa', 500);
    }
    
    private function updateMesa($id) {
        $data = ApiUtils::getJsonInput();
        
        $fields = [];
        $values = [];
        
        if (isset($data['nombre'])) {
            $nombre = Validator::string($data['nombre'], 'nombre', 100);
            $fields[] = 'nombre = ?';
            $values[] = $nombre;
        }
        
        if (isset($data['estado'])) {
            $estado = Validator::in($data['estado'], 'estado', ['libre', 'abierta', 'cerrada']);
            $fields[] = 'estado = ?';
            $values[] = $estado;
        }
        
        if (empty($fields)) {
            return ApiResponse::error('No hay campos para actualizar', 400);
        }
        
        $values[] = $id;
        $sql = "UPDATE mesas SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute($values);
        
        if ($success && $stmt->rowCount() > 0) {
            return ApiResponse::success(null, 'Mesa actualizada exitosamente');
        }
        
        return ApiResponse::error('Mesa no encontrada o no se pudo actualizar', 404);
    }
    
    private function deleteMesa($id) {
        // Verificar que no tenga órdenes activas
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM ordenes WHERE mesa_id = ? AND estado = 'abierta'");
        $stmt->execute([$id]);
        $activeOrders = $stmt->fetchColumn();
        
        if ($activeOrders > 0) {
            return ApiResponse::error('No se puede eliminar una mesa con órdenes activas', 400);
        }
        
        $stmt = $this->pdo->prepare("DELETE FROM mesas WHERE id = ?");
        $success = $stmt->execute([$id]);
        
        if ($success && $stmt->rowCount() > 0) {
            return ApiResponse::success(null, 'Mesa eliminada exitosamente');
        }
        
        return ApiResponse::error('Mesa no encontrada', 404);
    }
    
    // Métodos para Productos
    private function getProductos() {
        [$page, $limit, $offset] = ApiUtils::getPaginationParams();
        
        $search = $_GET['search'] ?? '';
        $categoria = $_GET['categoria'] ?? '';
        
        $where = "1";
        $params = [];
        
        if ($search) {
            $where .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($categoria) {
            $where .= " AND categoria = ?";
            $params[] = $categoria;
        }
        
        // Obtener productos
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE $where ORDER BY nombre LIMIT $limit OFFSET $offset");
        $stmt->execute($params);
        $productos = $stmt->fetchAll();
        
        // Contar total para paginación
        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM productos WHERE $where");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        
        return ApiResponse::paginated($productos, $page, $limit, $total);
    }
    
    private function getProducto($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();
        
        if (!$producto) {
            return ApiResponse::error('Producto no encontrado', 404);
        }
        
        return ApiResponse::success($producto);
    }
    
    private function createProducto() {
        $data = ApiUtils::getJsonInput();
        
        // Validaciones
        $nombre = Validator::required($data['nombre'] ?? '', 'nombre');
        $nombre = Validator::string($nombre, 'nombre', 255);
        
        $precio = Validator::required($data['precio'] ?? '', 'precio');
        $precio = Validator::numeric($precio, 'precio', 0);
        
        $categoria = Validator::required($data['categoria'] ?? '', 'categoria');
        $categoria = Validator::string($categoria, 'categoria', 100);
        
        $type = Validator::required($data['type'] ?? '', 'type');
        $type = Validator::string($type, 'type', 100);
        
        $descripcion = isset($data['descripcion']) ? Validator::string($data['descripcion'], 'descripcion', 500) : '';
        $imagen = $data['imagen'] ?? null;
        
        $stmt = $this->pdo->prepare("INSERT INTO productos (nombre, precio, descripcion, categoria, type, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$nombre, $precio, $descripcion, $categoria, $type, $imagen]);
        
        if ($success) {
            $id = $this->pdo->lastInsertId();
            return ApiResponse::success(['id' => $id], 'Producto creado exitosamente', 201);
        }
        
        return ApiResponse::error('Error al crear el producto', 500);
    }
    
    private function updateProducto($id) {
        $data = ApiUtils::getJsonInput();
        
        $fields = [];
        $values = [];
        
        if (isset($data['nombre'])) {
            $nombre = Validator::string($data['nombre'], 'nombre', 255);
            $fields[] = "nombre = ?";
            $values[] = $nombre;
        }
        
        if (isset($data['precio'])) {
            $precio = Validator::numeric($data['precio'], 'precio', 0);
            $fields[] = "precio = ?";
            $values[] = $precio;
        }
        
        if (isset($data['descripcion'])) {
            $descripcion = Validator::string($data['descripcion'], 'descripcion', 500);
            $fields[] = "descripcion = ?";
            $values[] = $descripcion;
        }
        
        if (isset($data['categoria'])) {
            $categoria = Validator::string($data['categoria'], 'categoria', 100);
            $fields[] = "categoria = ?";
            $values[] = $categoria;
        }
        
        if (isset($data['type'])) {
            $type = Validator::string($data['type'], 'type', 100);
            $fields[] = "type = ?";
            $values[] = $type;
        }
        
        if (isset($data['imagen'])) {
            $fields[] = "imagen = ?";
            $values[] = $data['imagen'];
        }
        
        if (empty($fields)) {
            return ApiResponse::error('No hay campos para actualizar', 400);
        }
        
        $values[] = $id;
        $sql = "UPDATE productos SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute($values);
        
        if ($success && $stmt->rowCount() > 0) {
            return ApiResponse::success(null, 'Producto actualizado exitosamente');
        }
        
        return ApiResponse::error('Producto no encontrado o no se pudo actualizar', 404);
    }
    
    private function deleteProducto($id) {
        // Verificar que no esté en órdenes activas
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM orden_productos op 
            JOIN ordenes o ON op.orden_id = o.id 
            WHERE op.producto_id = ? AND o.estado = 'abierta' AND op.cancelado = 0
        ");
        $stmt->execute([$id]);
        $activeOrders = $stmt->fetchColumn();
        
        if ($activeOrders > 0) {
            return ApiResponse::error('No se puede eliminar un producto que está en órdenes activas', 400);
        }
        
        // Obtener información de la imagen para eliminarla
        $stmt = $this->pdo->prepare("SELECT imagen FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $imagen = $stmt->fetchColumn();
        
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id = ?");
        $success = $stmt->execute([$id]);
        
        if ($success && $stmt->rowCount() > 0) {
            // Eliminar imagen si existe
            if ($imagen && file_exists(UPLOAD_DIR . $imagen)) {
                unlink(UPLOAD_DIR . $imagen);
            }
            
            return ApiResponse::success(null, 'Producto eliminado exitosamente');
        }
        
        return ApiResponse::error('Producto no encontrado', 404);
    }
    
    // Métodos para Órdenes
    private function getOrdenes() {
        [$page, $limit, $offset] = ApiUtils::getPaginationParams();
        
        $estado = $_GET['estado'] ?? '';
        $mesa = $_GET['mesa'] ?? '';
        $fecha_desde = $_GET['fecha_desde'] ?? '';
        $fecha_hasta = $_GET['fecha_hasta'] ?? '';
        
        $where = "1";
        $params = [];
        
        if ($estado) {
            $where .= " AND o.estado = ?";
            $params[] = $estado;
        }
        
        if ($mesa) {
            $where .= " AND m.nombre LIKE ?";
            $params[] = "%$mesa%";
        }
        
        if ($fecha_desde) {
            $where .= " AND DATE(o.creada_en) >= ?";
            $params[] = $fecha_desde;
        }
        
        if ($fecha_hasta) {
            $where .= " AND DATE(o.creada_en) <= ?";
            $params[] = $fecha_hasta;
        }
        
        $sql = "SELECT o.id, o.codigo, o.estado, o.creada_en, m.nombre AS mesa 
                FROM ordenes o 
                JOIN mesas m ON m.id = o.mesa_id 
                WHERE $where 
                ORDER BY o.creada_en DESC 
                LIMIT $limit OFFSET $offset";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $ordenes = $stmt->fetchAll();
        
        // Contar total
        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM ordenes o JOIN mesas m ON m.id = o.mesa_id WHERE $where");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        
        return ApiResponse::paginated($ordenes, $page, $limit, $total);
    }
    
    private function getOrden($id) {
        $stmt = $this->pdo->prepare("SELECT o.id, o.codigo, o.estado, o.creada_en, m.nombre AS mesa
            FROM ordenes o
            JOIN mesas m ON m.id = o.mesa_id
            WHERE o.id = ?");
        $stmt->execute([$id]);
        $orden = $stmt->fetch();
        
        if (!$orden) {
            return ApiResponse::error('Orden no encontrada', 404);
        }
        
        // Obtener productos de la orden
        $productos = $this->pdo->prepare("SELECT p.id, p.nombre, op.cantidad, op.preparado, op.cancelado, p.precio
            FROM orden_productos op
            JOIN productos p ON op.producto_id = p.id
            WHERE op.orden_id = ?");
        $productos->execute([$id]);
        $orden['productos'] = $productos->fetchAll();
        
        // Calcular totales
        $subtotal = 0;
        foreach ($orden['productos'] as $prod) {
            if (!$prod['cancelado']) {
                $subtotal += $prod['precio'] * $prod['cantidad'];
            }
        }
        $orden['subtotal'] = $subtotal;
        $orden['descuento'] = 0;
        $orden['impuestos'] = 0;
        $orden['total'] = $subtotal;
        
        return ApiResponse::success($orden);
    }
    
    private function getOrdenByCodigo($codigo) {
        $stmt = $this->pdo->prepare("SELECT o.id FROM ordenes o WHERE o.codigo = ?");
        $stmt->execute([$codigo]);
        $orden = $stmt->fetch();
        
        if (!$orden) {
            return ApiResponse::error('Orden no encontrada', 404);
        }
        
        return $this->getOrden($orden['id']);
    }
    
    private function createOrden() {
        $data = ApiUtils::getJsonInput();
        
        $mesa_id = Validator::required($data['mesa_id'] ?? '', 'mesa_id');
        $mesa_id = Validator::numeric($mesa_id, 'mesa_id', 1);
        
        // Verificar que la mesa existe
        $stmt = $this->pdo->prepare("SELECT id FROM mesas WHERE id = ?");
        $stmt->execute([$mesa_id]);
        if (!$stmt->fetch()) {
            return ApiResponse::error('Mesa no encontrada', 404);
        }
        
        $codigo = ApiUtils::generateCode('ORD');
        $stmt = $this->pdo->prepare("INSERT INTO ordenes (mesa_id, codigo, estado) VALUES (?, ?, 'abierta')");
        $success = $stmt->execute([$mesa_id, $codigo]);
        
        if ($success) {
            $id = $this->pdo->lastInsertId();
            $this->pdo->prepare("UPDATE mesas SET estado='abierta' WHERE id=?")->execute([$mesa_id]);
            return ApiResponse::success(['id' => $id, 'codigo' => $codigo], 'Orden creada exitosamente', 201);
        }
        
        return ApiResponse::error('Error al crear la orden', 500);
    }
    
    private function updateOrden($id) {
        $data = ApiUtils::getJsonInput();
        
        if (isset($data['estado'])) {
            $estado = Validator::in($data['estado'], 'estado', ['abierta', 'pagada', 'cancelada']);
            $stmt = $this->pdo->prepare("UPDATE ordenes SET estado = ? WHERE id = ?");
            $success = $stmt->execute([$estado, $id]);
            
            if ($success && $stmt->rowCount() > 0) {
                // Si se cancela o paga la orden, actualizar estado de la mesa
                if (in_array($estado, ['pagada', 'cancelada'])) {
                    $stmt = $this->pdo->prepare("SELECT mesa_id FROM ordenes WHERE id = ?");
                    $stmt->execute([$id]);
                    $mesa_id = $stmt->fetchColumn();
                    
                    if ($mesa_id) {
                        $this->pdo->prepare("UPDATE mesas SET estado='libre' WHERE id=?")->execute([$mesa_id]);
                    }
                }
                
                return ApiResponse::success(null, 'Orden actualizada exitosamente');
            }
        }
        
        return ApiResponse::error('Orden no encontrada o no se pudo actualizar', 404);
    }
    
    // Métodos para Categorías
    private function getCategorias() {
        $stmt = $this->pdo->query("SELECT DISTINCT categoria AS nombre, COUNT(*) as productos FROM productos WHERE categoria IS NOT NULL AND categoria != '' GROUP BY categoria ORDER BY categoria");
        $categorias = $stmt->fetchAll();
        return ApiResponse::success($categorias);
    }
    
    // Métodos para Estadísticas
    private function getEstadisticas() {
        $stats = [];
        
        try {
            // Total de mesas por estado
            $stmt = $this->pdo->query("SELECT COUNT(*) as total, estado FROM mesas GROUP BY estado");
            $mesasStats = $stmt->fetchAll();
            $stats['mesas'] = [];
            foreach ($mesasStats as $stat) {
                $stats['mesas'][$stat['estado']] = (int)$stat['total'];
            }
            
            // Total de productos
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM productos");
            $stats['productos_total'] = (int)$stmt->fetchColumn();
            
            // Productos por categoría
            $stmt = $this->pdo->query("SELECT categoria, COUNT(*) as total FROM productos WHERE categoria IS NOT NULL GROUP BY categoria");
            $stats['productos_por_categoria'] = $stmt->fetchAll();
            
            // Órdenes por estado
            $stmt = $this->pdo->query("SELECT COUNT(*) as total, estado FROM ordenes GROUP BY estado");
            $ordenesStats = $stmt->fetchAll();
            $stats['ordenes'] = [];
            foreach ($ordenesStats as $stat) {
                $stats['ordenes'][$stat['estado']] = (int)$stat['total'];
            }
            
            // Órdenes de hoy
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM ordenes WHERE DATE(creada_en) = CURDATE()");
            $stats['ordenes_hoy'] = (int)$stmt->fetchColumn();
            
            // Ventas de hoy
            $stmt = $this->pdo->query("
                SELECT COALESCE(SUM(p.precio * op.cantidad), 0) as total 
                FROM orden_productos op 
                JOIN productos p ON op.producto_id = p.id 
                JOIN ordenes o ON op.orden_id = o.id 
                WHERE DATE(o.creada_en) = CURDATE() 
                  AND o.estado = 'pagada'
                  AND op.cancelado = 0
            ");
            $stats['ventas_hoy'] = (float)$stmt->fetchColumn();
            
            // Productos más vendidos (últimos 30 días)
            $stmt = $this->pdo->query("
                SELECT p.nombre, SUM(op.cantidad) as vendidos, SUM(p.precio * op.cantidad) as ingresos
                FROM orden_productos op 
                JOIN productos p ON op.producto_id = p.id 
                JOIN ordenes o ON op.orden_id = o.id 
                WHERE o.creada_en >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                  AND op.cancelado = 0
                  AND o.estado = 'pagada'
                GROUP BY p.id, p.nombre 
                ORDER BY vendidos DESC 
                LIMIT 10
            ");
            $stats['productos_populares'] = $stmt->fetchAll();
            
            // Ventas por día (últimos 7 días)
            $stmt = $this->pdo->query("
                SELECT DATE(o.creada_en) as fecha, 
                       COUNT(DISTINCT o.id) as ordenes,
                       COALESCE(SUM(p.precio * op.cantidad), 0) as ventas
                FROM ordenes o
                LEFT JOIN orden_productos op ON o.id = op.orden_id AND op.cancelado = 0
                LEFT JOIN productos p ON op.producto_id = p.id
                WHERE o.creada_en >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  AND o.estado = 'pagada'
                GROUP BY DATE(o.creada_en)
                ORDER BY fecha DESC
            ");
            $stats['ventas_ultimos_7_dias'] = $stmt->fetchAll();
            
        } catch (Exception $e) {
            logApiError('Error getting statistics', ['error' => $e->getMessage()]);
            return ApiResponse::error('Error al obtener estadísticas', 500);
        }
        
        return ApiResponse::success($stats);
    }
}

// Ejecutar el controlador
try {
    $api = new ApiController();
    echo $api->processRequest();
} catch (Exception $e) {
    logApiError('Fatal error', ['error' => $e->getMessage()]);
    echo ApiResponse::error('Error fatal del servidor', 500);
}
?>