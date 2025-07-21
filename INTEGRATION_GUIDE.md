# Guía de Integración de la Nueva API

## Resumen

Se ha implementado una API REST completa para el sistema POS que permite integración moderna con JavaScript y otros sistemas externos. Esta guía te ayudará a migrar y mejorar las funcionalidades existentes.

## Archivos Creados

- `/api/index.php` - Controlador principal de la API
- `/api/config.php` - Configuración y utilidades de la API
- `/api/.htaccess` - Configuración de URLs limpias
- `/api/README.md` - Documentación completa de la API
- `/js/pos-api-client.js` - Biblioteca JavaScript para integración
- `/api_demo.html` - Página de demostración

## Cambios Mínimos Requeridos

### 1. Configuración de Base de Datos

En `/api/config.php`, cambia el modo demo a false cuando tengas la base de datos configurada:

```php
// Cambiar de true a false para usar base de datos real
define('DEMO_MODE', false);
```

### 2. Actualizar Referencias de JavaScript

Incluye la nueva biblioteca de API en tus vistas:

```html
<!-- En views/header.php, después de las otras librerías -->
<script src="/js/pos-api-client.js"></script>
```

### 3. Migrar Llamadas AJAX Existentes

#### Antes (ejemplo en el sistema actual):
```javascript
$.ajax({
    url: 'controllers/buscar_productos.php',
    method: 'GET',
    data: { q: searchTerm },
    success: function(data) {
        // procesar datos
    }
});
```

#### Después (con la nueva API):
```javascript
// Usando la biblioteca de API
const productos = await posApi.getProductos({ search: searchTerm });

// O usando fetch directamente
fetch('/api/productos?search=' + searchTerm)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // procesar data.data
        }
    });
```

## Ejemplos de Integración Específicos

### 1. Búsqueda de Productos (views/mesa.php)

Actualiza el JavaScript de búsqueda de productos:

```javascript
// Función mejorada para buscar productos
async function buscarProductos(termino, categoria = '') {
    try {
        const response = await posApi.getProductos({
            search: termino,
            categoria: categoria,
            limit: 20
        });
        
        const productos = response.items;
        mostrarProductosEnUI(productos);
        
        // Actualizar paginación si es necesario
        actualizarPaginacion(response.pagination);
        
    } catch (error) {
        console.error('Error buscando productos:', error);
        mostrarError('Error al buscar productos');
    }
}

function mostrarProductosEnUI(productos) {
    const container = document.getElementById('productos-container');
    container.innerHTML = '';
    
    productos.forEach(producto => {
        const html = `
            <div class="producto-card" onclick="agregarProducto(${producto.id})">
                <h4>${producto.nombre}</h4>
                <p class="precio">$${producto.precio}</p>
                <p class="descripcion">${producto.descripcion}</p>
            </div>
        `;
        container.innerHTML += html;
    });
}
```

### 2. Gestión de Órdenes

```javascript
// Crear nueva orden
async function crearNuevaOrden(mesaId) {
    try {
        const response = await posApi.crearOrden({ mesa_id: mesaId });
        
        if (response.success) {
            // Redirigir a la vista de orden
            window.location.href = `index.php?page=mesa&id=${mesaId}&orden=${response.data.id}`;
        }
    } catch (error) {
        alert('Error al crear la orden: ' + error.message);
    }
}

// Obtener detalles de orden
async function cargarOrden(ordenId) {
    try {
        const orden = await posApi.getOrden(ordenId);
        
        if (orden) {
            document.getElementById('orden-codigo').textContent = orden.codigo;
            document.getElementById('orden-mesa').textContent = orden.mesa;
            document.getElementById('orden-total').textContent = `$${orden.total}`;
            
            // Cargar productos de la orden
            cargarProductosOrden(orden.productos);
        }
    } catch (error) {
        console.error('Error cargando orden:', error);
    }
}
```

### 3. Actualizar Estado de Mesas

```javascript
// Función para actualizar estado de mesa
async function actualizarEstadoMesa(mesaId, nuevoEstado) {
    try {
        const response = await posApi.actualizarMesa(mesaId, { 
            estado: nuevoEstado 
        });
        
        if (response.success) {
            // Actualizar UI
            const mesaElement = document.querySelector(`[data-mesa-id="${mesaId}"]`);
            mesaElement.className = `mesa mesa-${nuevoEstado}`;
            
            posApi.showNotification('Mesa actualizada correctamente', 'success');
        }
    } catch (error) {
        posApi.handleError(error, 'Actualización de mesa');
    }
}
```

### 4. Dashboard de Estadísticas

```javascript
// Cargar estadísticas en el dashboard
async function cargarDashboard() {
    try {
        const stats = await posApi.getEstadisticas();
        
        // Actualizar contadores
        document.getElementById('total-ordenes-hoy').textContent = stats.ordenes_hoy;
        document.getElementById('ventas-hoy').textContent = `$${stats.ventas_hoy}`;
        
        // Actualizar gráficos
        actualizarGraficoVentas(stats.ventas_ultimos_7_dias);
        actualizarProductosPopulares(stats.productos_populares);
        
    } catch (error) {
        console.error('Error cargando estadísticas:', error);
    }
}
```

## Mejoras que Puedes Implementar

### 1. Notificaciones en Tiempo Real

```javascript
// Configurar polling para actualizaciones
setInterval(async () => {
    const ordenesActualizadas = await posApi.getOrdenes({ 
        estado: 'abierta',
        limit: 50 
    });
    
    // Actualizar UI con nuevas órdenes
    actualizarOrdenesEnPantalla(ordenesActualizadas.items);
}, 30000); // Cada 30 segundos
```

### 2. Validación de Formularios

```javascript
// Validar antes de enviar a la API
function validarProducto(data) {
    const errores = [];
    
    if (!data.nombre || data.nombre.length < 3) {
        errores.push('El nombre debe tener al menos 3 caracteres');
    }
    
    if (!data.precio || data.precio <= 0) {
        errores.push('El precio debe ser mayor a 0');
    }
    
    if (!data.categoria) {
        errores.push('La categoría es requerida');
    }
    
    return errores;
}

async function guardarProducto(formData) {
    const errores = validarProducto(formData);
    
    if (errores.length > 0) {
        mostrarErrores(errores);
        return;
    }
    
    try {
        const response = await posApi.crearProducto(formData);
        if (response.success) {
            posApi.showNotification('Producto creado exitosamente', 'success');
            // Limpiar formulario y recargar lista
        }
    } catch (error) {
        posApi.handleError(error, 'Creación de producto');
    }
}
```

### 3. Paginación Automática

```javascript
class PaginatedList {
    constructor(containerId, loadFunction) {
        this.container = document.getElementById(containerId);
        this.loadFunction = loadFunction;
        this.currentPage = 1;
        this.limit = 20;
    }
    
    async load(page = 1) {
        this.currentPage = page;
        const data = await this.loadFunction({
            page: this.currentPage,
            limit: this.limit
        });
        
        this.render(data.items);
        this.renderPagination(data.pagination);
    }
    
    render(items) {
        // Implementar renderizado específico
    }
    
    renderPagination(pagination) {
        // Implementar controles de paginación
    }
}

// Uso
const listaProductos = new PaginatedList('productos-lista', posApi.getProductos.bind(posApi));
listaProductos.load();
```

## Configuración de Producción

### 1. Desactivar Modo Demo

En `/api/config.php`:
```php
define('DEMO_MODE', false);
```

### 2. Configurar Base de Datos Real

Actualiza las credenciales en `/api/config.php`:
```php
define('DB_HOST', 'tu-servidor-db');
define('DB_NAME', 'tu-base-datos');
define('DB_USER', 'tu-usuario');
define('DB_PASS', 'tu-contraseña');
```

### 3. Configurar CORS para Producción

En `/api/config.php`, especifica dominios permitidos:
```php
define('CORS_ORIGINS', 'https://tu-dominio.com');
```

### 4. Añadir Autenticación (Opcional)

Puedes añadir un sistema de tokens o sesiones PHP modificando el controlador principal.

## Testing

Para probar la integración:

1. **Visita `/api_demo.html`** para ver la API en acción
2. **Usa las herramientas de desarrollador** para monitorear las llamadas de red
3. **Revisa los logs de PHP** para errores de la API

## Soporte

- **Documentación completa**: Ver `/api/README.md`
- **Ejemplos de código**: Ver `/js/pos-api-client.js`
- **Demo interactivo**: Ver `/api_demo.html`

La nueva API es completamente compatible con el sistema existente y puede implementarse gradualmente, un módulo a la vez.