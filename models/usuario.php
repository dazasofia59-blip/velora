<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $username;
    public $email;
    public $password;
    public $nombre_completo;
    public $rol;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar nuevo usuario
    public function registrar() {
        $query = "INSERT INTO " . $this->table_name . " 
                 SET username=:username, email=:email, password=:password, 
                     nombre_completo=:nombre_completo, rol=:rol";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->nombre_completo = htmlspecialchars(strip_tags($this->nombre_completo));
        $this->rol = htmlspecialchars(strip_tags($this->rol));

        // Encriptar contraseña
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Vincular parámetros
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":nombre_completo", $this->nombre_completo);
        $stmt->bindParam(":rol", $this->rol);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Verificar si el usuario existe
    public function existe() {
        $query = "SELECT id, username, email FROM " . $this->table_name . " 
                 WHERE username = ? OR email = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->bindParam(2, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Login de usuario
    public function login() {
        $query = "SELECT id, username, password, nombre_completo, rol, activo 
                 FROM " . $this->table_name . " 
                 WHERE username = ? AND activo = 1 LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->nombre_completo = $row['nombre_completo'];
                $this->rol = $row['rol'];
                $this->activo = $row['activo'];
                return true;
            }
        }
        return false;
    }

    // Obtener información del usuario por ID
    public function obtenerPorId() {
        $query = "SELECT username, email, nombre_completo, rol, fecha_creacion 
                 FROM " . $this->table_name . " 
                 WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->nombre_completo = $row['nombre_completo'];
            $this->rol = $row['rol'];
            return true;
        }
        return false;
    }
}
?>