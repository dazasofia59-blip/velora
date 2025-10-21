<?php
class Producto
{
    private $conn;
    private $table_name = "insumos";

    public $id;
    public $nombre;
    public $categoria;
    public $cantidad;
    public $unidad;
    public $prooveedor;
    public $precio;
    public $stock_minimo;
    public $Stock_maximo;
    public $fecha_creacion;
    public $fecha_actualizacion;
    public $activo;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Crear producto
    public function crear()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                SET nombre=:nombre, precio=:precio, cantidad=:cantidad, unidad=:unidad, 
                    prooveedor=:prooveedor, stock_minimo=:stock_minimo, Stock_maximo=:Stock_maximo, 
                    fecha_creacion=:fecha_creacion, fecha_actualizacion=:fecha_actualizacion, activo=:activo, 
                    categoria=:categoria";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->fecha_creacion = htmlspecialchars(strip_tags($this->fecha_creacion));
        $this->activo = htmlspecialchars(strip_tags($this->activo));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt -
            $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":stock", $this->stock_minimo);
        $stmt->bindParam(":categoria", $this->categoria);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Leer todos los productos
    public function leer()
    {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Leer un solo producto
    public function leerUno()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->nombre = $row['nombre'];
            $this->precio = $row['precio'];
            $this->stock_minimo = $row['stock_minimo'];
            $this->categoria = $row['categoria'];
            return true;
        }
        return false;
    }

    // Actualizar producto
    public function actualizar()
    {
        $query = "UPDATE " . $this->table_name . " 
                SET nombre=:nombre, descripcion=:descripcion, precio=:precio, 
                    stock_minimo=:stock_minimo, categoria=:categoria ,fecha_actualizacion=:fecha_actualizacion
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->fecha_actualizacion = htmlspecialchars(strip_tags($this->fecha_actualizacion));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":stock_minimo", $this->stock_minimo);
        $stmt->bindParam(":categoria", $this->categoria);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar producto
    public function eliminar()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
