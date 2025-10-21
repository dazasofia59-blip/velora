<?php
include_once 'config/database.php';
include_once 'models/Producto.php';

$database = new Database();
$db = $database->getConnection();
$producto = new Producto($db);

$producto->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID no encontrado.');

if($producto->eliminar()){
    header("Location: index.php?mensaje=Producto eliminado exitosamente&tipo=success");
} else{
    header("Location: index.php?mensaje=No se pudo eliminar el producto&tipo=danger");
}
?>