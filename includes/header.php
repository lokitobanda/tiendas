<?php
require_once __DIR__ . '/funciones.php';
iniciar_sesion_segura();

if (!isset($titulo_pagina)) {
    $titulo_pagina = 'SKYGAMES';
}
if (!isset($body_class)) {
    $body_class = '';
}
// Prefijo de rutas para páginas dentro de subcarpetas (ej. '../')
if (!isset($base)) {
    $base = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo_pagina); ?> - SKYGAMES</title>
    <meta name="description" content="Descubre la mejor tienda en venta de videojuegos. Encuentra títulos digitales y físicos al mejor precio con envío rápido y garantía asegurada.">
    <link rel="icon" href="<?= e($base); ?>imagenes/icono.png">
    <link rel="stylesheet" href="<?= e($base); ?>estilos.css">
</head>
<body<?php if ($body_class !== '') { echo ' class="' . e($body_class) . '"'; } ?>>
