<?php
/**
 * Test de redirección específico para detectar problemas en appscide.com
 * Uso: subir este archivo como test_redireccion.php y acceder desde el navegador
 */

// Headers para debugging
header('X-Debug-File: test_redireccion.php');
header('X-Debug-Time: ' . date('Y-m-d H:i:s'));

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Redirección - appForms</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test { background: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .warning { background: #fff3cd; color: #856404; }
        .info { background: #d1ecf1; color: #0c5460; }
        button { padding: 10px 15px; margin: 5px; border: none; border-radius: 3px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        #results { margin-top: 20px; }
    </style>
</head>
<body>
    <h1>🧪 Test de Redirección - appForms</h1>
    
    <div class="test info">
        <h3>Información de la petición actual</h3>
        <p><strong>URL actual:</strong> <?= (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?></p>
        <p><strong>Archivo ejecutado:</strong> <?= __FILE__ ?></p>
        <p><strong>Directorio:</strong> <?= __DIR__ ?></p>
        <p><strong>HTTP_HOST:</strong> <?= $_SERVER['HTTP_HOST'] ?></p>
        <p><strong>REQUEST_URI:</strong> <?= $_SERVER['REQUEST_URI'] ?></p>
        <p><strong>SCRIPT_NAME:</strong> <?= $_SERVER['SCRIPT_NAME'] ?></p>
    </div>

    <?php
    // Verificar si hay redirecciones
    $redirects = [];
    foreach ($_SERVER as $key => $value) {
        if (strpos($key, 'REDIRECT_') === 0) {
            $redirects[$key] = $value;
        }
    }
    
    if (!empty($redirects)) {
        echo '<div class="test error">';
        echo '<h3>⚠️ Redirecciones detectadas</h3>';
        foreach ($redirects as $key => $value) {
            echo "<p><strong>$key:</strong> $value</p>";
        }
        echo '</div>';
    } else {
        echo '<div class="test success"><h3>✅ No se detectaron redirecciones</h3></div>';
    }
    ?>

    <div class="test">
        <h3>🔗 Tests de URLs</h3>
        <button class="btn-primary" onclick="testURL('index.php')">Test index.php</button>
        <button class="btn-primary" onclick="testURL('config.php')">Test config.php</button>
        <button class="btn-primary" onclick="testURL('api/disenos.php')">Test API Diseños</button>
        <button class="btn-success" onclick="testURL('diagnostico.php')">Test Diagnóstico</button>
        <button class="btn-warning" onclick="testCurrentURL()">Test URL Actual</button>
    </div>

    <div id="results"></div>

    <script>
        async function testURL(url) {
            const resultsDiv = document.getElementById('results');
            const fullURL = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/') + url;
            
            resultsDiv.innerHTML += `<div class="test info"><h4>Testing: ${fullURL}</h4><p>Cargando...</p></div>`;
            
            try {
                const response = await fetch(fullURL, {
                    method: 'HEAD',
                    redirect: 'manual' // No seguir redirecciones automáticamente
                });
                
                let status = response.status;
                let statusText = response.statusText;
                let className = 'success';
                
                if (status >= 300 && status < 400) {
                    className = 'warning';
                    const location = response.headers.get('Location');
                    statusText += ` - Redirige a: ${location}`;
                } else if (status >= 400) {
                    className = 'error';
                }
                
                resultsDiv.innerHTML += `
                    <div class="test ${className}">
                        <h4>${url}</h4>
                        <p><strong>Estado:</strong> ${status} ${statusText}</p>
                        <p><strong>URL completa:</strong> ${fullURL}</p>
                        <p><strong>Headers relevantes:</strong></p>
                        <ul>
                            <li><strong>Content-Type:</strong> ${response.headers.get('Content-Type') || 'N/A'}</li>
                            <li><strong>Server:</strong> ${response.headers.get('Server') || 'N/A'}</li>
                            <li><strong>Location:</strong> ${response.headers.get('Location') || 'N/A'}</li>
                            <li><strong>X-Debug-File:</strong> ${response.headers.get('X-Debug-File') || 'N/A'}</li>
                        </ul>
                    </div>
                `;
                
            } catch (error) {
                resultsDiv.innerHTML += `
                    <div class="test error">
                        <h4>${url} - ERROR</h4>
                        <p><strong>Error:</strong> ${error.message}</p>
                    </div>
                `;
            }
        }
        
        function testCurrentURL() {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML += `
                <div class="test info">
                    <h4>Información de la URL actual</h4>
                    <p><strong>window.location.href:</strong> ${window.location.href}</p>
                    <p><strong>window.location.origin:</strong> ${window.location.origin}</p>
                    <p><strong>window.location.pathname:</strong> ${window.location.pathname}</p>
                    <p><strong>document.referrer:</strong> ${document.referrer}</p>
                    <p><strong>User Agent:</strong> ${navigator.userAgent}</p>
                </div>
            `;
        }
        
        // Test automático al cargar
        setTimeout(() => {
            console.log('🔍 Iniciando tests automáticos...');
            testCurrentURL();
        }, 1000);
    </script>

    <div class="test">
        <h3>📋 Instrucciones de uso</h3>
        <ol>
            <li>Sube este archivo como <code>test_redireccion.php</code> en tu carpeta appForms</li>
            <li>Accede a <code>https://appscide.com/appForms/test_redireccion.php</code></li>
            <li>Ejecuta los tests haciendo clic en los botones</li>
            <li>Observa si hay redirecciones no deseadas</li>
            <li>Comparte los resultados para obtener ayuda específica</li>
        </ol>
    </div>

    <div class="test warning">
        <h3>🎯 ¿Qué buscar?</h3>
        <ul>
            <li><strong>Estado 301/302:</strong> Indica redirección</li>
            <li><strong>Headers REDIRECT_*:</strong> Muestran redirecciones del servidor</li>
            <li><strong>Location header:</strong> Destino de la redirección</li>
            <li><strong>URL diferente:</strong> Si la URL mostrada no coincide con la esperada</li>
        </ul>
    </div>
</body>
</html>
