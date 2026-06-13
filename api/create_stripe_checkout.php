<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/config.php';        // $mysqli (para precios reales)
require_once __DIR__ . '/stripe_config.php';  // $STRIPE_* y setApiKey

// Lee el body JSON
$raw     = file_get_contents('php://input');
$payload = json_decode($raw, true);

$rawItems  = $payload['items']     ?? [];
$metodo    = $payload['metodo']    ?? 'pickup';   // 'pickup' | 'delivery'
$direccion = $payload['direccion'] ?? null;       // ['linea1' => '...']

if (!$rawItems || !is_array($rawItems)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Carrito vacío o inválido']);
  exit;
}

// Cantidades por producto. IMPORTANTE: los precios se toman de la BD,
// nunca de lo que envía el navegador.
$qtyById = [];
foreach ($rawItems as $it) {
  $pid = (int)($it['id'] ?? 0);
  $qty = max(1, (int)($it['qty'] ?? 1));
  if ($pid > 0) { $qtyById[$pid] = ($qtyById[$pid] ?? 0) + $qty; }
}
if (!$qtyById) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Carrito inválido']);
  exit;
}

// Precios reales desde la base de datos
$ids = array_keys($qtyById);
$ph  = implode(',', array_fill(0, count($ids), '?'));
$stmt = $mysqli->prepare("SELECT id, nombre, precio FROM productos WHERE id IN ($ph)");
$stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
$stmt->execute();
$res = $stmt->get_result();

$line_items   = [];
$trusted_cart = [];
while ($row = $res->fetch_assoc()) {
  $pid   = (int)$row['id'];
  $qty   = $qtyById[$pid];
  $price = (float)$row['precio'];
  $trusted_cart[] = ['id' => $pid, 'name' => $row['nombre'], 'price' => $price, 'qty' => $qty];
  $line_items[] = [
    'price_data' => [
      'currency'     => 'mxn',
      'product_data' => ['name' => $row['nombre']],
      'unit_amount'  => (int) round($price * 100), // en centavos
    ],
    'quantity' => $qty,
  ];
}
if (!$line_items) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Ningún producto válido en el carrito']);
  exit;
}

// Metadata útil para tu backoffice
$metadata = [
  'metodo'           => $metodo,
  'direccion_linea1' => $direccion['linea1'] ?? '',
  'cart'             => json_encode($trusted_cart, JSON_UNESCAPED_UNICODE),
  'user_email'       => $_SESSION['user']['email'] ?? '',
  'user_name'        => $_SESSION['user']['name']  ?? '',
];

$successUrl = $STRIPE_DOMAIN . '/api/success.php?session_id={CHECKOUT_SESSION_ID}';
$cancelUrl  = $STRIPE_DOMAIN . '/index.php';

$params = [
  'mode'                   => 'payment',
  'line_items'             => $line_items,
  'success_url'            => $successUrl,
  'cancel_url'             => $cancelUrl,
  'metadata'               => $metadata,
  'allow_promotion_codes'  => true,
];

// Si es entrega a domicilio, pide dirección en Checkout
if ($metodo === 'delivery') {
  $params['shipping_address_collection'] = ['allowed_countries' => ['MX']];
}

if (!empty($_SESSION['user']['email'])) {
  $params['customer_email'] = $_SESSION['user']['email'];
}

try {
  $session = \Stripe\Checkout\Session::create($params);

  echo json_encode([
    'ok'             => true,
    'id'             => $session->id,
    'url'            => $session->url,
    'publishableKey' => $STRIPE_PUBLISHABLE,
  ]);
} catch (\Throwable $e) {
  // No exponemos el detalle del error al cliente; lo registramos en el log del servidor.
  error_log('[Stripe] ' . $e->getMessage());
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'No se pudo iniciar el pago. Inténtalo de nuevo.']);
}
