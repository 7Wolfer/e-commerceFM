<?php
// Plantilla de configuración de Stripe.
// Copia este archivo a `stripe_config.php` y rellena tus claves reales:
//   cp api/stripe_config.example.php api/stripe_config.php
// `stripe_config.php` está en .gitignore, así que tu clave secreta NO se versiona.
require_once __DIR__ . '/../vendor/autoload.php';

$STRIPE_SECRET      = 'sk_test_TU_CLAVE_SECRETA';
$STRIPE_PUBLISHABLE = 'pk_test_TU_CLAVE_PUBLICABLE';

\Stripe\Stripe::setApiKey($STRIPE_SECRET);

// Dominio local (ajústalo si cambias la carpeta o usas otro host)
$STRIPE_DOMAIN = 'http://localhost/fruteria-madrid';
