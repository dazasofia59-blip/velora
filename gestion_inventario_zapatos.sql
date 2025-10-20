-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-10-2025 a las 22:12:56
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gestion_inventario_zapatos`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `generar_reporte_inventario` (IN `p_categoria_id` INT)   BEGIN
    IF p_categoria_id IS NULL THEN
        SELECT 
            c.nombre as categoria,
            p.nombre as producto,
            p.marca,
            p.talla,
            p.color,
            i.cantidad as stock_actual,
            p.stock_minimo,
            CASE 
                WHEN i.cantidad <= p.stock_minimo THEN 'CRÍTICO'
                WHEN i.cantidad <= p.stock_minimo * 1.5 THEN 'BAJO'
                ELSE 'NORMAL'
            END as estado_stock,
            p.precio,
            (i.cantidad * p.precio) as valor_total
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        INNER JOIN inventario i ON p.id_producto = i.id_producto
        WHERE p.activo = TRUE
        ORDER BY c.nombre, p.nombre;
    ELSE
        SELECT 
            c.nombre as categoria,
            p.nombre as producto,
            p.marca,
            p.talla,
            p.color,
            i.cantidad as stock_actual,
            p.stock_minimo,
            CASE 
                WHEN i.cantidad <= p.stock_minimo THEN 'CRÍTICO'
                WHEN i.cantidad <= p.stock_minimo * 1.5 THEN 'BAJO'
                ELSE 'NORMAL'
            END as estado_stock,
            p.precio,
            (i.cantidad * p.precio) as valor_total
        FROM productos p
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        INNER JOIN inventario i ON p.id_producto = i.id_producto
        WHERE p.id_categoria = p_categoria_id AND p.activo = TRUE
        ORDER BY p.nombre;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_movimiento` (IN `p_id_producto` INT, IN `p_tipo_movimiento` ENUM('entrada','salida','ajuste'), IN `p_cantidad` INT, IN `p_motivo` VARCHAR(200), IN `p_id_usuario` INT)   BEGIN
    DECLARE v_cantidad_actual INT;
    
    -- Obtener la cantidad actual
    SELECT cantidad INTO v_cantidad_actual FROM inventario WHERE id_producto = p_id_producto;
    
    -- Actualizar inventario según el tipo de movimiento
    IF p_tipo_movimiento = 'entrada' THEN
        UPDATE inventario SET cantidad = cantidad + p_cantidad WHERE id_producto = p_id_producto;
    ELSEIF p_tipo_movimiento = 'salida' THEN
        UPDATE inventario SET cantidad = cantidad - p_cantidad WHERE id_producto = p_id_producto;
    ELSEIF p_tipo_movimiento = 'ajuste' THEN
        UPDATE inventario SET cantidad = p_cantidad WHERE id_producto = p_id_producto;
    END IF;
    
    -- Registrar el movimiento
    INSERT INTO movimientos_inventario (id_producto, tipo_movimiento, cantidad, cantidad_anterior, cantidad_nueva, motivo, id_usuario)
    VALUES (p_id_producto, p_tipo_movimiento, p_cantidad, v_cantidad_actual, 
           (SELECT cantidad FROM inventario WHERE id_producto = p_id_producto), 
           p_motivo, p_id_usuario);
    
    -- Verificar si se genera una alerta de stock mínimo
    IF (SELECT cantidad FROM inventario WHERE id_producto = p_id_producto) <= 
       (SELECT stock_minimo FROM productos WHERE id_producto = p_id_producto) THEN
        INSERT INTO alertas_stock (id_producto, cantidad_actual, stock_minimo)
        VALUES (p_id_producto, 
               (SELECT cantidad FROM inventario WHERE id_producto = p_id_producto),
               (SELECT stock_minimo FROM productos WHERE id_producto = p_id_producto));
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas_stock`
--

CREATE TABLE `alertas_stock` (
  `id_alerta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_actual` int(11) NOT NULL,
  `stock_minimo` int(11) NOT NULL,
  `fecha_alerta` timestamp NOT NULL DEFAULT current_timestamp(),
  `leida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`, `activa`) VALUES
(1, 'Deportivos', 'Zapatos diseñados para actividades deportivas', 1),
(2, 'Formales', 'Zapatos elegantes para ocasiones formales', 1),
(3, 'Casuales', 'Zapatos informales para uso diario', 1),
(4, 'Botas', 'Calzado que cubre el pie y parte de la pierna', 1),
(5, 'Sandalias', 'Calzado abierto para climas cálidos', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `ubicacion` varchar(100) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_inventario`, `id_producto`, `cantidad`, `ubicacion`, `fecha_actualizacion`) VALUES
(1, 1, 25, 'Estante A-1', '2025-09-10 19:44:27'),
(2, 2, 8, 'Estante B-2', '2025-09-10 19:44:27'),
(3, 3, 3, 'Estante C-3', '2025-09-10 19:44:27'),
(4, 4, 15, 'Estante D-1', '2025-09-10 19:44:27'),
(5, 5, 20, 'Estante E-2', '2025-09-10 19:44:27');

--
-- Disparadores `inventario`
--
DELIMITER $$
CREATE TRIGGER `after_inventario_update` AFTER UPDATE ON `inventario` FOR EACH ROW BEGIN
    DECLARE v_stock_minimo INT;
    
    -- Obtener el stock mínimo para este producto
    SELECT stock_minimo INTO v_stock_minimo FROM productos WHERE id_producto = NEW.id_producto;
    
    -- Si el stock actual es menor o igual al stock mínimo y no existe una alerta reciente no leída
    IF NEW.cantidad <= v_stock_minimo AND NOT EXISTS (
        SELECT 1 FROM alertas_stock 
        WHERE id_producto = NEW.id_producto AND leida = FALSE
    ) THEN
        INSERT INTO alertas_stock (id_producto, cantidad_actual, stock_minimo)
        VALUES (NEW.id_producto, NEW.cantidad, v_stock_minimo);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id_movimiento` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `cantidad_anterior` int(11) NOT NULL,
  `cantidad_nueva` int(11) NOT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `movimientos_inventario`
--

INSERT INTO `movimientos_inventario` (`id_movimiento`, `id_producto`, `tipo_movimiento`, `cantidad`, `cantidad_anterior`, `cantidad_nueva`, `motivo`, `id_usuario`, `fecha_movimiento`) VALUES
(1, 1, 'entrada', 25, 0, 25, 'Compra inicial de inventario', 1, '2025-09-10 19:44:27'),
(2, 2, 'entrada', 8, 0, 8, 'Compra inicial de inventario', 1, '2025-09-10 19:44:27'),
(3, 3, 'entrada', 10, 0, 10, 'Compra inicial de inventario', 1, '2025-09-10 19:44:27'),
(4, 3, 'salida', 7, 10, 3, 'Venta a cliente', 2, '2025-09-10 19:44:27'),
(5, 4, 'entrada', 15, 0, 15, 'Compra inicial de inventario', 1, '2025-09-10 19:44:27'),
(6, 5, 'entrada', 20, 0, 20, 'Compra inicial de inventario', 1, '2025-09-10 19:44:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros_sistema`
--

CREATE TABLE `parametros_sistema` (
  `id_parametro` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `editable` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `parametros_sistema`
--

INSERT INTO `parametros_sistema` (`id_parametro`, `nombre`, `valor`, `descripcion`, `editable`) VALUES
(1, 'email_notificaciones', 'admin@zapatos.com', 'Email para enviar notificaciones del sistema', 1),
(2, 'dias_historico_movimientos', '90', 'Número de días a mantener en el historial de movimientos', 1),
(3, 'mostrar_alertas_stock', 'true', 'Mostrar alertas cuando productos estén bajo stock mínimo', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `talla` decimal(3,1) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock_minimo` int(11) DEFAULT 5,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `descripcion`, `id_categoria`, `marca`, `talla`, `color`, `precio`, `stock_minimo`, `activo`, `fecha_creacion`) VALUES
(1, 'Zapato Deportivo Runner', 'Zapato deportivo para running, cómodo y ligero', 1, 'SportFit', 42.0, 'Azul', 89.99, 10, 1, '2025-09-10 19:44:27'),
(2, 'Zapato Formal Clásico', 'Zapato de vestir en piel para ocasiones especiales', 2, 'Elegance', 41.0, 'Negro', 129.99, 5, 1, '2025-09-10 19:44:27'),
(3, 'Mocasín Casual', 'Mocasín de piel suave para uso diario', 3, 'Comfort', 40.0, 'Marrón', 69.99, 8, 1, '2025-09-10 19:44:27'),
(4, 'Bota de Cuero', 'Bota alta de cuero genuino', 4, 'LeatherPro', 43.0, 'Negro', 159.99, 6, 1, '2025-09-10 19:44:27'),
(5, 'Sandalia Playa', 'Sandalia cómoda para playa y piscina', 5, 'Summer', 38.0, 'Azul', 39.99, 12, 1, '2025-09-10 19:44:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('administrador','empleado','visor') DEFAULT 'empleado',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `contrasena`, `rol`, `fecha_registro`, `activo`) VALUES
(1, 'Admin Principal', 'admin@zapatos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador', '2025-09-10 19:44:27', 1),
(2, 'Maria Gonzalez', 'maria@zapatos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'empleado', '2025-09-10 19:44:27', 1),
(3, 'Carlos Lopez', 'carlos@zapatos.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'visor', '2025-09-10 19:44:27', 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_alertas_stock`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_alertas_stock` (
`id_producto` int(11)
,`nombre` varchar(200)
,`stock_actual` int(11)
,`stock_minimo` int(11)
,`dias_desde_alerta` int(7)
,`leida` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_productos_inventario`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_productos_inventario` (
`id_producto` int(11)
,`nombre` varchar(200)
,`descripcion` text
,`categoria` varchar(100)
,`marca` varchar(100)
,`talla` decimal(3,1)
,`color` varchar(50)
,`precio` decimal(10,2)
,`stock_actual` int(11)
,`stock_minimo` int(11)
,`estado_stock` varchar(7)
,`activo` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_alertas_stock`
--
DROP TABLE IF EXISTS `vista_alertas_stock`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_alertas_stock`  AS SELECT `p`.`id_producto` AS `id_producto`, `p`.`nombre` AS `nombre`, `i`.`cantidad` AS `stock_actual`, `p`.`stock_minimo` AS `stock_minimo`, to_days(current_timestamp()) - to_days(`a`.`fecha_alerta`) AS `dias_desde_alerta`, `a`.`leida` AS `leida` FROM ((`productos` `p` join `inventario` `i` on(`p`.`id_producto` = `i`.`id_producto`)) left join `alertas_stock` `a` on(`p`.`id_producto` = `a`.`id_producto`)) WHERE `i`.`cantidad` <= `p`.`stock_minimo` AND `p`.`activo` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_productos_inventario`
--
DROP TABLE IF EXISTS `vista_productos_inventario`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_productos_inventario`  AS SELECT `p`.`id_producto` AS `id_producto`, `p`.`nombre` AS `nombre`, `p`.`descripcion` AS `descripcion`, `c`.`nombre` AS `categoria`, `p`.`marca` AS `marca`, `p`.`talla` AS `talla`, `p`.`color` AS `color`, `p`.`precio` AS `precio`, `i`.`cantidad` AS `stock_actual`, `p`.`stock_minimo` AS `stock_minimo`, CASE WHEN `i`.`cantidad` <= `p`.`stock_minimo` THEN 'CRÍTICO' WHEN `i`.`cantidad` <= `p`.`stock_minimo` * 1.5 THEN 'BAJO' ELSE 'NORMAL' END AS `estado_stock`, `p`.`activo` AS `activo` FROM ((`productos` `p` join `categorias` `c` on(`p`.`id_categoria` = `c`.`id_categoria`)) join `inventario` `i` on(`p`.`id_producto` = `i`.`id_producto`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas_stock`
--
ALTER TABLE `alertas_stock`
  ADD PRIMARY KEY (`id_alerta`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `parametros_sistema`
--
ALTER TABLE `parametros_sistema`
  ADD PRIMARY KEY (`id_parametro`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas_stock`
--
ALTER TABLE `alertas_stock`
  MODIFY `id_alerta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `parametros_sistema`
--
ALTER TABLE `parametros_sistema`
  MODIFY `id_parametro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas_stock`
--
ALTER TABLE `alertas_stock`
  ADD CONSTRAINT `alertas_stock_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
