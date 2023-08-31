-- Archivo de inicialización SQL
CREATE DATABASE IF NOT EXISTS facturador;

-- Creación de usuario y otorgamiento de permisos
CREATE USER 'sistemas'@'%' IDENTIFIED BY 'sistemas';
GRANT ALL PRIVILEGES ON facturador.* TO 'sistemas'@'%';
FLUSH PRIVILEGES;

USE facturador;


