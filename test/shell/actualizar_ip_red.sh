#!/bin/bash

# Script para actualizar automáticamente la IP cuando cambias de red WiFi
echo "🔄 ACTUALIZADOR DE IP PARA NUEVA RED"
echo "=================================="
echo

# Obtener IP actual del sistema
NEW_IP=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)

if [ -z "$NEW_IP" ]; then
    echo "❌ No se pudo detectar una IP de red válida"
    echo "   💡 Asegúrate de estar conectado a una red WiFi"
    exit 1
fi

echo "📍 Nueva IP detectada: $NEW_IP"

# Leer IP actual del config.php
CURRENT_IP=$(grep "base_url.*http://" /Users/melquiromero/Documents/GitHub/appForms/config.php | grep -o '[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}' | head -1)

if [ "$CURRENT_IP" = "$NEW_IP" ]; then
    echo "✅ La IP ya está actualizada ($NEW_IP)"
    echo "   ℹ️  No es necesario cambiar nada"
    exit 0
fi

echo "🔧 IP actual en configuración: $CURRENT_IP"
echo "🔄 Actualizando a: $NEW_IP"

# Crear backup del config.php
cp /Users/melquiromero/Documents/GitHub/appForms/config.php /Users/melquiromero/Documents/GitHub/appForms/config.php.backup
echo "💾 Backup creado: config.php.backup"

# Actualizar IP en config.php
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    sed -i '' "s|http://$CURRENT_IP:8080|http://$NEW_IP:8080|g" /Users/melquiromero/Documents/GitHub/appForms/config.php
else
    # Linux
    sed -i "s|http://$CURRENT_IP:8080|http://$NEW_IP:8080|g" /Users/melquiromero/Documents/GitHub/appForms/config.php
fi

# Verificar que el cambio se hizo correctamente
NEW_CONFIG_IP=$(grep "base_url.*http://" /Users/melquiromero/Documents/GitHub/appForms/config.php | grep -o '[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}' | head -1)

if [ "$NEW_CONFIG_IP" = "$NEW_IP" ]; then
    echo "✅ Configuración actualizada exitosamente"
else
    echo "❌ Error al actualizar configuración"
    echo "   🔄 Restaurando backup..."
    cp /Users/melquiromero/Documents/GitHub/appForms/config.php.backup /Users/melquiromero/Documents/GitHub/appForms/config.php
    exit 1
fi

# Actualizar la guía de inicio con la nueva IP
echo "📝 Actualizando documentación..."
if [[ "$OSTYPE" == "darwin"* ]]; then
    sed -i '' "s|IP\*\*: [0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}:8080|IP**: $NEW_IP:8080|g" /Users/melquiromero/Documents/GitHub/appForms/GUIA_INICIO_SERVIDOR.md
    sed -i '' "s|http://[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}:8080|http://$NEW_IP:8080|g" /Users/melquiromero/Documents/GitHub/appForms/GUIA_INICIO_SERVIDOR.md
else
    sed -i "s|IP\*\*: [0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}:8080|IP**: $NEW_IP:8080|g" /Users/melquiromero/Documents/GitHub/appForms/GUIA_INICIO_SERVIDOR.md
    sed -i "s|http://[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}:8080|http://$NEW_IP:8080|g" /Users/melquiromero/Documents/GitHub/appForms/GUIA_INICIO_SERVIDOR.md
fi

echo
echo "🎯 ACTUALIZACIÓN COMPLETA"
echo "========================="
echo "✅ IP anterior: $CURRENT_IP"
echo "✅ IP nueva: $NEW_IP"
echo "✅ Configuración actualizada"
echo "✅ Documentación actualizada"
echo
echo "📱 NUEVAS URLs DE ACCESO:"
echo "   🖥️  Local: http://localhost:8080"
echo "   🌐 Red: http://$NEW_IP:8080"
echo
echo "🚀 SIGUIENTE PASO:"
echo "   Reinicia el servidor ejecutando:"
echo "   ./test/iniciar_servidor.sh"
echo
echo "💡 COMPARTIR CON USUARIOS:"
echo "   La nueva URL para otros dispositivos es:"
echo "   http://$NEW_IP:8080"
