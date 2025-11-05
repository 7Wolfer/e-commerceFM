<?php
// prevent notices from breaking JSON responses
ini_set('display_errors', 0);
ob_start();
session_start();
require_once 'config.php';

try {
    // Recibir datos POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos requeridos
    if (!$data || empty($data['email']) || empty($data['password']) || empty($data['nombre'])) {
        error_response('Todos los campos son requeridos');
    }

    // Validar email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        error_response('Email inválido');
    }
    
    // Validar contraseña
    if (strlen($data['password']) < 6) {
        error_response('La contraseña debe tener al menos 6 caracteres');
    }

    // Verificar si el email ya existe
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        error_response('Este email ya está registrado. Por favor inicia sesión o usa otro email.');
    }

    // Hashear contraseña
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

    try {
        // Insertar usuario
        $stmt = $db->prepare('INSERT INTO usuarios (email, password, nombre) VALUES (?, ?, ?)');
        $stmt->execute([$data['email'], $password_hash, $data['nombre']]);
        $id = $db->lastInsertId();

        // Crear sesión
        $_SESSION['user'] = [
            'id' => $id,
            'email' => $data['email'],
            'name' => $data['nombre'],
            'provider' => 'email'
        ];

    // clear buffer and return clean JSON
    $buf = ob_get_clean(); if($buf) error_log('register.php unexpected output: '. $buf);
    json_response(['ok' => true, 'user' => $_SESSION['user']]);

    } catch (PDOException $e) {
        error_log('Error en register.php (DB): ' . $e->getMessage());
        error_response('Error al crear el usuario. Por favor intenta más tarde.');
    }

} catch (Exception $e) {
    error_log('Error en register.php: ' . $e->getMessage());
    $buf = ob_get_clean(); if($buf) error_log('register.php unexpected output: '. $buf);
    error_response('Error al registrar usuario: ' . $e->getMessage(), 400);
}
?>