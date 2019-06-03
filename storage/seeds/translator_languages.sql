-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 13-07-2017 a las 17:01:35
-- Versión del servidor: 5.5.44-MariaDB
-- Versión de PHP: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fannics_pursury`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `translator_languages`
--
DROP TABLE IF EXISTS `translator_languages`;

CREATE TABLE `translator_languages` (
  `id` int(10) UNSIGNED NOT NULL,
  `locale` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `translator_languages`
--

INSERT INTO `translator_languages` (`id`, `locale`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(37, 'ES-es', 'ESPAñA-español', '2017-04-02 12:00:56', '2017-04-02 12:00:56', NULL),
(17, 'US-en', 'UNITED STATES-english', '2017-02-19 23:45:09', '2017-02-26 20:34:11', NULL),
(1, 'en', 'DEFAULT', '2017-02-19 04:00:00', '2017-02-19 04:00:00', NULL),
(38, 'US-es', 'ESTADOS UNIDOS-español', '2017-04-02 12:01:37', '2017-04-04 09:00:07', NULL),
(39, 'DE-de', 'GERMANY-german', '2017-04-04 17:29:36', '2017-04-04 17:29:36', NULL),
(40, 'IT-it', 'ITALY-italian', '2017-04-04 17:32:39', '2017-04-04 17:32:39', NULL),
(41, 'GB-en', 'UNITED KINGDOM-english', '2017-04-04 17:33:32', '2017-04-04 17:33:32', NULL),
(42, 'FR-fr', 'FRANCE-french', '2017-04-04 17:34:49', '2017-04-04 17:34:49', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `translator_languages`
--
ALTER TABLE `translator_languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `translator_languages_locale_unique` (`locale`),
  ADD UNIQUE KEY `translator_languages_name_unique` (`name`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `translator_languages`
--
ALTER TABLE `translator_languages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
