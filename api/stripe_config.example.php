<?php
// Configuración LOCAL de Stripe (solo para desarrollo con XAMPP).
// Copia este archivo a `stripe_config.php` y rellena tus claves de prueba:
//   cp api/stripe_config.example.php api/stripe_config.php
// `stripe_config.php` está en .gitignore.
// En PRODUCCIÓN no se usa este archivo: las claves se leen de variables de
// entorno (ver .env.example). La inicialización la hace api/stripe_init.php.

$STRIPE_SECRET      = 'sk_test_TU_CLAVE_SECRETA';
$STRIPE_PUBLISHABLE = 'pk_test_TU_CLAVE_PUBLICABLE';
$STRIPE_DOMAIN      = 'http://localhost/fruteria-madrid';
