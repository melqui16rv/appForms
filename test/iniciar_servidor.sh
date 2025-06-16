#!/bin/bash

# Script para iniciar el servidor completo
echo "🚀 INICIANDO SERVIDOR DE DISEÑOS CURRICULARES"
echo "============================================="
echo

cd /Users/melquiromero/Documents/GitHub/appForms

# Verificar MySQL
echo "🗄️  Verificando MySQL..."
if ! brew services list | grep mysql | grep -q started; then
    echo "   🔄 Iniciando MySQL..."
    brew services start mysql
    sleep 3
fi

# Obtener IP
MY_IP=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)

echo "🌐 Iniciando servidor PHP en $MY_IP:8080..."
echo "   📝 Logs se mostrarán abajo..."
echo "   🛑 Para detener: Ctrl+C"
echo "   🔗 URL local: http://localhost:8080"
echo "   🌍 URL red: http://$MY_IP:8080"
echo "   📱 Acceso desde otros dispositivos: http://$MY_IP:8080"
echo
echo "💡 IMPORTANTE: Si cambias de red WiFi, ejecuta:"
echo "   ./test/actualizar_ip_red.sh"
echo
echo "=== LOGS DEL SERVIDOR ==="

# Iniciar servidor PHP
php -S 0.0.0.0:8080
