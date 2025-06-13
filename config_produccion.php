<?php
/**
 * Configuración de producción para appscide.com
 * Este archivo debe reemplazar config.php una vez configurada la base de datos
 */

// Detectar el entorno
$isLocal = (
    strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
    strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
    strpos($_SERVER['HTTP_HOST'], '::1') !== false
);

// Configuración base
$config = [
    'app_name' => 'Sistema de Diseños Curriculares',
    'version' => '1.0.0',
    'environment' => $isLocal ? 'local' : 'production',
    'debug' => $isLocal,
];

// Configuración de base de datos
if ($isLocal) {
    // Configuración local (desarrollo)
    $config['db'] = [
        'host' => 'localhost',
        'dbname' => 'disenos_curriculares',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];
    $config['base_url'] = 'http://localhost:8081';
} else {
    // Configuración de producción (appscide.com)
    // INSTRUCCIONES PARA CONFIGURAR EN CPANEL:
    // 1. Crear base de datos en cPanel MySQL Databases
    // 2. Crear usuario y asignar privilegios
    // 3. Actualizar los valores below con los datos reales
    
    $config['db'] = [
        'host' => 'localhost', // Mantener como localhost en cPanel
        'dbname' => 'appscide_disenos', // Cambiar por: nombredecuenta_nombredb
        'username' => 'appscide_user', // Cambiar por: nombredecuenta_usuario
        'password' => 'PASSWORD_SEGURO', // Cambiar por la contraseña real
        'charset' => 'utf8mb4'
    ];
    $config['base_url'] = 'https://appscide.com/appForms';
}

// Configuración de errores
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
}

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Función para debug seguro
function debug_log($message, $data = null) {
    global $config;
    if ($config['debug']) {
        $logMessage = date('Y-m-d H:i:s') . ' - ' . $message;
        if ($data !== null) {
            $logMessage .= ' - ' . print_r($data, true);
        }
        error_log($logMessage);
    }
}

// Función para obtener URL base dinámicamente
function getBaseURL() {
    global $config;
    return $config['base_url'];
}

// Función para verificar conexión de base de datos
function testDatabaseConnection() {
    global $config;
    try {
        $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}";
        $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ['success' => true, 'message' => 'Conexión exitosa'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Variables globales de configuración
define('APP_NAME', $config['app_name']);
define('APP_VERSION', $config['version']);
define('APP_ENVIRONMENT', $config['environment']);
define('APP_DEBUG', $config['debug']);
define('BASE_URL', $config['base_url']);

// Configuración adicional para producción
if (!$isLocal) {
    // Configurar headers de seguridad
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    
    // Configurar cache para archivos estáticos
    if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|woff|woff2|ttf|eot|svg)$/', $_SERVER['REQUEST_URI'])) {
        header('Cache-Control: public, max-age=31536000');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
    }
}

// Información de debug para desarrollo
if ($config['debug']) {
    debug_log('Config loaded', [
        'environment' => $config['environment'],
        'host' => $_SERVER['HTTP_HOST'],
        'request_uri' => $_SERVER['REQUEST_URI'],
        'script_name' => $_SERVER['SCRIPT_NAME']
    ]);
}

?>

<!--
INSTRUCCIONES PARA DESPLIEGUE EN CPANEL:

1. CREAR BASE DE DATOS:
   - Ir a cPanel > MySQL Databases
   - Crear nueva base de datos: "disenos" (el nombre completo será: nombredecuenta_disenos)
   - Crear usuario: "appuser" (el nombre completo será: nombredecuenta_appuser)
   - Asignar usuario a la base de datos con todos los privilegios

2. ACTUALIZAR CONFIGURACIÓN:
   - Editar este archivo (config_produccion.php)
   - Cambiar 'dbname' por el nombre completo: nombredecuenta_disenos
   - Cambiar 'username' por el nombre completo: nombredecuenta_appuser
   - Cambiar 'password' por la contraseña real creada en cPanel

3. IMPORTAR ESQUEMA:
   - Ir a cPanel > phpMyAdmin
   - Seleccionar la base de datos creada
   - Importar el archivo sql/schema.sql

4. RENOMBRAR ARCHIVO:
   - Renombrar config_produccion.php a config.php
   - O copiar el contenido a config.php existente

5. VERIFICAR:
   - Acceder a https://appscide.com/appForms/diagnostico.php
   - Verificar que la conexión de base de datos sea exitosa

EJEMPLO DE CONFIGURACIÓN REAL:
Si tu cuenta de cPanel es "appscide123":
- dbname: "appscide123_disenos"
- username: "appscide123_appuser"
- password: "tu_contraseña_segura"
-->
