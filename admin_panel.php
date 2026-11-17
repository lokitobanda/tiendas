<?php
// Página protegida: solo rol admin
$rol_requerido = 'admin';
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/config/conexion.php';

$totales = [
    'productos' => (int) $pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn(),
    'usuarios'  => (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn(),
    'consultas' => (int) $pdo->query('SELECT COUNT(*) FROM consultas')->fetchColumn(),
];

$consultas = $pdo->query(
    'SELECT c.id, c.nombre, c.email, c.mensaje, c.created_at, cat.nombre AS categoria, e.nombre AS estado
     FROM consultas c
     INNER JOIN categorias cat ON cat.id = c.categoria_id
     INNER JOIN estados_consulta e ON e.id = c.estado_id
     ORDER BY c.id DESC LIMIT 10'
)->fetchAll();

$titulo_pagina = 'Panel de administración';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<h1>Panel de administración</h1>

<ul>
    <li>Productos: <strong><?= e($totales['productos']); ?></strong> — <a href="admin/index.php">Gestionar productos (CRUD)</a></li>
    <li>Usuarios: <strong><?= e($totales['usuarios']); ?></strong></li>
    <li>Consultas recibidas: <strong><?= e($totales['consultas']); ?></strong></li>
</ul>

<h2>Últimas consultas</h2>
<?php if (!$consultas): ?>
    <p>Todavía no hay consultas.</p>
<?php else: ?>
    <table border="1" cellpadding="6">
        <thead>
            <tr><th>ID</th><th>Fecha</th><th>Nombre</th><th>Email</th><th>Categoría</th><th>Estado</th><th>Mensaje</th></tr>
        </thead>
        <tbody>
        <?php foreach ($consultas as $c): ?>
            <tr>
                <td><?= e($c['id']); ?></td>
                <td><?= e($c['created_at']); ?></td>
                <td><?= e($c['nombre']); ?></td>
                <td><?= e($c['email']); ?></td>
                <td><?= e($c['categoria']); ?></td>
                <td><?= e($c['estado']); ?></td>
                <td><?= e($c['mensaje']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
