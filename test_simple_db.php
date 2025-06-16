<?php
require_once __DIR__ . '/api/db.php';

echo "🔌 Probando conexión a la base de datos...\n";

try {
    // Probar consulta simple
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM disenos");
    $result = $stmt->fetch();
    echo "✅ Conectado exitosamente a la BD\n";
    echo "📊 Número de diseños en BD: " . $result['count'] . "\n";
    
    // Probar consulta de competencias
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM competencias");
    $result = $stmt->fetch();
    echo "📊 Número de competencias en BD: " . $result['count'] . "\n";
    
    // Probar consulta de RAPs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM raps");
    $result = $stmt->fetch();
    echo "📊 Número de RAPs en BD: " . $result['count'] . "\n";
    
    echo "✅ Base de datos funcionando correctamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
