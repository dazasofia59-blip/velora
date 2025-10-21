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

if ($_POST) {
    $usuario->username = $_POST['username'];
    $usuario->password = $_POST['password'];

    if ($usuario->login()) {
        Session::login($usuario->id, $usuario->username, $usuario->nombre_completo, $usuario->rol);
        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .login-header {
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
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <h2>🔐 Iniciar Sesión</h2>
                        <p class="mb-0">Sistema de Inventario</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="mb-3">
                                <label class="form-label">👤 Usuario</label>
                                <input type="text" name="username" class="form-control form-control-lg"
                                    placeholder="Ingresa tu usuario" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">🔒 Contraseña</label>
                                <input type="password" name="password" class="form-control form-control-lg"
                                    placeholder="Ingresa tu contraseña" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">🚀 Ingresar al Sistema</button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted">
                                <strong>Usuarios de prueba:</strong><br>
                                👑 Admin: <code>admin</code> / <code>admin123</code><br>
                                👤 Usuario: <code>usuario1</code> / <code>admin123</code>
                            </p>
                            <hr>
                            <a href="registro.php" class="btn btn-outline-primary">📝 Crear Cuenta</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>