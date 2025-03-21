-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-12-2023 a las 15:14:15
-- Versión del servidor: 10.1.38-MariaDB
-- Versión de PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `labo2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juego`
--

CREATE TABLE `juego` (
  `id_juego` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `jugadores` smallint(6) DEFAULT NULL,
  `lanzamiento` date DEFAULT NULL,
  `genero` varchar(50) NOT NULL,
  `portada` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `juego`
--

INSERT INTO `juego` (`id_juego`, `titulo`, `jugadores`, `lanzamiento`, `genero`, `portada`) VALUES
(1, 'Diablo IV', 1, '2023-06-05', 'Rol', 'diablo_iv.jpg'),
(2, 'Alan Wake', 1, '2010-05-14', 'Survival', 'alan_wake.jpg'),
(3, 'Age Of Empires IV', 1, '2021-10-28', 'Estrategia', ''),
(4, 'Path Of Exile 2', 1, '2024-06-07', 'Rol', 'path_of_exile_2.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `pass` varchar(40) NOT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL,
  `activado` char(1) NOT NULL DEFAULT 'S'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `usuario`, `pass`, `tipo`, `foto`, `activado`) VALUES
(1, 'jpepe', '39d19e8bec728e2cd4d2a4199e9434ad7df5e459', 'Administrador', 'jpepe.jpg', 'S'),
(2, 'mruiz', '397747e2ea1fd4995810616087d0e6c4ab7014d4', 'Administrador', 'mruiz.jpg', 'S'),
(3, 'dsingh', 'abab1d2a5f608941022d1b43da6c92d574d55060', 'Común', 'dsingh.jpg', 'S'),
(4, 'cflores', 'd1662c4daf4585ab458111cff0b30c954603d0ea', 'Común', '', 'S'),
(5, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrador', 'admin.png', 'S'),
(6, 'test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 'Común', '', 'N');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `juego`
--
ALTER TABLE `juego`
  ADD PRIMARY KEY (`id_juego`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `juego`
--
ALTER TABLE `juego`
  MODIFY `id_juego` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
