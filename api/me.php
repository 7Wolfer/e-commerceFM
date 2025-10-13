<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if(isset($_SESSION['user'])) echo json_encode($_SESSION['user']); else http_response_code(204);
?>