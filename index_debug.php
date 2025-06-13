<?php 
// Sistema de debugging para identificar problemas
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Información de debugging
$debug_info = [
    'timestamp' => date('Y-m-d H:i:s'),
    'server_name' => $_SERVER['SERVER_NAME'] ?? 'N/A',
    'http_host' => $_SERVER['HTTP_HOST'] ?? 'N/A',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'current_file' => __FILE__,
    'current_dir' => __DIR__,
    'php_version' => PHP_VERSION,
];

// Verificar si config.php existe y es accesible
$config_exists = file_exists(__DIR__ . '/config.php');
$config_readable = $config_exists ? is_readable(__DIR__ . '/config.php') : false;

// Intentar cargar configuración
$config_loaded = false;
$config_error = '';
if ($config_readable) {
    try {
        require_once __DIR__ . '/config.php';
        $config = getConfig();
        $config_loaded = true;
    } catch (Exception $e) {
        $config_error = $e->getMessage();
    }
}

// Verificar archivos críticos
$critical_files = [
    'config.php' => file_exists(__DIR__ . '/config.php'),
    'js/main.js' => file_exists(__DIR__ . '/js/main.js'),
    'styles/main.css' => file_exists(__DIR__ . '/styles/main.css'),
    'api/db.php' => file_exists(__DIR__ . '/api/db.php'),
    'api/disenos.php' => file_exists(__DIR__ . '/api/disenos.php'),
    'api/competencias.php' => file_exists(__DIR__ . '/api/competencias.php'),
    'api/raps.php' => file_exists(__DIR__ . '/api/raps.php'),
];

// Verificar si estamos en modo debug
$show_debug = isset($_GET['debug']) || (isset($config) && $config['debug']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Diseños Curriculares - <?php echo $config_loaded ? $config['environment'] : 'ERROR'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css">
    
    <?php if ($show_debug): ?>
    <style>
        .debug-panel {
            position: fixed;
            top: 10px;
            right: 10px;
            background: #000;
            color: #0f0;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            max-width: 400px;
            max-height: 80vh;
            overflow-y: auto;
            z-index: 9999;
            border: 2px solid #0f0;
        }
        .debug-toggle {
            position: fixed;
            top: 10px;
            right: 420px;
            z-index: 10000;
        }
        .debug-success { color: #0f0; }
        .debug-error { color: #f00; }
        .debug-warning { color: #ff0; }
    </style>
    <?php endif; ?>
</head>
<body>
    <?php if ($show_debug): ?>
    <button class="btn btn-sm btn-info debug-toggle" onclick="toggleDebug()">
        <i class="fas fa-bug"></i> Debug
    </button>
    <div id="debugPanel" class="debug-panel">
        <h6><i class="fas fa-bug"></i> DEBUG INFO</h6>
        <hr>
        <strong>Servidor:</strong><br>
        <?php foreach ($debug_info as $key => $value): ?>
        • <?php echo htmlspecialchars($key); ?>: <?php echo htmlspecialchars($value); ?><br>
        <?php endforeach; ?>
        
        <hr>
        <strong>Configuración:</strong><br>
        • Config existe: <span class="<?php echo $config_exists ? 'debug-success' : 'debug-error'; ?>">
            <?php echo $config_exists ? 'SÍ' : 'NO'; ?>
        </span><br>
        • Config legible: <span class="<?php echo $config_readable ? 'debug-success' : 'debug-error'; ?>">
            <?php echo $config_readable ? 'SÍ' : 'NO'; ?>
        </span><br>
        • Config cargado: <span class="<?php echo $config_loaded ? 'debug-success' : 'debug-error'; ?>">
            <?php echo $config_loaded ? 'SÍ' : 'NO'; ?>
        </span><br>
        <?php if ($config_error): ?>
        • Error: <span class="debug-error"><?php echo htmlspecialchars($config_error); ?></span><br>
        <?php endif; ?>
        <?php if ($config_loaded): ?>
        • Entorno: <span class="debug-success"><?php echo htmlspecialchars($config['environment']); ?></span><br>
        • Base URL: <span class="debug-success"><?php echo htmlspecialchars($config['base_url']); ?></span><br>
        <?php endif; ?>
        
        <hr>
        <strong>Archivos:</strong><br>
        <?php foreach ($critical_files as $file => $exists): ?>
        • <?php echo htmlspecialchars($file); ?>: <span class="<?php echo $exists ? 'debug-success' : 'debug-error'; ?>">
            <?php echo $exists ? 'OK' : 'FALTA'; ?>
        </span><br>
        <?php endforeach; ?>
        
        <hr>
        <strong>APIs Test:</strong><br>
        <button class="btn btn-xs btn-success" onclick="testAPI('disenos')">Test Diseños</button>
        <button class="btn btn-xs btn-warning" onclick="testAPI('competencias')">Test Comp.</button>
        <button class="btn btn-xs btn-info" onclick="testAPI('raps')">Test RAPs</button>
        <div id="apiResults" style="margin-top: 10px; font-size: 10px;"></div>
    </div>
    
    <script>
        function toggleDebug() {
            const panel = document.getElementById('debugPanel');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }
        
        async function testAPI(endpoint) {
            const results = document.getElementById('apiResults');
            results.innerHTML += `<br>Testing ${endpoint}...`;
            
            try {
                const response = await fetch(`api/${endpoint}.php`);
                const data = await response.json();
                results.innerHTML += `<br><span class="debug-success">${endpoint}: OK (${data.length || 'N/A'} items)</span>`;
            } catch (error) {
                results.innerHTML += `<br><span class="debug-error">${endpoint}: ERROR - ${error.message}</span>`;
            }
        }
        
        // Test automático al cargar
        window.addEventListener('load', function() {
            if (document.getElementById('debugPanel')) {
                setTimeout(() => {
                    testAPI('disenos');
                    testAPI('competencias');
                    testAPI('raps');
                }, 1000);
            }
        });
    </script>
    <?php endif; ?>

    <!-- CONTENIDO PRINCIPAL DE LA APLICACIÓN -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-graduation-cap me-2"></i>Diseños Curriculares</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('disenos')">
                            <i class="fas fa-file-alt me-1"></i>Diseños
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('competencias')">
                            <i class="fas fa-cogs me-1"></i>Competencias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('raps')">
                            <i class="fas fa-tasks me-1"></i>RAPs
                        </a>
                    </li>
                    <?php if ($show_debug): ?>
                    <li class="nav-item">
                        <a class="nav-link text-warning" href="test_config.php">
                            <i class="fas fa-bug me-1"></i>Test Config
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Alertas globales -->
        <div id="globalAlert" class="alert alert-dismissible fade show" style="display: none;">
            <span id="globalAlertText"></span>
            <button type="button" class="btn-close" onclick="hideGlobalAlert()"></button>
        </div>

        <?php if (!$config_loaded): ?>
        <!-- Mensaje de error si no se puede cargar la configuración -->
        <div class="alert alert-danger">
            <h4><i class="fas fa-exclamation-triangle"></i> Error de Configuración</h4>
            <p><strong>No se pudo cargar la configuración de la aplicación.</strong></p>
            <ul>
                <li>Config existe: <?php echo $config_exists ? '✅ SÍ' : '❌ NO'; ?></li>
                <li>Config legible: <?php echo $config_readable ? '✅ SÍ' : '❌ NO'; ?></li>
                <?php if ($config_error): ?>
                <li>Error: <code><?php echo htmlspecialchars($config_error); ?></code></li>
                <?php endif; ?>
            </ul>
            <p>
                <a href="?debug=1" class="btn btn-warning">
                    <i class="fas fa-bug"></i> Activar Modo Debug
                </a>
                <a href="test_config.php" class="btn btn-info">
                    <i class="fas fa-cog"></i> Test Configuración
                </a>
            </p>
        </div>
        <?php else: ?>
        
        <!-- MENSAJE DE ESTADO -->
        <div class="alert alert-success alert-dismissible fade show">
            <h4><i class="fas fa-check-circle"></i> Aplicación Cargada Correctamente</h4>
            <p>
                <strong>Entorno:</strong> <?php echo htmlspecialchars($config['environment']); ?> |
                <strong>Versión:</strong> <?php echo htmlspecialchars($config['version']); ?>
                <?php if ($config['debug']): ?>
                | <span class="text-warning"><i class="fas fa-bug"></i> Modo Debug Activo</span>
                <?php endif; ?>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Aquí va el resto del contenido de la aplicación -->
        <!-- Por brevedad, incluiremos solo la estructura básica -->
        <div class="row">
            <div class="col-12">
                <h1><i class="fas fa-graduation-cap"></i> Sistema de Diseños Curriculares</h1>
                <p class="lead">Gestión integral de diseños curriculares, competencias y RAPs</p>
                
                <!-- Las secciones de formularios irán aquí -->
                <div id="disenosSection" class="section active">
                    <h2>Sección de Diseños</h2>
                    <p>Esta sección se cargará dinámicamente...</p>
                </div>
                
                <div id="competenciasSection" class="section">
                    <h2>Sección de Competencias</h2>
                    <p>Esta sección se cargará dinámicamente...</p>
                </div>
                
                <div id="rapsSection" class="section">
                    <h2>Sección de RAPs</h2>
                    <p>Esta sección se cargará dinámicamente...</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if ($config_loaded): ?>
    <!-- Solo cargar el JS principal si la configuración está bien -->
    <script>
        // Configuración para JavaScript
        window.APP_CONFIG = {
            environment: '<?php echo $config['environment']; ?>',
            base_url: '<?php echo $config['base_url']; ?>',
            debug: <?php echo $config['debug'] ? 'true' : 'false'; ?>
        };
        
        console.log('App Config:', window.APP_CONFIG);
    </script>
    <script src="js/main.js"></script>
    <?php else: ?>
    <script>
        console.error('No se pudo cargar la configuración de la aplicación');
    </script>
    <?php endif; ?>
    
    <script>
        function hideGlobalAlert() {
            document.getElementById('globalAlert').style.display = 'none';
        }
        
        function showSection(sectionName) {
            // Función básica de navegación
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(sectionName + 'Section').classList.add('active');
        }
        
        // Log de carga de página
        console.log('Página cargada:', {
            url: window.location.href,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent
        });
    </script>
</body>
</html>
