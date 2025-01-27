-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 10-04-2022 a las 23:08:32
-- Versión del servidor: 8.0.28
-- Versión de PHP: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: facturador
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla caja_cajeros
--

DROP TABLE IF EXISTS caja_cajeros;
CREATE TABLE IF NOT EXISTS caja_cajeros (
  id bigint NOT NULL AUTO_INCREMENT,
  nombre varchar(250) COLLATE utf8_bin NOT NULL,
  user_id bigint NOT NULL,
  puesto_cf bigint DEFAULT NULL,
  puesto_rem bigint DEFAULT NULL,
  puesto_ws bigint DEFAULT NULL,
  printer_rep varchar(250) COLLATE utf8_bin DEFAULT NULL,
  estado int NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_bin;
COMMIT;