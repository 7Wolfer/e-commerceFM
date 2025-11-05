<?php
require_once 'config.php';

try {
    // Recibir datos
    $data = json_decode(file_get_contents('php://input'), true);

    // Login con provider (Google)
    if (!$data && isset($_GET['provider'])) {
        if ($_GET['provider'] === 'google') {
            $_SESSION['user'] = [
                'provider' => 'google',
                'name' => 'Cliente Frutería',
                'email' => 'cliente@example.com'
            ];
            json_response($_SESSION['user']);
        }
        error_response('Proveedor no soportado');
    }

    // Validar datos para login con email
    if (!$data || empty($data['email']) || empty($data['password'])) {
        error_response('Email y contraseña son requeridos');
    }

    // Login con email/password
    $stmt = $db->prepare('SELECT id, email, nombre, password FROM usuarios WHERE email = ?');
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($data['password'], $user['password'])) {
        error_response('Email o contraseña incorrectos', 401);
    }

    // Crear sesión
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'name' => $user['nombre'],
        'provider' => 'email'
    ];

    json_response(['ok' => true, 'user' => $_SESSION['user']]);

} catch (Exception $e) {
    error_log('Error en login.php: ' . $e->getMessage());
    error_response('Error de autenticación: ' . $e->getMessage(), 401);
}

// Login con Firebase (mantener compatibilidad)
if (isset($data['providerId'])) {
    $_SESSION['user'] = [
        'uid' => $data['uid'] ?? '',
        'name' => $data['displayName'] ?? '',
        'email' => $data['email'] ?? '',
        'photoURL' => $data['photoURL'] ?? '',
        'provider' => $data['providerId']
    ];
    json_response(['ok' => true, 'user' => $_SESSION['user']]);
}

error_response('Método de login no soportado', 400);
?>