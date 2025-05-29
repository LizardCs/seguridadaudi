-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS repositorioseguridad;

-- Usar la base de datos
USE repositorioseguridad;

-- Crear la tabla productos si no existe
CREATE TABLE IF NOT EXISTS productos (
    idProducto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    stock DECIMAL(10,2),
    region VARCHAR(50),
    proveedor VARCHAR(100)
);

-- INSERT INTO productos (nombre, stock, region, proveedor) VALUES
-- ('Pitajaya', 2500.00, 'Amazonia', 'rsoria12@uta.edu.ec'),
-- ('Fresas', 128.50, 'Sierra', 'productosv13@uta.edu.ec');

-- Eliminamos la vista si ya existe
DROP VIEW IF EXISTS vista_productos_segura;

-- 3 tipos de enmascaramiento de datos
CREATE VIEW vista_productos_segura AS
SELECT 
    idProducto,
    nombre,
    -- Enmascarar stock: ejemplo 2$$$.00
    CONCAT(
        LEFT(FLOOR(stock), 1),
        REPEAT('$', LENGTH(FLOOR(stock)) - 1),
        '.',
        LEFT(LPAD(ROUND(stock * 100) % 100, 2, '0'), 1),
        '0'
    ) AS stock,

    -- Enmascarar region: ejemplo S*****
    CONCAT(LEFT(region, 1), REPEAT('*', LENGTH(region) - 1)) AS region,

    -- Enmascarar proveedor: ejemplo xxxxx@dominio.com
    CASE
        WHEN INSTR(proveedor, '@') > 0 THEN
            CONCAT(REPEAT('x', INSTR(proveedor, '@') - 1), SUBSTRING(proveedor, INSTR(proveedor, '@')))
        ELSE 'xxxxx'
    END AS proveedor

FROM productos;

-- Crear el usuario limitado para la vista
DROP USER IF EXISTS 'usuariovista'@'localhost';
CREATE USER 'usuariovista'@'localhost' IDENTIFIED BY 'clave123';

-- Otorgar solo acceso a la vista (no a la tabla productos)
GRANT SELECT ON repositorioseguridad.vista_productos_segura TO 'usuariovista'@'localhost';

-- actualizar privilegios
FLUSH PRIVILEGES;
