-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-01-2025 a las 22:26:59
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
-- Base de datos: `pasioninka`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `orden`) VALUES
(7, 'Entradas Frías', 1),
(8, 'Entradas Calientes', 2),
(9, 'Platos de Fondo', 4),
(10, 'Pastas', 3),
(11, 'Arroces', 5),
(12, 'Filetes', 6),
(13, 'Sopas', 7),
(14, 'Pescados y Mariscos', 8),
(15, 'Guarnición', 9),
(16, 'Postres', 10),
(17, 'Bebidas', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `descripcion`, `categoria_id`) VALUES
(4, 'Lomo Saltado', 11990.00, 'Carne salteada con cebolla, tomate y papas fritas', 9),
(27, 'Ceviche de Pescado', 9900.00, 'Cubos de pescado fresco marinados en limón', 7),
(28, 'Ceviche Mixto', 10990.00, 'Mezcla de mariscos y pescados marinados', 7),
(29, 'Causa rellena con camarones', 8990.00, 'Masa de papa con camarones y salsa de aceituna', 7),
(30, 'Causa de pollo a la limeña', 8900.00, 'Causa clásica de pollo con el auténtico sabor limeño', 7),
(31, 'Chicharrón de Pollo', 8990.00, 'Trozos de pollo crujientes, acompañada con papas y salsa tártara', 8),
(32, 'Chicharrón de pescado', 8990.00, 'Cubos de pescado fritos, acompañada con papas y ensalada criolla', 8),
(33, 'Chicharrón mixto', 9990.00, 'Pescados y mariscos fritos acompañada con papas y ensalada criolla', 8),
(34, 'Chicharrón de camarones', 9990.00, 'Camarones crocantes acompañado con papas', 8),
(35, 'Jalea marina Mixta', 17900.00, 'Variedad de Mariscos fritos acompañado de salsa criolla', 8),
(36, 'Ají de gallina', 8990.00, 'Pollo deshilachado bañado en una deliciosa crema de aji amarillo', 9),
(37, 'Filete a lo Pobre', 11990.00, 'Filete a la plancha + arroz + papas fritas + 2 huevos a la inglesa', 9),
(38, 'Pollo salteado', 10990.00, 'Trozos de pollo saltado con salsa de soya + cebolla + tomate + arroz + papas fritas', 9),
(39, 'Pollo a la plancha', 8900.00, 'Filete de pechuga de pollo a la plancha + papas fritas', 9),
(40, 'Suprema de Pollo', 8900.00, 'Filete de pechuga de pollo apanado con pan molido + papas fritas', 9),
(41, 'Chorrillana del inka', 12990.00, 'Trozos de filete + champiñones + milanesa + pollo + papas fritas + huevo ', 9),
(42, 'Saltado del Inka', 12990.00, 'Cubos de filetes + trozos de pechuga de pollo + camarones + arroz + papas fritas', 9),
(43, 'Crema Volteada', 3900.00, 'postre cremoso con caramelo', 16),
(44, 'Saltado mar y tierra', 11990.00, 'Camarones, filete y champiñones saltados al wok con cebolla, tomate, salsa de soja, arroz y papas', 9),
(45, 'Tallarin salteado de pollo', 8990.00, 'Tallarín + trozos de pechuga de pollo salteado con tomate y salsa de soya', 10),
(46, 'Tallarín saltado de carne', 10990.00, 'Tallarín + trozos de carne con tomate y salsa de soya', 10),
(47, 'Tallarín salteado 3 sabores', 11990.00, 'Carne + pollo + camarones', 10),
(48, 'Fetuccini a la huancaína con lomo', 11990.00, 'Bañada en salsa de soya, acompañada de cebolla, tomate y medallones de filete', 10),
(49, 'Chaufa Especial', 9990.00, 'Arroz frito con especias orientales, salteado con trozos de carne, pollo, camarones, cebollín', 11),
(50, 'Chaufa de Carne', 9500.00, 'Arroz chaufa de carne, salteado con huevo, cebollín, salsa de soya y especias orientales', 11),
(51, 'Chaufa de Pollo', 8990.00, 'Arroz frito a las especias orientales, salteado con trozos de pollo, sésamo y cebollín', 11),
(52, 'Arroz con mariscos', 10990.00, 'Arroz salteado con mixtura de mariscos, en salsa de coral de camarones con salsa criolla', 11),
(53, 'Risotto a la huancaína con lomo', 10990.00, 'Risotto cremoso con salsa huancaína, acompañado de lomo saltado con cebolla y pimientos', 11),
(54, 'Filete Mar y Tierra', 11990.00, 'Filete de vacuno a la plancha con camarones en salsa de estragón, acompañado de arroz blanco', 12),
(55, 'Filete a lo macho', 11990.00, 'Filete a la plancha bañado en una deliciosa salsa de mariscos, acompañado de arroz verde al cilantro', 12),
(56, 'Filete a la Plancha', 9990.00, 'Filete a la plancha, acompañado de arroz blanco y papas fritas crujientes', 12),
(57, 'Filete al Pisco con Risotto', 12990.00, 'Filete a la plancha con salsa bechamel flameado en pisco Perú, acompañado de risotto verde\r\n\r\n\r\n\r\n\r\n\r\n\r\n', 12),
(58, 'Filete Miñon', 10990.00, 'Filete a la plancha con reducción de vino tinto, champiñones, tocino y arroz blanco', 12),
(59, ' Parihuela receta de la abuela', 9990.00, 'fumet de pescado con mariscos en salsa de crustáceos', 13),
(60, 'Sudado de pescado', 8990.00, '\r\nFilete de pescado con cebolla, tomate, vino blanco y arroz blanco, suave y delicioso', 13),
(61, 'Pescado a lo macho', 10990.00, 'Pescado a la plancha con variedad de mariscos y una deliciosa salsa americana', 14),
(62, 'Pescado en salsa de camarones', 10990.00, 'Pescado a la plancha con salsa de camarones, acompañado de arroz y verduras', 14),
(63, 'Salmon Oriental', 10990.00, 'Salmón a la plancha bañado en salsa de coral, acompañado de verduras salteadas en salsa de soya', 14),
(64, 'Salmón a la Plancha', 10990.00, 'Salmón a la plancha con un toque de limón, acompañado de guarnición al gusto. Simple y delicioso', 14),
(65, 'Ensalada del Inka', 5990.00, 'Lechuga, tomate, palta, pepino, huevo', 15),
(66, 'Ensalada Cesar', 6990.00, 'Ensalada de lechuga, pollo a la parrilla, crutones, parmesano, alcaparras y aceite de oliva', 15),
(67, 'Suspiro Limeño', 3990.00, 'Postre peruano con manjar blanco y merengue, dulce y cremoso.', 16),
(68, 'Sour Clásico', 4500.00, 'Cóctel peruano con pisco, limón, clara de huevo y jarabe de goma, refrescante y suave', 17),
(69, 'Catedral', 6990.00, 'Cóctel con pisco, licor de hierbas y un toque cítrico, ideal para los amantes de sabores intensos', 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contrasena`) VALUES
(1, 'admin', '827ccb0eea8a706c4c34a16891f84e7b');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `numero_comprobante` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `numero_comprobante`, `total`, `fecha`) VALUES
(18, 349754, 11990.00, '2025-01-17 16:51:42');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
