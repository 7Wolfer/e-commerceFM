<?php
require_once 'config.php';

if (!isset($_SESSION['user'])) {
	http_response_code(401);
	json_response(['error' => 'No has iniciado sesión']);
}

try {
	$id = $_SESSION['user']['id'];
	$stmt = $db->prepare('SELECT id, nombre, email, telefono FROM usuarios WHERE id = ?');
	$stmt->execute([$id]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$user) {
		error_response('Usuario no encontrado');
	}

	// Añadir información de provider si está en sesión
	$user['provider'] = $_SESSION['user']['provider'] ?? ($_SESSION['user']['provider'] ?? 'email');

	json_response($user);
} catch (Exception $e) {
	error_log('Error en me.php: ' . $e->getMessage());
	error_response('Error al obtener datos del usuario');
}
?>