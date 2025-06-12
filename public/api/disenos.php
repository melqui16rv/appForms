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
    if ($pathParts[0] === 'api' && $pathParts[1] === 'disenos') {
        switch ($method) {
            case 'GET':
                if (isset($pathParts[2]) && $pathParts[2] === 'validate' && isset($pathParts[3]) && isset($pathParts[4])) {
                    validateDiseno($pdo, $pathParts[3], $pathParts[4]);
                } elseif (isset($pathParts[2])) {
                    getDisenoById($pdo, $pathParts[2]);
                } else {
                    getAllDisenos($pdo);
                }
                break;
            case 'POST':
                createDiseno($pdo);
                break;
            case 'PUT':
                if (isset($pathParts[2])) {
                    updateDiseno($pdo, $pathParts[2]);
                }
                break;
        }
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
}

function getAllDisenos($pdo) {
    $stmt = $pdo->query("SELECT * FROM diseños ORDER BY codigoDiseño");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getDisenoById($pdo, $codigo) {
    $stmt = $pdo->prepare("SELECT * FROM diseños WHERE codigoDiseño = ?");
    $stmt->execute([$codigo]);
    $diseno = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($diseno) {
        echo json_encode($diseno);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Diseño no encontrado']);
    }
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
    
    $codigoDiseno = $data['codigoPrograma'] . '-' . $data['versionPograma'];
    
    // Verificar si ya existe
    $stmt = $pdo->prepare("SELECT codigoDiseño FROM diseños WHERE codigoDiseño = ?");
    $stmt->execute([$codigoDiseno]);
    
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'El diseño ya existe', 'codigoDiseño' => $codigoDiseno]);
        return;
    }
    
    // Permitir valores NULL para horas y meses
    $horasDesarrollo = !empty($data['horasDesarrolloDiseño']) ? $data['horasDesarrolloDiseño'] : null;
    $mesesDesarrollo = !empty($data['mesesDesarrolloDiseño']) ? $data['mesesDesarrolloDiseño'] : null;
    
    $stmt = $pdo->prepare("
        INSERT INTO diseños (
            codigoDiseño, codigoPrograma, versionPograma, lineaTecnologica,
            redTecnologica, redConocimiento, horasDesarrolloDiseño,
            mesesDesarrolloDiseño, nivelAcademicoIngreso, gradoNivelAcademico,
            formacionTrabajoDesarrolloHumano, edadMinima, requisitosAdicionales
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([
            $codigoDiseno,
            $data['codigoPrograma'],
            $data['versionPograma'],
            $data['lineaTecnologica'],
            $data['redTecnologica'],
            $data['redConocimiento'],
            $horasDesarrollo,
            $mesesDesarrollo,
            $data['nivelAcademicoIngreso'],
            $data['gradoNivelAcademico'] ?? null,
            $data['formacionTrabajoDesarrolloHumano'],
            $data['edadMinima'],
            $data['requisitosAdicionales'] ?? null
        ]);
        
        echo json_encode(['message' => 'Diseño creado exitosamente', 'codigoDiseño' => $codigoDiseno]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear el diseño: ' . $e->getMessage()]);
    }
}

function updateDiseno($pdo, $codigo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Permitir valores NULL para horas y meses
    $horasDesarrollo = !empty($data['horasDesarrolloDiseño']) ? $data['horasDesarrolloDiseño'] : null;
    $mesesDesarrollo = !empty($data['mesesDesarrolloDiseño']) ? $data['mesesDesarrolloDiseño'] : null;
    
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
            $horasDesarrollo,
            $mesesDesarrollo,
            $data['nivelAcademicoIngreso'],
            $data['gradoNivelAcademico'] ?? null,
            $data['formacionTrabajoDesarrolloHumano'],
            $data['edadMinima'],
            $data['requisitosAdicionales'] ?? null,
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
?>
