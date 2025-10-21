<?php
include_once 'config/database.php';
include_once 'models/Producto.php';
include_once 'includes/session.php';
Session::requireLogin(); // Para crear, editar, eliminar


$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);

if($_POST){
    $producto->nombre = $_POST['nombre'];
    $producto->descripcion = $_POST['descripcion'];
    $producto->precio = $_POST['precio'];
    $producto->stock = $_POST['stock'];
    $producto->categoria = $_POST['categoria'];

    if($producto->crear()){
        header("Location: index.php?mensaje=Producto creado exitosamente&tipo=success");
    } else{
        echo "<div class='alert alert-danger'>No se pudo crear el producto.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">➕ Agregar Nuevo Producto</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label class="form-label">Nombre del Producto</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Precio</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="precio" step="0.01" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Stock</label>
                                        <input type="number" name="stock" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Categoría</label>
                                <input type="text" name="categoria" class="form-control">
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">← Volver</a>
                                <button type="submit" class="btn btn-success">💾 Guardar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>