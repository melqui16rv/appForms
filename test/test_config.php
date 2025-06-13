<?php
/**
 * Archivo de prueba para verificar la configuración
 * Acceder a: https://appscide.com/appForms/test_config.php
 */

require_once 'config.php';

$config = getConfig();

echo "<h1>Prueba de Configuración - {$config['app_name']}</h1>";
echo "<h2>Entorno: {$config['environment']}</h2>";

echo "<h3>Información del Servidor:</h3>";
echo "<ul>";
echo "<li><strong>HOST:</strong> " . $_SERVER['HTTP_HOST'] . "</li>";
echo "<li><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</li>";
echo "<li><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</li>";
echo "<li><strong>Base Path:</strong> " . getBasePath() . "</li>";
echo "<li><strong>Base URL:</strong> " . $config['base_url'] . "</li>";
echo "</ul>";

echo "<h3>Prueba de Base de Datos:</h3>";
try {
    require_once 'api/db.php';
    echo "✅ <strong style='color: green;'>Conexión a la base de datos exitosa</strong><br>";
    
    // Probar una consulta simple
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM diseños");
    $result = $stmt->fetch();
    echo "✅ <strong style='color: green;'>Consulta exitosa. Total de diseños: {$result['total']}</strong><br>";
    
} catch (Exception $e) {
    echo "❌ <strong style='color: red;'>Error de base de datos: " . $e->getMessage() . "</strong><br>";
}

echo "<h3>Prueba de APIs:</h3>";
$apiEndpoints = ['disenos.php', 'competencias.php', 'raps.php'];

foreach ($apiEndpoints as $endpoint) {
    $apiFile = __DIR__ . "/api/$endpoint";
    if (file_exists($apiFile)) {
        echo "✅ <strong style='color: green;'>API $endpoint existe</strong><br>";
    } else {
        echo "❌ <strong style='color: red;'>API $endpoint NO encontrada</strong><br>";
    }
}

echo "<h3>URLs generadas:</h3>";
echo "<ul>";
echo "<li><strong>Aplicación:</strong> <a href='" . url() . "'>" . url() . "</a></li>";
echo "<li><strong>API Diseños:</strong> <a href='" . apiUrl('disenos.php') . "'>" . apiUrl('disenos.php') . "</a></li>";
echo "<li><strong>API Competencias:</strong> <a href='" . apiUrl('competencias.php') . "'>" . apiUrl('competencias.php') . "</a></li>";
echo "<li><strong>API RAPs:</strong> <a href='" . apiUrl('raps.php') . "'>" . apiUrl('raps.php') . "</a></li>";
echo "</ul>";

echo "<p><a href='index.php'>← Volver a la aplicación</a></p>";
?>
