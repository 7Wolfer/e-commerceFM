<?php
// Inicializa Stripe.
// - En producción lee las claves de variables de entorno.
// - En local usa api/stripe_config.php (ignorado por git) si existe.
require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/stripe_config.php')) {
  require __DIR__ . '/stripe_config.php'; // define $STRIPE_* en local
}

$STRIPE_SECRET      = getenv('STRIPE_SECRET')      ?: ($STRIPE_SECRET      ?? '');
$STRIPE_PUBLISHABLE = getenv('STRIPE_PUBLISHABLE') ?: ($STRIPE_PUBLISHABLE ?? '');
$STRIPE_DOMAIN      = getenv('STRIPE_DOMAIN')      ?: ($STRIPE_DOMAIN      ?? 'http://localhost/fruteria-madrid');

\Stripe\Stripe::setApiKey($STRIPE_SECRET);
