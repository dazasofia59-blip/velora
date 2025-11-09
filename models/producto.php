<?php
class Producto
{
    private $conn;
    private $table_name = "insumos";

    public $id;
    public $nombre;
    public $categoria;
    public $descripcion;
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
                (nombre,  precio, stock_minimo, categoria) 
                VALUES 
                (:nombre, :precio, :stock_minimo, :categoria)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));

        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));

        // Vincular parámetros
        $stmt->bindParam(":nombre", $this->nombre);
        // $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":precio", $this->precio);
        $stmt->bindParam(":stock_minimo", $this->stock_minimo);
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
                SET nombre=:nombre, precio=:precio, 
                    stock_minimo=:stock_minimo, categoria=:categoria 
                WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->stock_minimo = htmlspecialchars(strip_tags($this->stock_minimo));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->id = htmlspecialchars(strip_tags($this->id));
        // $this->fecha_actualizacion = htmlspecialchars(strip_tags($this->fecha_actualizacion));

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


    // Obtener estadísticas del dashboard
    public function obtenerEstadisticas()
    {
        $estadisticas = [];

        // Total de productos
        $query = "SELECT COUNT(*) as total_productos FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['total_productos'] = $row['total_productos'];

        // Valor total del inventario
        $query = "SELECT SUM(precio * stock_minimo) as valor_total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['valor_total'] = $row['valor_total'] ?: 0;

        // Productos con stock bajo (menos de 10 unidades)
        // $query = "SELECT COUNT(*) as stock_bajo, nombre FROM " . $this->table_name . " WHERE stock_minimo < 20";
        // $stmt = $this->conn->prepare($query);
        // $stmt->execute();
        // $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // $estadisticas['stock_bajo'] = $row['stock_bajo']

        // Para el conteo
        $query_count = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE stock_minimo < 20";
        $stmt = $this->conn->prepare($query_count);
        $stmt->execute();
        $row_count = $stmt->fetch(PDO::FETCH_ASSOC);

        $query_tab = "SELECT nombre, stock_minimo FROM " . $this->table_name . " WHERE stock_minimo < 20 ORDER BY stock_minimo ASC";
        $stmt = $this->conn->prepare($query_tab);
        $stmt->execute();
        $estadisticas['tabla_stock_bajo'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Para los nombres
        $query_nombres = "SELECT nombre FROM " . $this->table_name . " WHERE stock_minimo < 20";
        $stmt = $this->conn->prepare($query_nombres);
        $stmt->execute();
        $nombres = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $estadisticas['stock_bajo'] = [
            'cantidad' => $row_count['total'],
            'productos' => $nombres
        ];

        // Productos agotados
        $query = "SELECT COUNT(*) as agotados FROM " . $this->table_name . " WHERE stock_minimo = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['agotados'] = $row['agotados'];

        // Productos por categoría
        $query = "SELECT categoria, COUNT(*) as cantidad FROM " . $this->table_name . " GROUP BY categoria";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $estadisticas['productos_por_categoria'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Productos más caros
        $query = "SELECT nombre, precio FROM " . $this->table_name . " ORDER BY precio DESC LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $estadisticas['productos_caros'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Productos con mayor stock
        $query = "SELECT nombre, stock_minimo FROM " . $this->table_name . " ORDER BY stock_minimo DESC LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $estadisticas['productos_mayor_stock'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Últimos productos agregados
        $query = "SELECT nombre, fecha_creacion FROM " . $this->table_name . " ORDER BY fecha_creacion DESC LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $estadisticas['ultimos_productos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $estadisticas;
    }

    // Obtener datos para gráficos
    public function obtenerDatosGraficos()
    {
        $datos = [];

        // Stock por categoría para gráfico de barras
        $query = "SELECT categoria, SUM(stock_minimo) as total_stock FROM " . $this->table_name . " GROUP BY categoria";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $datos['stock_por_categoria'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Valor por categoría para gráfico de torta
        $query = "SELECT categoria, SUM(precio * stock_minimo) as valor FROM " . $this->table_name . " GROUP BY categoria";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $datos['valor_por_categoria'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Evolución mensual de productos agregados
        $query = "SELECT 
                    DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
                    COUNT(*) as cantidad
                    FROM " . $this->table_name . " 
                    GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m')
                    ORDER BY mes DESC
                    LIMIT 6";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $datos['evolucion_mensual'] = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

        return $datos;
    }
}
