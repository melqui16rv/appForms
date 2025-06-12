<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de base de datos
$host = 'localhost';
$dbname = 'disenos_curriculares';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = explode('/', trim($path, '/'));
    
    // Enrutamiento básico
    if ($pathParts[0] === 'api' && $pathParts[1] === 'raps') {
        switch ($method) {
            case 'GET':
                if (isset($pathParts[2]) && $pathParts[2] === 'validate' && isset($pathParts[3]) && isset($pathParts[4]) && isset($pathParts[5])) {
                    validateRap($pdo, $pathParts[3], $pathParts[4], $pathParts[5]);
                } elseif (isset($pathParts[2]) && $pathParts[2] === 'competencia' && isset($pathParts[3])) {
                    getRapsByCompetencia($pdo, $pathParts[3]);
                } elseif (isset($pathParts[2])) {
                    getRapById($pdo, $pathParts[2]);
                } else {
                    getAllRaps($pdo);
                }
                break;
            case 'POST':
                createRap($pdo);
                break;
            case 'PUT':
                if (isset($pathParts[2])) {
                    updateRap($pdo, $pathParts[2]);
                }
                break;
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
}

function getAllRaps($pdo) {
    $stmt = $pdo->query("SELECT * FROM raps ORDER BY codigoDiseñoCompetenciaRap");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getRapsByCompetencia($pdo, $codigoDisenoCompetencia) {
    $stmt = $pdo->prepare("SELECT * FROM raps WHERE codigoDiseñoCompetenciaRap LIKE ? ORDER BY codigoDiseñoCompetenciaRap");
    $stmt->execute([$codigoDisenoCompetencia . '-%']);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function validateRap($pdo, $codigoDiseno, $codigoCompetencia, $codigoRap) {
    $codigoDisenoCompetenciaRap = $codigoDiseno . '-' . $codigoCompetencia . '-' . $codigoRap;
    $stmt = $pdo->prepare("SELECT * FROM raps WHERE codigoDiseñoCompetenciaRap = ?");
    $stmt->execute([$codigoDisenoCompetenciaRap]);
    $rap = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exists' => !empty($rap),
        'rap' => $rap ?: null,
        'codigoDiseñoCompetenciaRap' => $codigoDisenoCompetenciaRap
    ]);
}

function createRap($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Verificar que la competencia existe
    $codigoDisenoCompetencia = $data['codigoDiseño'] . '-' . $data['codigoCompetencia'];
    $stmt = $pdo->prepare("SELECT codigoDiseñoCompetencia FROM competencias WHERE codigoDiseñoCompetencia = ?");
    $stmt->execute([$codigoDisenoCompetencia]);
    
    if (!$stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'La competencia especificada no existe', 'codigoDiseñoCompetencia' => $codigoDisenoCompetencia]);
        return;
    }
    
    $codigoDisenoCompetenciaRap = $data['codigoDiseño'] . '-' . $data['codigoCompetencia'] . '-' . $data['codigoRap'];
    
    // Verificar si ya existe
    $stmt = $pdo->prepare("SELECT codigoDiseñoCompetenciaRap FROM raps WHERE codigoDiseñoCompetenciaRap = ?");
    $stmt->execute([$codigoDisenoCompetenciaRap]);
    
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'El RAP ya existe', 'codigoDiseñoCompetenciaRap' => $codigoDisenoCompetenciaRap]);
        return;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO raps (
            codigoDiseñoCompetenciaRap, codigoRap, nombreRap, horasDesarrolloRap
        ) VALUES (?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([
            $codigoDisenoCompetenciaRap,
            $data['codigoRap'],
            $data['nombreRap'],
            $data['horasDesarrolloRap']
        ]);
        
        echo json_encode(['message' => 'RAP creado exitosamente', 'codigoDiseñoCompetenciaRap' => $codigoDisenoCompetenciaRap]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear el RAP: ' . $e->getMessage()]);
    }
}

function updateRap($pdo, $codigo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("
        UPDATE raps SET nombreRap = ?, horasDesarrolloRap = ? 
        WHERE codigoDiseñoCompetenciaRap = ?
    ");
    
    try {
        $stmt->execute([
            $data['nombreRap'],
            $data['horasDesarrolloRap'],
            $codigo
        ]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'RAP no encontrado']);
        } else {
            echo json_encode(['message' => 'RAP actualizado exitosamente']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar el RAP: ' . $e->getMessage()]);
    }
}
?>
