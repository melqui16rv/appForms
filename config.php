<?php
/**
 * Archivo de configuración para la aplicación de diseños curriculares
 * Detecta automáticamente el entorno y configura las rutas apropiadas
 */

// Detectar el entorno
$isLocal = (
    (isset($_SERVER['HTTP_HOST']) && (
        strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
        strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
        strpos($_SERVER['HTTP_HOST'], '::1') !== false ||
        strpos($_SERVER['HTTP_HOST'], '172.30.5.255') !== false // Agregar tu IP local
    )) || 
    php_sapi_name() === 'cli' || 
    !isset($_SERVER['HTTP_HOST'])
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
    // Configuración local
    $config['db'] = [
        'host' => '172.30.5.255', // Tu IP para conexiones remotas
        'dbname' => 'disenos_curriculares',
        'username' => 'admin_remoto',
        'password' => 'admin123',
        'charset' => 'utf8mb4'
    ];
    $config['base_url'] = 'http://172.30.5.255:8080';
} else {
    // Configuración de producción
    $config['db'] = [
        'host' => '172.30.5.255', // Tu IP para conexiones remotas desde otros equipos
        'dbname' => 'disenos_curriculares',
        'username' => 'admin_remoto',
        'password' => 'admin123',
        'charset' => 'utf8mb4'
    ];
    $config['base_url'] = 'https://appscide.com/appForms';
}

// Configuración de errores
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
}

// Función para obtener configuración
function getConfig($key = null) {
    global $config;
    if ($key === null) {
        return $config;
    }
    return isset($config[$key]) ? $config[$key] : null;
}

// Función para detectar la ruta base de la aplicación
function getBasePath() {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = dirname($scriptName);
    
    // Si estamos en la raíz, devolver cadena vacía
    if ($basePath === '/' || $basePath === '\\') {
        return '';
    }
    
    return $basePath;
}

// Función para generar URLs absolutas
function url($path = '') {
    $config = getConfig();
    $basePath = getBasePath();
    
    if ($config['environment'] === 'local') {
        return $config['base_url'] . '/' . ltrim($path, '/');
    } else {
        return $config['base_url'] . '/' . ltrim($path, '/');
    }
}

// Función para generar rutas de API
function apiUrl($endpoint = '') {
    return url('api/' . ltrim($endpoint, '/'));
}
