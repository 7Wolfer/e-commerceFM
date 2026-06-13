<?php
// Página de éxito: valida la Checkout Session y crea el pedido en la BD
require_once __DIR__ . '/stripe_config.php';
require_once __DIR__ . '/config.php'; // mysqli
session_start();

$session_id = $_GET['session_id'] ?? null;
if (!$session_id) { http_response_code(400); echo "Falta session_id"; exit; }

try {
  $session = \Stripe\Checkout\Session::retrieve([
    'id' => $session_id,
    'expand' => ['line_items', 'payment_intent', 'customer_details', 'shipping']
  ]);
} catch (\Throwable $e) {
  http_response_code(400); echo "No se pudo recuperar la sesión: " . $e->getMessage(); exit;
}

$paid = ($session->payment_status === 'paid');
if (!$paid) { echo "El pago no está marcado como 'paid' aún."; exit; }

// Metadata enviados desde la creación de la sesión
$metodo = $session->metadata->metodo ?? 'pickup';
$dir_linea1 = $session->metadata->direccion_linea1 ?? '';

// Info del cliente (por si no hay sesión PHP activa)
$email = $session->customer_details->email ?? ($session->metadata->user_email ?? '');
$name  = $session->customer_details->name ?? ($session->metadata->user_name ?? '');

// UPSERT usuario por email
$user_db_id = null;
if ($email) {
  $st = $mysqli->prepare("SELECT id FROM usuarios WHERE email=?");
  $st->bind_param('s', $email);
  $st->execute();
  $res = $st->get_result();
  if ($row = $res->fetch_assoc()) {
    $user_db_id = intval($row['id']);
    $up = $mysqli->prepare("UPDATE usuarios SET nombre=? WHERE id=?");
    $up->bind_param('si', $name, $user_db_id);
    $up->execute();
  } else {
    $ins = $mysqli->prepare("INSERT INTO usuarios (nombre,email) VALUES (?,?)");
    $ins->bind_param('ss', $name, $email);
    $ins->execute();
    $user_db_id = $ins->insert_id;
  }
}

// Crear pedido
$total = 0;
foreach ($session->line_items->data as $li) {
  $total += ($li->amount_total/100.0);
}
$payment_id = $session->payment_intent->id ?? null;

$insP = $mysqli->prepare("INSERT INTO pedidos (usuario_id, total, metodo, estado, payment_id) VALUES (?,?,?,?,?)");
$estado = 'pagado';
$insP->bind_param('idsss', $user_db_id, $total, $metodo, $estado, $payment_id);
$insP->execute();
$pedido_id = $insP->insert_id;

// Dirección (si aplica)
if ($metodo === 'delivery') {
  $line1 = $dir_linea1;
  if (!$line1 && $session->shipping && $session->shipping->address) {
    $a = $session->shipping->address;
    $line1 = trim(($a->line1 ?? '') . ' ' . ($a->line2 ?? '') . ', ' . ($a->postal_code ?? '') . ' ' . ($a->city ?? ''));
  }
  if ($line1) {
    $insD = $mysqli->prepare("INSERT INTO direcciones (usuario_id, linea1) VALUES (?,?)");
    $insD->bind_param('is', $user_db_id, $line1);
    $insD->execute();
  }
}

// Items
foreach ($session->line_items->data as $li) {
  $name = $li->description;
  $qty  = $li->quantity;
  $price= $li->amount_subtotal/100.0 / max(1,$qty);
  $insI = $mysqli->prepare("INSERT INTO pedido_items (pedido_id, nombre, cantidad, precio) VALUES (?,?,?,?)");
  $insI->bind_param('isid', $pedido_id, $name, $qty, $price);
  $insI->execute();
}

?>
<!doctype html>
<html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pago exitoso | Frutería Madrid</title>
<link rel="stylesheet" href="styles.css">
<style>
  .success {max-width:720px;margin:60px auto;background:#fff;border-radius:16px;padding:24px;box-shadow:0 8px 30px rgba(0,0,0,.06)}
  .success h1{color:#1f7a32;margin:0 0 8px}
  .success .muted{opacity:.8}
  .actions{display:flex;gap:12px;margin-top:18px}
  .btn{border-radius:12px;border:1px solid #e3e9e2;padding:10px 14px;cursor:pointer}
  .btn.primary{background:#2d7d34;color:#fff;border-color:#2d7d34}
</style>
</head><body>
  <div class="success">
    <h1>¡Pago exitoso!</h1>
    <p class="muted">Tu pedido <strong>#<?php echo $pedido_id; ?></strong> ha sido creado correctamente.</p>
    <p>Importe: <strong>$<?php echo number_format($total,2); ?></strong></p>
    <div class="actions">
      <a class="btn" href="index.php">Volver al catálogo</a>
      <a class="btn primary" href="index.php#mis-pedidos">Ver mis pedidos</a>
    </div>
  </div>
</body></html>
