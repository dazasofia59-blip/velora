<?php
include_once 'config/database.php';
include_once 'models/Producto.php';
include_once 'includes/session.php';
Session::requireLogin(); // Para crear, editar, eliminar


$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);

$producto->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

if ($producto->leerUno()) {
    $descripcion = $producto->descripcion;
    $nombre = $producto->nombre;
    $precio = $producto->precio;
    $stock_minimo = $producto->stock_minimo;
    $categoria = $producto->categoria;
} else {
    header("Location: index.php?mensaje=Producto no encontrado&tipo=danger");
}

if ($_POST) {
    $producto->nombre = $_POST['nombre'];
    $producto->descripcion = $_POST['descripcion'];
    $producto->precio = $_POST['precio'];
    $producto->stock_minimo = $_POST['stock_minimo'];
    $producto->categoria = $_POST['categoria'];

    if ($producto->actualizar()) {
        header("Location: index.php?mensaje=Producto actualizado exitosamente&tipo=success");
    } else {
        echo "<div class='alert alert-danger'>No se pudo actualizar el producto.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">✏️ Editar Producto</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id={$producto->id}"); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Nombre del Producto</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo $nombre; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo isset($producto->descripcion) ? $producto->descripcion : ''; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Precio</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="precio" step="0.01" class="form-control" value="<?php echo $precio; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- <div class="mb-3">
                                        <label class="form-label">Stock</label>
                                        <input type="number" name="stock_minimo" class="form-control" value="<?php echo $stock_minimo; ?>" required>
                                    </div> -->
                                    <div class="mb-3">
                                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                        <input type="number" 
                                                class="form-control" 
                                                id="stock_minimo" 
                                                name="stock_minimo" 
                                                min="0" 
                                                max="1000" 
                                                value="<?php echo isset($producto->stock_minimo) ? $producto->stock_minimo : '0'; ?>" 
                                                required>
                                        <div class="form-text">El stock mínimo no puede ser negativo.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Categoría</label>
                                <input type="text" name="categoria" class="form-control" value="<?php echo $categoria; ?>">
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">← Volver</a>
                                <button type="submit" class="btn btn-warning">💾 Actualizar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    // En crear.php y editar.php
document.addEventListener('DOMContentLoaded', function() {
    const stockInput = document.getElementById('stock_minimo');
    
    stockInput.addEventListener('input', function() {
        if (this.value < 0) {
            this.value = 0;
            Swal.fire({
                icon: 'warning',
                title: 'Valor inválido',
                text: 'El stock mínimo no puede ser negativo',
                timer: 2000
            });
        }
    });
    
    // Validación antes de enviar el formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        if (stockInput.value < 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Por favor, ingrese un valor de stock mínimo válido (0 o mayor)',
                confirmButtonText: 'Entendido'
            });
            stockInput.focus();
        }
    });
});
</script>

</html>