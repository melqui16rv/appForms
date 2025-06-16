<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];

// Manejar OPTIONS para CORS
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Enrutamiento
switch ($method) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'validate' && isset($_GET['codigoDiseno']) && isset($_GET['codigoCompetencia']) && isset($_GET['codigoRap'])) {
            validateRap($pdo, $_GET['codigoDiseno'], $_GET['codigoCompetencia'], $_GET['codigoRap']);
        } elseif (isset($_GET['action']) && $_GET['action'] === 'by-competencia' && isset($_GET['codigoCompetencia'])) {
            getRapsByCompetencia($pdo, $_GET['codigoCompetencia']);
        } elseif (isset($_GET['codigo'])) {
            getRapById($pdo, $_GET['codigo']);
        } else {
            getAllRaps($pdo);
        }
        break;
    case 'POST':
        createRap($pdo);
        break;
    case 'PUT':
        if (isset($_GET['codigo'])) {
            updateRap($pdo, $_GET['codigo']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Código de RAP requerido para actualizar']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['codigo'])) {
            deleteRap($pdo, $_GET['codigo']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Código de RAP requerido para eliminar']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

function getAllRaps($pdo) {
    $stmt = $pdo->query("SELECT * FROM raps ORDER BY codigoDisenoCompetenciaRap");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getRapsByCompetencia($pdo, $codigoCompetencia) {
    $stmt = $pdo->prepare("SELECT * FROM raps WHERE codigoDisenoCompetenciaRap LIKE ? ORDER BY codigoDisenoCompetenciaRap");
    $stmt->execute([$codigoCompetencia . '-%']);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function validateRap($pdo, $codigoDiseno, $codigoCompetencia, $codigoRap) {
    $codigoDisenoCompetenciaRap = $codigoDiseno . '-' . $codigoCompetencia . '-' . $codigoRap;
    $stmt = $pdo->prepare("SELECT * FROM raps WHERE codigoDisenoCompetenciaRap = ?");
    $stmt->execute([$codigoDisenoCompetenciaRap]);
    $rap = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exists' => !empty($rap),
        'rap' => $rap ?: null,
        'codigoDisenoCompetenciaRap' => $codigoDisenoCompetenciaRap
    ]);
}

function createRap($pdo) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        // Validar campos requeridos
        $requiredFields = ['codigoDiseno', 'codigoCompetencia', 'codigoRap', 'nombreRap', 'horasDesarrolloRap'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Campo requerido: $field"]);
                return;
            }
        }
        
        // Generar código completo
        $codigoDisenoCompetenciaRap = $data['codigoDiseno'] . '-' . $data['codigoCompetencia'] . '-' . $data['codigoRap'];
        
        // Verificar si ya existe
        $checkStmt = $pdo->prepare("SELECT codigoDisenoCompetenciaRap FROM raps WHERE codigoDisenoCompetenciaRap = ?");
        $checkStmt->execute([$codigoDisenoCompetenciaRap]);
        if ($checkStmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'El RAP ya existe']);
            return;
        }
        
        // Insertar nuevo RAP
        $stmt = $pdo->prepare("
            INSERT INTO raps (
                codigoDisenoCompetenciaRap, codigoRap, nombreRap, horasDesarrolloRap
            ) VALUES (?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $codigoDisenoCompetenciaRap,
            $data['codigoRap'],
            $data['nombreRap'],
            $data['horasDesarrolloRap']
        ]);
        
        if ($result) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'RAP creado exitosamente',
                'codigoDisenoCompetenciaRap' => $codigoDisenoCompetenciaRap
            ]);
        } else {
            throw new Exception('Error al insertar en la base de datos');
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}

function updateRap($pdo, $codigo) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        // Verificar si el RAP existe
        $checkStmt = $pdo->prepare("SELECT codigoDisenoCompetenciaRap FROM raps WHERE codigoDisenoCompetenciaRap = ?");
        $checkStmt->execute([$codigo]);
        if (!$checkStmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'RAP no encontrado']);
            return;
        }
        
        // Actualizar RAP
        $stmt = $pdo->prepare("
            UPDATE raps SET 
                nombreRap = ?,
                horasDesarrolloRap = ?,
                fechaActualizacion = CURRENT_TIMESTAMP
            WHERE codigoDisenoCompetenciaRap = ?
        ");
        
        $result = $stmt->execute([
            $data['nombreRap'] ?? '',
            $data['horasDesarrolloRap'] ?? 0,
            $codigo
        ]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'RAP actualizado exitosamente'
            ]);
        } else {
            throw new Exception('Error al actualizar en la base de datos');
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}

function getRapById($pdo, $codigo) {
    $stmt = $pdo->prepare("SELECT * FROM raps WHERE codigoDisenoCompetenciaRap = ?");
    $stmt->execute([$codigo]);
    $rap = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rap) {
        echo json_encode($rap);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'RAP no encontrado']);
    }
}

function deleteRap($pdo, $codigo) {
    try {
        // Eliminar el RAP (no tiene dependencias)
        $stmt = $pdo->prepare("DELETE FROM raps WHERE codigoDisenoCompetenciaRap = ?");
        $result = $stmt->execute([$codigo]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'RAP eliminado exitosamente']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'RAP no encontrado']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}
