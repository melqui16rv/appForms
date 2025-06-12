#!/bin/bash

# Script de inicio para el Sistema de Gestión de Diseños Curriculares
# Autor: Sistema SENA
# Fecha: $(date)

echo "🎓 Iniciando Sistema de Gestión de Diseños Curriculares"
echo "=================================================="

# Verificar si PHP está instalado
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP no está instalado"
    echo "💡 Instalar PHP con: brew install php (macOS) o apt install php (Ubuntu)"
    exit 1
fi

# Verificar si MySQL está corriendo
if ! pgrep -x "mysqld" > /dev/null; then
    echo "⚠️  Advertencia: MySQL no parece estar ejecutándose"
    echo "💡 Iniciar MySQL con: brew services start mysql (macOS) o systemctl start mysql (Ubuntu)"
fi

# Ir al directorio del proyecto
cd "$(dirname "$0")"

# Verificar que la base de datos existe
echo "🔍 Verificando base de datos..."
if mysql -u root -e "USE disenos_curriculares;" 2>/dev/null; then
    echo "✅ Base de datos encontrada"
else
    echo "❌ Base de datos no encontrada"
    echo "🔧 Creando base de datos..."
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS disenos_curriculares CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -u root disenos_curriculares < sql/schema.sql
    echo "✅ Base de datos creada con datos de ejemplo"
fi

# Buscar puerto disponible
PORT=8081
while lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null; do
    ((PORT++))
done

echo "🚀 Iniciando servidor PHP en puerto $PORT..."
echo "📱 La aplicación estará disponible en: http://localhost:$PORT"
echo "⏹️  Para detener el servidor presiona Ctrl+C"
echo ""

# Cambiar al directorio public e iniciar servidor
cd public
php -S localhost:$PORT router.php
