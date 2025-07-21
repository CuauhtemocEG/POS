#!/bin/bash

# Script de Actualización del Sistema POS
# Automatiza la configuración de la nueva API

echo "🚀 Iniciando actualización del Sistema POS..."
echo "=================================="

# Verificar que estamos en el directorio correcto
if [ ! -f "index.php" ]; then
    echo "❌ Error: Debes ejecutar este script desde el directorio raíz del POS"
    exit 1
fi

echo "✅ Directorio de POS detectado"

# Verificar permisos de escritura
if [ ! -w "." ]; then
    echo "❌ Error: No tienes permisos de escritura en este directorio"
    exit 1
fi

echo "✅ Permisos verificados"

# Crear backup de configuración actual
echo "📦 Creando backup de configuración..."
if [ -f "conexion.php" ]; then
    cp conexion.php conexion.php.backup.$(date +%Y%m%d_%H%M%S)
    echo "✅ Backup creado: conexion.php.backup.*"
fi

# Verificar que la API está presente
if [ ! -d "api" ]; then
    echo "❌ Error: Directorio /api no encontrado"
    echo "   Asegúrate de que los archivos de la API se han subido correctamente"
    exit 1
fi

echo "✅ API detectada en /api/"

# Verificar archivos necesarios
required_files=("api/index.php" "api/config.php" "js/pos-api-client.js")
for file in "${required_files[@]}"; do
    if [ ! -f "$file" ]; then
        echo "❌ Error: Archivo requerido no encontrado: $file"
        exit 1
    fi
done

echo "✅ Todos los archivos de la API están presentes"

# Configurar permisos
echo "🔧 Configurando permisos..."
chmod 755 api/
chmod 644 api/*.php
chmod 644 api/.htaccess
chmod 644 js/pos-api-client.js

echo "✅ Permisos configurados"

# Verificar configuración de PHP
echo "🐘 Verificando configuración de PHP..."

if ! php -v > /dev/null 2>&1; then
    echo "❌ Error: PHP no está instalado o no está en el PATH"
    exit 1
fi

echo "✅ PHP detectado: $(php -r 'echo PHP_VERSION;')"

# Verificar sintaxis de archivos PHP
echo "🔍 Verificando sintaxis de archivos PHP..."
for file in api/*.php; do
    if ! php -l "$file" > /dev/null 2>&1; then
        echo "❌ Error de sintaxis en: $file"
        exit 1
    fi
done

echo "✅ Sintaxis de PHP verificada"

# Configurar base de datos
echo "🗄️  Configuración de base de datos..."
echo ""
echo "Para completar la instalación, necesitas configurar la base de datos:"
echo ""
echo "1. Edita el archivo: api/config.php"
echo "2. Busca las líneas de configuración de BD:"
echo "   define('DB_HOST', 'localhost:3306');"
echo "   define('DB_NAME', 'kallijag_pos_stage');"
echo "   define('DB_USER', 'kallijag_stage');"
echo "   define('DB_PASS', 'uNtiL.horSe@5');"
echo ""
echo "3. Actualiza con tus credenciales de base de datos"
echo ""
echo "4. Cambia DEMO_MODE a false:"
echo "   define('DEMO_MODE', false);"
echo ""

# Verificar servidor web
echo "🌐 Verificando configuración del servidor web..."

if command -v apache2 > /dev/null 2>&1; then
    echo "✅ Apache detectado"
    echo "🔧 Para URLs limpias, asegúrate de que mod_rewrite esté habilitado:"
    echo "   sudo a2enmod rewrite"
    echo "   sudo systemctl restart apache2"
elif command -v nginx > /dev/null 2>&1; then
    echo "✅ Nginx detectado"
    echo "🔧 Para URLs limpias con Nginx, añade esta configuración:"
    echo "   location /api/ {"
    echo "       try_files \$uri \$uri/ /api/index.php?\$query_string;"
    echo "   }"
else
    echo "⚠️  Servidor web no detectado automáticamente"
    echo "   La API funcionará con PHP built-in server para desarrollo"
fi

# Probar la API
echo ""
echo "🧪 Probando la API..."

# Iniciar servidor de desarrollo si no hay otro servidor
if ! curl -s http://localhost:8000/api/ > /dev/null 2>&1; then
    echo "⚡ Iniciando servidor de desarrollo..."
    php -S localhost:8000 > /dev/null 2>&1 &
    SERVER_PID=$!
    sleep 2
    
    # Verificar que el servidor está corriendo
    if curl -s http://localhost:8000/api/ > /dev/null 2>&1; then
        echo "✅ Servidor de desarrollo iniciado en http://localhost:8000"
        
        # Probar endpoint básico
        if curl -s http://localhost:8000/api/ | grep -q "success"; then
            echo "✅ API respondiendo correctamente"
        else
            echo "⚠️  API iniciada pero puede tener problemas de configuración"
        fi
        
        # Detener servidor de desarrollo
        kill $SERVER_PID 2>/dev/null
    else
        echo "❌ Error al iniciar servidor de desarrollo"
    fi
else
    echo "✅ API ya está corriendo y respondiendo"
fi

# Mostrar resumen
echo ""
echo "🎉 ¡Actualización completada!"
echo "============================="
echo ""
echo "📋 Resumen de archivos instalados:"
echo "   ✅ /api/index.php - Controlador principal de la API"
echo "   ✅ /api/config.php - Configuración de la API"
echo "   ✅ /api/.htaccess - URLs limpias"
echo "   ✅ /api/README.md - Documentación completa"
echo "   ✅ /js/pos-api-client.js - Cliente JavaScript"
echo "   ✅ /api_demo.html - Página de demostración"
echo "   ✅ /INTEGRATION_GUIDE.md - Guía de integración"
echo ""
echo "🔗 Próximos pasos:"
echo "   1. Configura la base de datos en api/config.php"
echo "   2. Visita /api_demo.html para probar la API"
echo "   3. Lee INTEGRATION_GUIDE.md para integrar con tu sistema"
echo "   4. Consulta /api/README.md para documentación completa"
echo ""
echo "🚀 Tu sistema POS ahora tiene una API REST moderna!"

# Mostrar información adicional si es necesario
if [ "$1" = "--info" ]; then
    echo ""
    echo "📊 Información adicional:"
    echo "   📁 Estructura de archivos creada:"
    find api/ js/ -name "*.php" -o -name "*.js" -o -name "*.md" -o -name ".htaccess" | sort
    echo ""
    echo "   🔧 Endpoints disponibles:"
    echo "   GET  /api/              - Información de la API"
    echo "   GET  /api/mesas         - Listar mesas"
    echo "   POST /api/mesas         - Crear mesa"
    echo "   GET  /api/productos     - Listar productos"
    echo "   POST /api/productos     - Crear producto"
    echo "   GET  /api/ordenes       - Listar órdenes"
    echo "   POST /api/ordenes       - Crear orden"
    echo "   GET  /api/categorias    - Listar categorías"
    echo "   GET  /api/estadisticas  - Estadísticas del sistema"
fi

echo ""
echo "💡 Tip: Ejecuta con --info para ver información detallada"
echo "📧 Si tienes problemas, revisa los logs de PHP y la documentación"
echo ""
echo "¡Gracias por usar el Sistema POS! 🍕🥤"