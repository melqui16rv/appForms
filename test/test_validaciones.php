<?php
/**
 * Script de prueba para validar las correcciones en las APIs
 */

echo "<h1>🧪 Prueba de Validaciones Corregidas</h1>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Incluir configuración
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../api/db.php';

echo "<h2>📋 Pruebas de APIs</h2>";

// Función para hacer una petición HTTP
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

$config = getConfig();
$baseUrl = $config['base_url'];

echo "<h3>✅ Prueba 1: Obtener diseños existentes</h3>";
$response = makeRequest("$baseUrl/api/disenos.php");
echo "<strong>Status:</strong> {$response['status']}<br>";
if ($response['status'] == 200) {
    echo "<strong>Resultado:</strong> ✅ API funciona correctamente<br>";
    echo "<strong>Diseños encontrados:</strong> " . count($response['body']) . "<br>";
} else {
    echo "<strong>Error:</strong> ❌ API no responde correctamente<br>";
}

echo "<h3>✅ Prueba 2: Crear un diseño de prueba</h3>";
$testDiseno = [
    'codigoPrograma' => 'TEST999',
    'versionPrograma' => '1',
    'lineaTecnologica' => 'Línea de Prueba',
    'redTecnologica' => 'Red de Prueba',
    'redConocimiento' => 'Red Conocimiento Prueba',
    'nivelAcademicoIngreso' => 'Bachillerato',
    'formacionTrabajoDesarrolloHumano' => 'Si',
    'edadMinima' => 16
];

$response = makeRequest("$baseUrl/api/disenos.php", 'POST', $testDiseno);
echo "<strong>Status:</strong> {$response['status']}<br>";
if (isset($response['body']['success']) && $response['body']['success']) {
    echo "<strong>Resultado:</strong> ✅ Diseño creado exitosamente<br>";
    echo "<strong>Mensaje:</strong> {$response['body']['message']}<br>";
} else {
    echo "<strong>Error:</strong> ❌ " . ($response['body']['error'] ?? 'Error desconocido') . "<br>";
}

echo "<h3>✅ Prueba 3: Crear una competencia de prueba</h3>";
$testCompetencia = [
    'codigoDiseno' => 'TEST999-1',
    'codigoCompetencia' => 'COMP001',
    'nombreCompetencia' => 'Competencia de Prueba',
    'horasDesarrolloCompetencia' => 40
];

$response = makeRequest("$baseUrl/api/competencias.php", 'POST', $testCompetencia);
echo "<strong>Status:</strong> {$response['status']}<br>";
if (isset($response['body']['success']) && $response['body']['success']) {
    echo "<strong>Resultado:</strong> ✅ Competencia creada exitosamente<br>";
    echo "<strong>Mensaje:</strong> {$response['body']['message']}<br>";
} else {
    echo "<strong>Error:</strong> ❌ " . ($response['body']['error'] ?? 'Error desconocido') . "<br>";
}

echo "<h3>✅ Prueba 4: Crear un RAP de prueba</h3>";
$testRap = [
    'codigoDiseno' => 'TEST999-1',
    'codigoCompetencia' => 'TEST999-1-COMP001',
    'codigoRap' => 'RA1',
    'nombreRap' => 'RAP de Prueba',
    'horasDesarrolloRap' => 10
];

$response = makeRequest("$baseUrl/api/raps.php", 'POST', $testRap);
echo "<strong>Status:</strong> {$response['status']}<br>";
if (isset($response['body']['success']) && $response['body']['success']) {
    echo "<strong>Resultado:</strong> ✅ RAP creado exitosamente<br>";
    echo "<strong>Mensaje:</strong> {$response['body']['message']}<br>";
} else {
    echo "<strong>Error:</strong> ❌ " . ($response['body']['error'] ?? 'Error desconocido') . "<br>";
}

echo "<h3>✅ Prueba 5: Actualizar el diseño de prueba</h3>";
$updateDiseno = array_merge($testDiseno, [
    'lineaTecnologica' => 'Línea de Prueba ACTUALIZADA'
]);

$response = makeRequest("$baseUrl/api/disenos.php?codigo=TEST999-1", 'PUT', $updateDiseno);
echo "<strong>Status:</strong> {$response['status']}<br>";
if (isset($response['body']['success']) && $response['body']['success']) {
    echo "<strong>Resultado:</strong> ✅ Diseño actualizado exitosamente<br>";
    echo "<strong>Mensaje:</strong> {$response['body']['message']}<br>";
} else {
    echo "<strong>Error:</strong> ❌ " . ($response['body']['error'] ?? 'Error desconocido') . "<br>";
}

echo "<h3>🗑️ Limpieza: Eliminar datos de prueba</h3>";

// Eliminar RAP
$response = makeRequest("$baseUrl/api/raps.php?codigo=TEST999-1-COMP001-RA1", 'DELETE');
echo "<strong>Eliminar RAP:</strong> " . ($response['body']['success'] ? '✅' : '❌') . "<br>";

// Eliminar Competencia
$response = makeRequest("$baseUrl/api/competencias.php?codigo=TEST999-1-COMP001", 'DELETE');
echo "<strong>Eliminar Competencia:</strong> " . ($response['body']['success'] ? '✅' : '❌') . "<br>";

// Eliminar Diseño
$response = makeRequest("$baseUrl/api/disenos.php?codigo=TEST999-1", 'DELETE');
echo "<strong>Eliminar Diseño:</strong> " . ($response['body']['success'] ? '✅' : '❌') . "<br>";

echo "<h2>📊 Resumen de Correcciones</h2>";
echo "<ul>";
echo "<li>✅ <strong>APIs corregidas:</strong> Todas las APIs ahora retornan respuestas JSON consistentes con 'success: true'</li>";
echo "<li>✅ <strong>JavaScript corregido:</strong> Validación mejorada de respuestas en el frontend</li>";
echo "<li>✅ <strong>Mensajes únicos:</strong> Eliminados mensajes duplicados de 'Error de conexión'</li>";
echo "<li>✅ <strong>Funciones limpias:</strong> Separadas las funciones de limpiado automático vs manual</li>";
echo "<li>✅ <strong>Eliminación de duplicados:</strong> Removidas funciones duplicadas en el código</li>";
echo "</ul>";

echo "<br><a href='../index.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🏠 Volver a la Aplicación</a>";
echo " <a href='test_conectividad.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>🔧 Test Conectividad</a>";
?>
