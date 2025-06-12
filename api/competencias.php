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
        if (isset($_GET['action']) && $_GET['action'] === 'validate' && isset($_GET['codigoDiseno']) && isset($_GET['codigoCompetencia'])) {
            validateCompetencia($pdo, $_GET['codigoDiseno'], $_GET['codigoCompetencia']);
        } elseif (isset($_GET['action']) && $_GET['action'] === 'by-diseno' && isset($_GET['codigoDiseno'])) {
            getCompetenciasByDiseno($pdo, $_GET['codigoDiseno']);
        } elseif (isset($_GET['codigo'])) {
            getCompetenciaById($pdo, $_GET['codigo']);
        } else {
            getAllCompetencias($pdo);
        }
        break;
    case 'POST':
        createCompetencia($pdo);
        break;
    case 'PUT':
        if (isset($_GET['codigo'])) {
            updateCompetencia($pdo, $_GET['codigo']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Código de competencia requerido para actualizar']);
        }
        break;
    case 'DELETE':
        if (isset($_GET['codigo'])) {
            deleteCompetencia($pdo, $_GET['codigo']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Código de competencia requerido para eliminar']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}

function getAllCompetencias($pdo) {
    $stmt = $pdo->query("SELECT * FROM competencias ORDER BY codigoDiseñoCompetencia");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getCompetenciasByDiseno($pdo, $codigoDiseno) {
    $stmt = $pdo->prepare("SELECT * FROM competencias WHERE codigoDiseñoCompetencia LIKE ? ORDER BY codigoDiseñoCompetencia");
    $stmt->execute([$codigoDiseno . '-%']);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function validateCompetencia($pdo, $codigoDiseno, $codigoCompetencia) {
    $codigoDisenoCompetencia = $codigoDiseno . '-' . $codigoCompetencia;
    $stmt = $pdo->prepare("SELECT * FROM competencias WHERE codigoDiseñoCompetencia = ?");
    $stmt->execute([$codigoDisenoCompetencia]);
    $competencia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exists' => !empty($competencia),
        'competencia' => $competencia ?: null,
        'codigoDiseñoCompetencia' => $codigoDisenoCompetencia
    ]);
}

function createCompetencia($pdo) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        // Validar campos requeridos
        $requiredFields = ['codigoDiseno', 'codigoCompetencia', 'nombreCompetencia', 'horasDesarrolloCompetencia'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Campo requerido: $field"]);
                return;
            }
        }
        
        // Generar código completo
        $codigoDisenoCompetencia = $data['codigoDiseno'] . '-' . $data['codigoCompetencia'];
        
        // Verificar si ya existe
        $checkStmt = $pdo->prepare("SELECT codigoDiseñoCompetencia FROM competencias WHERE codigoDiseñoCompetencia = ?");
        $checkStmt->execute([$codigoDisenoCompetencia]);
        if ($checkStmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'La competencia ya existe']);
            return;
        }
        
        // Insertar nueva competencia
        $stmt = $pdo->prepare("
            INSERT INTO competencias (
                codigoDiseñoCompetencia, codigoCompetencia, nombreCompetencia,
                normaUnidadCompetencia, horasDesarrolloCompetencia,
                requisitosAcademicosInstructor, experienciaLaboralInstructor
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $codigoDisenoCompetencia,
            $data['codigoCompetencia'],
            $data['nombreCompetencia'],
            $data['normaUnidadCompetencia'] ?? '',
            $data['horasDesarrolloCompetencia'],
            $data['requisitosAcademicosInstructor'] ?? '',
            $data['experienciaLaboralInstructor'] ?? ''
        ]);
        
        if ($result) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Competencia creada exitosamente',
                'codigoDisenoCompetencia' => $codigoDisenoCompetencia
            ]);
        } else {
            throw new Exception('Error al insertar en la base de datos');
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}

function updateCompetencia($pdo, $codigo) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        
        // Verificar si la competencia existe
        $checkStmt = $pdo->prepare("SELECT codigoDiseñoCompetencia FROM competencias WHERE codigoDiseñoCompetencia = ?");
        $checkStmt->execute([$codigo]);
        if (!$checkStmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Competencia no encontrada']);
            return;
        }
        
        // Actualizar competencia
        $stmt = $pdo->prepare("
            UPDATE competencias SET 
                nombreCompetencia = ?,
                normaUnidadCompetencia = ?,
                horasDesarrolloCompetencia = ?,
                requisitosAcademicosInstructor = ?,
                experienciaLaboralInstructor = ?,
                fechaActualizacion = CURRENT_TIMESTAMP
            WHERE codigoDiseñoCompetencia = ?
        ");
        
        $result = $stmt->execute([
            $data['nombreCompetencia'] ?? '',
            $data['normaUnidadCompetencia'] ?? '',
            $data['horasDesarrolloCompetencia'] ?? 0,
            $data['requisitosAcademicosInstructor'] ?? '',
            $data['experienciaLaboralInstructor'] ?? '',
            $codigo
        ]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Competencia actualizada exitosamente'
            ]);
        } else {
            throw new Exception('Error al actualizar en la base de datos');
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}

function getCompetenciaById($pdo, $codigo) {
    $stmt = $pdo->prepare("SELECT * FROM competencias WHERE codigoDiseñoCompetencia = ?");
    $stmt->execute([$codigo]);
    $competencia = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($competencia) {
        echo json_encode($competencia);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Competencia no encontrada']);
    }
}

function deleteCompetencia($pdo, $codigo) {
    try {
        // Verificar si tiene RAPs asociados
        $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM raps WHERE codigoDiseñoCompetenciaRap LIKE ?");
        $checkStmt->execute([$codigo . '-%']);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'No se puede eliminar la competencia porque tiene RAPs asociados. Elimine primero los RAPs.']);
            return;
        }
        
        // Eliminar la competencia
        $stmt = $pdo->prepare("DELETE FROM competencias WHERE codigoDiseñoCompetencia = ?");
        $result = $stmt->execute([$codigo]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Competencia eliminada exitosamente']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Competencia no encontrada']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}
