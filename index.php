<?php
// Incluir configuración y modelo
include_once 'config/database.php';
include_once 'models/Producto.php';
include_once 'includes/session.php';

// Verificar que el usuario esté logueado
Session::requireLogin();

// Conectar a la base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar producto
$producto = new Producto($db);

// Leer productos
$stmt = $producto->leer();
$num = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    <?php include_once 'includes/header.php'; ?>

    <div class="header">
        <div class="container">
            <h1 class="text-center">🏪 Sistema de Inventario</h1>
            <p class="text-center lead">
                Bienvenido, <?php echo Session::getUserInfo()['nombre_completo']; ?>!
                <?php if (Session::isAdmin()): ?>
                    <span class="badge bg-warning">👑 Administrador</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="container">
        <!-- Botón para agregar nuevo producto -->
        <div class="row mb-4">
            <div class="col">
                <a href="crear.php" class="btn btn-success btn-lg">
                    ➕ Agregar Nuevo Producto
                </a>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        <?php
        if (isset($_GET['mensaje'])) {
            $tipo = $_GET['tipo'] ?? 'success';
            echo "<div class='alert alert-{$tipo} alert-dismissible fade show' role='alert'>";
            echo htmlspecialchars($_GET['mensaje']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        ?>

        <!-- Resto del código del index anterior... -->
        <!-- ... (mantener el mismo contenido de la tabla) ... -->
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>Sistema de Inventario &copy; <?php echo date('Y'); ?> |
            Usuario: <?php echo Session::getUserInfo()['nombre_completo']; ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>