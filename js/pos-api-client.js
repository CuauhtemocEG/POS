/**
 * POS API Client Library
 * Biblioteca JavaScript para integrar fácilmente con la API del sistema POS
 * 
 * @version 1.0.0
 * @author Sistema POS
 */

class PosApiClient {
    constructor(baseUrl = '/api') {
        this.baseUrl = baseUrl.replace(/\/$/, ''); // Remover slash final
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
    }

    /**
     * Realizar petición HTTP a la API
     * @param {string} endpoint - Endpoint de la API
     * @param {Object} options - Opciones de la petición
     * @returns {Promise<Object>} Respuesta de la API
     */
    async request(endpoint, options = {}) {
        try {
            const url = `${this.baseUrl}${endpoint}`;
            const config = {
                headers: { ...this.defaultHeaders, ...options.headers },
                ...options
            };

            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || `HTTP ${response.status}: ${response.statusText}`);
            }

            return data;
        } catch (error) {
            console.error('Error en POS API:', error);
            throw error;
        }
    }

    /**
     * Petición GET
     * @param {string} endpoint - Endpoint de la API
     * @param {Object} params - Parámetros query string
     * @returns {Promise<Object>} Respuesta de la API
     */
    async get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return this.request(url, { method: 'GET' });
    }

    /**
     * Petición POST
     * @param {string} endpoint - Endpoint de la API
     * @param {Object} data - Datos a enviar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    /**
     * Petición PUT
     * @param {string} endpoint - Endpoint de la API
     * @param {Object} data - Datos a enviar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    /**
     * Petición DELETE
     * @param {string} endpoint - Endpoint de la API
     * @returns {Promise<Object>} Respuesta de la API
     */
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }

    // ===== MÉTODOS DE MESAS =====

    /**
     * Obtener todas las mesas
     * @returns {Promise<Array>} Lista de mesas
     */
    async getMesas() {
        const response = await this.get('/mesas');
        return response.success ? response.data : [];
    }

    /**
     * Obtener una mesa específica
     * @param {number} id - ID de la mesa
     * @returns {Promise<Object|null>} Datos de la mesa
     */
    async getMesa(id) {
        try {
            const response = await this.get(`/mesas/${id}`);
            return response.success ? response.data : null;
        } catch (error) {
            return null;
        }
    }

    /**
     * Crear nueva mesa
     * @param {Object} mesaData - Datos de la mesa
     * @param {string} mesaData.nombre - Nombre de la mesa
     * @returns {Promise<Object>} Respuesta de la creación
     */
    async crearMesa(mesaData) {
        return await this.post('/mesas', mesaData);
    }

    /**
     * Actualizar mesa
     * @param {number} id - ID de la mesa
     * @param {Object} mesaData - Datos a actualizar
     * @returns {Promise<Object>} Respuesta de la actualización
     */
    async actualizarMesa(id, mesaData) {
        return await this.put(`/mesas/${id}`, mesaData);
    }

    /**
     * Eliminar mesa
     * @param {number} id - ID de la mesa
     * @returns {Promise<Object>} Respuesta de la eliminación
     */
    async eliminarMesa(id) {
        return await this.delete(`/mesas/${id}`);
    }

    // ===== MÉTODOS DE PRODUCTOS =====

    /**
     * Obtener productos con paginación y filtros
     * @param {Object} options - Opciones de búsqueda
     * @param {number} options.page - Página (default: 1)
     * @param {number} options.limit - Elementos por página (default: 20)
     * @param {string} options.search - Búsqueda por texto
     * @param {string} options.categoria - Filtrar por categoría
     * @returns {Promise<Object>} Datos paginados de productos
     */
    async getProductos(options = {}) {
        const response = await this.get('/productos', options);
        return response.success ? response.data : { items: [], pagination: {} };
    }

    /**
     * Obtener un producto específico
     * @param {number} id - ID del producto
     * @returns {Promise<Object|null>} Datos del producto
     */
    async getProducto(id) {
        try {
            const response = await this.get(`/productos/${id}`);
            return response.success ? response.data : null;
        } catch (error) {
            return null;
        }
    }

    /**
     * Crear nuevo producto
     * @param {Object} productoData - Datos del producto
     * @param {string} productoData.nombre - Nombre del producto
     * @param {number} productoData.precio - Precio del producto
     * @param {string} productoData.categoria - Categoría
     * @param {string} productoData.type - Tipo (comida/bebida)
     * @param {string} [productoData.descripcion] - Descripción opcional
     * @returns {Promise<Object>} Respuesta de la creación
     */
    async crearProducto(productoData) {
        return await this.post('/productos', productoData);
    }

    /**
     * Actualizar producto
     * @param {number} id - ID del producto
     * @param {Object} productoData - Datos a actualizar
     * @returns {Promise<Object>} Respuesta de la actualización
     */
    async actualizarProducto(id, productoData) {
        return await this.put(`/productos/${id}`, productoData);
    }

    /**
     * Eliminar producto
     * @param {number} id - ID del producto
     * @returns {Promise<Object>} Respuesta de la eliminación
     */
    async eliminarProducto(id) {
        return await this.delete(`/productos/${id}`);
    }

    // ===== MÉTODOS DE ÓRDENES =====

    /**
     * Obtener órdenes con filtros
     * @param {Object} options - Opciones de filtrado
     * @param {number} options.page - Página
     * @param {number} options.limit - Elementos por página
     * @param {string} options.estado - Filtrar por estado
     * @param {string} options.mesa - Filtrar por mesa
     * @param {string} options.fecha_desde - Fecha desde (YYYY-MM-DD)
     * @param {string} options.fecha_hasta - Fecha hasta (YYYY-MM-DD)
     * @returns {Promise<Object>} Datos paginados de órdenes
     */
    async getOrdenes(options = {}) {
        const response = await this.get('/ordenes', options);
        return response.success ? response.data : { items: [], pagination: {} };
    }

    /**
     * Obtener una orden específica
     * @param {number|string} idOrCodigo - ID numérico o código de la orden
     * @returns {Promise<Object|null>} Datos completos de la orden
     */
    async getOrden(idOrCodigo) {
        try {
            const response = await this.get(`/ordenes/${idOrCodigo}`);
            return response.success ? response.data : null;
        } catch (error) {
            return null;
        }
    }

    /**
     * Crear nueva orden
     * @param {Object} ordenData - Datos de la orden
     * @param {number} ordenData.mesa_id - ID de la mesa
     * @returns {Promise<Object>} Respuesta de la creación
     */
    async crearOrden(ordenData) {
        return await this.post('/ordenes', ordenData);
    }

    /**
     * Actualizar estado de orden
     * @param {number} id - ID de la orden
     * @param {string} estado - Nuevo estado (abierta/pagada/cancelada)
     * @returns {Promise<Object>} Respuesta de la actualización
     */
    async actualizarOrden(id, estado) {
        return await this.put(`/ordenes/${id}`, { estado });
    }

    /**
     * Marcar orden como pagada
     * @param {number} id - ID de la orden
     * @returns {Promise<Object>} Respuesta de la actualización
     */
    async pagarOrden(id) {
        return await this.actualizarOrden(id, 'pagada');
    }

    /**
     * Cancelar orden
     * @param {number} id - ID de la orden
     * @returns {Promise<Object>} Respuesta de la actualización
     */
    async cancelarOrden(id) {
        return await this.actualizarOrden(id, 'cancelada');
    }

    // ===== MÉTODOS DE CATEGORÍAS =====

    /**
     * Obtener todas las categorías
     * @returns {Promise<Array>} Lista de categorías con conteos
     */
    async getCategorias() {
        const response = await this.get('/categorias');
        return response.success ? response.data : [];
    }

    // ===== MÉTODOS DE ESTADÍSTICAS =====

    /**
     * Obtener estadísticas del sistema
     * @returns {Promise<Object>} Estadísticas completas
     */
    async getEstadisticas() {
        const response = await this.get('/estadisticas');
        return response.success ? response.data : {};
    }

    // ===== MÉTODOS DE UTILIDAD =====

    /**
     * Obtener información de la API
     * @returns {Promise<Object>} Información de la API
     */
    async getApiInfo() {
        const response = await this.get('/');
        return response.success ? response.data : {};
    }

    /**
     * Manejar errores de forma consistente
     * @param {Error} error - Error capturado
     * @param {string} context - Contexto donde ocurrió el error
     */
    handleError(error, context = 'API') {
        console.error(`Error en ${context}:`, error);
        
        // Mostrar notificación al usuario (requiere implementación de UI)
        this.showNotification(`Error: ${error.message}`, 'error');
    }

    /**
     * Mostrar notificación (debe ser implementado según el sistema de UI)
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de notificación (success, error, warning, info)
     */
    showNotification(message, type = 'info') {
        // Implementación básica - puede ser personalizada
        console.log(`[${type.toUpperCase()}] ${message}`);
        
        // Ejemplo de integración con un sistema de notificaciones
        if (typeof window !== 'undefined' && window.showToast) {
            window.showToast(message, type);
        }
    }
}

// Crear instancia global para fácil acceso
const posApi = new PosApiClient();

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { PosApiClient, posApi };
}

// Agregar a window para uso global
if (typeof window !== 'undefined') {
    window.PosApiClient = PosApiClient;
    window.posApi = posApi;
}

/**
 * Ejemplos de uso:
 * 
 * // Obtener todas las mesas
 * const mesas = await posApi.getMesas();
 * 
 * // Crear nueva mesa
 * const nuevaMesa = await posApi.crearMesa({ nombre: 'Mesa VIP' });
 * 
 * // Obtener productos con filtros
 * const productos = await posApi.getProductos({ 
 *   page: 1, 
 *   limit: 10, 
 *   categoria: 'Pizzas',
 *   search: 'margherita'
 * });
 * 
 * // Crear nueva orden
 * const orden = await posApi.crearOrden({ mesa_id: 1 });
 * 
 * // Obtener estadísticas
 * const stats = await posApi.getEstadisticas();
 */