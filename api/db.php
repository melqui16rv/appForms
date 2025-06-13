<?php
// Incluir configuración
require_once __DIR__ . '/../config.php';

$config = getConfig();

try {
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}";
    $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if ($config['debug']) {
        die('Error de conexión: ' . $e->getMessage());
    } else {
        error_log('Error de conexión a BD: ' . $e->getMessage());
        die('Error de conexión a la base de datos');
    }
}
?>
