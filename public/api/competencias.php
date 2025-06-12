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
    if ($pathParts[0] === 'api' && $pathParts[1] === 'competencias') {
        switch ($method) {
            case 'GET':
                if (isset($pathParts[2]) && $pathParts[2] === 'validate' && isset($pathParts[3]) && isset($pathParts[4])) {
                    validateCompetencia($pdo, $pathParts[3], $pathParts[4]);
                } elseif (isset($pathParts[2]) && $pathParts[2] === 'diseno' && isset($pathParts[3])) {
                    getCompetenciasByDiseno($pdo, $pathParts[3]);
                } elseif (isset($pathParts[2])) {
                    getCompetenciaById($pdo, $pathParts[2]);
                } else {
                    getAllCompetencias($pdo);
                }
                break;
            case 'POST':
                createCompetencia($pdo);
                break;
            case 'PUT':
                if (isset($pathParts[2])) {
                    updateCompetencia($pdo, $pathParts[2]);
                }
                break;
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
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
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Verificar que el diseño existe
    $stmt = $pdo->prepare("SELECT codigoDiseño FROM diseños WHERE codigoDiseño = ?");
    $stmt->execute([$data['codigoDiseño']]);
    
    if (!$stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'El diseño especificado no existe', 'codigoDiseño' => $data['codigoDiseño']]);
        return;
    }
    
    $codigoDisenoCompetencia = $data['codigoDiseño'] . '-' . $data['codigoCompetencia'];
    
    // Verificar si ya existe
    $stmt = $pdo->prepare("SELECT codigoDiseñoCompetencia FROM competencias WHERE codigoDiseñoCompetencia = ?");
    $stmt->execute([$codigoDisenoCompetencia]);
    
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'La competencia ya existe', 'codigoDiseñoCompetencia' => $codigoDisenoCompetencia]);
        return;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO competencias (
            codigoDiseñoCompetencia, codigoCompetencia, nombreCompetencia,
            normaUnidadCompetencia, horasDesarrolloCompetencia,
            requisitosAcademicosInstructor, experienciaLaboralInstructor
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([
            $codigoDisenoCompetencia,
            $data['codigoCompetencia'],
            $data['nombreCompetencia'],
            $data['normaUnidadCompetencia'] ?? null,
            $data['horasDesarrolloCompetencia'],
            $data['requisitosAcademicosInstructor'] ?? null,
            $data['experienciaLaboralInstructor'] ?? null
        ]);
        
        echo json_encode(['message' => 'Competencia creada exitosamente', 'codigoDiseñoCompetencia' => $codigoDisenoCompetencia]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear la competencia: ' . $e->getMessage()]);
    }
}

function updateCompetencia($pdo, $codigo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $pdo->prepare("
        UPDATE competencias SET 
            nombreCompetencia = ?, normaUnidadCompetencia = ?,
            horasDesarrolloCompetencia = ?, requisitosAcademicosInstructor = ?,
            experienciaLaboralInstructor = ?
        WHERE codigoDiseñoCompetencia = ?
    ");
    
    try {
        $stmt->execute([
            $data['nombreCompetencia'],
            $data['normaUnidadCompetencia'] ?? null,
            $data['horasDesarrolloCompetencia'],
            $data['requisitosAcademicosInstructor'] ?? null,
            $data['experienciaLaboralInstructor'] ?? null,
            $codigo
        ]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Competencia no encontrada']);
        } else {
            echo json_encode(['message' => 'Competencia actualizada exitosamente']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar la competencia: ' . $e->getMessage()]);
    }
}
?>
