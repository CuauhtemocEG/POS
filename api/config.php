<?php
/**
 * Configuración de la API
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'kallijag_pos_stage');
define('DB_USER', 'kallijag_stage');
define('DB_PASS', 'uNtiL.horSe@5');

// Modo demo (para pruebas sin base de datos)
define('DEMO_MODE', true);

// Configuración de la API
define('API_VERSION', '1.0.0');
define('API_NAME', 'POS API');

// Configuración de CORS
define('CORS_ORIGINS', '*');
define('CORS_METHODS', 'GET, POST, PUT, DELETE, OPTIONS');
define('CORS_HEADERS', 'Content-Type, Authorization, X-Requested-With');

// Configuración de paginación
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);

// Configuración de uploads
define('UPLOAD_DIR', '../assets/img/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Timezone
date_default_timezone_set('America/Mexico_City');

/**
 * Función mejorada de conexión a la base de datos
 */
function getDbConnection() {
    if (defined('DEMO_MODE') && DEMO_MODE) {
        // Retornar un objeto mock para modo demo
        return new DemoDatabase();
    }
    
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Error de conexión a la base de datos");
    }
}

/**
 * Clase mock para modo demo
 */
class DemoDatabase {
    private $data = [
        'mesas' => [
            ['id' => 1, 'nombre' => 'Mesa 1', 'estado' => 'libre'],
            ['id' => 2, 'nombre' => 'Mesa 2', 'estado' => 'abierta'],
            ['id' => 3, 'nombre' => 'Mesa 3', 'estado' => 'libre'],
        ],
        'productos' => [
            ['id' => 1, 'nombre' => 'Pizza Margherita', 'precio' => 150.00, 'descripcion' => 'Pizza clásica', 'categoria' => 'Pizzas', 'type' => 'comida', 'imagen' => null],
            ['id' => 2, 'nombre' => 'Coca Cola', 'precio' => 25.00, 'descripcion' => 'Refresco', 'categoria' => 'Bebidas', 'type' => 'bebida', 'imagen' => null],
            ['id' => 3, 'nombre' => 'Hamburguesa Clásica', 'precio' => 120.00, 'descripcion' => 'Hamburguesa con queso', 'categoria' => 'Hamburguesas', 'type' => 'comida', 'imagen' => null],
        ],
        'ordenes' => [
            ['id' => 1, 'codigo' => 'ORD-20240121-ABC123', 'estado' => 'abierta', 'creada_en' => '2024-01-21 10:30:00', 'mesa_id' => 2, 'mesa' => 'Mesa 2'],
            ['id' => 2, 'codigo' => 'ORD-20240121-DEF456', 'estado' => 'pagada', 'creada_en' => '2024-01-21 09:15:00', 'mesa_id' => 1, 'mesa' => 'Mesa 1'],
        ]
    ];
    
    private $lastSql = '';
    
    public function query($sql) {
        $this->lastSql = strtolower($sql);
        $stmt = new DemoStatement($this->data, $this->lastSql);
        $stmt->execute();
        return $stmt;
    }
    
    public function prepare($sql) {
        $this->lastSql = strtolower($sql);
        return new DemoStatement($this->data, $this->lastSql);
    }
    
    public function lastInsertId() {
        return rand(10, 99);
    }
}

class DemoStatement {
    private $data;
    private $sql;
    private $result = [];
    private $executed = false;
    
    public function __construct($data, $sql = '') {
        $this->data = $data;
        $this->sql = strtolower($sql);
    }
    
    public function execute($params = []) {
        $this->executed = true;
        
        // Determinar qué datos retornar basado en la consulta SQL
        if (strpos($this->sql, 'distinct categoria') !== false || (strpos($this->sql, 'categoria') !== false && strpos($this->sql, 'group by categoria') !== false)) {
            // Para consultas de categorías
            $this->result = [
                ['nombre' => 'Pizzas', 'productos' => 5],
                ['nombre' => 'Bebidas', 'productos' => 8],
                ['nombre' => 'Hamburguesas', 'productos' => 3]
            ];
        } elseif (strpos($this->sql, 'from ordenes') !== false || strpos($this->sql, 'ordenes o') !== false) {
            $this->result = $this->data['ordenes'];
        } elseif (strpos($this->sql, 'from productos') !== false || strpos($this->sql, 'productos') !== false) {
            $this->result = $this->data['productos'];
        } elseif (strpos($this->sql, 'from mesas') !== false || strpos($this->sql, 'mesas') !== false) {
            $this->result = $this->data['mesas'];
        } else {
            $this->result = [];
        }
        
        // Si es una consulta con WHERE y parámetros, filtrar el primer elemento
        if (!empty($params) && strpos($this->sql, 'where') !== false) {
            $this->result = !empty($this->result) ? [$this->result[0]] : [];
        }
        
        return true;
    }
    
    public function fetchAll($mode = null) {
        return $this->result;
    }
    
    public function fetch($mode = null) {
        return !empty($this->result) ? $this->result[0] : false;
    }
    
    public function fetchColumn() {
        if (strpos($this->sql, 'count') !== false) {
            return count($this->result);
        }
        return !empty($this->result) ? array_values($this->result[0])[0] : 0;
    }
    
    public function rowCount() {
        return $this->executed ? 1 : 0;
    }
}

/**
 * Clase base para respuestas de la API
 */
class ApiResponse {
    public static function success($data = null, $message = null, $code = 200) {
        http_response_code($code);
        $response = [
            'success' => true,
            'timestamp' => date('c')
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public static function error($message, $code = 400, $details = null) {
        http_response_code($code);
        $response = [
            'success' => false,
            'error' => $message,
            'timestamp' => date('c')
        ];
        
        if ($details !== null) {
            $response['details'] = $details;
        }
        
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
    
    public static function paginated($data, $page, $limit, $total) {
        return self::success([
            'items' => $data,
            'pagination' => [
                'page' => (int)$page,
                'limit' => (int)$limit,
                'total' => (int)$total,
                'pages' => ceil($total / $limit),
                'has_next' => ($page * $limit) < $total,
                'has_prev' => $page > 1
            ]
        ]);
    }
}

/**
 * Clase para validación de datos
 */
class Validator {
    public static function required($value, $field) {
        if (empty($value) && $value !== '0' && $value !== 0) {
            throw new InvalidArgumentException("El campo '$field' es requerido");
        }
        return $value;
    }
    
    public static function numeric($value, $field, $min = null, $max = null) {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException("El campo '$field' debe ser numérico");
        }
        
        $value = (float)$value;
        
        if ($min !== null && $value < $min) {
            throw new InvalidArgumentException("El campo '$field' debe ser mayor o igual a $min");
        }
        
        if ($max !== null && $value > $max) {
            throw new InvalidArgumentException("El campo '$field' debe ser menor o igual a $max");
        }
        
        return $value;
    }
    
    public static function string($value, $field, $maxLength = null) {
        if (!is_string($value)) {
            throw new InvalidArgumentException("El campo '$field' debe ser una cadena de texto");
        }
        
        $value = trim($value);
        
        if ($maxLength !== null && strlen($value) > $maxLength) {
            throw new InvalidArgumentException("El campo '$field' no puede tener más de $maxLength caracteres");
        }
        
        return $value;
    }
    
    public static function email($value, $field) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("El campo '$field' debe ser un email válido");
        }
        
        return $value;
    }
    
    public static function in($value, $field, $options) {
        if (!in_array($value, $options)) {
            $optionsStr = implode(', ', $options);
            throw new InvalidArgumentException("El campo '$field' debe ser uno de: $optionsStr");
        }
        
        return $value;
    }
}

/**
 * Utilidades para la API
 */
class ApiUtils {
    public static function getJsonInput() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException('JSON inválido: ' . json_last_error_msg());
        }
        
        return $data ?: [];
    }
    
    public static function getPaginationParams() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(MAX_PAGE_SIZE, max(1, (int)($_GET['limit'] ?? DEFAULT_PAGE_SIZE)));
        $offset = ($page - 1) * $limit;
        
        return [$page, $limit, $offset];
    }
    
    public static function sanitizeFilename($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return substr($filename, 0, 255);
    }
    
    public static function generateCode($prefix = 'CODE') {
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}

/**
 * Configurar headers CORS
 */
function setCorsHeaders() {
    header('Access-Control-Allow-Origin: ' . CORS_ORIGINS);
    header('Access-Control-Allow-Methods: ' . CORS_METHODS);
    header('Access-Control-Allow-Headers: ' . CORS_HEADERS);
    header('Access-Control-Max-Age: 3600');
}

/**
 * Manejar solicitudes OPTIONS para CORS
 */
function handleOptionsRequest() {
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        setCorsHeaders();
        http_response_code(200);
        exit;
    }
}

/**
 * Log de errores para la API
 */
function logApiError($message, $context = []) {
    $logEntry = [
        'timestamp' => date('c'),
        'message' => $message,
        'context' => $context,
        'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
        'method' => $_SERVER['REQUEST_METHOD'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    error_log('API_ERROR: ' . json_encode($logEntry));
}
?>