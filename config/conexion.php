<?php
/**
 * Conexión PDO a MySQL (TP7).
 * Los datos salen de config/env.php; expone la variable $pdo.
 */
$config = require __DIR__ . '/env.php';

$host    = $config['db_host'] ?? 'localhost';
$port    = $config['db_port'] ?? 3306;
$db      = $config['db_name'] ?? 'skygames_db';
$user    = $config['db_user'] ?? 'root';
$pass    = $config['db_pass'] ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Solo se muestra el detalle técnico en modo desarrollo
    http_response_code(500);
    if (!empty($config['app_debug'])) {
        die('Error crítico de conexión: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
    die('Error crítico de conexión con la base de datos.');
}
