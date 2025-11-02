-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-11-2025 a las 19:50:06
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
(30, 19, 1, 0, NULL, '2025-10-23 21:07:52', NULL, 28000.00, 'emitida', 'Rocio', 'Gomez', '40829063', NULL, NULL, NULL, '2025-10-23 21:07:52');

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
(44, 19, 48, 28000.00, 'efectivo', '2025-10-23 18:07:52', '2025-10-23 21:07:52', 'COMP-20251023-000044', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'completado');

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
(7, 'Masajes', '', NULL, 0, '2025-11-02 18:12:14', '2025-11-02 15:12:14', 15000.00, 60),
(8, 'Semipermanente', 'Manicura semipermanente de larga duración', NULL, 1, '2025-10-10 21:05:58', NULL, 17600.00, 60),
(9, 'Kapping', 'Kapping gel para fortalecimiento de uñas naturales', NULL, 1, '2025-10-10 21:05:58', NULL, 23000.00, 75),
(10, 'Prueba', '', 'img/vacia.webp', 0, '2025-11-02 18:12:18', '2025-11-02 15:12:18', 25000.00, 60),
(11, 'Microshading', '', 'img/microshading.jpg', 1, '2025-11-01 22:40:32', '2025-11-01 19:40:32', 177000.00, 120);

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
(53, 'Florencia', 'Albarracin', '3513875107', '40829057', 5, 5, '2025-10-31', '09:00:00', 0, 22);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `grupo_turnos`
--
ALTER TABLE `grupo_turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `rubro_servicio`
--
ALTER TABLE `rubro_servicio`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

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
