<?php
include_once 'config/database.php';
include_once 'models/Usuario.php';
include_once 'includes/session.php';

// Si ya está logueado, redirigir al index
if (Session::isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$error = '';
$success = '';

if ($_POST) {
    $usuario->username = $_POST['username'];
    $usuario->email = $_POST['email'];
    $usuario->password = $_POST['password'];
    $usuario->nombre = $_POST['nombre'];
    $usuario->rol = 'usuario'; // Por defecto todos son usuarios normales

    // Validar que las contraseñas coincidan
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $error = "Las contraseñas no coinciden.";
    }
    // Validar que el usuario/email no exista
    else if ($usuario->existe()) {
        $error = "El usuario o email ya están registrados.";
    }
    // Registrar usuario
    else if ($usuario->registrar()) {
        $success = "¡Cuenta creada exitosamente! Ahora puedes iniciar sesión.";
    } else {
        $error = "Error al crear la cuenta. Intenta nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }

        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card">
                    <div class="register-header">
                        <h2>📝 Crear Cuenta</h2>
                        <p class="mb-0">Únete al Sistema de Inventario</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <div class="mt-2">
                                    <a href="login.php" class="btn btn-success">🚀 Ir al Login</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!$success): ?>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">👤 Usuario *</label>
                                            <input type="text" name="username" class="form-control"
                                                placeholder="Nombre de usuario" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">📧 Email *</label>
                                            <input type="email" name="email" class="form-control"
                                                placeholder="tu@email.com" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">👨‍💼 Nombre Completo *</label>
                                    <input type="text" name="nombre_completo" class="form-control"
                                        placeholder="Tu nombre completo" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">🔒 Contraseña *</label>
                                            <input type="password" name="password" class="form-control"
                                                placeholder="Mínimo 6 caracteres" minlength="6" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">✅ Confirmar Contraseña *</label>
                                            <input type="password" name="confirm_password" class="form-control"
                                                placeholder="Repite tu contraseña" minlength="6" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">📝 Crear Cuenta</button>
                                    <a href="login.php" class="btn btn-outline-secondary">← Volver al Login</a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>