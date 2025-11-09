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

// Obtener estadísticas para el dashboard
$estadisticas = $producto->obtenerEstadisticas();
$datos_graficos = $producto->obtenerDatosGraficos();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }

        .stat-card {
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }

        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .badge-stock {
            font-size: 0.8rem;
        }

        .product-list {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <?php include_once 'includes/header.php'; ?>

    <div class="dashboard-header">
        <div class="container">
            <h1 class="text-center">📊 Dashboard de Inventario</h1>
            <p class="text-center lead">
                Bienvenido, <?php echo Session::getUserInfo()['nombre']; ?>!
                <?php if (Session::isAdmin()): ?>
                    <span class="badge bg-warning">👑 Administrador</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="container">
        <!-- Tarjetas de Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div>📦</div>
                    <div class="stat-number"><?php echo $estadisticas['total_productos']; ?></div>
                    <div>Total Productos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div>💰</div>
                    <div class="stat-number">$<?php echo number_format($estadisticas['valor_total'], 2); ?></div>
                    <div>Valor Total</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div>⚠️</div>
                    <div class="stat-number"><?php echo $estadisticas['stock_bajo']['cantidad']; ?></div>
                    <div>Stock Bajo</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <div>🔄</div>
                    <div class="stat-number"><?php echo $estadisticas['agotados']; ?></div>
                    <div>Agotados</div>
                </div>
            </div>
        </div>

        <!-- Gráficos y Listas -->
        <div class="row">
            <!-- Columna izquierda: Gráficos -->
            <div class="col-md-8">
                <!-- Gráfico de Stock por Categoría -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📈 Stock por Categoría</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="stockChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Valor por Categoría -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">🥧 Valor del Inventario por Categoría</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="valorChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Listas -->
            <div class="col-md-4">
                <!-- Productos por Categoría -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">🏷️ Productos por Categoría</h5>
                    </div>
                    <div class="card-body product-list">
                        <?php foreach ($estadisticas['productos_por_categoria'] as $categoria): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                <span><?php echo $categoria['categoria'] ?: 'Sin categoría'; ?></span>
                                <span class="badge bg-primary"><?php echo $categoria['cantidad']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Productos Más Caros -->
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">💎 Productos Más Valiosos</h5>
                    </div>
                    <div class="card-body product-list">
                        <?php foreach ($estadisticas['productos_caros'] as $producto_caro): ?>
                            <div class="mb-2 p-2 border rounded">
                                <div class="fw-bold"><?php echo htmlspecialchars($producto_caro['nombre']); ?></div>
                                <div class="text-success">$<?php echo number_format($producto_caro['precio'], 2); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Productos con Mayor Stock -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">📦 Mayor Stock</h5>
                    </div>
                    <div class="card-body product-list">
                        <?php foreach ($estadisticas['productos_mayor_stock'] as $producto_stock): ?>
                            <div class="mb-2 p-2 border rounded">
                                <div class="fw-bold"><?php echo htmlspecialchars($producto_stock['nombre']); ?></div>
                                <div>
                                    <span class="badge <?php echo $producto_stock['stock_minimo'] > 50 ? 'bg-success' : 'bg-warning'; ?>">
                                        <?php echo $producto_stock['stock_minimo']; ?> unidades
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos (sección inferior) -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📋 Lista Completa de Productos</h5>
                        <a href="crear.php" class="btn btn-success btn-sm">➕ Agregar Producto</a>
                    </div>
                    <div class="card-body">
                        <?php if ($num > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
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
                                                <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $row['stock_minimo'] > 19 ? 'bg-success' : ($row['stock_minimo'] > 0 ? 'bg-warning' : 'bg-danger'); ?>">
                                                        <?php echo $row['stock_minimo']; ?> unidades
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
                                <p>Comienza agregando tu primer producto.</p>
                                <a href="crear.php" class="btn btn-primary">➕ Agregar Primer Producto</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>Sistema de Inventario &copy; <?php echo date('Y'); ?> |
            Usuario: <?php echo Session::getUserInfo()['username']; ?> |
            Última actualización: <?php echo date('d/m/Y H:i:s'); ?></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Datos para los gráficos
        const stockData = {
            labels: [<?php echo implode(',', array_map(function ($item) {
                            return "'" . ($item['categoria'] ?: 'Sin categoría') . "'";
                        }, $datos_graficos['stock_por_categoria'])); ?>],
            datasets: [{
                label: 'Stock Total',
                data: [<?php echo implode(',', array_map(function ($item) {
                            return $item['total_stock'];
                        }, $datos_graficos['stock_por_categoria'])); ?>],
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                ],
                borderWidth: 2
            }]
        };

        const valorData = {
            labels: [<?php echo implode(',', array_map(function ($item) {
                            return "'" . ($item['categoria'] ?: 'Sin categoría') . "'";
                        }, $datos_graficos['valor_por_categoria'])); ?>],
            datasets: [{
                label: 'Valor ($)',
                data: [<?php echo implode(',', array_map(function ($item) {
                            return $item['valor'];
                        }, $datos_graficos['valor_por_categoria'])); ?>],
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                    '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                ],
                borderWidth: 2
            }]
        };

        // Inicializar gráficos
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de barras - Stock por categoría
            const stockCtx = document.getElementById('stockChart').getContext('2d');
            new Chart(stockCtx, {
                type: 'bar',
                data: stockData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de torta - Valor por categoría
            const valorCtx = document.getElementById('valorChart').getContext('2d');
            new Chart(valorCtx, {
                type: 'pie',
                data: valorData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
</body>

</html>