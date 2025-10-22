<?php
include_once 'includes/session.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            🏪 Sistema de Inventario
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (Session::isLoggedIn()): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">📦 Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="crear.php">➕ Agregar Producto</a>
                    </li>
                    <?php if (Session::isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="#">👑 Administración</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            👋 <?php echo Session::getUserInfo()['nombre']; ?>
                            <?php if (Session::isAdmin()): ?>
                                <span class="badge bg-warning">👑 Admin</span>
                            <?php else: ?>
                                <span class="badge bg-info">👤 Usuario</span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">👤 Mi Perfil</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="logout.php">🚪 Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">🔐 Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registro.php">📝 Registro</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>