# API del Sistema POS - Documentación

## Información General

- **URL Base**: `http://tu-dominio.com/api/`
- **Formato de respuesta**: JSON
- **Métodos soportados**: GET, POST, PUT, DELETE
- **Codificación**: UTF-8

## Estructura de Respuestas

### Respuesta Exitosa
```json
{
  "success": true,
  "data": {...},
  "message": "Mensaje opcional",
  "timestamp": "2024-01-01T12:00:00+00:00"
}
```

### Respuesta con Error
```json
{
  "success": false,
  "error": "Descripción del error",
  "details": "Detalles adicionales (opcional)",
  "timestamp": "2024-01-01T12:00:00+00:00"
}
```

### Respuesta Paginada
```json
{
  "success": true,
  "data": {
    "items": [...],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 100,
      "pages": 5,
      "has_next": true,
      "has_prev": false
    }
  },
  "timestamp": "2024-01-01T12:00:00+00:00"
}
```

## Endpoints

### 1. Información de la API
- **GET** `/api/` o `/api/info`
- **Descripción**: Obtiene información general de la API y lista de endpoints disponibles
- **Parámetros**: Ninguno

### 2. Gestión de Mesas

#### Listar Mesas
- **GET** `/api/mesas`
- **Descripción**: Obtiene todas las mesas del sistema
- **Respuesta**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nombre": "Mesa 1",
      "estado": "libre"
    }
  ]
}
```

#### Obtener Mesa Específica
- **GET** `/api/mesas/{id}`
- **Descripción**: Obtiene los detalles de una mesa específica
- **Parámetros**:
  - `id` (required): ID de la mesa

#### Crear Mesa
- **POST** `/api/mesas`
- **Descripción**: Crea una nueva mesa
- **Body**:
```json
{
  "nombre": "Mesa 5"
}
```

#### Actualizar Mesa
- **PUT** `/api/mesas/{id}`
- **Descripción**: Actualiza una mesa existente
- **Parámetros**:
  - `id` (required): ID de la mesa
- **Body**:
```json
{
  "nombre": "Mesa 5 - VIP",
  "estado": "abierta"
}
```

#### Eliminar Mesa
- **DELETE** `/api/mesas/{id}`
- **Descripción**: Elimina una mesa (solo si no tiene órdenes activas)
- **Parámetros**:
  - `id` (required): ID de la mesa

### 3. Gestión de Productos

#### Listar Productos
- **GET** `/api/productos`
- **Descripción**: Obtiene lista de productos con paginación y filtros
- **Parámetros de consulta**:
  - `page` (optional): Número de página (default: 1)
  - `limit` (optional): Elementos por página (default: 20, max: 100)
  - `search` (optional): Búsqueda por nombre o descripción
  - `categoria` (optional): Filtrar por categoría

**Ejemplo**: `/api/productos?page=1&limit=10&search=pizza&categoria=comida`

#### Obtener Producto Específico
- **GET** `/api/productos/{id}`
- **Descripción**: Obtiene los detalles de un producto específico
- **Parámetros**:
  - `id` (required): ID del producto

#### Crear Producto
- **POST** `/api/productos`
- **Descripción**: Crea un nuevo producto
- **Body**:
```json
{
  "nombre": "Pizza Margherita",
  "precio": 150.00,
  "descripcion": "Pizza clásica con tomate y queso",
  "categoria": "Pizzas",
  "type": "comida",
  "imagen": "pizza_margherita.jpg"
}
```

#### Actualizar Producto
- **PUT** `/api/productos/{id}`
- **Descripción**: Actualiza un producto existente
- **Parámetros**:
  - `id` (required): ID del producto
- **Body**: Campos a actualizar (mismo formato que crear)

#### Eliminar Producto
- **DELETE** `/api/productos/{id}`
- **Descripción**: Elimina un producto (solo si no está en órdenes activas)
- **Parámetros**:
  - `id` (required): ID del producto

### 4. Gestión de Órdenes

#### Listar Órdenes
- **GET** `/api/ordenes`
- **Descripción**: Obtiene lista de órdenes con filtros y paginación
- **Parámetros de consulta**:
  - `page` (optional): Número de página
  - `limit` (optional): Elementos por página
  - `estado` (optional): Filtrar por estado (abierta, pagada, cancelada)
  - `mesa` (optional): Filtrar por nombre de mesa
  - `fecha_desde` (optional): Fecha desde (YYYY-MM-DD)
  - `fecha_hasta` (optional): Fecha hasta (YYYY-MM-DD)

#### Obtener Orden Específica
- **GET** `/api/ordenes/{id}`
- **Descripción**: Obtiene los detalles completos de una orden
- **Parámetros**:
  - `id` (required): ID de la orden o código de la orden

**Respuesta**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "codigo": "ORD-20240101-ABC123",
    "estado": "abierta",
    "creada_en": "2024-01-01 12:00:00",
    "mesa": "Mesa 1",
    "productos": [
      {
        "id": 1,
        "nombre": "Pizza Margherita",
        "cantidad": 2,
        "precio": 150.00,
        "preparado": 0,
        "cancelado": 0
      }
    ],
    "subtotal": 300.00,
    "descuento": 0,
    "impuestos": 0,
    "total": 300.00
  }
}
```

#### Crear Orden
- **POST** `/api/ordenes`
- **Descripción**: Crea una nueva orden para una mesa
- **Body**:
```json
{
  "mesa_id": 1
}
```

#### Actualizar Orden
- **PUT** `/api/ordenes/{id}`
- **Descripción**: Actualiza el estado de una orden
- **Parámetros**:
  - `id` (required): ID de la orden
- **Body**:
```json
{
  "estado": "pagada"
}
```

### 5. Categorías

#### Listar Categorías
- **GET** `/api/categorias`
- **Descripción**: Obtiene todas las categorías de productos con conteo
- **Respuesta**:
```json
{
  "success": true,
  "data": [
    {
      "nombre": "Pizzas",
      "productos": 15
    },
    {
      "nombre": "Bebidas",
      "productos": 8
    }
  ]
}
```

### 6. Estadísticas

#### Obtener Estadísticas
- **GET** `/api/estadisticas`
- **Descripción**: Obtiene estadísticas generales del sistema
- **Respuesta**:
```json
{
  "success": true,
  "data": {
    "mesas": {
      "libre": 10,
      "abierta": 3,
      "cerrada": 1
    },
    "productos_total": 45,
    "productos_por_categoria": [
      {
        "categoria": "Pizzas",
        "total": 15
      }
    ],
    "ordenes": {
      "abierta": 5,
      "pagada": 150,
      "cancelada": 3
    },
    "ordenes_hoy": 12,
    "ventas_hoy": 2450.00,
    "productos_populares": [
      {
        "nombre": "Pizza Margherita",
        "vendidos": 45,
        "ingresos": 6750.00
      }
    ],
    "ventas_ultimos_7_dias": [
      {
        "fecha": "2024-01-01",
        "ordenes": 12,
        "ventas": 2450.00
      }
    ]
  }
}
```

## Códigos de Estado HTTP

- **200 OK**: Solicitud exitosa
- **201 Created**: Recurso creado exitosamente
- **400 Bad Request**: Error en los datos enviados
- **404 Not Found**: Recurso no encontrado
- **405 Method Not Allowed**: Método HTTP no permitido
- **500 Internal Server Error**: Error interno del servidor

## Validaciones

### Campos Requeridos

#### Mesas
- `nombre`: String, máximo 100 caracteres

#### Productos
- `nombre`: String, máximo 255 caracteres
- `precio`: Número, mayor a 0
- `categoria`: String, máximo 100 caracteres
- `type`: String, máximo 100 caracteres

#### Órdenes
- `mesa_id`: Número entero, debe existir en la tabla mesas

### Estados Válidos

#### Mesas
- `libre`: Mesa disponible
- `abierta`: Mesa con orden activa
- `cerrada`: Mesa temporalmente cerrada

#### Órdenes
- `abierta`: Orden en proceso
- `pagada`: Orden pagada y completada
- `cancelada`: Orden cancelada

## Ejemplos de Uso

### Crear una orden completa

1. **Crear orden**:
```bash
curl -X POST http://tu-dominio.com/api/ordenes \
  -H "Content-Type: application/json" \
  -d '{"mesa_id": 1}'
```

2. **Obtener detalles**:
```bash
curl http://tu-dominio.com/api/ordenes/1
```

3. **Actualizar estado**:
```bash
curl -X PUT http://tu-dominio.com/api/ordenes/1 \
  -H "Content-Type: application/json" \
  -d '{"estado": "pagada"}'
```

### Buscar productos

```bash
curl "http://tu-dominio.com/api/productos?search=pizza&categoria=comida&page=1&limit=5"
```

### Obtener estadísticas

```bash
curl http://tu-dominio.com/api/estadisticas
```

## Consideraciones de Seguridad

1. La API incluye headers de seguridad básicos
2. Se validan todos los inputs para prevenir inyección SQL
3. Se utilizan prepared statements para todas las consultas
4. Los errores se registran pero no se exponen detalles internos

## Limitaciones

- Paginación máxima: 100 elementos por página
- Los archivos de imagen deben manejarse por separado
- No incluye autenticación (a implementar según necesidades)

## Compatibilidad

- PHP 7.4+
- MySQL 5.7+
- Apache con mod_rewrite habilitado (opcional para URLs limpias)