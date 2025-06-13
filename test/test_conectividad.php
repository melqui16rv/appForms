<?php
/**
 * Script de prueba de conectividad para la red local
 */

echo "<h1>🧪 Prueba de Conectividad - Diseños Curriculares</h1>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Incluir configuración
require_once __DIR__ . '/config.php';
$config = getConfig();

echo "<h2>📋 Configuración Actual</h2>";
echo "<ul>";
echo "<li><strong>Entorno:</strong> " . $config['environment'] . "</li>";
echo "<li><strong>Base URL:</strong> " . $config['base_url'] . "</li>";
echo "<li><strong>Host DB:</strong> " . $config['db']['host'] . "</li>";
echo "<li><strong>Usuario DB:</strong> " . $config['db']['username'] . "</li>";
echo "</ul>";

echo "<h2>🔌 Prueba de Base de Datos</h2>";

try {
    include 'api/db.php';
    echo "✅ <strong>Conexión a BD:</strong> EXITOSA<br>";
    
    // Contar registros en cada tabla
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM diseños");
    $disenos = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM competencias");
    $competencias = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM raps");
    $raps = $stmt->fetch()['total'];
    
    echo "📊 <strong>Registros:</strong> {$disenos} diseños, {$competencias} competencias, {$raps} RAPs<br>";
    
} catch (Exception $e) {
    echo "❌ <strong>Error de BD:</strong> " . $e->getMessage() . "<br>";
}

echo "<h2>🌐 Información de Red</h2>";
echo "<ul>";
echo "<li><strong>IP del servidor:</strong> " . $_SERVER['SERVER_ADDR'] ?? 'No disponible' . "</li>";
echo "<li><strong>Puerto:</strong> " . $_SERVER['SERVER_PORT'] ?? 'No disponible' . "</li>";
echo "<li><strong>Host solicitado:</strong> " . $_SERVER['HTTP_HOST'] ?? 'No disponible' . "</li>";
echo "</ul>";

echo "<h2>🚀 Para conectarse desde otros equipos:</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<strong>URL de acceso:</strong> <code>http://172.30.5.255:8080</code><br>";
echo "<strong>URL de prueba:</strong> <code>http://172.30.5.255:8080/test_conectividad.php</code><br>";
echo "<em>Nota: Asegúrense de estar en la misma red WiFi/LAN</em>";
echo "</div>";

echo "<br><a href='index.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🏠 Ir a la Aplicación</a>";
?>
