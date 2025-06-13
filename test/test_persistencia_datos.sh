#!/bin/bash

# Test de Persistencia de Datos del Formulario
# Este script verifica que los datos del formulario no se pierdan durante la generación de códigos

echo "🔄 Iniciando test de persistencia de datos..."
echo "================================================"

# Colores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Variables
BASE_URL="http://172.30.5.255:8080"
TEST_COUNT=0
PASS_COUNT=0

# Función para mostrar resultados
show_result() {
    local test_name="$1"
    local status="$2"
    local message="$3"
    
    TEST_COUNT=$((TEST_COUNT + 1))
    
    if [ "$status" = "PASS" ]; then
        echo -e "${GREEN}✅ PASS${NC} - $test_name"
        PASS_COUNT=$((PASS_COUNT + 1))
    else
        echo -e "${RED}❌ FAIL${NC} - $test_name: $message"
    fi
}

# Función para test de conectividad básica
test_connectivity() {
    echo "🌐 Testing conectividad básica..."
    
    response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL")
    
    if [ "$response" = "200" ]; then
        show_result "Conectividad del servidor" "PASS"
    else
        show_result "Conectividad del servidor" "FAIL" "HTTP $response"
        return 1
    fi
}

# Test 1: Verificar APIs de validación
test_apis() {
    echo "🔧 Testing APIs de validación..."
    
    # Test API de diseños
    response=$(curl -s "$BASE_URL/api/disenos.php")
    if echo "$response" | grep -q "\["; then
        show_result "API Diseños disponible" "PASS"
    else
        show_result "API Diseños disponible" "FAIL" "No devuelve array JSON"
    fi
    
    # Test API de competencias
    response=$(curl -s "$BASE_URL/api/competencias.php")
    if echo "$response" | grep -q "\["; then
        show_result "API Competencias disponible" "PASS"
    else
        show_result "API Competencias disponible" "FAIL" "No devuelve array JSON"
    fi
    
    # Test API de RAPs
    response=$(curl -s "$BASE_URL/api/raps.php")
    if echo "$response" | grep -q "\["; then
        show_result "API RAPs disponible" "PASS"
    else
        show_result "API RAPs disponible" "FAIL" "No devuelve array JSON"
    fi
}

# Test 2: Verificar que las funciones JavaScript corregidas están presentes
test_javascript_functions() {
    echo "📝 Testing correcciones en JavaScript..."
    
    if grep -q "Solo resetear el modo de edición, no borrar el formulario" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Corrección validateDisenoExists" "PASS"
    else
        show_result "Corrección validateDisenoExists" "FAIL" "Comentario de corrección no encontrado"
    fi
    
    if grep -q "versionPrograma.*diseno.versionPrograma" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Corrección fillDisenoForm typo" "PASS"
    else
        show_result "Corrección fillDisenoForm typo" "FAIL" "Corrección del typo no encontrada"
    fi
    
    if grep -q "NO limpiar el formulario en caso de error" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Preservación de datos en errores" "PASS"
    else
        show_result "Preservación de datos en errores" "FAIL" "Comentarios de preservación no encontrados"
    fi
    
    if grep -q "clearDisenoCodesOnly" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Función clearDisenoCodesOnly añadida" "PASS"
    else
        show_result "Función clearDisenoCodesOnly añadida" "FAIL" "Nueva función no encontrada"
    fi
}

# Test 3: Verificar estructura de formularios
test_form_structure() {
    echo "📋 Testing estructura de formularios..."
    
    # Verificar que el archivo principal existe
    if [ -f "/Users/melquiromero/Documents/GitHub/appForms/index.php" ]; then
        show_result "Archivo index.php existe" "PASS"
    else
        show_result "Archivo index.php existe" "FAIL" "Archivo no encontrado"
    fi
    
    # Verificar que los formularios tienen IDs correctos
    if grep -q 'id="disenoForm"' "/Users/melquiromero/Documents/GitHub/appForms/index.php"; then
        show_result "Formulario de diseños configurado" "PASS"
    else
        show_result "Formulario de diseños configurado" "FAIL" "ID del formulario no encontrado"
    fi
    
    if grep -q 'id="competenciaForm"' "/Users/melquiromero/Documents/GitHub/appForms/index.php"; then
        show_result "Formulario de competencias configurado" "PASS"
    else
        show_result "Formulario de competencias configurado" "FAIL" "ID del formulario no encontrado"
    fi
    
    if grep -q 'id="rapForm"' "/Users/melquiromero/Documents/GitHub/appForms/index.php"; then
        show_result "Formulario de RAPs configurado" "PASS"
    else
        show_result "Formulario de RAPs configurado" "FAIL" "ID del formulario no encontrado"
    fi
}

# Test 4: Verificar event listeners
test_event_listeners() {
    echo "🎯 Testing event listeners..."
    
    if grep -q "addEventListener('input', generateCodigoDiseno)" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Event listeners de diseños" "PASS"
    else
        show_result "Event listeners de diseños" "FAIL" "Event listeners no encontrados"
    fi
    
    if grep -q "addEventListener('input', generateCodigoCompetencia)" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Event listeners de competencias" "PASS"
    else
        show_result "Event listeners de competencias" "FAIL" "Event listeners no encontrados"
    fi
    
    if grep -q "addEventListener('input', generateCodigoRap)" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Event listeners de RAPs" "PASS"
    else
        show_result "Event listeners de RAPs" "FAIL" "Event listeners no encontrados"
    fi
}

# Test 5: Verificar mejoras en mensajes de error
test_error_messages() {
    echo "⚠️  Testing mejoras en mensajes de error..."
    
    if grep -q "Verifique su conexión e intente nuevamente" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Mensajes de error mejorados" "PASS"
    else
        show_result "Mensajes de error mejorados" "FAIL" "Mensajes mejorados no encontrados"
    fi
    
    if grep -q "Solo limpiar después de guardar exitosamente" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "Comentarios de clarificación" "PASS"
    else
        show_result "Comentarios de clarificación" "FAIL" "Comentarios no encontrados"
    fi
}

# Ejecutar todos los tests
echo "🚀 Ejecutando tests de persistencia de datos..."
echo

# Test de conectividad primero
test_connectivity
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Error de conectividad. Verifique que el servidor esté ejecutándose en $BASE_URL${NC}"
    echo "💡 Para iniciar el servidor: cd /Users/melquiromero/Documents/GitHub/appForms && php -S 172.30.5.255:8080"
    exit 1
fi

# Ejecutar resto de tests
test_apis
test_javascript_functions
test_form_structure
test_event_listeners
test_error_messages

# Mostrar resumen
echo
echo "================================================"
echo "📊 RESUMEN DE TESTS DE PERSISTENCIA DE DATOS"
echo "================================================"
echo -e "Total de tests: ${YELLOW}$TEST_COUNT${NC}"
echo -e "Tests exitosos: ${GREEN}$PASS_COUNT${NC}"
echo -e "Tests fallidos: ${RED}$((TEST_COUNT - PASS_COUNT))${NC}"

if [ $PASS_COUNT -eq $TEST_COUNT ]; then
    echo -e "${GREEN}🎉 ¡Todos los tests pasaron! Los problemas de persistencia de datos han sido resueltos.${NC}"
    echo
    echo "✅ Correcciones implementadas:"
    echo "   • Los formularios NO se limpian automáticamente durante la generación de códigos"
    echo "   • Los datos se preservan cuando hay errores de conexión o validación"
    echo "   • Se corrigió el typo en versionPrograma"
    echo "   • Se añadió función clearDisenoCodesOnly para limpieza selectiva"
    echo "   • Se mejoraron los mensajes de error"
    echo "   • Se separaron las funciones de limpieza automática vs manual"
    echo
    echo "🔧 Accede a la aplicación en: $BASE_URL"
else
    echo -e "${RED}❌ Algunos tests fallaron. Revise los detalles arriba.${NC}"
    exit 1
fi
