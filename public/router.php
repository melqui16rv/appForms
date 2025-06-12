<?php
// Router simple para manejar las rutas de la API

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remover el prefijo si existe
$path = ltrim($path, '/');

// Enrutamiento
if (strpos($path, 'api/disenos') === 0) {
    include 'api/disenos.php';
} elseif (strpos($path, 'api/competencias') === 0) {
    include 'api/competencias.php';
} elseif (strpos($path, 'api/raps') === 0) {
    include 'api/raps.php';
} else {
    // Servir archivos estáticos o index.html
    if ($path === '' || $path === 'index.html') {
        include 'index.html';
    } elseif (file_exists($path)) {
        // Servir archivo estático
        $mimeType = '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        switch ($extension) {
            case 'css':
                $mimeType = 'text/css';
                break;
            case 'js':
                $mimeType = 'application/javascript';
                break;
            case 'html':
                $mimeType = 'text/html';
                break;
            default:
                $mimeType = 'application/octet-stream';
        }
        
        header("Content-Type: $mimeType");
        readfile($path);
    } else {
        http_response_code(404);
        echo "404 - Archivo no encontrado";
    }
}
?>
