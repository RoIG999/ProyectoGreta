-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-11-2025 a las 23:16:07
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `grupo_turnos_id` int(11) DEFAULT NULL,
  `punto_venta` int(11) DEFAULT 1,
  `numero_factura` int(11) DEFAULT NULL,
  `cae` varchar(20) DEFAULT NULL,
  `fecha_emision` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_vencimiento_cae` date DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','emitida','error') DEFAULT 'pendiente',
  `cliente_nombre` varchar(100) DEFAULT NULL,
  `cliente_apellido` varchar(100) DEFAULT NULL,
  `cliente_dni` varchar(20) DEFAULT NULL,
  `cliente_direccion` varchar(200) DEFAULT NULL,
  `cliente_email` varchar(100) DEFAULT NULL,
  `url_factura` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id`, `grupo_turnos_id`, `punto_venta`, `numero_factura`, `cae`, `fecha_emision`, `fecha_vencimiento_cae`, `total`, `estado`, `cliente_nombre`, `cliente_apellido`, `cliente_dni`, `cliente_direccion`, `cliente_email`, `url_factura`, `created_at`) VALUES
(1, NULL, 1, 0, NULL, '2025-10-22 19:25:25', NULL, 27700.00, 'emitida', 'Liliana', 'Lula', '40852954', NULL, NULL, NULL, '2025-10-22 19:25:25'),
(2, NULL, 1, 0, NULL, '2025-10-22 19:25:56', NULL, 27700.00, 'emitida', 'Liliana', 'Lula', '40852954', NULL, NULL, NULL, '2025-10-22 19:25:56'),
(3, 1, 1, 0, NULL, '2025-10-22 19:52:30', NULL, 53000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-22 19:52:30'),
(4, 2, 1, 0, NULL, '2025-10-22 19:53:00', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-22 19:53:00'),
(5, 2, 1, 0, NULL, '2025-10-22 20:04:25', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-22 20:04:25'),
(6, 2, 1, 0, NULL, '2025-10-22 22:09:47', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-22 22:09:47'),
(7, 2, 1, 0, NULL, '2025-10-22 22:09:47', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-22 22:09:47'),
(8, 1, 1, 0, NULL, '2025-10-22 22:24:53', NULL, 53000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-22 22:24:53'),
(9, 2, 1, 0, NULL, '2025-10-22 22:43:46', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-22 22:43:46'),
(10, 2, 1, 0, NULL, '2025-10-22 22:44:44', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-22 22:44:44'),
(11, 2, 1, 0, NULL, '2025-10-22 22:44:44', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-22 22:44:44'),
(12, 2, 1, 0, NULL, '2025-10-22 22:48:15', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-22 22:48:15'),
(13, 3, 1, 0, NULL, '2025-10-23 00:35:54', NULL, 80700.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-23 00:35:54'),
(14, 2, 1, 0, NULL, '2025-10-23 00:38:34', NULL, 25000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-23 00:38:34'),
(15, 3, 1, 0, NULL, '2025-10-23 00:51:52', NULL, 80700.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-23 00:51:52'),
(16, 1, 1, 0, NULL, '2025-10-23 16:22:44', NULL, 53000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-23 16:22:44'),
(17, 1, 1, 0, NULL, '2025-10-23 16:36:02', NULL, 53000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-23 16:36:02'),
(18, 7, 1, 0, NULL, '2025-10-23 16:36:56', NULL, 28000.00, 'emitida', 'Rolando', 'Garibay', '40829060', NULL, NULL, NULL, '2025-10-23 16:36:56'),
(19, 7, 1, 0, NULL, '2025-10-23 16:37:18', NULL, 28000.00, 'emitida', 'Rolando', 'Garibay', '40829060', NULL, NULL, NULL, '2025-10-23 16:37:18'),
(20, 7, 1, 0, NULL, '2025-10-23 17:04:01', NULL, 28000.00, 'emitida', 'Rolando', 'Garibay', '40829060', NULL, NULL, NULL, '2025-10-23 17:04:01'),
(21, 1, 1, 0, NULL, '2025-10-23 17:04:55', NULL, 53000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-23 17:04:55'),
(22, 4, 1, 0, NULL, '2025-10-23 17:17:10', NULL, 27700.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-23 17:17:10'),
(23, 4, 1, 0, NULL, '2025-10-23 17:17:51', NULL, 27700.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-10-23 17:17:51'),
(24, 5, 1, 0, NULL, '2025-10-23 17:53:01', NULL, 28000.00, 'emitida', 'Lourdes', 'Rulo', '40829059', NULL, NULL, NULL, '2025-10-23 17:53:01'),
(25, 5, 1, 0, NULL, '2025-10-23 17:53:37', NULL, 28000.00, 'emitida', 'Lourdes', 'Rulo', '40829059', NULL, NULL, NULL, '2025-10-23 17:53:37'),
(26, 1, 1, 0, NULL, '2025-10-23 18:23:13', NULL, 53000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-23 18:23:13'),
(27, 6, 1, 0, NULL, '2025-10-23 20:48:33', NULL, 28000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-23 20:48:33'),
(28, 13, 1, 0, NULL, '2025-10-23 20:52:36', NULL, 28000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-10-23 20:52:36'),
(29, 15, 1, 0, NULL, '2025-10-23 20:57:55', NULL, 28000.00, 'emitida', 'Liliana', 'Lula', '40852954', NULL, NULL, NULL, '2025-10-23 20:57:55'),
(30, 19, 1, 0, NULL, '2025-10-23 21:07:52', NULL, 28000.00, 'emitida', 'Rocio', 'Gomez', '40829063', NULL, NULL, NULL, '2025-10-23 21:07:52'),
(31, 1, 1, NULL, NULL, '2025-09-03 17:51:00', NULL, 502800.00, 'emitida', 'Andrea', 'Silva', '21521586', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(32, 1, 1, NULL, NULL, '2025-09-03 17:51:00', NULL, 502800.00, 'emitida', 'Milagros', 'Ramírez', '31241104', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(33, 1, 1, NULL, NULL, '2025-09-03 17:51:00', NULL, 502800.00, 'emitida', 'Camila', 'Silva', '43547809', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(34, 1, 1, NULL, NULL, '2025-09-03 17:51:00', NULL, 502800.00, 'emitida', 'Gabriela', 'Cabrera', '27236172', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(35, 1, 1, NULL, NULL, '2025-09-03 17:51:00', NULL, 502800.00, 'emitida', 'Rocío', 'Fernández', '20757830', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(36, 1, 1, NULL, NULL, '2025-09-03 17:51:00', NULL, 1005600.00, 'emitida', 'Florencia', 'Herrera', '26815827', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(37, 1, 1, NULL, NULL, '2025-09-03 17:51:00', NULL, 502800.00, 'emitida', 'Daniela', 'Ortiz', '40740842', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(38, 1, 1, NULL, NULL, '2025-09-03 17:51:00', NULL, 1005600.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(39, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Claudia', 'Gómez', '40524499', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(40, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Ana', 'Díaz', '42648460', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(41, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Andrea', 'Sosa', '43443672', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(42, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Julieta', 'Silva', '26200228', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(43, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Viviana', 'Ortiz', '39191807', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(44, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Lucía', 'Ramírez', '26951047', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(45, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Tamara', 'Cabrera', '26273331', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(46, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Camila', 'Pérez', '21933538', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(47, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Carla', 'Torres', '31795959', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(48, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 1077500.00, 'emitida', 'Mariana', 'Martínez', '38750122', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(49, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 2155000.00, 'emitida', 'Carla', 'Cortes', '27093979', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(50, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 2155000.00, 'emitida', 'Sofía', 'Martínez', '29779377', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(51, 2, 1, NULL, NULL, '2025-09-02 00:06:00', NULL, 2155000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(52, 3, 1, NULL, NULL, '2025-09-02 14:02:00', NULL, 1616700.00, 'emitida', 'Ana', 'Peralta', '39021542', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(53, 3, 1, NULL, NULL, '2025-09-02 14:02:00', NULL, 538900.00, 'emitida', 'Rocío', 'Silva', '34425507', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(54, 3, 1, NULL, NULL, '2025-09-02 14:02:00', NULL, 538900.00, 'emitida', 'Marisol', 'Díaz', '29846298', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(55, 3, 1, NULL, NULL, '2025-09-02 14:02:00', NULL, 538900.00, 'emitida', 'Vanesa', 'Ibarra', '35267547', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(56, 3, 1, NULL, NULL, '2025-09-02 14:02:00', NULL, 538900.00, 'emitida', 'Carolina', 'Vega', '41933974', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(57, 3, 1, NULL, NULL, '2025-09-02 14:02:00', NULL, 538900.00, 'emitida', 'Martina', 'Ríos', '32740422', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(58, 3, 1, NULL, NULL, '2025-09-02 14:02:00', NULL, 538900.00, 'emitida', 'Milagros', 'Herrera', '41514398', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(59, 3, 1, NULL, NULL, '2025-09-02 14:02:00', NULL, 1616700.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(60, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Julieta', 'López', '33323497', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(61, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Claudia', 'Ortiz', '22771982', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(62, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Belén', 'Mendoza', '24698061', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(63, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Agustina', 'Suárez', '27362568', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(64, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Agustina', 'Romero', '44668781', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(65, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Viviana', 'Ibarra', '37632087', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(66, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Valentina', 'Torres', '42232614', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(67, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Ana', 'Domínguez', '40666589', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(68, 4, 1, NULL, NULL, '2025-09-03 20:13:00', NULL, 236800.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(69, 5, 1, NULL, NULL, '2025-09-09 16:30:00', NULL, 205000.00, 'emitida', 'Gabriela', 'Ramírez', '40674603', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(70, 5, 1, NULL, NULL, '2025-09-09 16:30:00', NULL, 205000.00, 'emitida', 'Viviana', 'Alvarez', '37025939', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(71, 5, 1, NULL, NULL, '2025-09-09 16:30:00', NULL, 205000.00, 'emitida', 'Valentina', 'Pérez', '24707570', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(72, 5, 1, NULL, NULL, '2025-09-09 16:30:00', NULL, 205000.00, 'emitida', 'Mariana', 'Alvarez', '40796394', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(73, 5, 1, NULL, NULL, '2025-09-09 16:30:00', NULL, 205000.00, 'emitida', 'Andrea', 'Acosta', '32639260', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(74, 5, 1, NULL, NULL, '2025-09-09 16:30:00', NULL, 205000.00, 'emitida', 'Sofía', 'Ramírez', '30962874', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(75, 5, 1, NULL, NULL, '2025-09-09 16:30:00', NULL, 205000.00, 'emitida', 'Lourdes', 'Rulo', '40829059', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(76, 6, 1, NULL, NULL, '2025-09-05 23:34:00', NULL, 129200.00, 'emitida', 'Agustina', 'Ortiz', '34896130', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(77, 6, 1, NULL, NULL, '2025-09-05 23:34:00', NULL, 129200.00, 'emitida', 'Camila', 'Herrera', '44586138', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(78, 6, 1, NULL, NULL, '2025-09-05 23:34:00', NULL, 129200.00, 'emitida', 'Marisol', 'Medina', '42002693', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(79, 6, 1, NULL, NULL, '2025-09-05 23:34:00', NULL, 129200.00, 'emitida', 'Andrea', 'Romero', '29684164', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(80, 6, 1, NULL, NULL, '2025-09-05 23:34:00', NULL, 129200.00, 'emitida', 'Viviana', 'López', '32657069', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(81, 6, 1, NULL, NULL, '2025-09-05 23:34:00', NULL, 129200.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(82, 7, 1, NULL, NULL, '2025-09-03 01:26:00', NULL, 202200.00, 'emitida', 'Ana', 'Martínez', '40023239', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(83, 7, 1, NULL, NULL, '2025-09-03 01:26:00', NULL, 202200.00, 'emitida', 'Brenda', 'Gómez', '37540513', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(84, 7, 1, NULL, NULL, '2025-09-03 01:26:00', NULL, 404400.00, 'emitida', 'Daniela', 'Benítez', '32006085', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(85, 7, 1, NULL, NULL, '2025-09-03 01:26:00', NULL, 202200.00, 'emitida', 'Micaela', 'Pérez', '26254107', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(86, 7, 1, NULL, NULL, '2025-09-03 01:26:00', NULL, 202200.00, 'emitida', 'Rolando', 'Garibay', '40829060', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(87, 8, 1, NULL, NULL, '2025-09-02 14:17:00', NULL, 165400.00, 'emitida', 'Andrea', 'Torres', '24760523', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(88, 8, 1, NULL, NULL, '2025-09-02 14:17:00', NULL, 165400.00, 'emitida', 'Micaela', 'Rojas', '29989454', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(89, 8, 1, NULL, NULL, '2025-09-02 14:17:00', NULL, 165400.00, 'emitida', 'Marisol', 'Alvarez', '43669686', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(90, 8, 1, NULL, NULL, '2025-09-02 14:17:00', NULL, 165400.00, 'emitida', 'Belén', 'Sosa', '41538834', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(91, 8, 1, NULL, NULL, '2025-09-02 14:17:00', NULL, 165400.00, 'emitida', 'Rocío', 'Sosa', '28140076', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(92, 8, 1, NULL, NULL, '2025-09-02 14:17:00', NULL, 165400.00, 'emitida', 'Camila', 'Vega', '36419761', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(93, 8, 1, NULL, NULL, '2025-09-02 14:17:00', NULL, 165400.00, 'emitida', 'Milagros', 'Fernández', '35166785', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(94, 8, 1, NULL, NULL, '2025-09-02 14:17:00', NULL, 165400.00, 'emitida', 'Rolando', 'Garibay', '40829060', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(95, 9, 1, NULL, NULL, '2025-09-01 13:52:00', NULL, 455000.00, 'emitida', 'Julieta', 'Romero', '41882351', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(96, 9, 1, NULL, NULL, '2025-09-01 13:52:00', NULL, 455000.00, 'emitida', 'Agustina', 'Mendoza', '40213155', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(97, 9, 1, NULL, NULL, '2025-09-01 13:52:00', NULL, 455000.00, 'emitida', 'Gabriela', 'Márquez', '29333385', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(98, 9, 1, NULL, NULL, '2025-09-01 13:52:00', NULL, 455000.00, 'emitida', 'Agustina', 'Ruiz', '31687970', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(99, 9, 1, NULL, NULL, '2025-09-01 13:52:00', NULL, 455000.00, 'emitida', 'Andrea', 'Alvarez', '30012381', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(100, 9, 1, NULL, NULL, '2025-09-01 13:52:00', NULL, 455000.00, 'emitida', 'Brenda', 'Sosa', '22089290', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(101, 9, 1, NULL, NULL, '2025-09-01 13:52:00', NULL, 455000.00, 'emitida', 'Claudia', 'Rojas', '44558743', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(102, 9, 1, NULL, NULL, '2025-09-01 13:52:00', NULL, 455000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(103, 10, 1, NULL, NULL, '2025-09-06 00:58:00', NULL, 105000.00, 'emitida', 'Agustina', 'Herrera', '29632706', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(104, 10, 1, NULL, NULL, '2025-09-06 00:58:00', NULL, 105000.00, 'emitida', 'Brenda', 'Ríos', '43212525', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(105, 10, 1, NULL, NULL, '2025-09-06 00:58:00', NULL, 105000.00, 'emitida', 'Gabriela', 'Pérez', '35137343', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(106, 10, 1, NULL, NULL, '2025-09-06 00:58:00', NULL, 210000.00, 'emitida', 'Ana', 'Acosta', '26531548', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(107, 10, 1, NULL, NULL, '2025-09-06 00:58:00', NULL, 105000.00, 'emitida', 'María', 'Rojas', '30525773', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(108, 10, 1, NULL, NULL, '2025-09-06 00:58:00', NULL, 210000.00, 'emitida', 'Tamara', 'Ruiz', '40829058', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(109, 11, 1, NULL, NULL, '2025-09-08 18:46:00', NULL, 323600.00, 'emitida', 'Valentina', 'Mendoza', '27735573', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(110, 11, 1, NULL, NULL, '2025-09-08 18:46:00', NULL, 323600.00, 'emitida', 'Gabriela', 'Gutiérrez', '42908629', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(111, 11, 1, NULL, NULL, '2025-09-08 18:46:00', NULL, 323600.00, 'emitida', 'Vanesa', 'Acosta', '37335133', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(112, 11, 1, NULL, NULL, '2025-09-08 18:46:00', NULL, 323600.00, 'emitida', 'Florencia', 'Ortiz', '34145368', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(113, 11, 1, NULL, NULL, '2025-09-08 18:46:00', NULL, 323600.00, 'emitida', 'Tamara', 'Mendoza', '40913115', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(114, 11, 1, NULL, NULL, '2025-09-08 18:46:00', NULL, 323600.00, 'emitida', 'Giselle', 'Ríos', '22481858', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(115, 11, 1, NULL, NULL, '2025-09-08 18:46:00', NULL, 323600.00, 'emitida', 'Liliana', 'Lula', '40852954', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(116, 12, 1, NULL, NULL, '2025-09-05 20:43:00', NULL, 193400.00, 'emitida', 'Gabriela', 'Peralta', '34755991', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(117, 12, 1, NULL, NULL, '2025-09-05 20:43:00', NULL, 193400.00, 'emitida', 'Claudia', 'Suárez', '24659934', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(118, 12, 1, NULL, NULL, '2025-09-05 20:43:00', NULL, 386800.00, 'emitida', 'Giselle', 'Herrera', '29534390', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(119, 12, 1, NULL, NULL, '2025-09-05 20:43:00', NULL, 386800.00, 'emitida', 'Agustina', 'Alvarez', '37036849', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(120, 12, 1, NULL, NULL, '2025-09-05 20:43:00', NULL, 193400.00, 'emitida', 'Viviana', 'Ríos', '43463912', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(121, 12, 1, NULL, NULL, '2025-09-05 20:43:00', NULL, 193400.00, 'emitida', 'Milagros', 'Gutiérrez', '31674413', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(122, 12, 1, NULL, NULL, '2025-09-05 20:43:00', NULL, 193400.00, 'emitida', 'Rolando', 'Garibay', '40829060', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(123, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Carolina', 'Romero', '43168532', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(124, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Belén', 'Romero', '38846608', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(125, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Camila', 'Márquez', '27352735', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(126, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Milagros', 'Medina', '28318777', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(127, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Lucía', 'Peralta', '36872858', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(128, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Gabriela', 'Medina', '32522458', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(129, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Tamara', 'Romero', '41426157', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(130, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Ana', 'Medina', '35116756', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(131, 13, 1, NULL, NULL, '2025-09-02 23:56:00', NULL, 211000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(132, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 416700.00, 'emitida', 'Valentina', 'Peralta', '27635083', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(133, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 833400.00, 'emitida', 'Camila', 'Díaz', '27789460', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(134, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 416700.00, 'emitida', 'Lucía', 'Ríos', '30808279', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(135, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 416700.00, 'emitida', 'Sofía', 'Martínez', '40973466', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(136, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 416700.00, 'emitida', 'Tamara', 'Romero', '31068442', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(137, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 833400.00, 'emitida', 'Andrea', 'Gutiérrez', '40833650', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(138, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 416700.00, 'emitida', 'Giselle', 'Mendoza', '30196351', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(139, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 416700.00, 'emitida', 'Carolina', 'Sosa', '33370299', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(140, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 416700.00, 'emitida', 'Marisol', 'Márquez', '20306736', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(141, 14, 1, NULL, NULL, '2025-09-05 00:47:00', NULL, 833400.00, 'emitida', 'Rolando', 'Garibay', '40829060', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(142, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Julieta', 'Acosta', '25281799', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(143, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Giselle', 'Ibarra', '21333508', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(144, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 504000.00, 'emitida', 'Giselle', 'Alvarez', '30042066', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(145, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Julieta', 'Pérez', '24802233', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(146, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Claudia', 'Díaz', '38703434', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(147, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Noelia', 'Torres', '43394432', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(148, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Noelia', 'Mendoza', '43781361', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(149, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Carla', 'Domínguez', '38846966', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(150, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Florencia', 'Márquez', '28153634', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(151, 15, 1, NULL, NULL, '2025-09-04 23:37:00', NULL, 252000.00, 'emitida', 'Liliana', 'Lula', '40852954', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(152, 16, 1, NULL, NULL, '2025-09-05 15:45:00', NULL, 44600.00, 'emitida', 'Noelia', 'Mendoza', '25832214', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(153, 16, 1, NULL, NULL, '2025-09-05 15:45:00', NULL, 44600.00, 'emitida', 'Belén', 'Benítez', '30727672', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(154, 16, 1, NULL, NULL, '2025-09-05 15:45:00', NULL, 44600.00, 'emitida', 'Giselle', 'Díaz', '30015969', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(155, 16, 1, NULL, NULL, '2025-09-05 15:45:00', NULL, 44600.00, 'emitida', 'Lourdes', 'Rulo', '40829059', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(156, 17, 1, NULL, NULL, '2025-09-02 00:32:00', NULL, 133800.00, 'emitida', 'Ana', 'Torres', '37800903', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(157, 17, 1, NULL, NULL, '2025-09-02 00:32:00', NULL, 133800.00, 'emitida', 'María', 'Medina', '27983659', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(158, 17, 1, NULL, NULL, '2025-09-02 00:32:00', NULL, 133800.00, 'emitida', 'Carolina', 'Ortiz', '28573811', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(159, 17, 1, NULL, NULL, '2025-09-02 00:32:00', NULL, 133800.00, 'emitida', 'Maria', 'Romero', '40829061', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(160, 18, 1, NULL, NULL, '2025-09-09 23:26:00', NULL, 232200.00, 'emitida', 'Valentina', 'Rojas', '39392235', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(161, 18, 1, NULL, NULL, '2025-09-09 23:26:00', NULL, 232200.00, 'emitida', 'Paula', 'Silva', '26510102', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(162, 18, 1, NULL, NULL, '2025-09-09 23:26:00', NULL, 232200.00, 'emitida', 'Carla', 'Márquez', '42752886', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(163, 18, 1, NULL, NULL, '2025-09-09 23:26:00', NULL, 232200.00, 'emitida', 'Maria Pia', 'Curtis', '40829062', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(164, 19, 1, NULL, NULL, '2025-09-03 17:09:00', NULL, 499000.00, 'emitida', 'Giselle', 'Rojas', '28907573', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(165, 19, 1, NULL, NULL, '2025-09-03 17:09:00', NULL, 499000.00, 'emitida', 'Claudia', 'Ruiz', '24884511', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(166, 19, 1, NULL, NULL, '2025-09-03 17:09:00', NULL, 499000.00, 'emitida', 'Paula', 'Vega', '37295699', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(167, 19, 1, NULL, NULL, '2025-09-03 17:09:00', NULL, 499000.00, 'emitida', 'Julieta', 'Domínguez', '23007109', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(168, 19, 1, NULL, NULL, '2025-09-03 17:09:00', NULL, 499000.00, 'emitida', 'Martina', 'Gutiérrez', '29008076', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(169, 19, 1, NULL, NULL, '2025-09-03 17:09:00', NULL, 499000.00, 'emitida', 'Carolina', 'López', '41065902', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(170, 19, 1, NULL, NULL, '2025-09-03 17:09:00', NULL, 499000.00, 'emitida', 'Paula', 'Peralta', '35959461', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(171, 19, 1, NULL, NULL, '2025-09-03 17:09:00', NULL, 499000.00, 'emitida', 'Rocio', 'Gomez', '40829063', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(172, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Agustina', 'Suárez', '25199097', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(173, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Carolina', 'Ortiz', '24900601', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(174, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Martina', 'Gutiérrez', '41467921', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(175, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Belén', 'Alvarez', '23001803', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(176, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Milagros', 'Medina', '40257268', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(177, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Giselle', 'Ríos', '37764308', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(178, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Daniela', 'Cabrera', '31650225', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(179, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Noelia', 'Silva', '31147359', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(180, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Florencia', 'Díaz', '44169711', NULL, NULL, NULL, '2025-11-03 22:15:16'),
(181, 20, 1, NULL, NULL, '2025-09-05 17:29:00', NULL, 336000.00, 'emitida', 'Florencia', 'Albarracin', '40829057', NULL, NULL, NULL, '2025-11-03 22:15:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_turnos`
--

CREATE TABLE `grupo_turnos` (
  `id` int(11) NOT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `cliente_apellido` varchar(100) NOT NULL,
  `cliente_telefono` varchar(20) NOT NULL,
  `cliente_dni` varchar(20) DEFAULT NULL,
  `fecha` date NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','pagado','cancelado') DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupo_turnos`
--

INSERT INTO `grupo_turnos` (`id`, `cliente_nombre`, `cliente_apellido`, `cliente_telefono`, `cliente_dni`, `fecha`, `total`, `estado`, `created_at`) VALUES
(1, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-10-23', 53000.00, 'pagado', '2025-10-22 18:12:22'),
(2, 'Tamara', 'Ruiz', '351352123', '40829058', '2025-10-23', 25000.00, 'pagado', '2025-10-22 18:41:04'),
(3, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-10-25', 80700.00, 'pagado', '2025-10-22 23:27:10'),
(4, 'Tamara', 'Ruiz', '351352123', '40829058', '2025-10-25', 27700.00, 'pagado', '2025-10-22 23:30:43'),
(5, 'Lourdes', 'Rulo', '3513521215', '40829059', '2025-10-25', 28000.00, 'pagado', '2025-10-22 23:33:15'),
(6, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-10-25', 28000.00, 'pagado', '2025-10-23 00:47:28'),
(7, 'Rolando', 'Garibay', '3513521035', '40829060', '2025-10-25', 28000.00, 'pagado', '2025-10-23 00:56:05'),
(8, 'Rolando', 'Garibay', '3513521035', '40829060', '2025-11-29', 25000.00, 'pendiente', '2025-10-23 01:34:05'),
(9, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-10-24', 28000.00, 'pendiente', '2025-10-23 18:27:15'),
(10, 'Tamara', 'Ruiz', '351352123', '40829058', '2025-10-25', 52700.00, 'pendiente', '2025-10-23 20:49:16'),
(11, 'Liliana', 'Lula', '3513878521', '40852954', '2025-10-25', 27500.00, 'pendiente', '2025-10-23 20:49:52'),
(12, 'Rolando', 'Garibay', '3513521035', '40829060', '2025-10-25', 27500.00, 'pendiente', '2025-10-23 20:50:15'),
(13, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-10-27', 28000.00, 'pagado', '2025-10-23 20:50:58'),
(14, 'Rolando', 'Garibay', '3513521035', '40829060', '2025-10-27', 54200.00, 'pendiente', '2025-10-23 20:51:06'),
(15, 'Liliana', 'Lula', '3513878521', '40852954', '2025-10-27', 28000.00, 'pagado', '2025-10-23 20:51:16'),
(16, 'Lourdes', 'Rulo', '3513521215', '40829059', '2025-10-27', 26200.00, 'pendiente', '2025-10-23 20:51:31'),
(17, 'Maria', 'Romero', '3513521254', '40829061', '2025-10-27', 28000.00, 'pendiente', '2025-10-23 21:00:59'),
(18, 'Maria Pia', 'Curtis', '351352124', '40829062', '2025-10-27', 28000.00, 'pendiente', '2025-10-23 21:01:39'),
(19, 'Rocio', 'Gomez', '3513521512', '40829063', '2025-10-27', 28000.00, 'pagado', '2025-10-23 21:02:23'),
(20, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-10-25', 28000.00, 'pendiente', '2025-10-24 00:12:06'),
(21, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-10-30', 27500.00, 'pendiente', '2025-10-29 18:14:30'),
(22, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-10-31', 55700.00, 'pendiente', '2025-10-29 18:52:57'),
(23, 'Florencia', 'Albarracin', '3513875107', '40829057', '2025-11-01', 28000.00, 'pendiente', '2025-10-29 19:46:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `grupo_turnos_id` int(11) DEFAULT NULL,
  `turno_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `fecha_pago` datetime NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transaccion_id` varchar(100) DEFAULT NULL,
  `alias_cbu` varchar(100) DEFAULT NULL,
  `cbu` varchar(22) DEFAULT NULL,
  `titular_cuenta` varchar(100) DEFAULT NULL,
  `cuit_cuenta` varchar(20) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `comprobante_url` varchar(500) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'completado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `grupo_turnos_id`, `turno_id`, `monto`, `metodo_pago`, `fecha_pago`, `updated_at`, `transaccion_id`, `alias_cbu`, `cbu`, `titular_cuenta`, `cuit_cuenta`, `banco`, `comprobante_url`, `notas`, `estado`) VALUES
(1, NULL, 14, 30000.00, 'efectivo', '2025-10-10 11:52:38', '2025-10-22 17:39:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(2, NULL, 15, 20000.00, 'efectivo', '2025-10-10 11:57:50', '2025-10-22 17:39:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(3, NULL, 14, 27700.00, 'efectivo', '2025-10-10 21:21:27', '2025-10-22 17:39:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(4, NULL, 20, 26200.00, 'efectivo', '2025-10-14 19:38:18', '2025-10-22 17:39:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(5, NULL, 22, 27700.00, 'efectivo', '2025-10-14 22:43:09', '2025-10-22 17:39:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(6, NULL, 24, 27700.00, 'transferencia', '2025-10-22 15:09:46', '2025-10-22 18:09:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(8, NULL, 24, 27700.00, 'transferencia', '2025-10-22 15:15:52', '2025-10-22 18:15:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(13, NULL, 24, 27700.00, 'transferencia', '2025-10-22 16:25:25', '2025-10-22 19:25:25', 'FAC-20251022-000013', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(14, NULL, 24, 27700.00, 'transferencia', '2025-10-22 16:25:56', '2025-10-22 19:25:56', 'FAC-20251022-000014', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(17, 1, 25, 53000.00, 'transferencia', '2025-10-22 16:52:30', '2025-10-22 19:52:30', 'FAC-20251022-000017', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(18, 2, 27, 25000.00, 'transferencia', '2025-10-22 16:53:00', '2025-10-22 19:53:00', 'FAC-20251022-000018', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(19, 2, 27, 25000.00, 'transferencia', '2025-10-22 17:04:25', '2025-10-22 20:04:25', 'FAC-20251022-000019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(20, 2, 27, 25000.00, 'transferencia', '2025-10-22 19:09:47', '2025-10-22 22:09:47', 'FAC-20251023-000020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(21, 2, 27, 25000.00, 'transferencia', '2025-10-22 19:09:47', '2025-10-22 22:09:47', 'FAC-20251023-000021', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(22, 1, 25, 53000.00, 'efectivo', '2025-10-22 19:24:53', '2025-10-22 22:24:53', 'FAC-20251023-000022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(23, 2, 27, 25000.00, 'efectivo', '2025-10-22 19:43:46', '2025-10-22 22:43:46', 'FAC-20251023-000023', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(24, 2, 27, 25000.00, 'transferencia', '2025-10-22 19:44:44', '2025-10-22 22:44:44', 'FAC-20251023-000024', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(25, 2, 27, 25000.00, 'transferencia', '2025-10-22 19:44:44', '2025-10-22 22:44:44', 'FAC-20251023-000025', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(26, 2, 27, 25000.00, 'efectivo', '2025-10-22 19:48:15', '2025-10-22 22:48:15', 'FAC-20251023-000026', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(27, 3, 28, 80700.00, 'transferencia', '2025-10-22 21:35:54', '2025-10-23 00:35:54', 'FAC-20251023-000027', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(28, 2, 27, 25000.00, 'efectivo', '2025-10-22 21:38:32', '2025-10-23 00:38:35', 'FAC-20251023-000028', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(29, 3, 28, 80700.00, 'efectivo', '2025-10-22 21:51:52', '2025-10-23 00:51:53', 'FAC-20251023-000029', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(30, 1, 25, 53000.00, 'transferencia', '2025-10-23 13:22:44', '2025-10-23 16:22:44', 'FAC-20251023-000030', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(31, 1, 25, 53000.00, 'efectivo', '2025-10-23 13:36:02', '2025-10-23 16:36:02', 'FAC-20251023-000031', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(32, 7, 34, 28000.00, 'efectivo', '2025-10-23 13:36:56', '2025-10-23 16:36:56', 'FAC-20251023-000032', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(33, 7, 34, 28000.00, 'efectivo', '2025-10-23 13:37:18', '2025-10-23 16:37:18', 'FAC-20251023-000033', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(34, 7, 34, 28000.00, 'efectivo', '2025-10-23 14:04:01', '2025-10-23 17:04:01', 'COMP-20251023-000034', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(35, 1, 25, 53000.00, 'efectivo', '2025-10-23 14:04:55', '2025-10-23 17:04:55', 'COMP-20251023-000035', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(36, 4, 30, 27700.00, 'efectivo', '2025-10-23 14:17:10', '2025-10-23 17:17:10', 'COMP-20251023-000036', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(37, 4, 30, 27700.00, 'efectivo', '2025-10-23 14:17:51', '2025-10-23 17:17:51', 'COMP-20251023-000037', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(38, 5, 31, 28000.00, 'transferencia', '2025-10-23 14:53:01', '2025-10-23 17:53:01', 'COMP-20251023-000038', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(39, 5, 31, 28000.00, 'efectivo', '2025-10-23 14:53:37', '2025-10-23 17:53:37', 'COMP-20251023-000039', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(40, 1, 25, 53000.00, 'efectivo', '2025-10-23 15:23:13', '2025-10-23 18:23:13', 'COMP-20251023-000040', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(41, 6, 33, 28000.00, 'transferencia', '2025-10-23 17:48:33', '2025-10-23 20:48:33', 'COMP-20251023-000041', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(42, 13, 41, 28000.00, 'transferencia', '2025-10-23 17:52:36', '2025-10-23 20:52:36', 'COMP-20251023-000042', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(43, 15, 43, 28000.00, 'efectivo', '2025-10-23 17:57:55', '2025-10-23 20:57:55', 'COMP-20251023-000043', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(44, 19, 48, 28000.00, 'efectivo', '2025-10-23 18:07:52', '2025-10-23 21:07:52', 'COMP-20251023-000044', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado'),
(45, NULL, 266, 28000.00, 'mercadopago', '2025-09-01 19:38:00', '2025-09-02 01:27:00', 'TX266-51596', 'ALIAS266', '0042582558507642296058', 'Titular Gutiérrez', '30162868426', 'BBVA', 'https://comprobantes.fake/266.pdf', 'Pago generado automáticamente', 'completado'),
(46, NULL, 278, 28000.00, 'mercadopago', '2025-09-17 20:09:00', '2025-09-17 21:56:00', 'TX278-331262', 'ALIAS278', '0512091956578280770566', 'Titular Fernández', '30297080583', 'BBVA', 'https://comprobantes.fake/278.pdf', 'Pago generado automáticamente', 'completado'),
(47, NULL, 314, 28000.00, 'efectivo', '2025-09-24 21:43:00', '2025-09-24 22:46:00', 'TX314-556176', NULL, NULL, 'Titular Fernández', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(48, 12, 325, 28000.00, 'efectivo', '2025-09-19 11:26:00', '2025-09-19 15:36:00', 'TX325-131363', NULL, NULL, 'Titular Ríos', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(49, 13, 329, 28000.00, 'mercadopago', '2025-09-24 12:10:00', '2025-09-24 16:28:00', 'TX329-372426', 'ALIAS329', '0187467082578192540820', 'Titular Romero', '30537872662', 'Banco Provincia', 'https://comprobantes.fake/329.pdf', 'Pago generado automáticamente', 'completado'),
(50, 2, 333, 28000.00, 'transferencia', '2025-09-15 17:40:00', '2025-09-15 19:20:00', 'TX333-448692', 'ALIAS333', '0420525741223735500756', 'Titular Ramírez', '30521185374', 'Banco Provincia', 'https://comprobantes.fake/333.pdf', 'Pago generado automáticamente', 'completado'),
(51, 20, 376, 28000.00, 'efectivo', '2025-09-12 13:59:00', '2025-09-12 17:56:00', 'TX376-996341', NULL, NULL, 'Titular Gutiérrez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(52, NULL, 382, 28000.00, 'mercadopago', '2025-09-30 17:45:00', '2025-09-30 22:07:00', 'TX382-276619', 'ALIAS382', '0158437333217298000962', 'Titular Ibarra', '30336333446', 'BBVA', 'https://comprobantes.fake/382.pdf', 'Pago generado automáticamente', 'completado'),
(53, 3, 392, 28000.00, 'mercadopago', '2025-09-26 16:03:00', '2025-09-26 19:26:00', 'TX392-169097', 'ALIAS392', '0955702220979800600271', 'Titular Ríos', '30488989939', 'BBVA', 'https://comprobantes.fake/392.pdf', 'Pago generado automáticamente', 'completado'),
(54, 2, 398, 28000.00, 'mercadopago', '2025-09-09 18:39:00', '2025-09-09 23:17:00', 'TX398-836211', 'ALIAS398', '0082902144717892770905', 'Titular Silva', '30280681188', 'BBVA', 'https://comprobantes.fake/398.pdf', 'Pago generado automáticamente', 'completado'),
(55, 20, 415, 28000.00, 'transferencia', '2025-10-02 14:07:00', '2025-10-02 17:29:00', 'TX415-487193', 'ALIAS415', '0565413752166008300365', 'Titular Díaz', '30131207396', 'Banco Macro', 'https://comprobantes.fake/415.pdf', 'Pago generado automáticamente', 'completado'),
(56, 14, 422, 28000.00, 'transferencia', '2025-09-05 16:16:00', '2025-09-05 20:44:00', 'TX422-469142', 'ALIAS422', '0847938363298716400832', 'Titular Díaz', '30617512183', 'Banco Macro', 'https://comprobantes.fake/422.pdf', 'Pago generado automáticamente', 'completado'),
(57, 5, 440, 28000.00, 'efectivo', '2025-09-12 17:27:00', '2025-09-12 20:13:00', 'TX440-631754', NULL, NULL, 'Titular Pérez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(58, NULL, 456, 28000.00, 'mercadopago', '2025-09-05 11:10:00', '2025-09-05 15:57:00', 'TX456-899805', 'ALIAS456', '0652546737019481900563', 'Titular Rojas', '30858941245', 'BBVA', 'https://comprobantes.fake/456.pdf', 'Pago generado automáticamente', 'completado'),
(59, 2, 256, 25000.00, 'transferencia', '2025-10-23 16:45:00', '2025-10-23 22:02:00', 'TX256-547526', 'ALIAS256', '0452138198029378600618', 'Titular Ruiz', '30734138169', 'Santander', 'https://comprobantes.fake/256.pdf', 'Pago generado automáticamente', 'completado'),
(60, 3, 257, 25000.00, 'mercadopago', '2025-09-02 16:20:00', '2025-09-02 17:24:00', 'TX257-661553', 'ALIAS257', '0004739422355535833039', 'Titular Suárez', '30180966640', 'BBVA', 'https://comprobantes.fake/257.pdf', 'Pago generado automáticamente', 'completado'),
(61, 2, 258, 25000.00, 'transferencia', '2025-09-04 14:20:00', '2025-09-04 19:33:00', 'TX258-602934', 'ALIAS258', '0345206143656006200917', 'Titular Sosa', '30550522513', 'Banco Nación', 'https://comprobantes.fake/258.pdf', 'Pago generado automáticamente', 'completado'),
(62, 1, 260, 25000.00, 'transferencia', '2025-09-07 18:17:00', '2025-09-07 22:30:00', 'TX260-480659', 'ALIAS260', '0142258768102395120269', 'Titular Ramírez', '30919800132', 'BBVA', 'https://comprobantes.fake/260.pdf', 'Pago generado automáticamente', 'completado'),
(63, 3, 261, 25000.00, 'efectivo', '2025-09-02 16:34:00', '2025-09-02 20:01:00', 'TX261-398399', NULL, NULL, 'Titular Peralta', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(64, 14, 265, 25000.00, 'efectivo', '2025-09-17 20:29:00', '2025-09-17 21:57:00', 'TX265-147302', NULL, NULL, 'Titular Gutiérrez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(65, 15, 270, 25000.00, 'transferencia', '2025-10-01 18:19:00', '2025-10-01 22:16:00', 'TX270-205941', 'ALIAS270', '0757795286139282700171', 'Titular Márquez', '30582376922', 'Banco Provincia', 'https://comprobantes.fake/270.pdf', 'Pago generado automáticamente', 'completado'),
(66, 13, 274, 25000.00, 'efectivo', '2025-09-11 09:35:00', '2025-09-11 14:39:00', 'TX274-979220', NULL, NULL, 'Titular Peralta', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(67, 9, 312, 25000.00, 'mercadopago', '2025-09-12 20:20:00', '2025-09-13 00:00:00', 'TX312-493980', 'ALIAS312', '0520375053882948160119', 'Titular Márquez', '30038538164', 'Santander', 'https://comprobantes.fake/312.pdf', 'Pago generado automáticamente', 'completado'),
(68, 4, 313, 25000.00, 'efectivo', '2025-09-26 19:44:00', '2025-09-26 23:19:00', 'TX313-669813', NULL, NULL, 'Titular Torres', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(69, 1, 330, 25000.00, 'transferencia', '2025-09-03 14:51:00', '2025-09-03 18:30:00', 'TX330-334513', 'ALIAS330', '0105329100140676930523', 'Titular Silva', '30299538953', 'Santander', 'https://comprobantes.fake/330.pdf', 'Pago generado automáticamente', 'completado'),
(70, NULL, 338, 25000.00, 'mercadopago', '2025-09-17 17:32:00', '2025-09-17 19:57:00', 'TX338-338439', 'ALIAS338', '0412873667118021950049', 'Titular Rojas', '30006630564', 'Santander', 'https://comprobantes.fake/338.pdf', 'Pago generado automáticamente', 'completado'),
(71, 5, 340, 25000.00, 'transferencia', '2025-09-10 21:25:00', '2025-09-11 02:40:00', 'TX340-485082', 'ALIAS340', '0227711801629245100683', 'Titular Alvarez', '30733421298', 'BBVA', 'https://comprobantes.fake/340.pdf', 'Pago generado automáticamente', 'completado'),
(72, 4, 346, 25000.00, 'mercadopago', '2025-09-03 17:13:00', '2025-09-03 19:54:00', 'TX346-398494', 'ALIAS346', '0315717183347528000383', 'Titular López', '30968366262', 'BBVA', 'https://comprobantes.fake/346.pdf', 'Pago generado automáticamente', 'completado'),
(73, 1, 347, 25000.00, 'transferencia', '2025-09-30 16:37:00', '2025-09-30 21:30:00', 'TX347-256574', 'ALIAS347', '0650998591120353500485', 'Titular Ortiz', '30473349375', 'Santander', 'https://comprobantes.fake/347.pdf', 'Pago generado automáticamente', 'completado'),
(74, 19, 352, 25000.00, 'efectivo', '2025-10-03 14:19:00', '2025-10-03 16:11:00', 'TX352-669824', NULL, NULL, 'Titular Peralta', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(75, 12, 354, 25000.00, 'mercadopago', '2025-09-17 22:35:00', '2025-09-18 02:34:00', 'TX354-314753', 'ALIAS354', '0080729957745159000459', 'Titular Silva', '30054759603', 'Santander', 'https://comprobantes.fake/354.pdf', 'Pago generado automáticamente', 'completado'),
(76, 3, 359, 25000.00, 'efectivo', '2025-10-01 17:08:00', '2025-10-01 19:52:00', 'TX359-705523', NULL, NULL, 'Titular Herrera', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(77, NULL, 378, 25000.00, 'transferencia', '2025-09-22 19:34:00', '2025-09-23 01:05:00', 'TX378-778329', 'ALIAS378', '0578810556399459600559', 'Titular Medina', '30058888377', 'BBVA', 'https://comprobantes.fake/378.pdf', 'Pago generado automáticamente', 'completado'),
(78, 5, 418, 25000.00, 'mercadopago', '2025-09-17 17:05:00', '2025-09-17 21:30:00', 'TX418-672029', 'ALIAS418', '0103817541248926460503', 'Titular Alvarez', '30203549591', 'Banco Macro', 'https://comprobantes.fake/418.pdf', 'Pago generado automáticamente', 'completado'),
(79, NULL, 424, 25000.00, 'mercadopago', '2025-09-20 17:25:00', '2025-09-21 00:05:00', 'TX424-65760', 'ALIAS424', '0638817305340261500996', 'Titular López', '30067570721', 'Banco Provincia', 'https://comprobantes.fake/424.pdf', 'Pago generado automáticamente', 'completado'),
(80, 2, 437, 25000.00, 'transferencia', '2025-09-24 16:38:00', '2025-09-24 20:26:00', 'TX437-845338', 'ALIAS437', '0585273506664925700390', 'Titular Cabrera', '30195933391', 'Santander', 'https://comprobantes.fake/437.pdf', 'Pago generado automáticamente', 'completado'),
(81, 2, 442, 25000.00, 'transferencia', '2025-10-02 20:03:00', '2025-10-03 00:32:00', 'TX442-882383', 'ALIAS442', '0751546334243888400110', 'Titular Martínez', '30298273252', 'Banco Nación', 'https://comprobantes.fake/442.pdf', 'Pago generado automáticamente', 'completado'),
(82, 8, 443, 25000.00, 'mercadopago', '2025-09-03 17:06:00', '2025-09-03 22:24:00', 'TX443-276281', 'ALIAS443', '0948103212703115500911', 'Titular Rojas', '30714055690', 'Santander', 'https://comprobantes.fake/443.pdf', 'Pago generado automáticamente', 'completado'),
(83, NULL, 262, 90000.00, 'efectivo', '2025-09-04 17:14:00', '2025-09-04 19:38:00', 'TX262-115187', NULL, NULL, 'Titular Mendoza', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(84, 2, 277, 90000.00, 'transferencia', '2025-09-01 21:06:00', '2025-09-02 02:18:00', 'TX277-387568', 'ALIAS277', '0835683685574386000015', 'Titular Gómez', '30571506540', 'Santander', 'https://comprobantes.fake/277.pdf', 'Pago generado automáticamente', 'completado'),
(85, 17, 334, 90000.00, 'transferencia', '2025-09-02 12:46:00', '2025-09-02 16:34:00', 'TX334-573501', 'ALIAS334', '0062313541828015390591', 'Titular Medina', '30768378553', 'Banco Nación', 'https://comprobantes.fake/334.pdf', 'Pago generado automáticamente', 'completado'),
(86, 2, 341, 90000.00, 'efectivo', '2025-09-02 21:57:00', '2025-09-03 01:45:00', 'TX341-110951', NULL, NULL, 'Titular Díaz', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(87, 11, 405, 90000.00, 'mercadopago', '2025-09-09 20:38:00', '2025-09-10 00:32:00', 'TX405-658657', 'ALIAS405', '0511210549167553500580', 'Titular Gutiérrez', '30366768254', 'Banco Nación', 'https://comprobantes.fake/405.pdf', 'Pago generado automáticamente', 'completado'),
(88, 2, 426, 90000.00, 'transferencia', '2025-09-11 21:55:00', '2025-09-12 02:29:00', 'TX426-754015', 'ALIAS426', '0713796463528458500306', 'Titular Ortiz', '30393288813', 'Banco Nación', 'https://comprobantes.fake/426.pdf', 'Pago generado automáticamente', 'completado'),
(89, 1, 268, 27500.00, 'efectivo', '2025-09-24 17:33:00', '2025-09-24 22:15:00', 'TX268-578304', NULL, NULL, 'Titular Herrera', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(90, 3, 273, 27500.00, 'transferencia', '2025-09-19 16:45:00', '2025-09-19 21:55:00', 'TX273-223504', 'ALIAS273', '0482013044396427500739', 'Titular Ibarra', '30251721914', 'Banco Nación', 'https://comprobantes.fake/273.pdf', 'Pago generado automáticamente', 'completado'),
(91, 15, 275, 27500.00, 'transferencia', '2025-09-06 16:18:00', '2025-09-06 20:13:00', 'TX275-600031', 'ALIAS275', '0454492833888617150472', 'Titular Ibarra', '30998365596', 'Banco Macro', 'https://comprobantes.fake/275.pdf', 'Pago generado automáticamente', 'completado'),
(92, 19, 287, 27500.00, 'mercadopago', '2025-09-22 12:45:00', '2025-09-22 17:04:00', 'TX287-526932', 'ALIAS287', '0515399916577525400996', 'Titular Vega', '30434809384', 'Banco Nación', 'https://comprobantes.fake/287.pdf', 'Pago generado automáticamente', 'completado'),
(93, NULL, 288, 27500.00, 'transferencia', '2025-09-13 17:55:00', '2025-09-13 23:25:00', 'TX288-924898', 'ALIAS288', '0865924930075113600554', 'Titular Ruiz', '30176866179', 'Banco Provincia', 'https://comprobantes.fake/288.pdf', 'Pago generado automáticamente', 'completado'),
(94, 14, 309, 27500.00, 'transferencia', '2025-09-13 16:01:00', '2025-09-13 19:44:00', 'TX309-387605', 'ALIAS309', '0386816199297845570771', 'Titular Martínez', '30695869014', 'Banco Nación', 'https://comprobantes.fake/309.pdf', 'Pago generado automáticamente', 'completado'),
(95, 8, 316, 27500.00, 'mercadopago', '2025-09-18 16:51:00', '2025-09-18 22:57:00', 'TX316-389147', 'ALIAS316', '0544749812730355100556', 'Titular Alvarez', '30147290298', 'Banco Nación', 'https://comprobantes.fake/316.pdf', 'Pago generado automáticamente', 'completado'),
(96, 8, 331, 27500.00, 'mercadopago', '2025-09-27 14:19:00', '2025-09-27 19:46:00', 'TX331-641098', 'ALIAS331', '0123573787625482110694', 'Titular Vega', '30102147480', 'Banco Macro', 'https://comprobantes.fake/331.pdf', 'Pago generado automáticamente', 'completado'),
(97, 18, 370, 27500.00, 'mercadopago', '2025-09-29 12:20:00', '2025-09-29 16:32:00', 'TX370-537575', 'ALIAS370', '0213474560727807300454', 'Titular Márquez', '30632807857', 'Santander', 'https://comprobantes.fake/370.pdf', 'Pago generado automáticamente', 'completado'),
(98, 13, 372, 27500.00, 'efectivo', '2025-09-08 14:19:00', '2025-09-08 18:28:00', 'TX372-890833', NULL, NULL, 'Titular Romero', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(99, 13, 385, 27500.00, 'efectivo', '2025-09-22 20:23:00', '2025-09-22 22:54:00', 'TX385-376417', NULL, NULL, 'Titular Medina', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(100, 7, 408, 27500.00, 'efectivo', '2025-09-16 14:05:00', '2025-09-16 17:52:00', 'TX408-432974', NULL, NULL, 'Titular Benítez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(101, 2, 410, 27500.00, 'efectivo', '2025-09-27 18:08:00', '2025-09-27 19:44:00', 'TX410-217134', NULL, NULL, 'Titular Pérez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(102, 19, 411, 27500.00, 'transferencia', '2025-09-25 21:41:00', '2025-09-26 01:23:00', 'TX411-789733', 'ALIAS411', '0157548046817582140418', 'Titular Gutiérrez', '30620060357', 'Santander', 'https://comprobantes.fake/411.pdf', 'Pago generado automáticamente', 'completado'),
(103, NULL, 276, 27700.00, 'transferencia', '2025-09-05 18:35:00', '2025-09-05 22:31:00', 'TX276-756569', 'ALIAS276', '0821242638697142400836', 'Titular Romero', '30718791030', 'Banco Nación', 'https://comprobantes.fake/276.pdf', 'Pago generado automáticamente', 'completado'),
(104, 4, 299, 27700.00, 'efectivo', '2025-10-01 17:28:00', '2025-10-01 23:04:00', 'TX299-682087', NULL, NULL, 'Titular Domínguez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(105, NULL, 304, 27700.00, 'transferencia', '2025-09-19 15:08:00', '2025-09-19 17:51:00', 'TX304-557208', 'ALIAS304', '0763134432735978100144', 'Titular Sosa', '30430837351', 'BBVA', 'https://comprobantes.fake/304.pdf', 'Pago generado automáticamente', 'completado'),
(106, NULL, 306, 27700.00, 'efectivo', '2025-09-04 13:01:00', '2025-09-04 15:53:00', 'TX306-526706', NULL, NULL, 'Titular Ibarra', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(107, 15, 323, 27700.00, 'efectivo', '2025-09-15 18:24:00', '2025-09-15 23:31:00', 'TX323-362106', NULL, NULL, 'Titular Pérez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(108, NULL, 324, 27700.00, 'transferencia', '2025-09-20 18:06:00', '2025-09-20 21:17:00', 'TX324-136159', 'ALIAS324', '0331091647344726700246', 'Titular Ramírez', '30241621477', 'Banco Macro', 'https://comprobantes.fake/324.pdf', 'Pago generado automáticamente', 'completado'),
(109, 8, 335, 27700.00, 'transferencia', '2025-10-03 14:27:00', '2025-10-03 17:44:00', 'TX335-210243', 'ALIAS335', '0748934633796042400113', 'Titular Fernández', '30322912267', 'Banco Provincia', 'https://comprobantes.fake/335.pdf', 'Pago generado automáticamente', 'completado'),
(110, 12, 350, 27700.00, 'transferencia', '2025-09-05 17:43:00', '2025-09-05 23:08:00', 'TX350-517267', 'ALIAS350', '0794431696454465200420', 'Titular Peralta', '30718490027', 'Banco Provincia', 'https://comprobantes.fake/350.pdf', 'Pago generado automáticamente', 'completado'),
(111, 18, 358, 27700.00, 'transferencia', '2025-09-24 21:47:00', '2025-09-24 23:59:00', 'TX358-764404', 'ALIAS358', '0639998139478264600906', 'Titular Silva', '30613888670', 'Banco Provincia', 'https://comprobantes.fake/358.pdf', 'Pago generado automáticamente', 'completado'),
(112, 1, 362, 27700.00, 'mercadopago', '2025-09-10 19:39:00', '2025-09-11 00:20:00', 'TX362-829173', 'ALIAS362', '0198461574687121020504', 'Titular Cabrera', '30928549456', 'Banco Nación', 'https://comprobantes.fake/362.pdf', 'Pago generado automáticamente', 'completado'),
(113, 7, 363, 27700.00, 'mercadopago', '2025-09-02 22:26:00', '2025-09-03 02:53:00', 'TX363-873619', 'ALIAS363', '0630641278466806900532', 'Titular Martínez', '30769820526', 'Banco Provincia', 'https://comprobantes.fake/363.pdf', 'Pago generado automáticamente', 'completado'),
(114, 4, 368, 27700.00, 'mercadopago', '2025-09-06 18:44:00', '2025-09-06 19:47:00', 'TX368-692083', 'ALIAS368', '0048900837124232984168', 'Titular Ortiz', '30694567497', 'Santander', 'https://comprobantes.fake/368.pdf', 'Pago generado automáticamente', 'completado'),
(115, NULL, 377, 27700.00, 'mercadopago', '2025-09-06 16:23:00', '2025-09-06 17:45:00', 'TX377-991159', 'ALIAS377', '0578632323610254000919', 'Titular López', '30862520779', 'Banco Macro', 'https://comprobantes.fake/377.pdf', 'Pago generado automáticamente', 'completado'),
(116, 8, 379, 27700.00, 'efectivo', '2025-09-20 17:28:00', '2025-09-20 22:53:00', 'TX379-584879', NULL, NULL, 'Titular Sosa', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(117, 12, 397, 27700.00, 'efectivo', '2025-09-17 12:05:00', '2025-09-17 17:41:00', 'TX397-763587', NULL, NULL, 'Titular Herrera', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(118, NULL, 449, 27700.00, 'transferencia', '2025-09-13 13:41:00', '2025-09-13 16:11:00', 'TX449-171610', 'ALIAS449', '0339202504920961800181', 'Titular Fernández', '30888299498', 'Santander', 'https://comprobantes.fake/449.pdf', 'Pago generado automáticamente', 'completado'),
(119, 3, 259, 26200.00, 'mercadopago', '2025-09-02 11:02:00', '2025-09-02 15:57:00', 'TX259-97560', 'ALIAS259', '0454399702562391500979', 'Titular Vega', '30533388335', 'BBVA', 'https://comprobantes.fake/259.pdf', 'Pago generado automáticamente', 'completado'),
(120, NULL, 264, 26200.00, 'efectivo', '2025-09-01 18:21:00', '2025-09-01 21:57:00', 'TX264-140936', NULL, NULL, 'Titular Benítez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(121, NULL, 269, 26200.00, 'efectivo', '2025-09-04 13:52:00', '2025-09-04 17:57:00', 'TX269-81223', NULL, NULL, 'Titular Ruiz', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(122, 14, 282, 26200.00, 'mercadopago', '2025-09-22 15:34:00', '2025-09-22 17:06:00', 'TX282-47578', 'ALIAS282', '0159549854844389340655', 'Titular Sosa', '30796414536', 'Banco Nación', 'https://comprobantes.fake/282.pdf', 'Pago generado automáticamente', 'completado'),
(123, NULL, 300, 26200.00, 'mercadopago', '2025-09-25 15:47:00', '2025-09-25 18:13:00', 'TX300-988877', 'ALIAS300', '0779695388655825900931', 'Titular Alvarez', '30320138687', 'Santander', 'https://comprobantes.fake/300.pdf', 'Pago generado automáticamente', 'completado'),
(124, NULL, 305, 26200.00, 'efectivo', '2025-09-19 19:59:00', '2025-09-19 22:11:00', 'TX305-70334', NULL, NULL, 'Titular Martínez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(125, NULL, 328, 26200.00, 'mercadopago', '2025-09-15 20:20:00', '2025-09-16 00:57:00', 'TX328-421646', 'ALIAS328', '0199812930263404670734', 'Titular Pérez', '30071187186', 'Banco Nación', 'https://comprobantes.fake/328.pdf', 'Pago generado automáticamente', 'completado'),
(126, 15, 339, 26200.00, 'transferencia', '2025-09-22 20:25:00', '2025-09-23 02:33:00', 'TX339-517856', 'ALIAS339', '0919631773531093900044', 'Titular Domínguez', '30464048228', 'Banco Nación', 'https://comprobantes.fake/339.pdf', 'Pago generado automáticamente', 'completado'),
(127, 15, 357, 26200.00, 'transferencia', '2025-09-11 16:55:00', '2025-09-11 20:21:00', 'TX357-14559', 'ALIAS357', '0808950519011309800001', 'Titular Torres', '30578521422', 'Santander', 'https://comprobantes.fake/357.pdf', 'Pago generado automáticamente', 'completado'),
(128, 3, 366, 26200.00, 'mercadopago', '2025-09-13 12:24:00', '2025-09-13 14:29:00', 'TX366-879198', 'ALIAS366', '0468970073823789200707', 'Titular Díaz', '30129363696', 'Banco Macro', 'https://comprobantes.fake/366.pdf', 'Pago generado automáticamente', 'completado'),
(129, 6, 371, 26200.00, 'efectivo', '2025-09-05 20:34:00', '2025-09-05 23:37:00', 'TX371-878509', NULL, NULL, 'Titular Ortiz', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(130, NULL, 386, 26200.00, 'transferencia', '2025-10-03 13:18:00', '2025-10-03 15:47:00', 'TX386-65590', 'ALIAS386', '0105977691808694690333', 'Titular Martínez', '30347651765', 'BBVA', 'https://comprobantes.fake/386.pdf', 'Pago generado automáticamente', 'completado'),
(131, 15, 387, 26200.00, 'transferencia', '2025-09-20 16:37:00', '2025-09-20 21:00:00', 'TX387-132610', 'ALIAS387', '0909328235228851600148', 'Titular Mendoza', '30016062895', 'BBVA', 'https://comprobantes.fake/387.pdf', 'Pago generado automáticamente', 'completado'),
(132, NULL, 406, 26200.00, 'efectivo', '2025-09-25 14:51:00', '2025-09-25 16:53:00', 'TX406-681439', NULL, NULL, 'Titular López', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(133, 17, 452, 26200.00, 'efectivo', '2025-09-01 21:32:00', '2025-09-02 02:13:00', 'TX452-177373', NULL, NULL, 'Titular Torres', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(134, NULL, 455, 26200.00, 'mercadopago', '2025-09-05 19:07:00', '2025-09-06 00:50:00', 'TX455-172660', 'ALIAS455', '0988008833479144400422', 'Titular Ortiz', '30146289717', 'Banco Macro', 'https://comprobantes.fake/455.pdf', 'Pago generado automáticamente', 'completado'),
(135, 9, 272, 15000.00, 'mercadopago', '2025-09-01 10:52:00', '2025-09-01 16:25:00', 'TX272-598326', 'ALIAS272', '0358207657335519500996', 'Titular Romero', '30905665527', 'Banco Macro', 'https://comprobantes.fake/272.pdf', 'Pago generado automáticamente', 'completado'),
(136, NULL, 281, 15000.00, 'mercadopago', '2025-09-16 14:38:00', '2025-09-16 19:24:00', 'TX281-808555', 'ALIAS281', '0420490051079997800676', 'Titular Alvarez', '30122453579', 'Banco Macro', 'https://comprobantes.fake/281.pdf', 'Pago generado automáticamente', 'completado'),
(137, NULL, 284, 15000.00, 'transferencia', '2025-09-01 20:38:00', '2025-09-01 22:02:00', 'TX284-103440', 'ALIAS284', '0917600586933643300277', 'Titular Silva', '30635609467', 'Banco Provincia', 'https://comprobantes.fake/284.pdf', 'Pago generado automáticamente', 'completado'),
(138, 15, 285, 15000.00, 'mercadopago', '2025-09-19 17:55:00', '2025-09-20 00:32:00', 'TX285-956570', 'ALIAS285', '0308341066640188100671', 'Titular Torres', '30434942036', 'Banco Nación', 'https://comprobantes.fake/285.pdf', 'Pago generado automáticamente', 'completado'),
(139, 16, 291, 15000.00, 'transferencia', '2025-09-06 14:39:00', '2025-09-06 16:44:00', 'TX291-962538', 'ALIAS291', '0692854613710990700576', 'Titular Benítez', '30804718579', 'Banco Provincia', 'https://comprobantes.fake/291.pdf', 'Pago generado automáticamente', 'completado'),
(140, 9, 292, 15000.00, 'efectivo', '2025-09-27 16:40:00', '2025-09-27 22:07:00', 'TX292-747218', NULL, NULL, 'Titular Sosa', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(141, 4, 293, 15000.00, 'transferencia', '2025-09-18 17:38:00', '2025-09-18 21:51:00', 'TX293-236098', 'ALIAS293', '0600612850487803000294', 'Titular Suárez', '30672001075', 'Banco Macro', 'https://comprobantes.fake/293.pdf', 'Pago generado automáticamente', 'completado'),
(142, 20, 298, 15000.00, 'transferencia', '2025-09-24 12:09:00', '2025-09-24 17:51:00', 'TX298-41672', 'ALIAS298', '0689602401749791900322', 'Titular Cabrera', '30546165440', 'BBVA', 'https://comprobantes.fake/298.pdf', 'Pago generado automáticamente', 'completado'),
(143, NULL, 315, 15000.00, 'efectivo', '2025-09-11 15:12:00', '2025-09-11 18:18:00', 'TX315-936912', NULL, NULL, 'Titular López', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(144, 8, 342, 15000.00, 'efectivo', '2025-09-02 11:17:00', '2025-09-02 15:10:00', 'TX342-601880', NULL, NULL, 'Titular Torres', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(145, 7, 353, 15000.00, 'efectivo', '2025-09-11 16:35:00', '2025-09-11 21:26:00', 'TX353-84839', NULL, NULL, 'Titular Gómez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(146, 10, 360, 15000.00, 'mercadopago', '2025-09-23 12:09:00', '2025-09-23 17:21:00', 'TX360-600179', 'ALIAS360', '0290936379964404160654', 'Titular Pérez', '30397905507', 'Banco Nación', 'https://comprobantes.fake/360.pdf', 'Pago generado automáticamente', 'completado'),
(147, 6, 367, 15000.00, 'mercadopago', '2025-09-22 15:22:00', '2025-09-22 18:15:00', 'TX367-680342', 'ALIAS367', '0463843517437431500278', 'Titular Medina', '30999418338', 'Banco Nación', 'https://comprobantes.fake/367.pdf', 'Pago generado automáticamente', 'completado'),
(148, 2, 369, 15000.00, 'mercadopago', '2025-09-29 11:45:00', '2025-09-29 15:24:00', 'TX369-637442', 'ALIAS369', '0753153354630948400853', 'Titular Torres', '30007740370', 'Banco Macro', 'https://comprobantes.fake/369.pdf', 'Pago generado automáticamente', 'completado'),
(149, NULL, 380, 15000.00, 'transferencia', '2025-09-10 16:13:00', '2025-09-10 22:14:00', 'TX380-452088', 'ALIAS380', '0454266748814160700915', 'Titular Martínez', '30212539316', 'Banco Provincia', 'https://comprobantes.fake/380.pdf', 'Pago generado automáticamente', 'completado'),
(150, 13, 381, 15000.00, 'mercadopago', '2025-09-10 12:53:00', '2025-09-10 14:32:00', 'TX381-280891', 'ALIAS381', '0001767325216687587166', 'Titular Medina', '30825501886', 'BBVA', 'https://comprobantes.fake/381.pdf', 'Pago generado automáticamente', 'completado'),
(151, NULL, 383, 15000.00, 'mercadopago', '2025-10-01 14:52:00', '2025-10-01 18:05:00', 'TX383-4075', 'ALIAS383', '0197007892836823970972', 'Titular Vega', '30273039005', 'Banco Macro', 'https://comprobantes.fake/383.pdf', 'Pago generado automáticamente', 'completado'),
(152, 10, 384, 15000.00, 'transferencia', '2025-09-24 20:11:00', '2025-09-24 23:11:00', 'TX384-907690', 'ALIAS384', '0274294102819910340648', 'Titular Acosta', '30419117615', 'Banco Nación', 'https://comprobantes.fake/384.pdf', 'Pago generado automáticamente', 'completado'),
(153, 10, 389, 15000.00, 'transferencia', '2025-09-27 12:18:00', '2025-09-27 18:15:00', 'TX389-60245', 'ALIAS389', '0425882229978109000948', 'Titular Rojas', '30465732722', 'Banco Macro', 'https://comprobantes.fake/389.pdf', 'Pago generado automáticamente', 'completado'),
(154, 20, 395, 15000.00, 'efectivo', '2025-09-15 19:23:00', '2025-09-15 21:26:00', 'TX395-664308', NULL, NULL, 'Titular Alvarez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(155, 2, 403, 15000.00, 'mercadopago', '2025-09-30 20:20:00', '2025-10-01 01:38:00', 'TX403-336678', 'ALIAS403', '0071528698384322880347', 'Titular Martínez', '30523460340', 'Banco Macro', 'https://comprobantes.fake/403.pdf', 'Pago generado automáticamente', 'completado'),
(156, NULL, 404, 15000.00, 'efectivo', '2025-09-05 18:51:00', '2025-09-05 20:06:00', 'TX404-773627', NULL, NULL, 'Titular Sosa', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(157, NULL, 409, 15000.00, 'mercadopago', '2025-09-25 18:05:00', '2025-09-25 23:21:00', 'TX409-229422', 'ALIAS409', '0750152046559520000062', 'Titular Martínez', '30062001274', 'Banco Nación', 'https://comprobantes.fake/409.pdf', 'Pago generado automáticamente', 'completado'),
(158, 8, 412, 15000.00, 'transferencia', '2025-09-26 15:17:00', '2025-09-26 18:41:00', 'TX412-445216', 'ALIAS412', '0580722217057572900567', 'Titular Sosa', '30097636155', 'BBVA', 'https://comprobantes.fake/412.pdf', 'Pago generado automáticamente', 'completado'),
(159, 15, 421, 15000.00, 'transferencia', '2025-09-11 21:07:00', '2025-09-11 22:30:00', 'TX421-952049', 'ALIAS421', '0566947095624121900978', 'Titular Alvarez', '30192095544', 'Banco Nación', 'https://comprobantes.fake/421.pdf', 'Pago generado automáticamente', 'completado'),
(160, 20, 428, 15000.00, 'transferencia', '2025-09-25 17:14:00', '2025-09-25 21:25:00', 'TX428-361483', 'ALIAS428', '0798366606047718400907', 'Titular Silva', '30141811610', 'Santander', 'https://comprobantes.fake/428.pdf', 'Pago generado automáticamente', 'completado'),
(161, 2, 429, 15000.00, 'transferencia', '2025-10-02 20:30:00', '2025-10-02 23:50:00', 'TX429-230393', 'ALIAS429', '0960959468000530700113', 'Titular Torres', '30685205557', 'Banco Nación', 'https://comprobantes.fake/429.pdf', 'Pago generado automáticamente', 'completado'),
(162, 11, 431, 15000.00, 'transferencia', '2025-09-29 21:02:00', '2025-09-30 02:13:00', 'TX431-554000', 'ALIAS431', '0165123828840557300163', 'Titular Ríos', '30322719626', 'Banco Nación', 'https://comprobantes.fake/431.pdf', 'Pago generado automáticamente', 'completado'),
(163, NULL, 432, 15000.00, 'transferencia', '2025-09-03 21:49:00', '2025-09-04 00:11:00', 'TX432-227007', 'ALIAS432', '0051618568647297630577', 'Titular Martínez', '30730499350', 'Santander', 'https://comprobantes.fake/432.pdf', 'Pago generado automáticamente', 'completado'),
(164, NULL, 451, 15000.00, 'transferencia', '2025-09-09 17:25:00', '2025-09-09 21:14:00', 'TX451-612749', 'ALIAS451', '0136742771730518690845', 'Titular Benítez', '30817098818', 'Banco Macro', 'https://comprobantes.fake/451.pdf', 'Pago generado automáticamente', 'completado'),
(165, 4, 453, 15000.00, 'efectivo', '2025-09-08 21:43:00', '2025-09-08 23:41:00', 'TX453-707865', NULL, NULL, 'Titular Mendoza', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(166, NULL, 271, 17600.00, 'transferencia', '2025-09-16 15:04:00', '2025-09-16 17:08:00', 'TX271-797199', 'ALIAS271', '0316277547102680000189', 'Titular Domínguez', '30000107733', 'Banco Macro', 'https://comprobantes.fake/271.pdf', 'Pago generado automáticamente', 'completado'),
(167, 15, 289, 17600.00, 'efectivo', '2025-09-16 12:42:00', '2025-09-16 18:34:00', 'TX289-319666', NULL, NULL, 'Titular Díaz', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(168, NULL, 294, 17600.00, 'transferencia', '2025-09-02 12:00:00', '2025-09-02 17:58:00', 'TX294-282800', 'ALIAS294', '0992573332965908000114', 'Titular Pérez', '30594605289', 'BBVA', 'https://comprobantes.fake/294.pdf', 'Pago generado automáticamente', 'completado'),
(169, 11, 301, 17600.00, 'transferencia', '2025-09-13 13:32:00', '2025-09-13 16:32:00', 'TX301-56706', 'ALIAS301', '0570452617081303700682', 'Titular Acosta', '30699354025', 'Banco Macro', 'https://comprobantes.fake/301.pdf', 'Pago generado automáticamente', 'completado'),
(170, 16, 308, 17600.00, 'efectivo', '2025-09-05 12:45:00', '2025-09-05 17:30:00', 'TX308-874413', NULL, NULL, 'Titular Mendoza', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(171, NULL, 319, 17600.00, 'mercadopago', '2025-09-03 14:14:00', '2025-09-03 16:39:00', 'TX319-209182', 'ALIAS319', '0172796086569126800236', 'Titular Díaz', '30663782468', 'BBVA', 'https://comprobantes.fake/319.pdf', 'Pago generado automáticamente', 'completado'),
(172, NULL, 321, 17600.00, 'efectivo', '2025-09-06 16:51:00', '2025-09-06 19:24:00', 'TX321-152472', NULL, NULL, 'Titular Gutiérrez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(173, 1, 337, 17600.00, 'transferencia', '2025-09-08 20:25:00', '2025-09-09 00:37:00', 'TX337-40828', 'ALIAS337', '0170723929228935300731', 'Titular Silva', '30143498652', 'Banco Macro', 'https://comprobantes.fake/337.pdf', 'Pago generado automáticamente', 'completado'),
(174, 15, 343, 17600.00, 'efectivo', '2025-09-04 20:37:00', '2025-09-05 00:16:00', 'TX343-456126', NULL, NULL, 'Titular Acosta', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(175, 17, 345, 17600.00, 'mercadopago', '2025-09-19 16:14:00', '2025-09-19 18:39:00', 'TX345-78737', 'ALIAS345', '0896819686421025300247', 'Titular Ortiz', '30548966144', 'Banco Nación', 'https://comprobantes.fake/345.pdf', 'Pago generado automáticamente', 'completado'),
(176, NULL, 364, 17600.00, 'transferencia', '2025-10-03 19:52:00', '2025-10-04 00:31:00', 'TX364-25493', 'ALIAS364', '0487701473280509440362', 'Titular Acosta', '30347029400', 'BBVA', 'https://comprobantes.fake/364.pdf', 'Pago generado automáticamente', 'completado'),
(177, 3, 399, 17600.00, 'efectivo', '2025-09-24 12:43:00', '2025-09-24 19:01:00', 'TX399-564929', NULL, NULL, 'Titular Vega', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(178, NULL, 402, 17600.00, 'mercadopago', '2025-09-29 11:07:00', '2025-09-29 17:23:00', 'TX402-563157', 'ALIAS402', '0457192502410330300596', 'Titular Medina', '30610866977', 'Banco Provincia', 'https://comprobantes.fake/402.pdf', 'Pago generado automáticamente', 'completado'),
(179, NULL, 450, 17600.00, 'transferencia', '2025-09-06 12:29:00', '2025-09-06 17:22:00', 'TX450-234027', 'ALIAS450', '0634347437540392800469', 'Titular Gómez', '30445228383', 'Santander', 'https://comprobantes.fake/450.pdf', 'Pago generado automáticamente', 'completado'),
(180, NULL, 279, 23000.00, 'mercadopago', '2025-09-01 11:23:00', '2025-09-01 14:59:00', 'TX279-336349', 'ALIAS279', '0940795123521979000694', 'Titular Romero', '30652245726', 'Banco Nación', 'https://comprobantes.fake/279.pdf', 'Pago generado automáticamente', 'completado'),
(181, 7, 280, 23000.00, 'mercadopago', '2025-09-16 19:47:00', '2025-09-17 01:51:00', 'TX280-278030', 'ALIAS280', '0246548653809864670398', 'Titular Medina', '30253609686', 'Banco Nación', 'https://comprobantes.fake/280.pdf', 'Pago generado automáticamente', 'completado'),
(182, 10, 302, 23000.00, 'transferencia', '2025-09-16 16:20:00', '2025-09-16 17:56:00', 'TX302-208135', 'ALIAS302', '0708293861437862700917', 'Titular Ríos', '30460425570', 'Banco Macro', 'https://comprobantes.fake/302.pdf', 'Pago generado automáticamente', 'completado'),
(183, 5, 327, 23000.00, 'transferencia', '2025-09-27 12:23:00', '2025-09-27 19:03:00', 'TX327-114651', 'ALIAS327', '0718590233212886700248', 'Titular Ramírez', '30089211580', 'BBVA', 'https://comprobantes.fake/327.pdf', 'Pago generado automáticamente', 'completado'),
(184, NULL, 348, 23000.00, 'efectivo', '2025-09-05 17:37:00', '2025-09-05 23:05:00', 'TX348-493753', NULL, NULL, 'Titular Ortiz', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(185, 5, 355, 23000.00, 'transferencia', '2025-09-22 14:01:00', '2025-09-22 16:34:00', 'TX355-440610', 'ALIAS355', '0019294655899791660774', 'Titular Acosta', '30815325442', 'BBVA', 'https://comprobantes.fake/355.pdf', 'Pago generado automáticamente', 'completado'),
(186, 20, 356, 23000.00, 'efectivo', '2025-09-05 14:29:00', '2025-09-05 19:46:00', 'TX356-487213', NULL, NULL, 'Titular Suárez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(187, 4, 365, 23000.00, 'transferencia', '2025-09-22 11:55:00', '2025-09-22 15:40:00', 'TX365-407508', 'ALIAS365', '0535720885298886300456', 'Titular Romero', '30673239350', 'Santander', 'https://comprobantes.fake/365.pdf', 'Pago generado automáticamente', 'completado'),
(188, 12, 374, 23000.00, 'mercadopago', '2025-09-16 19:04:00', '2025-09-16 21:29:00', 'TX374-290629', 'ALIAS374', '0337188383878384100814', 'Titular Suárez', '30058708460', 'Santander', 'https://comprobantes.fake/374.pdf', 'Pago generado automáticamente', 'completado'),
(189, 14, 388, 23000.00, 'efectivo', '2025-09-04 21:47:00', '2025-09-04 22:46:00', 'TX388-481769', NULL, NULL, 'Titular Peralta', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(190, 4, 390, 23000.00, 'transferencia', '2025-09-25 17:20:00', '2025-09-25 19:22:00', 'TX390-825904', 'ALIAS390', '0280923526064421570926', 'Titular Ibarra', '30791748909', 'Banco Nación', 'https://comprobantes.fake/390.pdf', 'Pago generado automáticamente', 'completado'),
(191, NULL, 394, 23000.00, 'transferencia', '2025-10-01 10:52:00', '2025-10-01 16:52:00', 'TX394-167021', 'ALIAS394', '0862012836022314500808', 'Titular Martínez', '30458957001', 'Santander', 'https://comprobantes.fake/394.pdf', 'Pago generado automáticamente', 'completado'),
(192, 9, 396, 23000.00, 'mercadopago', '2025-09-22 14:52:00', '2025-09-22 18:20:00', 'TX396-122449', 'ALIAS396', '0166220374560188830463', 'Titular Alvarez', '30820109925', 'BBVA', 'https://comprobantes.fake/396.pdf', 'Pago generado automáticamente', 'completado'),
(193, 14, 414, 23000.00, 'efectivo', '2025-09-15 16:39:00', '2025-09-15 20:16:00', 'TX414-367076', NULL, NULL, 'Titular Romero', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(194, 20, 417, 23000.00, 'efectivo', '2025-09-18 19:10:00', '2025-09-18 23:31:00', 'TX417-347782', NULL, NULL, 'Titular Ríos', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(195, 9, 434, 23000.00, 'mercadopago', '2025-09-20 16:34:00', '2025-09-20 20:13:00', 'TX434-518550', 'ALIAS434', '0486424121527396300876', 'Titular Ruiz', '30923069560', 'Santander', 'https://comprobantes.fake/434.pdf', 'Pago generado automáticamente', 'completado'),
(196, 6, 447, 23000.00, 'efectivo', '2025-09-06 19:32:00', '2025-09-06 23:28:00', 'TX447-175742', NULL, NULL, 'Titular Herrera', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(197, 13, 454, 23000.00, 'mercadopago', '2025-09-02 20:56:00', '2025-09-03 02:51:00', 'TX454-245727', 'ALIAS454', '0711781085200404000821', 'Titular Romero', '30973275844', 'Banco Macro', 'https://comprobantes.fake/454.pdf', 'Pago generado automáticamente', 'completado'),
(198, 14, 286, 25000.00, 'efectivo', '2025-09-19 13:55:00', '2025-09-19 20:15:00', 'TX286-710383', NULL, NULL, 'Titular Mendoza', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(199, NULL, 296, 25000.00, 'efectivo', '2025-09-16 12:25:00', '2025-09-16 17:11:00', 'TX296-633956', NULL, NULL, 'Titular Benítez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(200, 13, 307, 25000.00, 'mercadopago', '2025-09-29 16:32:00', '2025-09-29 18:38:00', 'TX307-834374', 'ALIAS307', '0500781399664265500000', 'Titular Medina', '30501569533', 'Banco Macro', 'https://comprobantes.fake/307.pdf', 'Pago generado automáticamente', 'completado'),
(201, 12, 317, 25000.00, 'efectivo', '2025-09-18 11:47:00', '2025-09-18 17:10:00', 'TX317-801972', NULL, NULL, 'Titular Sosa', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(202, 6, 318, 25000.00, 'efectivo', '2025-09-30 16:59:00', '2025-09-30 20:51:00', 'TX318-17182', NULL, NULL, 'Titular López', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(203, 14, 336, 25000.00, 'transferencia', '2025-09-10 13:01:00', '2025-09-10 15:15:00', 'TX336-988379', 'ALIAS336', '0181003964674662750939', 'Titular Ríos', '30156393875', 'Santander', 'https://comprobantes.fake/336.pdf', 'Pago generado automáticamente', 'completado'),
(204, 10, 351, 25000.00, 'transferencia', '2025-09-05 21:58:00', '2025-09-05 23:23:00', 'TX351-19068', 'ALIAS351', '0798429528994885800934', 'Titular Herrera', '30279415546', 'Banco Macro', 'https://comprobantes.fake/351.pdf', 'Pago generado automáticamente', 'completado'),
(205, NULL, 375, 25000.00, 'efectivo', '2025-10-01 20:45:00', '2025-10-02 01:02:00', 'TX375-598990', NULL, NULL, 'Titular Ramírez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(206, NULL, 401, 25000.00, 'transferencia', '2025-09-29 19:52:00', '2025-09-29 22:38:00', 'TX401-546397', 'ALIAS401', '0878907550944860700755', 'Titular Cabrera', '30140006860', 'Banco Macro', 'https://comprobantes.fake/401.pdf', 'Pago generado automáticamente', 'completado'),
(207, 1, 413, 25000.00, 'mercadopago', '2025-09-24 16:35:00', '2025-09-24 22:42:00', 'TX413-602676', 'ALIAS413', '0045693652746913630420', 'Titular Romero', '30965115626', 'Banco Macro', 'https://comprobantes.fake/413.pdf', 'Pago generado automáticamente', 'completado'),
(208, 14, 416, 25000.00, 'mercadopago', '2025-10-03 19:48:00', '2025-10-04 00:07:00', 'TX416-724484', 'ALIAS416', '0901147656981970700332', 'Titular Márquez', '30957983397', 'BBVA', 'https://comprobantes.fake/416.pdf', 'Pago generado automáticamente', 'completado'),
(209, 19, 425, 25000.00, 'efectivo', '2025-09-23 17:43:00', '2025-09-23 21:27:00', 'TX425-336773', NULL, NULL, 'Titular Domínguez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(210, 7, 436, 25000.00, 'mercadopago', '2025-09-26 12:31:00', '2025-09-26 16:22:00', 'TX436-133370', 'ALIAS436', '0318450890778052500192', 'Titular Pérez', '30005368299', 'Banco Macro', 'https://comprobantes.fake/436.pdf', 'Pago generado automáticamente', 'completado'),
(211, 12, 438, 25000.00, 'efectivo', '2025-09-23 19:14:00', '2025-09-23 21:59:00', 'TX438-687304', NULL, NULL, 'Titular Gutiérrez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(212, NULL, 439, 25000.00, 'transferencia', '2025-09-10 19:56:00', '2025-09-10 23:23:00', 'TX439-359949', 'ALIAS439', '0567979896970074560760', 'Titular López', '30096320758', 'Banco Provincia', 'https://comprobantes.fake/439.pdf', 'Pago generado automáticamente', 'completado'),
(213, NULL, 441, 25000.00, 'mercadopago', '2025-09-29 19:42:00', '2025-09-29 23:22:00', 'TX441-940644', 'ALIAS441', '0362173504533407740988', 'Titular López', '30858145065', 'Banco Provincia', 'https://comprobantes.fake/441.pdf', 'Pago generado automáticamente', 'completado'),
(214, 5, 444, 25000.00, 'efectivo', '2025-09-09 13:30:00', '2025-09-09 16:47:00', 'TX444-914406', NULL, NULL, 'Titular Ramírez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(215, NULL, 263, 177000.00, 'efectivo', '2025-09-05 17:09:00', '2025-09-05 21:08:00', 'TX263-252975', NULL, NULL, 'Titular Ramírez', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(216, 9, 267, 177000.00, 'transferencia', '2025-10-02 12:56:00', '2025-10-02 17:30:00', 'TX267-895254', 'ALIAS267', '0027017096082695852449', 'Titular Rojas', '30165551574', 'Banco Macro', 'https://comprobantes.fake/267.pdf', 'Pago generado automáticamente', 'completado'),
(217, 2, 290, 177000.00, 'mercadopago', '2025-10-01 17:13:00', '2025-10-01 23:07:00', 'TX290-54451', 'ALIAS290', '0309551069801255200384', 'Titular Cortes', '30993344862', 'Santander', 'https://comprobantes.fake/290.pdf', 'Pago generado automáticamente', 'completado'),
(218, 20, 311, 177000.00, 'efectivo', '2025-09-17 12:29:00', '2025-09-17 15:53:00', 'TX311-628654', NULL, NULL, 'Titular Medina', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(219, 11, 320, 177000.00, 'mercadopago', '2025-09-26 20:08:00', '2025-09-27 00:40:00', 'TX320-452286', 'ALIAS320', '0006558018742648902675', 'Titular Mendoza', '30359978049', 'BBVA', 'https://comprobantes.fake/320.pdf', 'Pago generado automáticamente', 'completado'),
(220, 19, 322, 177000.00, 'mercadopago', '2025-09-16 20:00:00', '2025-09-17 00:06:00', 'TX322-194764', 'ALIAS322', '0201247442701130600421', 'Titular Ruiz', '30505982253', 'Banco Provincia', 'https://comprobantes.fake/322.pdf', 'Pago generado automáticamente', 'completado'),
(221, 14, 326, 177000.00, 'mercadopago', '2025-09-17 20:24:00', '2025-09-18 02:01:00', 'TX326-802377', 'ALIAS326', '0934548786780376700265', 'Titular Silva', '30524413488', 'Santander', 'https://comprobantes.fake/326.pdf', 'Pago generado automáticamente', 'completado'),
(222, NULL, 332, 177000.00, 'transferencia', '2025-09-13 12:07:00', '2025-09-13 17:53:00', 'TX332-61876', 'ALIAS332', '0952250033572549000575', 'Titular Ibarra', '30021352848', 'Banco Provincia', 'https://comprobantes.fake/332.pdf', 'Pago generado automáticamente', 'completado');
INSERT INTO `pagos` (`id`, `grupo_turnos_id`, `turno_id`, `monto`, `metodo_pago`, `fecha_pago`, `updated_at`, `transaccion_id`, `alias_cbu`, `cbu`, `titular_cuenta`, `cuit_cuenta`, `banco`, `comprobante_url`, `notas`, `estado`) VALUES
(223, 3, 344, 177000.00, 'mercadopago', '2025-09-08 14:21:00', '2025-09-08 20:28:00', 'TX344-294574', 'ALIAS344', '0428513401587040500258', 'Titular Silva', '30008681188', 'Banco Provincia', 'https://comprobantes.fake/344.pdf', 'Pago generado automáticamente', 'completado'),
(224, 9, 349, 177000.00, 'efectivo', '2025-09-10 21:43:00', '2025-09-11 02:07:00', 'TX349-676985', NULL, NULL, 'Titular Mendoza', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(225, NULL, 419, 177000.00, 'transferencia', '2025-09-25 19:36:00', '2025-09-26 01:28:00', 'TX419-298589', 'ALIAS419', '0698829997050417500598', 'Titular Peralta', '30895413890', 'BBVA', 'https://comprobantes.fake/419.pdf', 'Pago generado automáticamente', 'completado'),
(226, 2, 420, 177000.00, 'mercadopago', '2025-10-01 15:12:00', '2025-10-01 19:44:00', 'TX420-715258', 'ALIAS420', '0518995870388109100449', 'Titular Ríos', '30689032750', 'Banco Nación', 'https://comprobantes.fake/420.pdf', 'Pago generado automáticamente', 'completado'),
(227, NULL, 445, 177000.00, 'transferencia', '2025-09-09 17:55:00', '2025-09-09 22:09:00', 'TX445-524268', 'ALIAS445', '0247622769556588260665', 'Titular Medina', '30583672957', 'Santander', 'https://comprobantes.fake/445.pdf', 'Pago generado automáticamente', 'completado'),
(228, 19, 446, 177000.00, 'mercadopago', '2025-09-27 17:51:00', '2025-09-27 20:11:00', 'TX446-931201', 'ALIAS446', '0327830408073803840845', 'Titular López', '30244242473', 'BBVA', 'https://comprobantes.fake/446.pdf', 'Pago generado automáticamente', 'completado'),
(229, 18, 448, 177000.00, 'mercadopago', '2025-09-09 20:26:00', '2025-09-10 02:24:00', 'TX448-380928', 'ALIAS448', '0161571435780796640665', 'Titular Rojas', '30840649007', 'Banco Provincia', 'https://comprobantes.fake/448.pdf', 'Pago generado automáticamente', 'completado'),
(230, 19, 283, 12000.00, 'transferencia', '2025-09-03 14:09:00', '2025-09-03 15:52:00', 'TX283-498119', 'ALIAS283', '0651377335797415400762', 'Titular Rojas', '30858503232', 'Banco Nación', 'https://comprobantes.fake/283.pdf', 'Pago generado automáticamente', 'completado'),
(231, 11, 295, 12000.00, 'transferencia', '2025-09-17 11:56:00', '2025-09-17 17:58:00', 'TX295-345025', 'ALIAS295', '0302269802710292700476', 'Titular Ortiz', '30474555723', 'Santander', 'https://comprobantes.fake/295.pdf', 'Pago generado automáticamente', 'completado'),
(232, NULL, 297, 12000.00, 'efectivo', '2025-09-02 16:11:00', '2025-09-02 19:10:00', 'TX297-831094', NULL, NULL, 'Titular Romero', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(233, 11, 303, 12000.00, 'transferencia', '2025-09-08 15:46:00', '2025-09-08 20:22:00', 'TX303-82418', 'ALIAS303', '0502432559153467970264', 'Titular Mendoza', '30817238544', 'Banco Provincia', 'https://comprobantes.fake/303.pdf', 'Pago generado automáticamente', 'completado'),
(234, 6, 310, 12000.00, 'efectivo', '2025-09-29 11:42:00', '2025-09-29 17:45:00', 'TX310-298673', NULL, NULL, 'Titular Romero', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(235, 20, 361, 12000.00, 'mercadopago', '2025-09-09 10:48:00', '2025-09-09 15:23:00', 'TX361-557254', 'ALIAS361', '0370533245960747140180', 'Titular Ortiz', '30792914328', 'Banco Macro', 'https://comprobantes.fake/361.pdf', 'Pago generado automáticamente', 'completado'),
(236, NULL, 373, 12000.00, 'mercadopago', '2025-09-29 12:09:00', '2025-09-29 17:28:00', 'TX373-561490', 'ALIAS373', '0573461158735175800182', 'Titular Alvarez', '30193784070', 'Banco Macro', 'https://comprobantes.fake/373.pdf', 'Pago generado automáticamente', 'completado'),
(237, 16, 391, 12000.00, 'transferencia', '2025-09-15 15:01:00', '2025-09-15 18:06:00', 'TX391-723271', 'ALIAS391', '0165938700703846940659', 'Titular Díaz', '30801574125', 'Banco Nación', 'https://comprobantes.fake/391.pdf', 'Pago generado automáticamente', 'completado'),
(238, 12, 393, 12000.00, 'mercadopago', '2025-09-18 13:17:00', '2025-09-18 18:06:00', 'TX393-100123', 'ALIAS393', '0164876260948252160524', 'Titular Alvarez', '30125429608', 'Banco Nación', 'https://comprobantes.fake/393.pdf', 'Pago generado automáticamente', 'completado'),
(239, 13, 400, 12000.00, 'mercadopago', '2025-09-09 17:44:00', '2025-09-10 00:04:00', 'TX400-793804', 'ALIAS400', '0096863838934212770102', 'Titular Márquez', '30223930741', 'Santander', 'https://comprobantes.fake/400.pdf', 'Pago generado automáticamente', 'completado'),
(240, NULL, 407, 12000.00, 'transferencia', '2025-09-03 17:56:00', '2025-09-03 21:02:00', 'TX407-854367', 'ALIAS407', '0492105911944160100897', 'Titular Alvarez', '30010820694', 'Banco Provincia', 'https://comprobantes.fake/407.pdf', 'Pago generado automáticamente', 'completado'),
(241, NULL, 423, 12000.00, 'mercadopago', '2025-09-25 13:23:00', '2025-09-25 17:08:00', 'TX423-900675', 'ALIAS423', '0527038465744851600933', 'Titular Vega', '30084720347', 'BBVA', 'https://comprobantes.fake/423.pdf', 'Pago generado automáticamente', 'completado'),
(242, 14, 427, 12000.00, 'mercadopago', '2025-09-05 16:22:00', '2025-09-05 21:17:00', 'TX427-111743', 'ALIAS427', '0464745829314688060988', 'Titular Ríos', '30548260237', 'BBVA', 'https://comprobantes.fake/427.pdf', 'Pago generado automáticamente', 'completado'),
(243, NULL, 430, 12000.00, 'efectivo', '2025-09-30 15:46:00', '2025-09-30 18:48:00', 'TX430-49100', NULL, NULL, 'Titular Fernández', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(244, 10, 433, 12000.00, 'efectivo', '2025-09-24 19:10:00', '2025-09-24 20:52:00', 'TX433-493875', NULL, NULL, 'Titular Romero', NULL, NULL, NULL, 'Pago generado automáticamente', 'completado'),
(245, 1, 435, 12000.00, 'mercadopago', '2025-09-22 22:15:00', '2025-09-23 00:17:00', 'TX435-854370', 'ALIAS435', '0306233370030516160968', 'Titular Fernández', '30921570331', 'BBVA', 'https://comprobantes.fake/435.pdf', 'Pago generado automáticamente', 'completado');

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
(7, 'Masajes', '', NULL, 1, '2025-11-02 21:30:31', '2025-11-02 18:30:31', 15000.00, 60),
(8, 'Semipermanente', 'Manicura semipermanente de larga duración', NULL, 1, '2025-10-10 21:05:58', NULL, 17600.00, 60),
(9, 'Kapping', 'Kapping gel para fortalecimiento de uñas naturales', NULL, 1, '2025-10-10 21:05:58', NULL, 23000.00, 75),
(10, 'Prueba', '', 'img/vacia.webp', 0, '2025-11-02 18:12:18', '2025-11-02 15:12:18', 25000.00, 60),
(11, 'Microshading', '', 'img/microshading.jpg', 0, '2025-11-02 21:30:55', '2025-11-02 18:30:55', 177000.00, 120),
(12, 'prueba de calidad', 'dfgg,i.o´puiyinbvtcrvfbfnnm,minbjvhcgbn,.,kñjmlk', 'img/vacia.webp', 0, '2025-11-02 21:33:44', '2025-11-02 18:33:44', 12000.00, 41);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `ID` int(11) NOT NULL,
  `nombre_cliente` varchar(100) NOT NULL,
  `apellido_cliente` varchar(100) NOT NULL,
  `telefono_cliente` varchar(20) NOT NULL,
  `dni_cliente` varchar(20) DEFAULT NULL,
  `ID_servicio_FK` int(11) NOT NULL,
  `ID_estado_turno_FK` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `recordatorio_enviado` tinyint(1) DEFAULT 0,
  `grupo_turnos_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turno`
--

INSERT INTO `turno` (`ID`, `nombre_cliente`, `apellido_cliente`, `telefono_cliente`, `dni_cliente`, `ID_servicio_FK`, `ID_estado_turno_FK`, `fecha`, `hora`, `recordatorio_enviado`, `grupo_turnos_id`) VALUES
(9, 'Sofi', 'Lopez', '3513874512', NULL, 1, 5, '2025-10-02', '12:00:00', 0, NULL),
(10, 'Lorena', 'Martin', '3512325212', NULL, 2, 5, '2025-10-08', '12:00:00', 0, NULL),
(11, 'Lula', 'Lara', '21245464', NULL, 1, 5, '2025-10-08', '10:00:00', 0, NULL),
(12, 'Marta', 'Nuñez', '54564323', NULL, 5, 5, '2025-10-09', '09:00:00', 0, NULL),
(13, 'Eugenia', 'Lur', '1235678', NULL, 3, 1, '2025-10-10', '11:00:00', 0, NULL),
(14, 'Lourdes', 'Yandre', '2541325413', NULL, 5, 7, '2025-10-10', '09:00:00', 0, NULL),
(15, 'Lula', 'Lopez', '134142354', NULL, 1, 7, '2025-10-10', '09:00:00', 0, NULL),
(16, 'Mariel', 'Ferrando', '3512526412', NULL, 1, 5, '2025-10-11', '11:00:00', 0, NULL),
(17, 'Lourdes', 'Ruiz', '3351257452', NULL, 2, 5, '2025-10-14', '18:00:00', 0, NULL),
(18, 'Sofi', 'Martinez', '3513514123', NULL, 5, 5, '2025-10-14', '17:00:00', 0, NULL),
(19, 'Maura', 'Martinez', '35135254561', NULL, 1, 5, '2025-10-14', '19:00:00', 0, NULL),
(20, 'Maria Elena', 'Tello', '3513254212', NULL, 6, 7, '2025-10-15', '11:00:00', 0, NULL),
(21, 'Jorgelina', 'Martinez', '354132654', NULL, 4, 1, '2025-10-15', '12:00:00', 0, NULL),
(22, 'Sofia', 'Muzzi', '35423165465', NULL, 5, 7, '2025-10-15', '13:00:00', 0, NULL),
(23, 'Liliana', 'Lula', '3513878521', '40852954', 1, 6, '2025-10-23', '09:00:00', 0, NULL),
(24, 'Liliana', 'Lula', '3513878521', '40852954', 5, 6, '2025-10-23', '09:00:00', 0, NULL),
(25, 'Florencia', 'Albarracin', '3513875107', '40829057', 1, 7, '2025-10-23', '12:00:00', 0, 1),
(26, 'Florencia', 'Albarracin', '3513875107', '40829057', 2, 7, '2025-10-23', '13:00:00', 0, 1),
(27, 'Tamara', 'Ruiz', '351352123', '40829058', 2, 7, '2025-10-23', '15:00:00', 0, 2),
(28, 'Florencia', 'Albarracin', '3513875107', '40829057', 2, 7, '2025-10-25', '09:00:00', 0, 3),
(29, 'Florencia', 'Albarracin', '3513875107', '40829057', 5, 7, '2025-10-25', '13:00:00', 0, 3),
(30, 'Tamara', 'Ruiz', '351352123', '40829058', 5, 7, '2025-10-25', '17:00:00', 0, 4),
(31, 'Lourdes', 'Rulo', '3513521215', '40829059', 1, 7, '2025-10-25', '16:00:00', 0, 5),
(32, 'Florencia', 'Albarracin', '3513875107', '40829057', 1, 7, '2025-10-25', '11:00:00', 0, 3),
(33, 'Florencia', 'Albarracin', '3513875107', '40829057', 1, 7, '2025-10-25', '14:00:00', 0, 6),
(34, 'Rolando', 'Garibay', '3513521035', '40829060', 1, 7, '2025-10-25', '18:00:00', 0, 7),
(35, 'Rolando', 'Garibay', '3513521035', '40829060', 2, 5, '2025-11-29', '11:00:00', 0, 8),
(36, 'Florencia', 'Albarracin', '3513875107', '40829057', 1, 5, '2025-10-24', '11:00:00', 0, 9),
(37, 'Tamara', 'Ruiz', '351352123', '40829058', 5, 4, '2025-10-25', '09:00:00', 0, 10),
(38, 'Tamara', 'Ruiz', '351352123', '40829058', 2, 5, '2025-10-25', '16:00:00', 0, 10),
(39, 'Liliana', 'Lula', '3513878521', '40852954', 4, 5, '2025-10-25', '13:00:00', 0, 11),
(40, 'Rolando', 'Garibay', '3513521035', '40829060', 4, 5, '2025-10-25', '17:00:00', 0, 12),
(41, 'Florencia', 'Albarracin', '3513875107', '40829057', 1, 7, '2025-10-27', '11:00:00', 0, 13),
(42, 'Rolando', 'Garibay', '3513521035', '40829060', 1, 5, '2025-10-27', '15:00:00', 0, 14),
(43, 'Liliana', 'Lula', '3513878521', '40852954', 1, 7, '2025-10-27', '13:00:00', 0, 15),
(44, 'Lourdes', 'Rulo', '3513521215', '40829059', 6, 5, '2025-10-27', '17:00:00', 0, 16),
(45, 'Rolando', 'Garibay', '3513521035', '40829060', 6, 5, '2025-10-27', '15:00:00', 0, 14),
(46, 'Maria', 'Romero', '3513521254', '40829061', 1, 5, '2025-10-27', '18:00:00', 0, 17),
(47, 'Maria Pia', 'Curtis', '351352124', '40829062', 1, 5, '2025-10-27', '17:00:00', 0, 18),
(48, 'Rocio', 'Gomez', '3513521512', '40829063', 1, 7, '2025-10-27', '09:00:00', 0, 19),
(49, 'Florencia', 'Albarracin', '3513875107', '40829057', 1, 5, '2025-10-25', '09:00:00', 0, 20),
(50, 'Florencia', 'Albarracin', '3513875107', '40829057', 4, 5, '2025-10-30', '12:00:00', 0, 21),
(51, 'Florencia', 'Albarracin', '3513875107', '40829057', 1, 5, '2025-10-31', '16:00:00', 0, 22),
(52, 'Florencia', 'Albarracin', '3513875107', '40829057', 1, 5, '2025-11-01', '13:00:00', 0, 23),
(53, 'Florencia', 'Albarracin', '3513875107', '40829057', 5, 5, '2025-10-31', '09:00:00', 0, 22),
(256, 'Tamara', 'Ruiz', '351352123', '40829058', 2, 7, '2025-10-23', '15:00:00', 0, 2),
(257, 'Milagros', 'Suárez', '3518666188', '40671109', 2, 5, '2025-09-02', '13:00:00', 1, 3),
(258, 'Andrea', 'Sosa', '3512856914', '43443672', 2, 4, '2025-09-04', '12:00:00', 1, 2),
(259, 'Ana', 'Vega', '3519904210', '43753202', 6, 6, '2025-09-02', '09:15:00', 1, 3),
(260, 'Milagros', 'Ramírez', '3514252410', '31241104', 2, 7, '2025-09-07', '15:30:00', 1, 1),
(261, 'Brenda', 'Peralta', '3511778507', '39021542', 2, 5, '2025-09-02', '14:15:00', 1, 3),
(262, 'Giselle', 'Mendoza', '3511380003', '21542884', 3, 4, '2025-09-04', '14:45:00', 1, NULL),
(263, 'Micaela', 'Ramírez', '3516353654', '22613456', 11, 7, '2025-09-05', '14:00:00', 1, NULL),
(264, 'Julieta', 'Benítez', '3518061744', '30982396', 6, 1, '2025-09-01', '17:45:00', 1, NULL),
(265, 'Gabriela', 'Gutiérrez', '3515146270', '44229388', 2, 7, '2025-09-17', '17:15:00', 1, 14),
(266, 'Marisol', 'Gutiérrez', '3518932528', '37795716', 1, 6, '2025-09-01', '18:30:00', 1, NULL),
(267, 'Claudia', 'Rojas', '3514303911', '44558743', 11, 4, '2025-10-02', '10:15:00', 0, 9),
(268, 'Florencia', 'Herrera', '3519638346', '42538074', 4, 1, '2025-09-24', '16:45:00', 0, 1),
(269, 'Martina', 'Ruiz', '3519839301', '43751200', 6, 1, '2025-09-04', '12:00:00', 0, NULL),
(270, 'Florencia', 'Márquez', '3517382997', '28153634', 2, 1, '2025-10-01', '15:15:00', 1, 15),
(271, 'Agustina', 'Domínguez', '3516701065', '23666460', 8, 7, '2025-09-16', '12:15:00', 1, NULL),
(272, 'Julieta', 'Romero', '3517317810', '41882351', 7, 4, '2025-09-01', '10:15:00', 1, 9),
(273, 'Vanesa', 'Ibarra', '3516026064', '35267547', 4, 4, '2025-09-19', '15:45:00', 0, 3),
(274, 'Lucía', 'Peralta', '3518050097', '36872858', 2, 4, '2025-09-11', '09:00:00', 0, 13),
(275, 'Giselle', 'Ibarra', '3513619399', '21333508', 4, 5, '2025-09-06', '15:30:00', 1, 15),
(276, 'Carolina', 'Romero', '3516247510', '35377510', 5, 7, '2025-09-05', '17:15:00', 1, NULL),
(277, 'Claudia', 'Gómez', '3513542784', '40524499', 3, 1, '2025-09-01', '19:30:00', 0, 2),
(278, 'Claudia', 'Fernández', '3518244935', '26831592', 1, 5, '2025-09-17', '17:45:00', 1, NULL),
(279, 'Marisol', 'Romero', '3510052427', '38511323', 9, 4, '2025-09-01', '10:00:00', 1, NULL),
(280, 'Daniela', 'Medina', '3512620450', '32006085', 9, 5, '2025-09-16', '19:00:00', 0, 7),
(281, 'Andrea', 'Alvarez', '3513226025', '33814801', 7, 1, '2025-09-16', '13:15:00', 1, NULL),
(282, 'Carolina', 'Sosa', '3517543303', '33370299', 6, 5, '2025-09-22', '13:00:00', 1, 14),
(283, 'Giselle', 'Rojas', '3518501429', '28907573', 12, 5, '2025-09-03', '10:45:00', 1, 19),
(284, 'Valentina', 'Silva', '3518169340', '43782887', 7, 5, '2025-09-01', '17:15:00', 1, NULL),
(285, 'Noelia', 'Torres', '3511484656', '43394432', 7, 4, '2025-09-19', '17:15:00', 1, 15),
(286, 'Giselle', 'Mendoza', '3512994680', '30196351', 10, 7, '2025-09-19', '12:45:00', 1, 14),
(287, 'Paula', 'Vega', '3517387214', '37295699', 4, 5, '2025-09-22', '10:15:00', 0, 19),
(288, 'María', 'Ruiz', '3510379176', '41131419', 4, 6, '2025-09-13', '15:45:00', 0, NULL),
(289, 'Claudia', 'Díaz', '3511632870', '38703434', 8, 4, '2025-09-16', '10:45:00', 1, 15),
(290, 'Gabriela', 'Cortes', '3517986872', '44952935', 11, 4, '2025-10-01', '16:30:00', 1, 2),
(291, 'Belén', 'Benítez', '3514714345', '30727672', 7, 4, '2025-09-06', '11:15:00', 0, 16),
(292, 'Brenda', 'Sosa', '3511665876', '22089290', 7, 7, '2025-09-27', '15:00:00', 0, 9),
(293, 'Agustina', 'Suárez', '3516688937', '27362568', 7, 1, '2025-09-18', '15:45:00', 1, 4),
(294, 'Lorena', 'Pérez', '3517298069', '38937852', 8, 4, '2025-09-02', '10:45:00', 0, NULL),
(295, 'Florencia', 'Ortiz', '3515375564', '34145368', 12, 1, '2025-09-17', '10:45:00', 0, 11),
(296, 'Agustina', 'Benítez', '3511003309', '25113103', 10, 1, '2025-09-16', '11:45:00', 1, NULL),
(297, 'Brenda', 'Romero', '3515299124', '23627192', 12, 6, '2025-09-02', '13:45:00', 0, NULL),
(298, 'Daniela', 'Cabrera', '3513149190', '31650225', 7, 1, '2025-09-24', '10:30:00', 1, 20),
(299, 'Ana', 'Domínguez', '3515726284', '40666589', 5, 7, '2025-10-01', '16:45:00', 0, 4),
(300, 'Marisol', 'Alvarez', '3511473799', '42420684', 6, 6, '2025-09-25', '14:00:00', 1, NULL),
(301, 'Vanesa', 'Acosta', '3514549480', '37335133', 8, 6, '2025-09-13', '10:15:00', 0, 11),
(302, 'Brenda', 'Ríos', '3517701436', '43212525', 9, 6, '2025-09-16', '13:30:00', 1, 10),
(303, 'Valentina', 'Mendoza', '3518557444', '27735573', 12, 1, '2025-09-08', '12:30:00', 0, 11),
(304, 'Vanesa', 'Sosa', '3517498941', '26513505', 5, 4, '2025-09-19', '12:30:00', 0, NULL),
(305, 'Florencia', 'Martínez', '3510842710', '39262398', 6, 6, '2025-09-19', '16:45:00', 0, NULL),
(306, 'Florencia', 'Ibarra', '3517116719', '41120693', 5, 7, '2025-09-04', '11:15:00', 0, NULL),
(307, 'Ana', 'Medina', '3516999386', '35116756', 10, 5, '2025-09-29', '13:45:00', 0, 13),
(308, 'Noelia', 'Mendoza', '3511334123', '25832214', 8, 6, '2025-09-05', '11:00:00', 1, 16),
(309, 'Sofía', 'Martínez', '3513447134', '40973466', 4, 7, '2025-09-13', '15:00:00', 0, 14),
(310, 'Andrea', 'Romero', '3512102499', '29684164', 12, 5, '2025-09-29', '10:45:00', 1, 6),
(311, 'Milagros', 'Medina', '3517719065', '40257268', 11, 4, '2025-09-17', '09:00:00', 0, 20),
(312, 'Gabriela', 'Márquez', '3514902787', '29333385', 2, 6, '2025-09-12', '18:45:00', 1, 9),
(313, 'Valentina', 'Torres', '3515125674', '42232614', 2, 6, '2025-09-26', '17:00:00', 1, 4),
(314, 'Martina', 'Fernández', '3516808760', '26294225', 1, 6, '2025-09-24', '18:45:00', 1, NULL),
(315, 'Micaela', 'López', '3514771093', '43816359', 7, 7, '2025-09-11', '13:00:00', 0, NULL),
(316, 'Marisol', 'Alvarez', '3511712748', '43669686', 4, 6, '2025-09-18', '15:45:00', 0, 8),
(317, 'Camila', 'Sosa', '3519821465', '37036849', 10, 5, '2025-09-18', '09:30:00', 1, 12),
(318, 'Viviana', 'López', '3517875588', '32657069', 10, 4, '2025-09-30', '14:15:00', 0, 6),
(319, 'Viviana', 'Díaz', '3516057662', '36620471', 8, 1, '2025-09-03', '11:30:00', 1, NULL),
(320, 'Tamara', 'Mendoza', '3512621745', '40913115', 11, 5, '2025-09-26', '19:00:00', 1, 11),
(321, 'Martina', 'Gutiérrez', '3517809134', '27633172', 8, 1, '2025-09-06', '15:00:00', 1, NULL),
(322, 'Claudia', 'Ruiz', '3510504556', '24884511', 11, 7, '2025-09-16', '17:45:00', 0, 19),
(323, 'Julieta', 'Pérez', '3511969379', '24802233', 5, 6, '2025-09-15', '16:30:00', 0, 15),
(324, 'Claudia', 'Ramírez', '3517482175', '39715634', 5, 6, '2025-09-20', '15:30:00', 1, NULL),
(325, 'Viviana', 'Ríos', '3511369594', '43463912', 1, 5, '2025-09-19', '09:45:00', 0, 12),
(326, 'Andrea', 'Silva', '3517439533', '40833650', 11, 1, '2025-09-17', '19:15:00', 0, 14),
(327, 'Sofía', 'Ramírez', '3517095214', '30962874', 9, 4, '2025-09-27', '11:15:00', 1, 5),
(328, 'Marisol', 'Pérez', '3514745171', '24723021', 6, 7, '2025-09-15', '19:45:00', 1, NULL),
(329, 'Tamara', 'Romero', '3518175496', '41426157', 1, 6, '2025-09-24', '10:15:00', 1, 13),
(330, 'Andrea', 'Silva', '3513174612', '21521586', 2, 1, '2025-09-03', '13:45:00', 0, 1),
(331, 'Camila', 'Vega', '3515869261', '36419761', 4, 5, '2025-09-27', '13:00:00', 1, 8),
(332, 'Carolina', 'Ibarra', '3515158506', '29257045', 11, 1, '2025-09-13', '10:45:00', 0, NULL),
(333, 'Lucía', 'Ramírez', '3515329318', '26951047', 1, 7, '2025-09-15', '14:15:00', 0, 2),
(334, 'María', 'Medina', '3514210205', '27983659', 3, 1, '2025-09-02', '11:30:00', 1, 17),
(335, 'Milagros', 'Fernández', '3511775891', '35166785', 5, 7, '2025-10-03', '12:00:00', 0, 8),
(336, 'Lucía', 'Ríos', '3516617711', '30808279', 10, 5, '2025-09-10', '10:15:00', 1, 14),
(337, 'Camila', 'Silva', '3518478961', '43547809', 8, 6, '2025-09-08', '19:15:00', 1, 1),
(338, 'Martina', 'Rojas', '3517661565', '42317316', 2, 6, '2025-09-17', '14:15:00', 0, NULL),
(339, 'Carla', 'Domínguez', '3511528098', '38846966', 6, 6, '2025-09-22', '19:00:00', 1, 15),
(340, 'Viviana', 'Alvarez', '3510494519', '37025939', 2, 4, '2025-09-10', '19:45:00', 1, 5),
(341, 'Ana', 'Díaz', '3514936899', '42648460', 3, 1, '2025-09-02', '18:30:00', 1, 2),
(342, 'Andrea', 'Torres', '3515022961', '24760523', 7, 6, '2025-09-02', '10:15:00', 1, 8),
(343, 'Julieta', 'Acosta', '3514599102', '25281799', 8, 5, '2025-09-04', '19:00:00', 1, 15),
(344, 'Rocío', 'Silva', '3517643815', '34425507', 11, 7, '2025-09-08', '13:45:00', 0, 3),
(345, 'Carolina', 'Ortiz', '3519003432', '28573811', 8, 1, '2025-09-19', '14:00:00', 1, 17),
(346, 'Julieta', 'López', '3516838851', '33323497', 2, 6, '2025-09-03', '15:00:00', 1, 4),
(347, 'Daniela', 'Ortiz', '3516416052', '40740842', 2, 6, '2025-09-30', '14:00:00', 1, 1),
(348, 'Daniela', 'Ortiz', '3518164535', '25641597', 9, 7, '2025-09-05', '17:00:00', 1, NULL),
(349, 'Agustina', 'Mendoza', '3512312432', '40213155', 11, 4, '2025-09-10', '19:00:00', 1, 9),
(350, 'Gabriela', 'Peralta', '3519799552', '34755991', 5, 5, '2025-09-05', '16:45:00', 0, 12),
(351, 'Agustina', 'Herrera', '3511477005', '29632706', 10, 7, '2025-09-05', '19:00:00', 1, 10),
(352, 'Paula', 'Peralta', '3518079359', '35959461', 2, 6, '2025-10-03', '11:00:00', 1, 19),
(353, 'Brenda', 'Gómez', '3518203778', '37540513', 7, 5, '2025-09-11', '14:30:00', 1, 7),
(354, 'Giselle', 'Silva', '3510515186', '29534390', 2, 5, '2025-09-17', '19:15:00', 0, 12),
(355, 'Andrea', 'Acosta', '3514629148', '32639260', 9, 6, '2025-09-22', '11:00:00', 0, 5),
(356, 'Agustina', 'Suárez', '3512357332', '25199097', 9, 7, '2025-09-05', '13:00:00', 0, 20),
(357, 'Giselle', 'Torres', '3519296222', '43248041', 6, 6, '2025-09-11', '16:00:00', 0, 15),
(358, 'Paula', 'Silva', '3514738347', '26510102', 5, 6, '2025-09-24', '19:45:00', 1, 18),
(359, 'Milagros', 'Herrera', '3516239240', '41514398', 2, 7, '2025-10-01', '14:00:00', 0, 3),
(360, 'Gabriela', 'Pérez', '3514782613', '35137343', 7, 1, '2025-09-23', '09:45:00', 1, 10),
(361, 'Carolina', 'Ortiz', '3511530515', '24900601', 12, 6, '2025-09-09', '09:30:00', 0, 20),
(362, 'Gabriela', 'Cabrera', '3517790104', '27236172', 5, 1, '2025-09-10', '17:45:00', 1, 1),
(363, 'Ana', 'Martínez', '3513697117', '40023239', 5, 4, '2025-09-02', '19:15:00', 0, 7),
(364, 'Noelia', 'Acosta', '3513962185', '22270941', 8, 6, '2025-10-03', '17:00:00', 1, NULL),
(365, 'Agustina', 'Romero', '3510515319', '44668781', 9, 5, '2025-09-22', '11:00:00', 0, 4),
(366, 'Marisol', 'Díaz', '3517722170', '29846298', 6, 1, '2025-09-13', '09:15:00', 1, 3),
(367, 'Marisol', 'Medina', '3517403450', '42002693', 7, 5, '2025-09-22', '13:00:00', 1, 6),
(368, 'Claudia', 'Ortiz', '3515277584', '22771982', 5, 7, '2025-09-06', '15:15:00', 0, 4),
(369, 'Carla', 'Torres', '3514479627', '31795959', 7, 7, '2025-09-29', '09:30:00', 0, 2),
(370, 'Carla', 'Márquez', '3516582029', '42752886', 4, 1, '2025-09-29', '09:15:00', 1, 18),
(371, 'Agustina', 'Ortiz', '3519092755', '34896130', 6, 7, '2025-09-05', '18:15:00', 1, 6),
(372, 'Belén', 'Romero', '3513102786', '38846608', 4, 6, '2025-09-08', '13:30:00', 1, 13),
(373, 'Brenda', 'Alvarez', '3517312172', '43936721', 12, 5, '2025-09-29', '10:30:00', 1, NULL),
(374, 'Claudia', 'Suárez', '3512258313', '24659934', 9, 5, '2025-09-16', '16:00:00', 1, 12),
(375, 'Paula', 'Ramírez', '3518291146', '44056299', 10, 6, '2025-10-01', '17:45:00', 0, NULL),
(376, 'Martina', 'Gutiérrez', '3511778528', '41467921', 1, 7, '2025-09-12', '11:45:00', 0, 20),
(377, 'Milagros', 'López', '3514225358', '29531079', 5, 7, '2025-09-06', '13:15:00', 1, NULL),
(378, 'Noelia', 'Medina', '3511829922', '42103407', 2, 1, '2025-09-22', '18:00:00', 1, NULL),
(379, 'Belén', 'Sosa', '3519690784', '41538834', 5, 6, '2025-09-20', '16:15:00', 0, 8),
(380, 'Brenda', 'Martínez', '3512767735', '40356140', 7, 5, '2025-09-10', '14:30:00', 1, NULL),
(381, 'Milagros', 'Medina', '3511537147', '28318777', 7, 5, '2025-09-10', '10:00:00', 1, 13),
(382, 'Carolina', 'Ibarra', '3512595327', '37271299', 1, 6, '2025-09-30', '16:30:00', 1, NULL),
(383, 'Milagros', 'Vega', '3513395004', '36605893', 7, 1, '2025-10-01', '13:00:00', 1, NULL),
(384, 'Lorena', 'Acosta', '3516506098', '26531548', 7, 1, '2025-09-24', '17:30:00', 1, 10),
(385, 'Gabriela', 'Medina', '3514991216', '32522458', 4, 4, '2025-09-22', '17:30:00', 1, 13),
(386, 'Milagros', 'Martínez', '3510025787', '24997619', 6, 7, '2025-10-03', '11:30:00', 1, NULL),
(387, 'Noelia', 'Mendoza', '3514958887', '43781361', 6, 5, '2025-09-20', '16:00:00', 0, 15),
(388, 'Valentina', 'Peralta', '3514097499', '27635083', 9, 4, '2025-09-04', '18:45:00', 1, 14),
(389, 'María', 'Rojas', '3513091307', '30525773', 7, 4, '2025-09-27', '11:45:00', 1, 10),
(390, 'Viviana', 'Ibarra', '3510283857', '37632087', 9, 6, '2025-09-25', '14:15:00', 1, 4),
(391, 'Giselle', 'Díaz', '3514973348', '30015969', 12, 4, '2025-09-15', '13:30:00', 1, 16),
(392, 'Martina', 'Ríos', '3515844198', '32740422', 1, 5, '2025-09-26', '14:15:00', 0, 3),
(393, 'Agustina', 'Alvarez', '3517473384', '38858195', 12, 7, '2025-09-18', '11:00:00', 0, 12),
(394, 'Carolina', 'Martínez', '3518330165', '44070542', 9, 1, '2025-10-01', '10:15:00', 1, NULL),
(395, 'Belén', 'Alvarez', '3517734540', '23001803', 7, 4, '2025-09-15', '17:00:00', 0, 20),
(396, 'Andrea', 'Alvarez', '3510672400', '30012381', 9, 7, '2025-09-22', '13:45:00', 0, 9),
(397, 'Viviana', 'Herrera', '3517431527', '41752846', 5, 5, '2025-09-17', '11:00:00', 0, 12),
(398, 'Julieta', 'Silva', '3516685161', '26200228', 1, 4, '2025-09-09', '16:30:00', 1, 2),
(399, 'Carolina', 'Vega', '3514549904', '41933974', 8, 1, '2025-09-24', '12:00:00', 0, 3),
(400, 'Camila', 'Márquez', '3518414593', '27352735', 12, 6, '2025-09-09', '16:15:00', 1, 13),
(401, 'Valentina', 'Cabrera', '3518783391', '37633077', 10, 1, '2025-09-29', '17:30:00', 0, NULL),
(402, 'Marisol', 'Medina', '3518398225', '37459764', 8, 7, '2025-09-29', '10:15:00', 1, NULL),
(403, 'Mariana', 'Martínez', '3517286790', '38750122', 7, 1, '2025-09-30', '19:30:00', 0, 2),
(404, 'Lorena', 'Sosa', '3519106518', '22021843', 7, 5, '2025-09-05', '16:00:00', 0, NULL),
(405, 'Gabriela', 'Gutiérrez', '3516567661', '42908629', 3, 1, '2025-09-09', '19:30:00', 1, 11),
(406, 'Milagros', 'López', '3513004786', '37848730', 6, 6, '2025-09-25', '12:15:00', 1, NULL),
(407, 'Vanesa', 'Alvarez', '3511069033', '22257908', 12, 4, '2025-09-03', '16:00:00', 1, NULL),
(408, 'Paula', 'Benítez', '3518302843', '44545653', 4, 7, '2025-09-16', '13:15:00', 1, 7),
(409, 'Florencia', 'Martínez', '3518926870', '31556643', 7, 6, '2025-09-25', '17:30:00', 1, NULL),
(410, 'Camila', 'Pérez', '3518733427', '21933538', 4, 6, '2025-09-27', '15:15:00', 0, 2),
(411, 'Martina', 'Gutiérrez', '3511751830', '29008076', 4, 1, '2025-09-25', '19:00:00', 0, 19),
(412, 'Rocío', 'Sosa', '3515403317', '28140076', 7, 5, '2025-09-26', '12:30:00', 1, 8),
(413, 'Sofía', 'Romero', '3515877175', '26815827', 10, 1, '2025-09-24', '14:45:00', 0, 1),
(414, 'Tamara', 'Romero', '3518996423', '31068442', 9, 6, '2025-09-15', '15:15:00', 1, 14),
(415, 'Florencia', 'Díaz', '3517772522', '44169711', 1, 7, '2025-10-02', '11:00:00', 0, 20),
(416, 'Marisol', 'Márquez', '3510062312', '20306736', 10, 1, '2025-10-03', '16:30:00', 1, 14),
(417, 'Giselle', 'Ríos', '3510088600', '37764308', 9, 1, '2025-09-18', '17:30:00', 1, 20),
(418, 'Mariana', 'Alvarez', '3512118233', '40796394', 2, 6, '2025-09-17', '14:30:00', 1, 5),
(419, 'Paula', 'Peralta', '3513341016', '32725988', 11, 1, '2025-09-25', '17:45:00', 0, NULL),
(420, 'Carla', 'Ríos', '3516591803', '27093979', 11, 1, '2025-10-01', '13:00:00', 0, 2),
(421, 'Julieta', 'Alvarez', '3515039261', '30042066', 7, 7, '2025-09-11', '18:15:00', 1, 15),
(422, 'Camila', 'Díaz', '3512185016', '27789460', 1, 5, '2025-09-05', '14:45:00', 1, 14),
(423, 'Mariana', 'Vega', '3517358662', '42806774', 12, 4, '2025-09-25', '10:30:00', 0, NULL),
(424, 'Florencia', 'López', '3516265511', '20174322', 2, 5, '2025-09-20', '16:30:00', 0, NULL),
(425, 'Julieta', 'Domínguez', '3517888610', '23007109', 10, 7, '2025-09-23', '17:00:00', 1, 19),
(426, 'Viviana', 'Ortiz', '3510466183', '39191807', 3, 4, '2025-09-11', '19:45:00', 1, 2),
(427, 'Florencia', 'Ríos', '3512126464', '36248106', 12, 4, '2025-09-05', '14:30:00', 1, 14),
(428, 'Noelia', 'Silva', '3513712406', '31147359', 7, 5, '2025-09-25', '14:45:00', 0, 20),
(429, 'Sofía', 'Torres', '3519375265', '29779377', 7, 5, '2025-10-02', '18:15:00', 1, 2),
(430, 'Agustina', 'Fernández', '3519398280', '44401231', 12, 5, '2025-09-30', '12:45:00', 1, NULL),
(431, 'Giselle', 'Ríos', '3512434266', '22481858', 7, 7, '2025-09-29', '18:45:00', 1, 11),
(432, 'Micaela', 'Martínez', '3515891135', '25581393', 7, 5, '2025-09-03', '18:45:00', 0, NULL),
(433, 'Ana', 'Romero', '3513888993', '34957846', 12, 7, '2025-09-24', '15:45:00', 0, 10),
(434, 'Agustina', 'Ruiz', '3517146112', '31687970', 9, 4, '2025-09-20', '14:45:00', 1, 9),
(435, 'Rocío', 'Fernández', '3517751401', '20757830', 12, 4, '2025-09-22', '19:00:00', 0, 1),
(436, 'Micaela', 'Pérez', '3515867362', '26254107', 10, 6, '2025-09-26', '09:15:00', 1, 7),
(437, 'Tamara', 'Cabrera', '3513341918', '26273331', 2, 6, '2025-09-24', '14:15:00', 1, 2),
(438, 'Milagros', 'Gutiérrez', '3515969691', '31674413', 10, 5, '2025-09-23', '16:15:00', 0, 12),
(439, 'Giselle', 'López', '3515134125', '43267869', 10, 6, '2025-09-10', '17:45:00', 1, NULL),
(440, 'Valentina', 'Pérez', '3517828274', '24707570', 1, 7, '2025-09-12', '14:45:00', 0, 5),
(441, 'Rocío', 'López', '3513688767', '33946243', 10, 1, '2025-09-29', '16:15:00', 0, NULL),
(442, 'Sofía', 'Martínez', '3514384279', '44914778', 2, 7, '2025-10-02', '17:00:00', 1, 2),
(443, 'Micaela', 'Rojas', '3510783613', '29989454', 2, 5, '2025-09-03', '16:30:00', 0, 8),
(444, 'Gabriela', 'Ramírez', '3513395962', '40674603', 10, 1, '2025-09-09', '12:15:00', 1, 5),
(445, 'Noelia', 'Medina', '3512544948', '42793502', 11, 5, '2025-09-09', '15:00:00', 0, NULL),
(446, 'Carolina', 'López', '3515367294', '41065902', 11, 1, '2025-09-27', '15:45:00', 0, 19),
(447, 'Camila', 'Herrera', '3514577590', '44586138', 9, 1, '2025-09-06', '16:30:00', 0, 6),
(448, 'Valentina', 'Rojas', '3513745458', '39392235', 11, 5, '2025-09-09', '18:45:00', 0, 18),
(449, 'Lucía', 'Fernández', '3517012770', '34354562', 5, 4, '2025-09-13', '11:30:00', 1, NULL),
(450, 'Florencia', 'Gómez', '3515206436', '41994434', 8, 4, '2025-09-06', '10:00:00', 0, NULL),
(451, 'Daniela', 'Benítez', '3518700516', '43287816', 7, 4, '2025-09-09', '16:00:00', 0, NULL),
(452, 'Ana', 'Torres', '3515426221', '37800903', 6, 6, '2025-09-01', '18:15:00', 0, 17),
(453, 'Belén', 'Mendoza', '3512697311', '24698061', 7, 5, '2025-09-08', '18:45:00', 0, 4),
(454, 'Carolina', 'Romero', '3511389994', '43168532', 9, 4, '2025-09-02', '19:30:00', 0, 13),
(455, 'Lorena', 'Ortiz', '3514037613', '36763208', 6, 5, '2025-09-05', '17:00:00', 1, NULL),
(456, 'Viviana', 'Rojas', '3519658271', '20560003', 1, 4, '2025-09-05', '09:30:00', 1, NULL);

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
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupo_turnos_id` (`grupo_turnos_id`);

--
-- Indices de la tabla `grupo_turnos`
--
ALTER TABLE `grupo_turnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fecha_estado` (`fecha`,`estado`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `turno_id` (`turno_id`),
  ADD KEY `idx_grupo_turnos_id` (`grupo_turnos_id`);

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
  ADD KEY `turno_ibfk_3` (`ID_servicio_FK`),
  ADD KEY `idx_grupo_turnos_id` (`grupo_turnos_id`);

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
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT de la tabla `grupo_turnos`
--
ALTER TABLE `grupo_turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT de la tabla `rubro_servicio`
--
ALTER TABLE `rubro_servicio`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=457;

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
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`grupo_turnos_id`) REFERENCES `grupo_turnos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`turno_id`) REFERENCES `turno` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`grupo_turnos_id`) REFERENCES `grupo_turnos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `turno`
--
ALTER TABLE `turno`
  ADD CONSTRAINT `turno_ibfk_2` FOREIGN KEY (`ID_estado_turno_FK`) REFERENCES `estado_turno` (`ID`),
  ADD CONSTRAINT `turno_ibfk_3` FOREIGN KEY (`ID_servicio_FK`) REFERENCES `servicio` (`ID`),
  ADD CONSTRAINT `turno_ibfk_4` FOREIGN KEY (`grupo_turnos_id`) REFERENCES `grupo_turnos` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
