<?php
// MOSTRAR ERRORES (solo en desarrollo)
ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/stripe_config.php'; // aquí defines $STRIPE_DOMAIN y setApiKey

// Lee el body JSON
$raw     = file_get_contents('php://input');
$payload = json_decode($raw, true);

$items     = $payload['items']     ?? [];
$metodo    = $payload['metodo']    ?? 'pickup';   // 'pickup' | 'delivery'
$direccion = $payload['direccion'] ?? null;       // ['linea1' => '...']

if (!$items || !is_array($items)) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Carrito vacío o inválido']);
  exit;
}

// Construye line_items para Stripe
$line_items = [];
foreach ($items as $it) {
  $name         = $it['name']  ?? 'Producto';
  $qty          = max(1, (int)($it['qty']   ?? 1));
  $price        = (float)($it['price'] ?? 0);
  $unit_amount  = (int) round($price * 100); // en centavos

  $line_items[] = [
    'price_data' => [
      'currency'     => 'mxn',
      'product_data' => ['name' => $name],
      'unit_amount'  => $unit_amount,
    ],
    'quantity' => $qty,
  ];
}

// Metadata útil para tu backoffice
$metadata = [
  'metodo'           => $metodo,
  'direccion_linea1' => $direccion['linea1'] ?? '',
  'cart'             => json_encode($items, JSON_UNESCAPED_UNICODE),
  'user_email'       => $_SESSION['user']['email'] ?? '',
  'user_name'        => $_SESSION['user']['name']  ?? '',
];

// Ajusta las URLs a tu estructura real
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

// Si es entrega a domicilio, pide dirección en Checkout (opcional)
if ($metodo === 'delivery') {
  $params['shipping_address_collection'] = ['allowed_countries' => ['MX']];
}

if (!empty($_SESSION['user']['email'])) {
  $params['customer_email'] = $_SESSION['user']['email'];
}

try {
  // Crea la sesión de checkout
  $session = \Stripe\Checkout\Session::create($params);

  echo json_encode([
    'ok'             => true,
    'id'             => $session->id,
    'url'            => $session->url,
    // usa esta clave en el front si haces redirectToCheckout por id
    'publishableKey' => $STRIPE_PUBLISHABLE,
  ]);
} catch (\Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
