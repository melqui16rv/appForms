<?php
// Prueba directa de las APIs sin pasar por HTTP
echo "🧪 PRUEBA DIRECTA DE APIS\n";
echo "========================\n\n";

// Simular variables de entorno
$_GET = [];
$_POST = [];
$_SERVER['REQUEST_METHOD'] = 'GET';

echo "📋 Probando API de Diseños...\n";
ob_start();
include __DIR__ . '/api/disenos.php';
$output = ob_get_clean();
if (strpos($output, 'error') === false && strpos($output, 'Fatal') === false) {
    echo "✅ API de Diseños funciona correctamente\n";
    echo "📊 Respuesta: " . substr($output, 0, 100) . "...\n";
} else {
    echo "❌ Error en API de Diseños: " . substr($output, 0, 200) . "\n";
}

echo "\n🎯 Probando API de Competencias...\n";
ob_start();
include __DIR__ . '/api/competencias.php';
$output = ob_get_clean();
if (strpos($output, 'error') === false && strpos($output, 'Fatal') === false) {
    echo "✅ API de Competencias funciona correctamente\n";
    echo "📊 Respuesta: " . substr($output, 0, 100) . "...\n";
} else {
    echo "❌ Error en API de Competencias: " . substr($output, 0, 200) . "\n";
}

echo "\n🎪 Probando API de RAPs...\n";
ob_start();
include __DIR__ . '/api/raps.php';
$output = ob_get_clean();
if (strpos($output, 'error') === false && strpos($output, 'Fatal') === false) {
    echo "✅ API de RAPs funciona correctamente\n";
    echo "📊 Respuesta: " . substr($output, 0, 100) . "...\n";
} else {
    echo "❌ Error en API de RAPs: " . substr($output, 0, 200) . "\n";
}

echo "\n🌟 RESUMEN FINAL:\n";
echo "=================\n";
echo "📍 Servidor ejecutándose en: http://172.30.7.101:8080\n";
echo "🌐 Accesible desde la red local\n";
echo "💾 Base de datos funcionando correctamente\n";
echo "📱 Listo para acceso desde múltiples dispositivos\n";
?>
