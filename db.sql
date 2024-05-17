-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-05-2024 a las 14:18:51
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
-- Base de datos: `db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_cate` int(11) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `nombre` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `mensaje` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`nombre`, `email`, `telefono`, `mensaje`) VALUES
('asdasds', 'sdasdas@sena.com', 'asdasd', 'areasda'),
('asdasdas', 'asdasaaad@sena.com', '123456789', 'asdsadasdMessage'),
('brayan', 'ezbrayanp@gmail.com', '3202174961', 'debo mirar los campos text number (definir)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pres`
--

CREATE TABLE `detalle_pres` (
  `id_de_pres` int(11) NOT NULL,
  `id_prestamo` int(11) DEFAULT NULL,
  `id_herramienta` int(11) DEFAULT NULL,
  `cantidad_prestada` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_usuarios`
--

CREATE TABLE `detalle_usuarios` (
  `id_detalle` bigint(11) NOT NULL,
  `documento` int(11) NOT NULL,
  `ficha` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_reportes`
--

CREATE TABLE `deta_reportes` (
  `id_de_reporte` int(11) NOT NULL,
  `id_reporte` int(11) DEFAULT NULL,
  `id_herramienta` int(11) DEFAULT NULL,
  `cantidad_reportada` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `nit` int(9) NOT NULL,
  `nombre_empre` varchar(100) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `gmail` varchar(150) NOT NULL,
  `telefono` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`nit`, `nombre_empre`, `direccion`, `gmail`, `telefono`) VALUES
(123456789, 'sena', 'picaleñaa', 'sena@sema.com', '3124758405');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formacion`
--

CREATE TABLE `formacion` (
  `id_formacion` int(6) NOT NULL,
  `formacion` varchar(50) DEFAULT NULL,
  `jornada` enum('mañana','tarde','noche','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `formacion`
--

INSERT INTO `formacion` (`id_formacion`, `formacion`, `jornada`) VALUES
(2500591, 'ADSO', 'mañana');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herrramienta`
--

CREATE TABLE `herrramienta` (
  `id_herramienta` int(11) NOT NULL,
  `nombre_he` varchar(40) DEFAULT NULL,
  `id_cate` int(11) DEFAULT NULL,
  `img_herramienta` text NOT NULL,
  `estado` enum('disponible','no disponible','','') DEFAULT NULL,
  `codigo_barras` text DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `licencia`
--

CREATE TABLE `licencia` (
  `licencia` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo','','') NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `nit` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `licencia`
--

INSERT INTO `licencia` (`licencia`, `estado`, `fecha_inicio`, `fecha_fin`, `nit`) VALUES
('123456', 'activo', '2024-02-26 16:43:18', '2025-02-26 16:43:18', 123456789),
('65e88323d19aa', 'activo', '2024-03-06 15:52:19', '2025-03-06 15:52:19', 154879),
('660bf2c24920a', 'inactivo', '2024-04-02 13:57:54', '2024-04-02 13:58:54', 14789);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL,
  `documento` bigint(11) DEFAULT NULL,
  `fecha_prestamo` date DEFAULT NULL,
  `fecha_devolucion` date DEFAULT NULL,
  `estado_prestamo` enum('prestado','devuelto','reportado','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id_reporte` int(11) NOT NULL,
  `id_prestamo` int(11) DEFAULT NULL,
  `fecha_reporte` date DEFAULT NULL,
  `estado_reporte` enum('activo','inactivo','','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `rol` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `rol`) VALUES
(1, 'administrador'),
(2, 'aprendiz'),
(3, 'superadmin'),
(4, 'instructor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tip_doc`
--

CREATE TABLE `tip_doc` (
  `id_tip_doc` int(11) NOT NULL,
  `tipo_doc` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tip_doc`
--

INSERT INTO `tip_doc` (`id_tip_doc`, `tipo_doc`) VALUES
(1, 'cedula de ciudadania '),
(2, 'tarjeta de identidad');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `documento` bigint(11) NOT NULL,
  `contraseña` text DEFAULT NULL,
  `nombre` varchar(40) NOT NULL,
  `id_tip_doc` int(11) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `nit` int(9) DEFAULT NULL,
  `tyc` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`documento`, `contraseña`, `nombre`, `id_tip_doc`, `email`, `id_rol`, `estado`, `nit`, `tyc`) VALUES
(412545, '$2y$15$jSe6TacwVGidi668X0zRVuoapJ3GjcTAbwUy8/NX5fVmr3VucG7o2', 'adasdasdasd', NULL, 'dasd@sena.com', 1, 'activo', 123456789, 'si'),
(1101546, '$2y$15$O5pAdl0Fa.e6wpibp6wKvOvZxLwbzTcRa7ehFgb8HIJ.SLV7VUR52', 'adsadvthllo', NULL, 'asda@sena.com', 1, 'activo', 123456789, 'si'),
(1234564, '$2y$15$7ASqZJ59PRH1AfgpeEMJCuemPhBz2R/VucuC8g7plL0/sQ8rqEude', 'sadasdasdfdfd', NULL, 'asdasd@sena.com', 1, 'activo', 123456789, 'si'),
(1245785, '$2y$15$7Qr.UjY7iPmhqBDC5ScgM.eXqhto1ECM343TGySLLHGagFi4tayTu', 'deigo betanic', NULL, 'asdadasdghhj@sena.com', 1, 'activo', 123456789, 'si'),
(14412348, '$2y$15$HG3HIJDyz.//Jzm1ISor1uvr5Gx3iml9Yjp/K2Qz30U7Ze5qYwrre', 'asascascasadas', 1, 'cjfifueasdascasc@sena.com', 4, 'activo', 123456789, 'si'),
(123456781, '$2y$15$WLEIoupjefUbOY2SstCmUuOr1Ochm5w3O.Lb84c5UtkoPWPGc46cS', 'asdasdasdasd', 1, 'asdasda@sena.com', 4, 'activo', 123456789, 'si'),
(456789123, '$2y$15$zLnhZJGgze25ocCb85b6ou8U/l.Osjw5GQ.EZJ43dcm3WY4DboF0W', 'armero julian', 1, 'asdasdasd@sena.com', 4, 'activo', 123456789, 'si'),
(1107975321, '$2y$15$nOt2zC5P5CcV9XjghEiMqOoi04tW4NJLLCCTXDAR0LobQok.vLY5m', 'asdasdadasd', 1, 'sadasdas@sena.com', 2, 'activo', 123456789, 'si'),
(1107975322, '$2y$10$XGTsQD2pLExCOHti07bZh.XtQ27it3b4qsBfDzekKnR7WAACygQXW', 'cristian figueroa', 1, 'cristianfigueroa040@gmail.com', 1, 'activo', 123456789, 'si'),
(1223054461, '$2y$15$idUHq7aewPgutM0PTrn.9.vHsD7a4813TNJ6nV.vPIbeJ1W7rKc7u', 'armero figueroa', 1, 'asdfweda@sena.com', 4, 'activo', 123456789, 'si'),
(2512314542, '$2y$15$qBcy5zQ4ec92KBPcj5O.4.Hmed.sbk7evvOeQ3xXb3ExquZDDygt2', 'asdasfdsfsf', 1, 'armero@sena.com', 4, 'activo', 123456789, 'si');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_cate`);

--
-- Indices de la tabla `detalle_pres`
--
ALTER TABLE `detalle_pres`
  ADD PRIMARY KEY (`id_de_pres`);

--
-- Indices de la tabla `detalle_usuarios`
--
ALTER TABLE `detalle_usuarios`
  ADD PRIMARY KEY (`id_detalle`);

--
-- Indices de la tabla `deta_reportes`
--
ALTER TABLE `deta_reportes`
  ADD PRIMARY KEY (`id_de_reporte`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`nit`);

--
-- Indices de la tabla `formacion`
--
ALTER TABLE `formacion`
  ADD PRIMARY KEY (`id_formacion`);

--
-- Indices de la tabla `herrramienta`
--
ALTER TABLE `herrramienta`
  ADD PRIMARY KEY (`id_herramienta`);

--
-- Indices de la tabla `licencia`
--
ALTER TABLE `licencia`
  ADD PRIMARY KEY (`licencia`),
  ADD KEY `licencia_ibfk_1` (`nit`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id_prestamo`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id_reporte`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tip_doc`
--
ALTER TABLE `tip_doc`
  ADD PRIMARY KEY (`id_tip_doc`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`documento`),
  ADD KEY `id_tip_doc` (`id_tip_doc`),
  ADD KEY `id_rol` (`id_rol`),
  ADD KEY `nit` (`nit`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_cate` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalle_pres`
--
ALTER TABLE `detalle_pres`
  MODIFY `id_de_pres` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT de la tabla `detalle_usuarios`
--
ALTER TABLE `detalle_usuarios`
  MODIFY `id_detalle` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `deta_reportes`
--
ALTER TABLE `deta_reportes`
  MODIFY `id_de_reporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `herrramienta`
--
ALTER TABLE `herrramienta`
  MODIFY `id_herramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tip_doc`
--
ALTER TABLE `tip_doc`
  MODIFY `id_tip_doc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_tip_doc`) REFERENCES `tip_doc` (`id_tip_doc`),
  ADD CONSTRAINT `usuario_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`),
  ADD CONSTRAINT `usuario_ibfk_5` FOREIGN KEY (`nit`) REFERENCES `empresa` (`nit`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
