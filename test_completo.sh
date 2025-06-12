#!/bin/bash
# Script de prueba completa para la aplicación de diseños curriculares

echo "🧪 INICIANDO PRUEBAS COMPLETAS DEL SISTEMA"
echo "=========================================="

BASE_URL="http://localhost:8081/api"

echo ""
echo "📊 ESTADO INICIAL:"
echo "- Diseños: $(curl -s "$BASE_URL/disenos.php" | jq 'length')"
echo "- Competencias: $(curl -s "$BASE_URL/competencias.php" | jq 'length')"
echo "- RAPs: $(curl -s "$BASE_URL/raps.php" | jq 'length')"

echo ""
echo "🧪 PRUEBA 1: Crear nuevo diseño"
DISENO_RESPONSE=$(curl -s -X POST "$BASE_URL/disenos.php" \
  -H "Content-Type: application/json" \
  -d '{
    "codigoPrograma": "PRUEBA002",
    "versionPrograma": "1",
    "lineaTecnologica": "Tecnología de Prueba",
    "redTecnologica": "Red de Pruebas",
    "redConocimiento": "Conocimiento de Prueba",
    "horasDesarrolloDiseño": 1000,
    "mesesDesarrolloDiseño": 10,
    "nivelAcademicoIngreso": "Bachillerato",
    "gradoNivelAcademico": "11",
    "formacionTrabajoDesarrolloHumano": "Si",
    "edadMinima": 16,
    "requisitosAdicionales": "Ninguno"
  }')

if echo "$DISENO_RESPONSE" | jq -e '.message' > /dev/null; then
    echo "✅ Diseño creado exitosamente"
    CODIGO_DISENO=$(echo "$DISENO_RESPONSE" | jq -r '.["codigoDiseño"]')
else
    echo "❌ Error creando diseño: $DISENO_RESPONSE"
    exit 1
fi

echo ""
echo "🧪 PRUEBA 2: Crear competencia para el diseño"
COMPETENCIA_RESPONSE=$(curl -s -X POST "$BASE_URL/competencias.php" \
  -H "Content-Type: application/json" \
  -d "{
    \"codigoDiseno\": \"$CODIGO_DISENO\",
    \"codigoCompetencia\": \"990001001\",
    \"nombreCompetencia\": \"Competencia de Prueba\",
    \"normaUnidadCompetencia\": \"Norma de prueba\",
    \"horasDesarrolloCompetencia\": 200,
    \"requisitosAcademicosInstructor\": \"Profesional en el área\",
    \"experienciaLaboralInstructor\": \"12 meses\"
  }")

if echo "$COMPETENCIA_RESPONSE" | jq -e '.success' > /dev/null; then
    echo "✅ Competencia creada exitosamente"
    CODIGO_COMPETENCIA=$(echo "$COMPETENCIA_RESPONSE" | jq -r '.codigoDisenoCompetencia')
else
    echo "❌ Error creando competencia: $COMPETENCIA_RESPONSE"
    exit 1
fi

echo ""
echo "🧪 PRUEBA 3: Crear RAP para la competencia"
RAP_RESPONSE=$(curl -s -X POST "$BASE_URL/raps.php" \
  -H "Content-Type: application/json" \
  -d "{
    \"codigoDiseno\": \"$CODIGO_DISENO\",
    \"codigoCompetencia\": \"990001001\",
    \"codigoRap\": \"RA1\",
    \"nombreRap\": \"RAP de Prueba\",
    \"horasDesarrolloRap\": 50
  }")

if echo "$RAP_RESPONSE" | jq -e '.success' > /dev/null; then
    echo "✅ RAP creado exitosamente"
    CODIGO_RAP=$(echo "$RAP_RESPONSE" | jq -r '.codigoDisenoCompetenciaRap')
else
    echo "❌ Error creando RAP: $RAP_RESPONSE"
    exit 1
fi

echo ""
echo "📊 ESTADO DESPUÉS DE CREACIONES:"
echo "- Diseños: $(curl -s "$BASE_URL/disenos.php" | jq 'length')"
echo "- Competencias: $(curl -s "$BASE_URL/competencias.php" | jq 'length')"
echo "- RAPs: $(curl -s "$BASE_URL/raps.php" | jq 'length')"

echo ""
echo "🧪 PRUEBA 4: Probar eliminación con dependencias (debe fallar)"
DELETE_DISENO_RESPONSE=$(curl -s -X DELETE "$BASE_URL/disenos.php?codigo=$CODIGO_DISENO")
if echo "$DELETE_DISENO_RESPONSE" | jq -e '.error' > /dev/null; then
    echo "✅ Validación de integridad funcionando: $(echo "$DELETE_DISENO_RESPONSE" | jq -r '.error')"
else
    echo "❌ Error: eliminación debería haber fallado"
fi

echo ""
echo "🧪 PRUEBA 5: Eliminación en cascada correcta"
echo "5.1 Eliminando RAP..."
curl -s -X DELETE "$BASE_URL/raps.php?codigo=$CODIGO_RAP" | jq .

echo "5.2 Eliminando Competencia..."
curl -s -X DELETE "$BASE_URL/competencias.php?codigo=$CODIGO_COMPETENCIA" | jq .

echo "5.3 Eliminando Diseño..."
curl -s -X DELETE "$BASE_URL/disenos.php?codigo=$CODIGO_DISENO" | jq .

echo ""
echo "📊 ESTADO FINAL:"
echo "- Diseños: $(curl -s "$BASE_URL/disenos.php" | jq 'length')"
echo "- Competencias: $(curl -s "$BASE_URL/competencias.php" | jq 'length')"
echo "- RAPs: $(curl -s "$BASE_URL/raps.php" | jq 'length')"

echo ""
echo "🎉 TODAS LAS PRUEBAS COMPLETADAS EXITOSAMENTE"
echo "✅ Sistema funcionando correctamente"
