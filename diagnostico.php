<?php
/**
 * Archivo de diagnóstico completo para detectar problemas de redirección
 * Uso: https://appscide.com/appForms/diagnostico.php
 */

// Headers para evitar cache
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Redirección - appForms</title>
    <style>
        body { 
            font-family: monospace; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        .section { 
            background: white; 
            padding: 15px; 
            margin: 10px 0; 
            border-left: 4px solid #007bff; 
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .error { border-left-color: #dc3545; }
        .success { border-left-color: #28a745; }
        .warning { border-left-color: #ffc107; }
        h2 { margin-top: 0; color: #333; }
        .code { 
            background: #f8f9fa; 
            padding: 10px; 
            border-radius: 4px; 
            border: 1px solid #e9ecef;
            overflow-x: auto;
        }
        .highlight { background: yellow; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0;
        }
        td, th { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        th { background: #f8f9fa; }
        .status { 
            display: inline-block; 
            padding: 2px 8px; 
            border-radius: 12px; 
            font-size: 12px; 
            font-weight: bold;
        }
        .status.ok { background: #d4edda; color: #155724; }
        .status.error { background: #f8d7da; color: #721c24; }
        .status.warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de Redirección - appForms</h1>
    <p><strong>Timestamp:</strong> <?= date('Y-m-d H:i:s') ?> | <strong>Zona horaria:</strong> <?= date_default_timezone_get() ?></p>

    <?php
    // 1. INFORMACIÓN BÁSICA DEL SERVIDOR
    ?>
    <div class="section">
        <h2>🌐 Información del Servidor</h2>
        <table>
            <tr><td><strong>Dominio actual:</strong></td><td class="highlight"><?= $_SERVER['HTTP_HOST'] ?? 'N/A' ?></td></tr>
            <tr><td><strong>URL completa:</strong></td><td class="highlight"><?= (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?></td></tr>
            <tr><td><strong>REQUEST_URI:</strong></td><td><?= $_SERVER['REQUEST_URI'] ?? 'N/A' ?></td></tr>
            <tr><td><strong>SCRIPT_NAME:</strong></td><td><?= $_SERVER['SCRIPT_NAME'] ?? 'N/A' ?></td></tr>
            <tr><td><strong>PHP_SELF:</strong></td><td><?= $_SERVER['PHP_SELF'] ?? 'N/A' ?></td></tr>
            <tr><td><strong>DOCUMENT_ROOT:</strong></td><td><?= $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' ?></td></tr>
            <tr><td><strong>PWD (Current Dir):</strong></td><td><?= getcwd() ?></td></tr>
            <tr><td><strong>__FILE__:</strong></td><td><?= __FILE__ ?></td></tr>
            <tr><td><strong>__DIR__:</strong></td><td><?= __DIR__ ?></td></tr>
        </table>
    </div>

    <?php
    // 2. DETECCIÓN DE REDIRECCIONES
    ?>
    <div class="section">
        <h2>🔄 Detección de Redirecciones</h2>
        <?php
        $redirect_headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'REDIRECT_') === 0) {
                $redirect_headers[$key] = $value;
            }
        }
        
        if (!empty($redirect_headers)) {
            echo '<div class="status error">❌ REDIRECCIONES DETECTADAS</div>';
            echo '<table>';
            foreach ($redirect_headers as $key => $value) {
                echo "<tr><td><strong>$key:</strong></td><td>$value</td></tr>";
            }
            echo '</table>';
        } else {
            echo '<div class="status ok">✅ No se detectaron redirecciones</div>';
        }
        ?>
    </div>

    <?php
    // 3. VERIFICACIÓN DE ARCHIVOS .HTACCESS
    ?>
    <div class="section">
        <h2>📄 Verificación de .htaccess</h2>
        <?php
        $htaccess_paths = [
            __DIR__ . '/.htaccess',
            dirname(__DIR__) . '/.htaccess',
            $_SERVER['DOCUMENT_ROOT'] . '/.htaccess'
        ];
        
        foreach ($htaccess_paths as $path) {
            echo "<h4>📁 $path</h4>";
            if (file_exists($path)) {
                echo '<div class="status ok">✅ Existe</div>';
                $content = file_get_contents($path);
                echo '<div class="code">' . htmlspecialchars($content) . '</div>';
            } else {
                echo '<div class="status warning">⚠️ No existe</div>';
            }
        }
        ?>
    </div>

    <?php
    // 4. VERIFICACIÓN DE ARCHIVOS CRÍTICOS
    ?>
    <div class="section">
        <h2>📂 Verificación de Archivos Críticos</h2>
        <?php
        $critical_files = [
            'index.php',
            'config.php',
            'api/db.php',
            'api/disenos.php',
            'js/main.js',
            'styles/main.css'
        ];
        
        echo '<table>';
        echo '<tr><th>Archivo</th><th>Estado</th><th>Tamaño</th><th>Última modificación</th></tr>';
        foreach ($critical_files as $file) {
            $path = __DIR__ . '/' . $file;
            echo '<tr>';
            echo "<td>$file</td>";
            if (file_exists($path)) {
                echo '<td><div class="status ok">✅ Existe</div></td>';
                echo '<td>' . number_format(filesize($path)) . ' bytes</td>';
                echo '<td>' . date('Y-m-d H:i:s', filemtime($path)) . '</td>';
            } else {
                echo '<td><div class="status error">❌ No existe</div></td>';
                echo '<td>-</td><td>-</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        ?>
    </div>

    <?php
    // 5. CONFIGURACIÓN ACTUAL
    ?>
    <div class="section">
        <h2>⚙️ Configuración Actual</h2>
        <?php
        if (file_exists(__DIR__ . '/config.php')) {
            require_once __DIR__ . '/config.php';
            echo '<div class="status ok">✅ config.php cargado</div>';
            echo '<div class="code">';
            echo '<strong>Entorno:</strong> ' . $config['environment'] . '<br>';
            echo '<strong>Debug:</strong> ' . ($config['debug'] ? 'Activado' : 'Desactivado') . '<br>';
            echo '<strong>Base URL:</strong> ' . $config['base_url'] . '<br>';
            echo '<strong>DB Host:</strong> ' . $config['db']['host'] . '<br>';
            echo '<strong>DB Name:</strong> ' . $config['db']['dbname'] . '<br>';
            echo '</div>';
        } else {
            echo '<div class="status error">❌ config.php no encontrado</div>';
        }
        ?>
    </div>

    <?php
    // 6. TEST DE CONECTIVIDAD DE BASE DE DATOS
    ?>
    <div class="section">
        <h2>🗄️ Test de Base de Datos</h2>
        <?php
        if (isset($config)) {
            try {
                $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}";
                $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo '<div class="status ok">✅ Conexión exitosa</div>';
                
                // Test de tabla
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo '<div class="code"><strong>Tablas encontradas:</strong> ' . implode(', ', $tables) . '</div>';
                
            } catch (PDOException $e) {
                echo '<div class="status error">❌ Error de conexión: ' . $e->getMessage() . '</div>';
            }
        } else {
            echo '<div class="status warning">⚠️ Configuración no disponible</div>';
        }
        ?>
    </div>

    <?php
    // 7. INFORMACIÓN DEL ENTORNO PHP
    ?>
    <div class="section">
        <h2>🐘 Información de PHP</h2>
        <table>
            <tr><td><strong>Versión PHP:</strong></td><td><?= PHP_VERSION ?></td></tr>
            <tr><td><strong>SAPI:</strong></td><td><?= php_sapi_name() ?></td></tr>
            <tr><td><strong>Extensions:</strong></td><td><?= implode(', ', ['pdo_mysql' => extension_loaded('pdo_mysql') ? '✅' : '❌', 'json' => extension_loaded('json') ? '✅' : '❌', 'curl' => extension_loaded('curl') ? '✅' : '❌']) ?></td></tr>
            <tr><td><strong>Error Reporting:</strong></td><td><?= error_reporting() ?></td></tr>
            <tr><td><strong>Display Errors:</strong></td><td><?= ini_get('display_errors') ? 'On' : 'Off' ?></td></tr>
            <tr><td><strong>Memory Limit:</strong></td><td><?= ini_get('memory_limit') ?></td></tr>
            <tr><td><strong>Max Execution Time:</strong></td><td><?= ini_get('max_execution_time') ?>s</td></tr>
        </table>
    </div>

    <?php
    // 8. HEADERS DE RESPUESTA
    ?>
    <div class="section">
        <h2>📋 Headers de la Petición</h2>
        <div class="code">
            <?php
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    echo "<strong>$key:</strong> $value<br>";
                }
            }
            ?>
        </div>
    </div>

    <?php
    // 9. TEST DE APIS
    ?>
    <div class="section">
        <h2>🔌 Test de APIs</h2>
        <div id="apiTests">
            <p>Ejecutando tests...</p>
        </div>
    </div>

    <div class="section">
        <h2>🎯 Diagnóstico Automático</h2>
        <div id="diagnosis">
            <?php
            $issues = [];
            $recommendations = [];

            // Verificar si estamos en producción
            $isProduction = strpos($_SERVER['HTTP_HOST'], 'appscide.com') !== false;
            
            if ($isProduction) {
                echo '<div class="status warning">⚠️ Entorno de PRODUCCIÓN detectado</div>';
                
                // Verificar configuración
                if (isset($config['db']['dbname']) && $config['db']['dbname'] === 'tu_base_de_datos') {
                    $issues[] = "❌ Base de datos no configurada para producción";
                    $recommendations[] = "Actualizar config.php con credenciales reales de cPanel";
                }
                
                // Verificar redirecciones
                if (!empty($redirect_headers)) {
                    $issues[] = "❌ Redirecciones silenciosas detectadas";
                    $recommendations[] = "Revisar .htaccess del dominio raíz y configuración de cPanel";
                }
                
                // Verificar URL actual vs esperada
                $expected_url = 'https://appscide.com/appForms/diagnostico.php';
                $current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                
                if ($current_url !== $expected_url) {
                    $issues[] = "❌ URL no coincide con la esperada";
                    $recommendations[] = "URL actual: $current_url | Esperada: $expected_url";
                }
                
            } else {
                echo '<div class="status ok">✅ Entorno LOCAL detectado</div>';
            }
            
            if (empty($issues)) {
                echo '<div class="status ok">✅ No se detectaron problemas graves</div>';
            } else {
                echo '<h4>🚨 Problemas detectados:</h4><ul>';
                foreach ($issues as $issue) {
                    echo "<li>$issue</li>";
                }
                echo '</ul>';
                
                echo '<h4>💡 Recomendaciones:</h4><ul>';
                foreach ($recommendations as $rec) {
                    echo "<li>$rec</li>";
                }
                echo '</ul>';
            }
            ?>
        </div>
    </div>

    <script>
        // Test de APIs vía JavaScript
        async function testAPIs() {
            const apiTests = document.getElementById('apiTests');
            const apis = [
                { name: 'Diseños', url: 'api/disenos.php' },
                { name: 'Competencias', url: 'api/competencias.php' },
                { name: 'RAPs', url: 'api/raps.php' }
            ];
            
            let html = '<table><tr><th>API</th><th>Estado</th><th>Tiempo</th><th>Respuesta</th></tr>';
            
            for (const api of apis) {
                const start = Date.now();
                try {
                    const response = await fetch(api.url, {
                        method: 'GET',
                        headers: { 'X-Test': 'diagnostico' }
                    });
                    const time = Date.now() - start;
                    const text = await response.text();
                    
                    html += `<tr>
                        <td>${api.name}</td>
                        <td><div class="status ${response.ok ? 'ok' : 'error'}">${response.status} ${response.statusText}</div></td>
                        <td>${time}ms</td>
                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">${text.substring(0, 100)}...</td>
                    </tr>`;
                } catch (error) {
                    html += `<tr>
                        <td>${api.name}</td>
                        <td><div class="status error">ERROR</div></td>
                        <td>-</td>
                        <td>${error.message}</td>
                    </tr>`;
                }
            }
            
            html += '</table>';
            apiTests.innerHTML = html;
        }
        
        // Ejecutar tests al cargar
        testAPIs();
        
        // Información adicional del navegador
        console.log('🔍 Información del navegador:');
        console.log('URL actual:', window.location.href);
        console.log('Referrer:', document.referrer);
        console.log('User Agent:', navigator.userAgent);
    </script>

    <div class="section">
        <h2>📞 Información de Contacto</h2>
        <p>Si continúan los problemas, comparta este diagnóstico completo para obtener ayuda específica.</p>
        <p><strong>Archivo:</strong> <?= __FILE__ ?></p>
        <p><strong>Generado:</strong> <?= date('Y-m-d H:i:s T') ?></p>
    </div>
</body>
</html>
