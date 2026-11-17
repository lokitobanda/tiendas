<?php
require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/config/conexion.php';
iniciar_sesion_segura();

if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errores = [];
$nombre = '';
$email = '';
$telefono = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = limpiar($_POST['nombre'] ?? '');
    $email    = strtolower(limpiar($_POST['email'] ?? ''));
    $telefono = limpiar($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!csrf_valido()) {
        $errores[] = 'Sesión de formulario inválida, recargá la página e intentá de nuevo.';
    }
    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre es obligatorio (máximo 100 caracteres).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $errores[] = 'Ingresá un correo electrónico válido.';
    }
    if ($telefono !== '' && !preg_match('/^[0-9+\-\s]{6,30}$/', $telefono)) {
        $errores[] = 'El teléfono solo puede contener números, espacios, + y -.';
    }
    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($password !== $password2) {
        $errores[] = 'Las contraseñas no coinciden.';
    }

    if (!$errores) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errores[] = 'Ese correo ya está registrado.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare(
                    "INSERT INTO usuarios (rol_id, nombre, email, password, telefono)
                     VALUES ((SELECT id FROM roles WHERE nombre = 'usuario'), :nombre, :email, :password, :telefono)"
                );
                $stmt->execute([
                    ':nombre'   => $nombre,
                    ':email'    => $email,
                    ':password' => $hash,
                    ':telefono' => $telefono !== '' ? $telefono : null,
                ]);
                header('Location: login.php?status=registrado');
                exit;
            }
        } catch (PDOException $ex) {
            // 23000 = clave duplicada (registro simultáneo con el mismo correo)
            $errores[] = $ex->getCode() === '23000'
                ? 'Ese correo ya está registrado.'
                : 'No se pudo completar el registro. Intentá más tarde.';
        }
    }
}

$titulo_pagina = 'Registro';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<h1>Crear cuenta</h1>

<?php if ($errores): ?>
    <div class="mensaje mensaje-error" role="alert">
        <ul>
            <?php foreach ($errores as $error): ?>
                <li><?= e($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="registro.php" class="form-servidor" novalidate>
    <?= csrf_campo(); ?>

    <label for="nombre">Nombre completo:</label><br>
    <input type="text" id="nombre" name="nombre" value="<?= e($nombre); ?>" maxlength="100" required><br><br>

    <label for="email">Correo electrónico:</label><br>
    <input type="email" id="email" name="email" value="<?= e($email); ?>" maxlength="150" required><br><br>

    <label for="telefono">Teléfono (opcional):</label><br>
    <input type="text" id="telefono" name="telefono" value="<?= e($telefono); ?>" maxlength="30"><br><br>

    <label for="password">Contraseña (mínimo 8 caracteres):</label><br>
    <input type="password" id="password" name="password" autocomplete="new-password" required><br><br>

    <label for="password2">Repetir contraseña:</label><br>
    <input type="password" id="password2" name="password2" autocomplete="new-password" required><br><br>

    <button type="submit" class="boton-registro">Registrarme</button>
</form>

<p>¿Ya tenés cuenta? <a href="login.php">Iniciá sesión</a></p>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
