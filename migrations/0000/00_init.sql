-- Archivo de inicialización SQL
CREATE DATABASE IF NOT EXISTS facturador;

-- Verifica si el usuario ya existe antes de crearlo
CREATE USER IF NOT EXISTS 'sistemas'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON facturador.* TO 'sistemas'@'%';
FLUSH PRIVILEGES;

USE facturador;


