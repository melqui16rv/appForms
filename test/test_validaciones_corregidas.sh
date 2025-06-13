#!/bin/bash

echo "🔍 MONITOR DE VALIDACIONES CORREGIDAS"
echo "===================================="
echo "Este script te ayudará a probar las correcciones realizadas"
echo

# Verificar que el servidor esté corriendo
if ! pgrep -f "php.*8080" > /dev/null; then
    echo "❌ El servidor PHP no está corriendo"
    echo "💡 Inicia el servidor con: ./iniciar_servidor.sh"
    exit 1
fi

MY_IP=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)

echo "✅ Servidor PHP detectado corriendo"
echo "🌐 URL de prueba: http://$MY_IP:8080"
echo

echo "📋 INSTRUCCIONES DE PRUEBA:"
echo "=========================="
echo "1. Abre la aplicación en: http://$MY_IP:8080"
echo "2. Realiza las siguientes operaciones:"
echo "   📝 Crear un diseño"
echo "   ✏️  Editarlo"
echo "   🗑️  Eliminarlo"
echo "   📝 Crear una competencia"
echo "   ✏️  Editarla"
echo "   🗑️  Eliminarla"
echo "   📝 Crear un RAP"
echo "   ✏️  Editarlo"
echo "   🗑️  Eliminarlo"
echo

echo "✅ CORRECCIONES APLICADAS:"
echo "========================="
echo "• APIs ahora retornan 'success: true' en operaciones exitosas"
echo "• JavaScript valida tanto response.ok como result.success"
echo "• Eliminados mensajes duplicados de 'Error de conexión'"
echo "• Funciones de limpiado separadas (automático vs manual)"
echo "• Removidas funciones duplicadas"
echo

echo "🎯 QUÉ BUSCAR:"
echo "============="
echo "✅ Solo debería aparecer UN mensaje por operación"
echo "✅ Los mensajes de éxito deberían ser claros y específicos"
echo "✅ Los errores reales deberían mostrar información útil"
echo "✅ No debería aparecer 'Error de conexión' si la operación fue exitosa"
echo

echo "🔗 Enlaces útiles:"
echo "=================="
echo "• Aplicación principal: http://$MY_IP:8080"
echo "• Test de validaciones: http://$MY_IP:8080/test/test_validaciones.php"
echo "• Debug BD: http://$MY_IP:8080/debug_bd.php"
echo

echo "✨ ¡Las validaciones han sido corregidas y mejoradas!"
echo "💪 El código ahora es de mayor calidad y más confiable."
