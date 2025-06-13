<?php
echo "<h1>🔍 Diagnóstico de Base de Datos</h1>";

// Incluir configuración
require_once __DIR__ . '/config.php';
$config = getConfig();

echo "<h2>📋 Configuración detectada:</h2>";
echo "<pre>";
print_r($config);
echo "</pre>";

echo "<h2>🌐 Variables del servidor:</h2>";
echo "<strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'No definido') . "<br>";
echo "<strong>SERVER_NAME:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'No definido') . "<br>";
echo "<strong>SERVER_ADDR:</strong> " . ($_SERVER['SERVER_ADDR'] ?? 'No definido') . "<br>";

echo "<h2>🔌 Prueba de conexión paso a paso:</h2>";

$host = $config['db']['host'];
$dbname = $config['db']['dbname'];
$username = $config['db']['username'];
$password = $config['db']['password'];

echo "Intentando conectar a:<br>";
echo "Host: <strong>$host</strong><br>";
echo "Base de datos: <strong>$dbname</strong><br>";
echo "Usuario: <strong>$username</strong><br>";
echo "Contraseña: <strong>" . str_repeat('*', strlen($password)) . "</strong><br><br>";

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    echo "DSN: <code>$dsn</code><br><br>";
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ <strong>Conexión exitosa!</strong><br><br>";
    
    // Verificar qué tablas existen
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>📊 Tablas encontradas:</h3>";
    if (empty($tables)) {
        echo "❌ No se encontraron tablas en la base de datos.<br>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li><strong>$table</strong>";
            
            // Contar registros
            try {
                $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
                $count = $countStmt->fetchColumn();
                echo " - $count registros";
            } catch (Exception $e) {
                echo " - Error al contar: " . $e->getMessage();
            }
            echo "</li>";
        }
        echo "</ul>";
    }
    
} catch (PDOException $e) {
    echo "❌ <strong>Error de conexión:</strong><br>";
    echo "Código: " . $e->getCode() . "<br>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
}

echo "<br><a href='test_conectividad.php'>← Volver a prueba de conectividad</a>";
?>
