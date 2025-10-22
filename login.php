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
        Session::login($usuario->id, $usuario->username, $usuario->nombre, $usuario->rol);
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

        /* Estilos para el modal del manual */
        .manual-modal .modal-dialog {
            max-width: 95%;
            height: 95vh;
            margin: 2.5vh auto;
        }

        .manual-modal .modal-content {
            height: 100%;
            border-radius: 15px;
            border: none;
        }

        .manual-modal .modal-body {
            height: calc(100% - 120px);
            overflow-y: auto;
            padding: 0;
        }

        .manual-modal .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .manual-modal .modal-footer {
            border-top: 1px solid #dee2e6;
        }

        /* Estilos específicos para el contenido del manual */
        .manual-content {
            padding: 2rem;
        }

        .manual-section {
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e9ecef;
        }

        .manual-section:last-child {
            border-bottom: none;
        }

        .step-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .step-card:hover {
            transform: translateY(-5px);
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: #667eea;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #667eea;
        }

        .role-badge {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .table-custom th {
            background: #667eea;
            color: white;
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

                            <!-- Botón para abrir el manual -->
                            <button type="button" class="btn btn-outline-info mb-2" data-bs-toggle="modal" data-bs-target="#manualUsuario">
                                📚 Manual de Usuario
                            </button>
                            <br>
                            <a href="registro.php" class="btn btn-outline-primary">📝 Crear Cuenta</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal del Manual de Usuario -->
    <div class="modal fade manual-modal" id="manualUsuario" tabindex="-1" aria-labelledby="manualUsuarioLabel" aria-hidden="true" style="z-index: 1055;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="manualUsuarioLabel">
                        📚 Manual de Usuario - Sistema de Inventario
                    </h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="manual-content">
                        <!-- Sección Introducción -->
                        <section class="manual-section">
                            <h3 class="mb-4">🚀 Introducción al Sistema</h3>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <p>El <strong>Sistema de Gestión de Inventario</strong> es una aplicación web diseñada para administrar eficientemente el inventario de productos.</p>
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3">✨ Características Principales</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card step-card h-100">
                                        <div class="card-body text-center">
                                            <div class="feature-icon">📦</div>
                                            <h6>Gestión de Productos</h6>
                                            <p class="text-muted small">Administre productos, categorías, precios y stock</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card step-card h-100">
                                        <div class="card-body text-center">
                                            <div class="feature-icon">📊</div>
                                            <h6>Dashboard Interactivo</h6>
                                            <p class="text-muted small">Métricas en tiempo real con gráficos y estadísticas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card step-card h-100">
                                        <div class="card-body text-center">
                                            <div class="feature-icon">⚠️</div>
                                            <h6>Alertas Inteligentes</h6>
                                            <p class="text-muted small">Notificaciones automáticas de stock bajo</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Sección Acceso -->
                        <section class="manual-section">
                            <h3 class="mb-4">🔑 Acceso al Sistema</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <h5>👤 Inicio de Sesión</h5>
                                    <div class="card step-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="step-number">1</div>
                                                <h6 class="mb-0">Ingrese sus credenciales</h6>
                                            </div>
                                            <ul class="small">
                                                <li><strong>Usuario:</strong> Su nombre de usuario</li>
                                                <li><strong>Contraseña:</strong> Su contraseña</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="card step-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="step-number">2</div>
                                                <h6 class="mb-0">Haga clic en "Ingresar"</h6>
                                            </div>
                                            <p class="small mb-0">Será redirigido automáticamente al dashboard principal</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5>📝 Registro de Nuevos Usuarios</h5>
                                    <div class="card step-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="step-number">1</div>
                                                <h6 class="mb-0">Complete todos los campos</h6>
                                            </div>
                                            <ul class="small">
                                                <li>Usuario único</li>
                                                <li>Email válido</li>
                                                <li>Nombre completo</li>
                                                <li>Contraseña (mínimo 6 caracteres)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Sección Dashboard -->
                        <section class="manual-section">
                            <h3 class="mb-4">📊 Dashboard Principal</h3>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <p class="small">El dashboard es el centro de control donde encontrará toda la información importante del sistema.</p>
                                </div>
                            </div>

                            <h5 class="mb-3">📈 Métricas Principales</h5>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="card text-white bg-primary text-center p-2">
                                        <small>📦 Total Productos</small>
                                        <div class="fw-bold">X</div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card text-white bg-success text-center p-2">
                                        <small>💰 Valor Total</small>
                                        <div class="fw-bold">$X</div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card text-white bg-warning text-center p-2">
                                        <small>⚠️ Stock Bajo</small>
                                        <div class="fw-bold">X</div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="card text-white bg-danger text-center p-2">
                                        <small>🔄 Agotados</small>
                                        <div class="fw-bold">X</div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Sección Productos -->
                        <section class="manual-section">
                            <h3 class="mb-4">📦 Gestión de Productos</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>🆕 Agregar Producto</h6>
                                    <div class="card step-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="step-number">1</div>
                                                <h6 class="mb-0">Haga clic en "➕ Agregar Producto"</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card step-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="step-number">2</div>
                                                <h6 class="mb-0">Complete el formulario</h6>
                                            </div>
                                            <table class="table table-sm small mb-0">
                                                <tr>
                                                    <td><strong>Nombre</strong></td>
                                                    <td>Obligatorio</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Precio</strong></td>
                                                    <td>Formato: 99.99</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Stock</strong></td>
                                                    <td>Número entero</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6>✏️ Editar Producto</h6>
                                    <div class="card step-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="step-number">1</div>
                                                <h6 class="mb-0">Busque el producto en la tabla</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card step-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="step-number">2</div>
                                                <h6 class="mb-0">Haga clic en "✏️ Editar"</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Sección Roles -->
                        <section class="manual-section">
                            <h3 class="mb-4">👥 Roles de Usuario</h3>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card step-card h-100">
                                        <div class="card-body text-center">
                                            <h4>👑</h4>
                                            <h6>Administrador</h6>
                                            <span class="badge bg-warning role-badge">Acceso Completo</span>
                                            <ul class="list-unstyled mt-2 small text-start">
                                                <li>✅ Gestión total de productos</li>
                                                <li>✅ Administración de usuarios</li>
                                                <li>✅ Eliminación de productos</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card step-card h-100">
                                        <div class="card-body text-center">
                                            <h4>👨‍💼</h4>
                                            <h6>Usuario</h6>
                                            <span class="badge bg-primary role-badge">Acceso Básico</span>
                                            <ul class="list-unstyled mt-2 small text-start">
                                                <li>✅ Crear y editar productos</li>
                                                <li>✅ Consultar inventario</li>
                                                <li>❌ Eliminar productos</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Sección Problemas -->
                        <section class="manual-section">
                            <h3 class="mb-4">🔧 Solución de Problemas</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>❌ Problemas Comunes</h6>

                                    <div class="card step-card">
                                        <div class="card-body">
                                            <h6>No puedo iniciar sesión</h6>
                                            <ul class="small mb-0">
                                                <li>Verifique usuario y contraseña</li>
                                                <li>Asegúrese de que su cuenta esté activa</li>
                                                <li>Contacte al administrador</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6>📞 Contacto de Soporte</h6>

                                    <div class="card step-card">
                                        <div class="card-body">
                                            <h6>Información necesaria</h6>
                                            <ul class="small mb-0">
                                                <li>Usuario afectado</li>
                                                <li>Descripción del problema</li>
                                                <li>Pasos para reproducir</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar Manual</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">🖨️ Imprimir Manual</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-enfocar el primer campo cuando se cierra el modal
        document.getElementById('manualUsuario').addEventListener('hidden.bs.modal', function() {
            document.querySelector('input[name="username"]').focus();
        });

        // Abrir automáticamente el manual si hay un parámetro en la URL
        if (window.location.search.includes('manual=1')) {
            var manualModal = new bootstrap.Modal(document.getElementById('manualUsuario'));
            manualModal.show();
        }
    </script>
</body>

</html>