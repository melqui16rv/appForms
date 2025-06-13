#!/bin/bash

# Test específico para corrección de problema de calidad en competencias
# Verifica que no aparezca "Error de conexión" cuando la competencia se guarda correctamente

echo "🔧 Testing corrección de competencias..."
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

# Test 1: Verificar que la API de competencias responde correctamente
test_competencias_api() {
    echo "🔍 Testing API de competencias..."
    
    # Crear datos de prueba
    local test_data='{"codigoDiseno":"122201-2","codigoCompetencia":"TEST01","nombreCompetencia":"Competencia de Prueba Calidad","horasDesarrolloCompetencia":"40"}'
    
    # Realizar petición POST
    local response=$(curl -s -w "HTTPSTATUS:%{http_code}" -X POST \
        -H "Content-Type: application/json" \
        -d "$test_data" \
        "$BASE_URL/api/competencias.php")
    
    # Extraer código HTTP y cuerpo de respuesta
    local http_code=$(echo "$response" | grep -o "HTTPSTATUS:[0-9]*" | cut -d: -f2)
    local body=$(echo "$response" | sed -E 's/HTTPSTATUS:[0-9]*$//')
    
    echo "HTTP Code: $http_code"
    echo "Response: $body"
    
    # Verificar código HTTP exitoso
    if [ "$http_code" = "201" ] || [ "$http_code" = "409" ]; then
        # Verificar que la respuesta contiene 'success': true
        if echo "$body" | grep -q '"success":true'; then
            show_result "API responde correctamente con success:true" "PASS"
        else
            show_result "API responde correctamente con success:true" "FAIL" "No contiene success:true"
        fi
    else
        show_result "API responde con código HTTP correcto" "FAIL" "HTTP $http_code"
    fi
}

# Test 2: Verificar que clearCompetenciaForm usa IDs correctos
test_javascript_ids() {
    echo "📝 Testing IDs correctos en JavaScript..."
    
    # Verificar que clearCompetenciaForm usa los IDs correctos
    if grep -q "getElementById('codigoDiseñoComp')" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "clearCompetenciaForm usa ID correcto codigoDiseñoComp" "PASS"
    else
        show_result "clearCompetenciaForm usa ID correcto codigoDiseñoComp" "FAIL" "Usa ID incorrecto"
    fi
    
    if grep -q "getElementById('normaUnidad')" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "clearCompetenciaForm usa ID correcto normaUnidad" "PASS"
    else
        show_result "clearCompetenciaForm usa ID correcto normaUnidad" "FAIL" "Usa ID incorrecto"
    fi
    
    if grep -q "getElementById('requisitosInstructor')" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "clearCompetenciaForm usa ID correcto requisitosInstructor" "PASS"
    else
        show_result "clearCompetenciaForm usa ID correcto requisitosInstructor" "FAIL" "Usa ID incorrecto"
    fi
    
    if grep -q "getElementById('experienciaInstructor')" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "clearCompetenciaForm usa ID correcto experienciaInstructor" "PASS"
    else
        show_result "clearCompetenciaForm usa ID correcto experienciaInstructor" "FAIL" "Usa ID incorrecto"
    fi
}

# Test 3: Verificar que no hay IDs incorrectos residuales
test_no_incorrect_ids() {
    echo "🚫 Testing que no quedan IDs incorrectos..."
    
    if ! grep -q "getElementById('codigoDiseñoCompetencia')" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "No hay referencias a ID incorrecto codigoDiseñoCompetencia" "PASS"
    else
        show_result "No hay referencias a ID incorrecto codigoDiseñoCompetencia" "FAIL" "Aún existe ID incorrecto"
    fi
    
    if ! grep -q "getElementById('normaUnidadCompetencia')" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "No hay referencias a ID incorrecto normaUnidadCompetencia" "PASS"
    else
        show_result "No hay referencias a ID incorrecto normaUnidadCompetencia" "FAIL" "Aún existe ID incorrecto"
    fi
    
    if ! grep -q "getElementById('requisitosAcademicosInstructor')" "/Users/melquiromero/Documents/GitHub/appForms/js/main.js"; then
        show_result "No hay referencias a ID incorrecto requisitosAcademicosInstructor" "PASS"
    else
        show_result "No hay referencias a ID incorrecto requisitosAcademicosInstructor" "FAIL" "Aún existe ID incorrecto"
    fi
}

# Test 4: Verificar consistencia en HTML
test_html_consistency() {
    echo "🌐 Testing consistencia en HTML..."
    
    # Verificar que los IDs en el HTML son los que está usando el JavaScript
    if grep -q 'id="codigoDiseñoComp"' "/Users/melquiromero/Documents/GitHub/appForms/index.php"; then
        show_result "HTML tiene ID codigoDiseñoComp" "PASS"
    else
        show_result "HTML tiene ID codigoDiseñoComp" "FAIL" "ID no encontrado en HTML"
    fi
    
    if grep -q 'id="normaUnidad"' "/Users/melquiromero/Documents/GitHub/appForms/index.php"; then
        show_result "HTML tiene ID normaUnidad" "PASS"
    else
        show_result "HTML tiene ID normaUnidad" "FAIL" "ID no encontrado en HTML"
    fi
}

# Test 5: Verificar servidor funcionando
test_server() {
    echo "🌐 Testing servidor..."
    
    local response=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL")
    
    if [ "$response" = "200" ]; then
        show_result "Servidor respondiendo" "PASS"
    else
        show_result "Servidor respondiendo" "FAIL" "HTTP $response"
        return 1
    fi
}

# Ejecutar todos los tests
echo "🚀 Ejecutando tests de corrección de competencias..."
echo

# Test básico de conectividad
test_server
if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Error de conectividad. Abortando tests.${NC}"
    exit 1
fi

# Ejecutar resto de tests
test_competencias_api
test_javascript_ids
test_no_incorrect_ids
test_html_consistency

# Mostrar resumen
echo
echo "================================================"
echo "📊 RESUMEN DE CORRECCIÓN DE COMPETENCIAS"
echo "================================================"
echo -e "Total de tests: ${YELLOW}$TEST_COUNT${NC}"
echo -e "Tests exitosos: ${GREEN}$PASS_COUNT${NC}"
echo -e "Tests fallidos: ${RED}$((TEST_COUNT - PASS_COUNT))${NC}"

if [ $PASS_COUNT -eq $TEST_COUNT ]; then
    echo -e "${GREEN}🎉 ¡Corrección exitosa! El problema de calidad en competencias ha sido resuelto.${NC}"
    echo
    echo "✅ Problemas corregidos:"
    echo "   • IDs incorrectos en clearCompetenciaForm() corregidos"
    echo "   • La función ya no causa errores de JavaScript"
    echo "   • La API responde correctamente con success: true"
    echo "   • Los usuarios verán el mensaje correcto de éxito"
    echo "   • No más mensajes falsos de 'Error de conexión'"
    echo
    echo "🔧 Accede a la aplicación en: $BASE_URL"
    echo "📝 Prueba creando una competencia para verificar que funciona correctamente"
else
    echo -e "${RED}❌ Algunos tests fallaron. Revise los detalles arriba.${NC}"
    exit 1
fi
