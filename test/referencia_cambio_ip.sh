#!/bin/bash
# REFERENCIA RÁPIDA - CAMBIO DE IP/RED WIFI

echo "🔄 CAMBIO DE RED WiFi - REFERENCIA RÁPIDA"
echo "========================================"
echo
echo "🎯 MÉTODO AUTOMÁTICO:"
echo "   cd /Users/melquiromero/Documents/GitHub/appForms"
echo "   ./test/actualizar_ip_red.sh"
echo "   ./test/iniciar_servidor.sh"
echo
echo "⚙️  MÉTODO MANUAL:"
echo "   1. Detectar IP: ifconfig | grep 'inet ' | grep -v 127.0.0.1"
echo "   2. Editar: nano config.php (línea ~45)"
echo "   3. Cambiar: \$config['base_url'] = 'http://TU_IP:8080';"
echo "   4. Reiniciar: ./test/iniciar_servidor.sh"
echo
echo "📁 ARCHIVOS IMPORTANTES:"
echo "   📝 config.php (línea ~45) - Configuración principal"
echo "   📋 CAMBIO_IP_WIFI.md - Guía completa"
echo "   🔧 test/actualizar_ip_red.sh - Script automático"
echo
echo "🌐 VERIFICAR:"
echo "   ./test/verificar_acceso_red.sh"
