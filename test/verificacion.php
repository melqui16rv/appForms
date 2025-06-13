<?php
/**
 * Verificación rápida de estado del sistema
 * Archivo simple para confirmar que appForms está funcionando correctamente
 */

// Headers de debugging
header('X-AppForms-Status: OK');
header('X-AppForms-File: verificacion.php');
header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Verificación - appForms</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: rgba(255,255,255,0.1); 
            padding: 30px; 
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        .status { 
            text-align: center; 
            font-size: 3em; 
            margin: 30px 0; 
        }
        .info { 
            background: rgba(255,255,255,0.2); 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0;
        }
        .success { color: #4CAF50; }
        .error { color: #f44336; }
        .warning { color: #ff9800; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
        }
        h1, h2 { text-align: center; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Verificación del Sistema appForms</h1>
        
        <?php
        // Verificaciones básicas
        $checks = [];
        
        // 1. Verificar URL actual
        $currentURL = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $expectedDomain = 'appscide.com';
        $isCorrectDomain = strpos($_SERVER['HTTP_HOST'], $expectedDomain) !== false;
        
        $checks['url'] = [
            'name' => 'URL y Dominio',
            'status' => $isCorrectDomain ? 'success' : 'warning',
            'message' => $isCorrectDomain ? 'Dominio correcto' : 'Verificar dominio',
            'details' => $currentURL
        ];
        
        // 2. Verificar redirecciones
        $redirectHeaders = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'REDIRECT_') === 0) {
                $redirectHeaders[$key] = $value;
            }
        }
        
        $checks['redirect'] = [
            'name' => 'Redirecciones',
            'status' => empty($redirectHeaders) ? 'success' : 'error',
            'message' => empty($redirectHeaders) ? 'Sin redirecciones' : 'Redirecciones detectadas',
            'details' => empty($redirectHeaders) ? 'OK' : implode(', ', array_keys($redirectHeaders))
        ];
        
        // 3. Verificar archivos críticos
        $criticalFiles = ['index.php', 'config.php', 'api/disenos.php'];
        $missingFiles = [];
        foreach ($criticalFiles as $file) {
            if (!file_exists(__DIR__ . '/' . $file)) {
                $missingFiles[] = $file;
            }
        }
        
        $checks['files'] = [
            'name' => 'Archivos Críticos',
            'status' => empty($missingFiles) ? 'success' : 'error',
            'message' => empty($missingFiles) ? 'Todos los archivos presentes' : 'Archivos faltantes',
            'details' => empty($missingFiles) ? 'OK' : implode(', ', $missingFiles)
        ];
        
        // 4. Verificar configuración
        $configExists = file_exists(__DIR__ . '/config.php');
        $checks['config'] = [
            'name' => 'Configuración',
            'status' => $configExists ? 'success' : 'error',
            'message' => $configExists ? 'Configuración disponible' : 'Configuración faltante',
            'details' => $configExists ? 'config.php OK' : 'config.php no encontrado'
        ];
        
        // 5. Verificar PHP
        $phpVersion = PHP_VERSION;
        $isGoodVersion = version_compare($phpVersion, '7.4', '>=');
        $checks['php'] = [
            'name' => 'PHP',
            'status' => $isGoodVersion ? 'success' : 'warning',
            'message' => $isGoodVersion ? 'Versión compatible' : 'Versión puede ser muy antigua',
            'details' => "PHP $phpVersion"
        ];
        
        // Calcular estado general
        $successCount = 0;
        $totalChecks = count($checks);
        foreach ($checks as $check) {
            if ($check['status'] === 'success') $successCount++;
        }
        
        $overallStatus = $successCount === $totalChecks ? 'success' : ($successCount > $totalChecks / 2 ? 'warning' : 'error');
        $overallIcon = $overallStatus === 'success' ? '✅' : ($overallStatus === 'warning' ? '⚠️' : '❌');
        ?>
        
        <div class="status <?= $overallStatus ?>">
            <?= $overallIcon ?> <?= $successCount ?>/<?= $totalChecks ?> Verificaciones Pasadas
        </div>
        
        <div class="grid">
            <?php foreach ($checks as $key => $check): ?>
                <div class="info">
                    <h3><?= $check['name'] ?></h3>
                    <div class="<?= $check['status'] ?>">
                        <strong><?= $check['message'] ?></strong>
                    </div>
                    <p><small><?= $check['details'] ?></small></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="info">
            <h2>🔗 Enlaces Útiles</h2>
            <div style="text-align: center;">
                <a href="index.php" class="btn">🏠 Aplicación Principal</a>
                <a href="diagnostico.php" class="btn">🔍 Diagnóstico Completo</a>
                <a href="test_redireccion.php" class="btn">🧪 Test de Redirección</a>
                <a href="test.html" class="btn">⚙️ Test de APIs</a>
            </div>
        </div>
        
        <div class="info">
            <h2>📊 Información del Sistema</h2>
            <table style="width: 100%; color: white;">
                <tr><td><strong>Timestamp:</strong></td><td><?= date('Y-m-d H:i:s T') ?></td></tr>
                <tr><td><strong>Servidor:</strong></td><td><?= $_SERVER['HTTP_HOST'] ?></td></tr>
                <tr><td><strong>IP del Servidor:</strong></td><td><?= $_SERVER['SERVER_ADDR'] ?? 'N/A' ?></td></tr>
                <tr><td><strong>User Agent:</strong></td><td style="word-break: break-all;"><?= $_SERVER['HTTP_USER_AGENT'] ?? 'N/A' ?></td></tr>
            </table>
        </div>
        
        <?php if ($overallStatus === 'success'): ?>
            <div class="info success">
                <h2>🎉 ¡Sistema Funcionando Correctamente!</h2>
                <p>Todas las verificaciones han pasado exitosamente. El sistema appForms está listo para usar.</p>
            </div>
        <?php elseif ($overallStatus === 'warning'): ?>
            <div class="info warning">
                <h2>⚠️ Sistema Parcialmente Funcional</h2>
                <p>Hay algunas advertencias que deberían revisarse, pero el sistema básico debería funcionar.</p>
            </div>
        <?php else: ?>
            <div class="info error">
                <h2>❌ Problemas Detectados</h2>
                <p>Se encontraron errores críticos que deben resolverse antes de usar el sistema.</p>
                <p><strong>Recomendación:</strong> Ejecutar <a href="diagnostico.php" style="color: yellow;">diagnóstico completo</a> para más detalles.</p>
            </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 30px; opacity: 0.7;">
            <p><small>Verificación generada el <?= date('Y-m-d H:i:s') ?> | appForms v1.0</small></p>
        </div>
    </div>
    
    <script>
        // Auto-refresh cada 30 segundos si hay errores
        <?php if ($overallStatus !== 'success'): ?>
        setTimeout(() => {
            if (confirm('¿Desea actualizar la verificación automáticamente?')) {
                location.reload();
            }
        }, 30000);
        <?php endif; ?>
        
        console.log('🎯 Verificación appForms completada');
        console.log('Estado:', '<?= $overallStatus ?>');
        console.log('Verificaciones:', <?= $successCount ?>, '/', <?= $totalChecks ?>);
    </script>
</body>
</html>
