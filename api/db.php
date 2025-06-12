<?php
// Conexión centralizada a la base de datos
$host = 'localhost';
$dbname = 'disenos_curriculares';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // No hacer echo aquí, solo lanzar excepción
    throw $e;
}
?>
