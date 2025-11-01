-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-10-2025 a las 01:40:06
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
-- Base de datos: `abmgreta`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `asistencia` tinyint(1) NOT NULL,
  `hora_ingreso` time DEFAULT NULL,
  `hora_salida` time DEFAULT NULL,
  `anotaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`id`, `id_usuario`, `fecha`, `asistencia`, `hora_ingreso`, `hora_salida`, `anotaciones`) VALUES
(13, 4, '2025-07-08', 1, NULL, NULL, ''),
(14, 1, '2025-07-08', 1, NULL, NULL, ''),
(15, 2, '2025-07-08', 1, NULL, NULL, ''),
(16, 3, '2025-07-08', 1, NULL, NULL, ''),
(17, 5, '2025-07-08', 1, NULL, NULL, ''),
(19, 3, '2025-07-23', 0, NULL, NULL, 'Llego tarde'),
(21, 5, '2025-07-30', 1, NULL, NULL, '1'),
(22, 10, '2025-09-01', 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_turno`
--

CREATE TABLE `estado_turno` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estado_turno`
--

INSERT INTO `estado_turno` (`ID`, `nombre`) VALUES
(6, 'Cancelado'),
(1, 'Confirmado'),
(4, 'En Proceso'),
(7, 'Pagado'),
(5, 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `transaccion_id` varchar(100) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'completado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `turno_id`, `monto`, `metodo_pago`, `fecha_pago`, `transaccion_id`, `estado`) VALUES
(1, 14, 30000.00, 'efectivo', '2025-10-10 11:52:38', NULL, 'completado'),
(2, 15, 20000.00, 'efectivo', '2025-10-10 11:57:50', NULL, 'completado'),
(3, 14, 27700.00, 'efectivo', '2025-10-10 21:21:27', NULL, 'completado'),
(4, 20, 26200.00, 'efectivo', '2025-10-14 19:38:18', NULL, 'completado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubro_servicio`
--

CREATE TABLE `rubro_servicio` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rubro_servicio`
--

INSERT INTO `rubro_servicio` (`ID`, `nombre`) VALUES
(1, 'Bronceado'),
(5, 'Esculpidas'),
(2, 'Faciales'),
(3, 'Microblading'),
(4, 'Perfilado de Cejas'),
(6, 'Pestañas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `fecha_alta` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_modificacion` datetime DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT 0.00,
  `duracion` int(11) DEFAULT 60
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`ID`, `nombre`, `descripcion`, `imagen`, `estado`, `fecha_alta`, `fecha_modificacion`, `precio`, `duracion`) VALUES
(1, 'Bronceado', 'Servicio de bronceado profesional', NULL, 1, '2025-10-10 20:52:45', NULL, 28000.00, 60),
(2, 'Faciales', 'Tratamientos faciales personalizados', NULL, 1, '2025-10-10 21:05:58', '2025-10-10 16:46:59', 25000.00, 60),
(3, 'Microblading', 'Técnica de microblading semipermanente', NULL, 1, '2025-10-10 19:44:02', NULL, 90000.00, 120),
(4, 'Perfilado de Cejas', 'Diseño y perfilado de cejas', NULL, 1, '2025-10-10 20:52:44', NULL, 27500.00, 60),
(5, 'Esculpidas', 'Uñas esculpidas profesionales', NULL, 1, '2025-10-10 20:52:44', NULL, 27700.00, 120),
(6, 'Pestañas', 'Extensiones y mantenimiento de pestañas', NULL, 1, '2025-10-10 20:52:44', NULL, 26200.00, 60),
(7, 'Masajes', '', NULL, 0, '2025-10-10 21:05:58', '2025-10-10 17:45:47', 15000.00, 60),
(8, 'Semipermanente', 'Manicura semipermanente de larga duración', NULL, 1, '2025-10-10 21:05:58', NULL, 17600.00, 60),
(9, 'Kapping', 'Kapping gel para fortalecimiento de uñas naturales', NULL, 1, '2025-10-10 21:05:58', NULL, 23000.00, 75);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `ID` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `apellido_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(20) NOT NULL,
  `ID_servicio_FK` int(11) NOT NULL,
  `ID_estado_turno_FK` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `recordatorio_enviado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turno`
--

INSERT INTO `turno` (`ID`, `nombre_cliente`, `apellido_cliente`, `telefono_cliente`, `ID_servicio_FK`, `ID_estado_turno_FK`, `fecha`, `hora`, `recordatorio_enviado`) VALUES
(9, 'Sofi', 'Lopez', '3513874512', 1, 5, '2025-10-02', '12:00:00', 0),
(10, 'Lorena', 'Martin', '3512325212', 2, 5, '2025-10-08', '12:00:00', 0),
(11, 'Lula', 'Lara', '21245464', 1, 5, '2025-10-08', '10:00:00', 0),
(12, 'Marta', 'Nuñez', '54564323', 5, 5, '2025-10-09', '09:00:00', 0),
(13, 'Eugenia', 'Lur', '1235678', 3, 1, '2025-10-10', '11:00:00', 0),
(14, 'Lourdes', 'Yandre', '2541325413', 5, 7, '2025-10-10', '09:00:00', 0),
(15, 'Lula', 'Lopez', '134142354', 1, 7, '2025-10-10', '09:00:00', 0),
(16, 'Mariel', 'Ferrando', '3512526412', 1, 5, '2025-10-11', '11:00:00', 0),
(17, 'Lourdes', 'Ruiz', '3351257452', 2, 5, '2025-10-14', '18:00:00', 0),
(18, 'Sofi', 'Martinez', '3513514123', 5, 5, '2025-10-14', '17:00:00', 0),
(19, 'Maura', 'Martinez', '35135254561', 1, 5, '2025-10-14', '19:00:00', 0),
(20, 'Maria Elena', 'Tello', '3513254212', 6, 7, '2025-10-15', '11:00:00', 0),
(21, 'Jorgelina', 'Martinez', '354132654', 4, 1, '2025-10-15', '12:00:00', 0),
(22, 'Sofia', 'Muzzi', '35423165465', 5, 5, '2025-10-15', '13:00:00', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `telefono_validado` tinyint(1) DEFAULT 0,
  `email` varchar(255) DEFAULT NULL,
  `DNI` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `domicilio` varchar(255) DEFAULT NULL,
  `telefono_emergencia` varchar(20) DEFAULT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` enum('admin','Supervisor','empleado') NOT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `inicio_activacion` datetime DEFAULT NULL,
  `fin_activacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `telefono`, `telefono_validado`, `email`, `DNI`, `fecha_nacimiento`, `domicilio`, `telefono_emergencia`, `usuario`, `clave`, `rol`, `estado`, `inicio_activacion`, `fin_activacion`) VALUES
(1, 'Laura Albarracin', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'LauLo', '$2y$10$dUoCRkrM8lRxBS/jKNJrNOV0ifLY4ChQ70hsu2buJx1vPjaMsyIUe', 'admin', 1, NULL, NULL),
(2, 'Rolando Garibay 1', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Rola', '$2y$10$oHsBaBSx2nTNcM8kycvsa.j5SDeZsSe0VcAH7WA9hI7WeJaqOHJUm', 'empleado', 1, NULL, NULL),
(3, 'Flor Albarracin 23', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Flora', '$2y$10$fpDoVtuVzv5.nS2sbREXM.hidLs4/MuvIsFQsGMgPh67ZXDT00aW6', 'empleado', 1, NULL, NULL),
(4, 'Pepe gomez 3', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'pego', '$2y$10$iTIKV6g8rBX4RieTkQ5SHOO3Cjtr7ZWA0fGKsuBsE/kf2mxW6MJx.', 'empleado', 1, NULL, NULL),
(5, 'asdas', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'sadas', '$2y$10$JkqQsuU/CV0P.nsuTR3VweQkZd2Q4zC4HbrhWlWnX3Iv1/aVbaLI2', 'admin', 1, NULL, NULL),
(6, 'Julita', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Julia', '$2y$10$SzuNYuXopB3lux1f3NaMW.gu0OxYY2KRM8mkxrTi6xfBp/uQgyJYe', 'empleado', 1, NULL, NULL),
(7, 'Jose Perez Lua', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Jope', '$2y$10$ZD.olPRDo/r8wHUlztbVVejUGmXA.WQExbM/5f1jZ9dEnarnb.GHe', 'empleado', 1, NULL, NULL),
(8, 'Flor Lopez', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Flor Lopez', '$2y$10$kuXd.W/cgfhENynJRQ8JhOhprHbYHj/jlpShHCnmqpxaO/9TH5GDa', 'empleado', 1, NULL, NULL),
(9, 'Laura Se fue ', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'LaSe', '$2y$10$8fQ7zeVb8lWdy6FgST2X3.E4CPgYPUxR7cUXBzzK/q0BSNGcnH8vq', 'admin', 1, NULL, NULL),
(10, 'Pepe Valerio 12', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'PeVa', '$2y$10$8o7qIutC/7CdXS/T48aK7uQVWJWtoBUi7zq7IFV4ymm0R0kmI1or2', 'admin', 1, NULL, NULL),
(11, 'Sofia Yandre', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'SofiaS', '$2y$10$tv59TXXIg7hoSxyIga6UXOb3VXxG2C6/7jS0pzMFwuIfxXpXbO3Sq', 'admin', 0, NULL, NULL),
(12, 'Valentina Maza', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'VaMa', '$2y$10$L2D0K0u0IrhZP6RQsgwSG.I2VxhCtiib.rKTSm0zOsGl7sMj6nTwa', 'Supervisor', 1, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `estado_turno`
--
ALTER TABLE `estado_turno`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `turno_id` (`turno_id`);

--
-- Indices de la tabla `rubro_servicio`
--
ALTER TABLE `rubro_servicio`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `turno`
--
ALTER TABLE `turno`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_estado_turno_FK` (`ID_estado_turno_FK`),
  ADD KEY `ID_servicio_FK` (`ID_servicio_FK`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `DNI` (`DNI`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `estado_turno`
--
ALTER TABLE `estado_turno`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `rubro_servicio`
--
ALTER TABLE `rubro_servicio`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`turno_id`) REFERENCES `turno` (`ID`) ON DELETE CASCADE;

--
-- Filtros para la tabla `turno`
--
ALTER TABLE `turno`
  ADD CONSTRAINT `turno_ibfk_2` FOREIGN KEY (`ID_estado_turno_FK`) REFERENCES `estado_turno` (`ID`),
  ADD CONSTRAINT `turno_ibfk_3` FOREIGN KEY (`ID_servicio_FK`) REFERENCES `rubro_servicio` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
