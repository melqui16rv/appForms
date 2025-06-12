<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Obtener parámetros de consulta
$queryParams = $_GET;

try {        switch ($method) {
        case 'GET':
            if (isset($queryParams['validate']) && isset($queryParams['version'])) {
                validateDiseno($pdo, $queryParams['validate'], $queryParams['version']);
            } elseif (isset($queryParams['codigo'])) {
                getDisenoById($pdo, $queryParams['codigo']);
            } else {
                getAllDisenos($pdo);
            }
            break;
        case 'POST':
            createDiseno($pdo);
            break;
        case 'PUT':
            if (isset($queryParams['codigo'])) {
                updateDiseno($pdo, $queryParams['codigo']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Código de diseño requerido para actualizar']);
            }
            break;
        case 'DELETE':
            if (isset($queryParams['codigo'])) {
                deleteDiseno($pdo, $queryParams['codigo']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Código de diseño requerido para eliminar']);
            }
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}

function getAllDisenos($pdo) {
    $stmt = $pdo->query("SELECT * FROM diseños ORDER BY codigoDiseño");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function validateDiseno($pdo, $codigoPrograma, $versionPrograma) {
    $codigoDiseno = $codigoPrograma . '-' . $versionPrograma;
    $stmt = $pdo->prepare("SELECT * FROM diseños WHERE codigoDiseño = ?");
    $stmt->execute([$codigoDiseno]);
    $diseno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'exists' => !empty($diseno),
        'diseno' => $diseno ?: null,
        'codigoDiseño' => $codigoDiseno
    ]);
}

function createDiseno($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        return;
    }
    
    $codigoDiseno = $data['codigoPrograma'] . '-' . $data['versionPrograma'];
    
    // Verificar si ya existe
    $stmt = $pdo->prepare("SELECT codigoDiseño FROM diseños WHERE codigoDiseño = ?");
    $stmt->execute([$codigoDiseno]);
    
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'El diseño ya existe', 'codigoDiseño' => $codigoDiseno]);
        return;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO diseños (
            codigoDiseño, codigoPrograma, versionPograma, lineaTecnologica,
            redTecnologica, redConocimiento, horasDesarrolloDiseño, mesesDesarrolloDiseño,
            nivelAcademicoIngreso, gradoNivelAcademico, formacionTrabajoDesarrolloHumano,
            edadMinima, requisitosAdicionales
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([
            $codigoDiseno,
            $data['codigoPrograma'],
            $data['versionPrograma'],
            $data['lineaTecnologica'],
            $data['redTecnologica'],
            $data['redConocimiento'],
            $data['horasDesarrolloDiseño'] ?: 0,
            $data['mesesDesarrolloDiseño'] ?: 0,
            $data['nivelAcademicoIngreso'],
            $data['gradoNivelAcademico'],
            $data['formacionTrabajoDesarrolloHumano'],
            $data['edadMinima'],
            $data['requisitosAdicionales']
        ]);
        
        echo json_encode(['message' => 'Diseño creado exitosamente', 'codigoDiseño' => $codigoDiseno]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear el diseño: ' . $e->getMessage()]);
    }
}

function updateDiseno($pdo, $codigo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        return;
    }
    
    $stmt = $pdo->prepare("
        UPDATE diseños SET 
            lineaTecnologica = ?, redTecnologica = ?, redConocimiento = ?,
            horasDesarrolloDiseño = ?, mesesDesarrolloDiseño = ?,
            nivelAcademicoIngreso = ?, gradoNivelAcademico = ?,
            formacionTrabajoDesarrolloHumano = ?, edadMinima = ?,
            requisitosAdicionales = ?
        WHERE codigoDiseño = ?
    ");
    
    try {
        $stmt->execute([
            $data['lineaTecnologica'],
            $data['redTecnologica'],
            $data['redConocimiento'],
            $data['horasDesarrollo'] ?: 0,
            $data['mesesDesarrollo'] ?: 0,
            $data['nivelAcademico'],
            $data['gradoNivel'],
            $data['formacionTrabajo'],
            $data['edadMinima'],
            $data['requisitosAdicionales'],
            $codigo
        ]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Diseño no encontrado']);
        } else {
            echo json_encode(['message' => 'Diseño actualizado exitosamente']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar el diseño: ' . $e->getMessage()]);
    }
}

function getDisenoById($pdo, $codigo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM diseños WHERE codigoDiseño = ?");
        $stmt->execute([$codigo]);
        $diseno = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($diseno) {
            echo json_encode($diseno);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Diseño no encontrado']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}

function deleteDiseno($pdo, $codigo) {
    try {
        // Verificar si tiene competencias asociadas
        $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM competencias WHERE codigoDiseñoCompetencia LIKE ?");
        $checkStmt->execute([$codigo . '-%']);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'No se puede eliminar el diseño porque tiene competencias asociadas. Elimine primero las competencias y sus RAPs.']);
            return;
        }
        
        // Eliminar el diseño
        $stmt = $pdo->prepare("DELETE FROM diseños WHERE codigoDiseño = ?");
        $result = $stmt->execute([$codigo]);
        
        if ($result && $stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Diseño eliminado exitosamente']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Diseño no encontrado']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
    }
}
?>
