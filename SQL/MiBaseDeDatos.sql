-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 01-06-2026 a las 12:33:25
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
-- Base de datos: `MiBaseDeDatos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Curso`
--

CREATE TABLE `Curso` (
  `ID_curso` int(11) NOT NULL,
  `Nombre` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Curso`
--

INSERT INTO `Curso` (`ID_curso`, `Nombre`) VALUES
(1, '1º ESO A'),
(2, '1º ESO B'),
(3, '1º ESO C'),
(4, '1º ESO D'),
(5, '2º ESO A'),
(6, '2º ESO B'),
(7, '2º ESO C'),
(8, '2º ESO D'),
(9, '3º ESO A'),
(10, '3º ESO B'),
(11, '3º ESO C'),
(12, '3º ESO D'),
(13, '4º ESO A'),
(14, '4º ESO B'),
(15, '4º ESO C'),
(16, '4º ESO D'),
(17, '1º BACH A'),
(18, '1º BACH B'),
(19, '2º BACH A'),
(20, '2º BACH B'),
(21, '1º FP DAM'),
(22, '2º FP DAM');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Reserva`
--

CREATE TABLE `Reserva` (
  `ID_reservas` int(11) NOT NULL,
  `Fecha_reservada` date DEFAULT NULL,
  `Hora_reservada` varchar(10) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Reserva`
--

INSERT INTO `Reserva` (`ID_reservas`, `Fecha_reservada`, `Hora_reservada`, `id_usuario`, `id_curso`) VALUES
(1, '2026-06-01', '1', 1, 1),
(2, '2026-05-31', '1', 1, 1),
(3, '2026-05-31', '2', 1, 1),
(4, '2026-06-01', '2', 3, 1),
(5, '2026-06-01', '3', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuarios`
--

CREATE TABLE `Usuarios` (
  `ID_usuario` int(11) NOT NULL,
  `Nombre` varchar(100) DEFAULT NULL,
  `Apellido1` varchar(100) DEFAULT NULL,
  `Apellido2` varchar(100) DEFAULT NULL,
  `Correo` varchar(255) DEFAULT NULL,
  `Contraseña` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Usuarios`
--

INSERT INTO `Usuarios` (`ID_usuario`, `Nombre`, `Apellido1`, `Apellido2`, `Correo`, `Contraseña`) VALUES
(1, 'María', 'García', 'López', 'info@info.es', '1234'),
(2, 'Juan', 'Pérez', 'Martínez', 'info1@info.es', '1234'),
(3, 'Lucía', 'Santos', 'Ruiz', 'info2@info.es', '1234');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Curso`
--
ALTER TABLE `Curso`
  ADD PRIMARY KEY (`ID_curso`);

--
-- Indices de la tabla `Reserva`
--
ALTER TABLE `Reserva`
  ADD PRIMARY KEY (`ID_reservas`),
  ADD KEY `fk_reserva_usuario` (`id_usuario`),
  ADD KEY `fk_reserva_curso` (`id_curso`);

--
-- Indices de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD PRIMARY KEY (`ID_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Reserva`
--
ALTER TABLE `Reserva`
  MODIFY `ID_reservas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Reserva`
--
ALTER TABLE `Reserva`
  ADD CONSTRAINT `fk_reserva_curso` FOREIGN KEY (`id_curso`) REFERENCES `Curso` (`ID_curso`),
  ADD CONSTRAINT `fk_reserva_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios` (`ID_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
