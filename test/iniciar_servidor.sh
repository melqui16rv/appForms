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

# Verificar si el puerto 8080 está libre
if lsof -i :8080 >/dev/null 2>&1; then
    echo "   ⚠️  Puerto 8080 ya está en uso"
    echo "   🔍 Proceso actual:"
    lsof -i :8080
    echo
    read -p "¿Quieres terminar el proceso existente? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "   🛑 Terminando proceso..."
        sudo lsof -t -i :8080 | xargs kill -9
        sleep 2
    else
        echo "   ❌ Cancelando inicio..."
        exit 1
    fi
fi

echo "🌐 Iniciando servidor PHP en $MY_IP:8080..."
echo "   📝 Logs se mostrarán abajo..."
echo "   🛑 Para detener: Ctrl+C"
echo "   🔗 URL: http://$MY_IP:8080"
echo
echo "=== LOGS DEL SERVIDOR ==="

# Iniciar servidor PHP
php -S 0.0.0.0:8080
