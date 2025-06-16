#!/bin/bash

# Script para verificar el estado completo del sistema
echo "🔍 VERIFICACIÓN COMPLETA DEL SISTEMA"
echo "===================================="
echo

# 1. Verificar IP actual
echo "📍 IP ACTUAL:"
MY_IP=$(ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1)
echo "   Tu IP local: $MY_IP"
echo

# 2. Verificar si PHP está corriendo
echo "🚀 SERVIDOR PHP:"
PHP_RUNNING=$(ps aux | grep -E 'php.*8080' | grep -v grep)
if [ -n "$PHP_RUNNING" ]; then
    echo "   ✅ Servidor PHP corriendo en puerto 8080"
    echo "   📊 Proceso: $(echo $PHP_RUNNING | awk '{print $2}')"
else
    echo "   ❌ Servidor PHP NO está corriendo"
    echo "   💡 Para iniciar: cd /Users/melquiromero/Documents/GitHub/appForms && php -S 0.0.0.0:8080"
fi
echo

# 3. Verificar MySQL
echo "🗄️  MYSQL:"
MYSQL_RUNNING=$(brew services list | grep mysql | grep started)
if [ -n "$MYSQL_RUNNING" ]; then
    echo "   ✅ MySQL está corriendo"
    
    # Verificar conexión
    mysql -u admin_remoto -padmin123 -e "USE disenos_curriculares; SELECT 'Conexión exitosa' as estado;" 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "   ✅ Usuario remoto puede conectarse"
        
        # Contar registros
        DISENOS=$(mysql -u admin_remoto -padmin123 -e "USE disenos_curriculares; SELECT COUNT(*) FROM diseños;" 2>/dev/null | tail -1)
        COMPETENCIAS=$(mysql -u admin_remoto -padmin123 -e "USE disenos_curriculares; SELECT COUNT(*) FROM competencias;" 2>/dev/null | tail -1)
        RAPS=$(mysql -u admin_remoto -padmin123 -e "USE disenos_curriculares; SELECT COUNT(*) FROM raps;" 2>/dev/null | tail -1)
        
        echo "   📊 Datos: $DISENOS diseños, $COMPETENCIAS competencias, $RAPS RAPs"
    else
        echo "   ❌ Error en conexión remota"
    fi
else
    echo "   ❌ MySQL NO está corriendo"
    echo "   💡 Para iniciar: brew services start mysql"
fi
echo

# 4. Puertos en uso
echo "🔌 PUERTOS:"
echo "   Puerto 8080: $(lsof -i :8080 | grep LISTEN | awk '{print "✅ En uso por " $1}' || echo "❌ Libre")"
echo "   Puerto 3306: $(lsof -i :3306 | grep LISTEN | awk '{print "✅ En uso por " $1}' || echo "❌ Libre")"
echo

# 5. URLs de acceso
echo "🌐 URLS DE ACCESO:"
echo "   🏠 Aplicación principal: http://$MY_IP:8080"
echo "   🔧 Prueba conectividad:  http://$MY_IP:8080/test_conectividad.php"
echo "   🐛 Debug base datos:     http://$MY_IP:8080/debug_bd.php"
echo "   📊 APIs:                 http://$MY_IP:8080/test_apis.php"
echo

# 6. Para otros equipos
echo "👥 PARA OTROS EQUIPOS:"
echo "   📱 Asegúrense de estar en la misma red WiFi"
echo "   🌐 Abrir navegador e ir a: http://$MY_IP:8080"
echo "   🧪 Para probar conectividad: ping $MY_IP"
echo

echo "✅ Verificación completada!"
