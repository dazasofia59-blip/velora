<?php
// Incluir configuración y modelo
include_once 'config/database.php';
include_once 'models/Producto.php';

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
    <title>Sistema de Inventario velora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
    <div class="header">
        <div class="container">
            <h1 class="text-center">🏪 Sistema de Inventario</h1>
            <p class="text-center lead">Gestión completa de productos</p>
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
        if(isset($_GET['mensaje'])) {
            $tipo = $_GET['tipo'] ?? 'success';
            echo "<div class='alert alert-{$tipo} alert-dismissible fade show' role='alert'>";
            echo htmlspecialchars($_GET['mensaje']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        ?>

        <!-- Tabla de productos -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">📦 Lista de Productos</h4>
            </div>
            <div class="card-body">
                <?php if($num > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                    <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['stock'] > 10 ? 'bg-success' : 'bg-warning'; ?>">
                                            <?php echo $row['stock']; ?> unidades
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($row['categoria']); ?></span>
                                    </td>
                                    <td>
                                        <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">
                                            ✏️ Editar
                                        </a>
                                        <a href="eliminar.php?id=<?php echo $row['id']; ?>" 
                                            class="btn btn-danger btn-sm" 
                                            onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                            🗑️ Eliminar
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <h4>No hay productos en el inventario</h4>
                        <p>agrega tu primer producto.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5>Total Productos</h5>
                        <h2><?php echo $num; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>Sistema de Inventario velora &copy; <?php echo date('Y'); ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>