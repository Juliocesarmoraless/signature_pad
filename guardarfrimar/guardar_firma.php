<?php
// guardar_firma.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Recibir datos
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['firma']) || empty($data['firma'])) {
    echo json_encode(['success' => false, 'message' => 'No se recibió la firma.']);
    exit;
}

$firmaBase64 = $data['firma'];

// Quitar el encabezado "data:image/png;base64,"
$firmaBase64 = str_replace('data:image/png;base64,', '', $firmaBase64);
$firmaBinaria = base64_decode($firmaBase64);

// Conexión a la base de datos
$host = 'localhost';
$db   = 'firmas_db';      // Nombre de tu base de datos
$user = 'root';           // Usuario (normalmente root en XAMPP)
$pass = '';               // Contraseña (vacía en XAMPP por defecto)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insertar en la tabla
    $stmt = $pdo->prepare("INSERT INTO firmas (firma_imagen) VALUES (?)");
    $stmt->execute([$firmaBinaria]);

    echo json_encode(['success' => true, 'message' => 'Firma guardada en la base de datos.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>