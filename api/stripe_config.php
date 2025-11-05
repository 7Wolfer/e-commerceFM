<?php
// Stripe base config (edit keys below)
require_once __DIR__ . '/../vendor/autoload.php';

// REEMPLAZA estas llaves por tus claves de STRIPE (modo test primero)
$STRIPE_SECRET = 'sk_test_51SIjBmJkKiqCnW3FqLOnPm9YWzePZ4WwzfocFdNnoxf7P8GY7dEzWtaa72NrXk8CozYtkTih6RWlqIh3lhynXTKn00CBhe3V89';
$STRIPE_PUBLISHABLE = 'pk_test_51SIjBmJkKiqCnW3FrOcXgip7ONeaY8sTp5hZvl5eyqABBrvc8yOFCP7DM3oN9M9vixS4ENdvm7WWQXwpLbOA4jRb00N4SZkGSS';

\Stripe\Stripe::setApiKey($STRIPE_SECRET);

// Dominio local (ajústalo si cambias la carpeta o usas otro host)
$STRIPE_DOMAIN = 'http://localhost/e-commerceFM';
?>