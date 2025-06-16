#!/bin/bash

# Script de verificación de acceso en red local
echo "🌐 VERIFICACIÓN DE ACCESO EN RED LOCAL"
echo "======================================"
echo

# Obtener IP actual
MY_IP=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)
echo "📍 IP detectada: $MY_IP"

# Verificar que el servidor esté corriendo
echo "🔍 Verificando servidor..."
if curl -s "http://$MY_IP:8080" > /dev/null; then
    echo "✅ Servidor funcionando en http://$MY_IP:8080"
else
    echo "❌ Servidor no responde"
    exit 1
fi

# Verificar APIs
echo "🔧 Verificando APIs..."
if curl -s "http://$MY_IP:8080/api/disenos.php" | grep -q '\['; then
    echo "✅ API de diseños funcionando"
else
    echo "❌ API de diseños no responde"
fi

if curl -s "http://$MY_IP:8080/api/competencias.php" | grep -q '\['; then
    echo "✅ API de competencias funcionando"
else
    echo "❌ API de competencias no responde"
fi

if curl -s "http://$MY_IP:8080/api/raps.php" | grep -q '\['; then
    echo "✅ API de RAPs funcionando"
else
    echo "❌ API de RAPs no responde"
fi

# Verificar configuración de red
echo "🌍 Verificando configuración de red..."

# Verificar que PHP esté escuchando en todas las interfaces
if netstat -an | grep ':8080' | grep -q '0.0.0.0'; then
    echo "✅ Servidor escuchando en todas las interfaces (0.0.0.0:8080)"
else
    echo "⚠️  Servidor podría no estar escuchando en todas las interfaces"
fi

# Verificar firewall
echo "🛡️  Verificando configuración de firewall..."
if /usr/libexec/ApplicationFirewall/socketfilterfw --getglobalstate | grep -q "enabled"; then
    echo "⚠️  Firewall está habilitado - puede necesitar configuración"
    echo "   💡 Ir a: Sistema > Seguridad y Privacidad > Firewall"
    echo "   💡 Permitir conexiones entrantes para PHP"
else
    echo "✅ Firewall deshabilitado - acceso libre"
fi

echo
echo "📱 INSTRUCCIONES PARA ACCESO DESDE OTROS DISPOSITIVOS:"
echo "======================================================="
echo "1. Asegúrate de que ambos dispositivos estén en la misma red WiFi"
echo "2. En el dispositivo remoto, abrir navegador web"
echo "3. Ir a: http://$MY_IP:8080"
echo
echo "📋 URLs de acceso:"
echo "   🖥️  Local: http://localhost:8080"
echo "   🌐 Red: http://$MY_IP:8080"
echo
echo "🧪 Para probar desde otro dispositivo:"
echo "   curl http://$MY_IP:8080"
echo
echo "✅ El servidor está configurado para acceso en red local"
